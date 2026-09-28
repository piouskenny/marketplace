<?php

namespace App\Actions\Payment;

use App\Actions\Conversations\CreateConversationAction;
use App\Contracts\PaymentGateway;
use App\Enums\ConnectionStatus;
use App\Enums\PaymentStatus;
use App\Events\ConnectionActivated;
use App\Models\ConnectionRequest;
use App\Models\Payment;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Verify a payment via the gateway and activate the connection if successful.
 *
 * This is the ONLY path that transitions a connection to Connected status.
 * Called from both the callback controller and the webhook handler.
 * Must be idempotent — safe to call multiple times for the same reference.
 */
class VerifyAndActivatePayment
{
    public function __construct(
        private readonly PaymentGateway $gateway,
        private readonly CreateConversationAction $createConversationAction,
    ) {}

    public function execute(string $reference): ConnectionRequest
    {
        $payment = Payment::where('reference', $reference)->first();

        // 1. Idempotency Check: If payment exists and is ALREADY successful, return connection
        if ($payment && $payment->status === PaymentStatus::Successful) {
            $conn = $payment->connectionRequest;
            $statusValue = $conn->status instanceof ConnectionStatus ? $conn->status->value : (string) $conn->status;
            if ($conn && $statusValue === ConnectionStatus::Connected->value) {
                // Ensure conversation exists
                $this->createConversationAction->execute($conn);
                return $conn;
            }
        }

        // 2. Call Gateway Verification
        $verificationResult = $this->gateway->verify($reference);

        // Find associated connection request from payment record or verification metadata
        $connectionRequestId = $payment?->connection_request_id
            ?? ($verificationResult->metadata['connection_request_id'] ?? null);

        if (!$connectionRequestId) {
            Log::error('Payment verification failed: No connection request ID found for reference', ['reference' => $reference]);
            throw new \InvalidArgumentException("No connection request associated with reference: {$reference}");
        }

        $connectionRequest = ConnectionRequest::findOrFail($connectionRequestId);
        $expectedAmountInKobo = (int) config('marketplace.connection_fee', 100000);
        $expectedCurrency = (string) config('marketplace.currency', 'NGN');

        // 3. Validation
        if (!$verificationResult->successful || $verificationResult->status !== PaymentStatus::Successful) {
            if ($payment) {
                $payment->update([
                    'status' => $verificationResult->status,
                    'metadata' => array_merge($payment->metadata ?? [], ['verification_error' => 'Payment status is not successful']),
                ]);
            }
            Log::warning('Paystack transaction verification unsuccessful', [
                'reference' => $reference,
                'status' => $verificationResult->status->value,
            ]);
            throw new \RuntimeException('Payment verification failed or payment was not successful.');
        }

        if ($verificationResult->amountInKobo !== $expectedAmountInKobo) {
            if ($payment) {
                $payment->update([
                    'status' => PaymentStatus::Failed,
                    'metadata' => array_merge($payment->metadata ?? [], ['amount_mismatch' => true, 'received' => $verificationResult->amountInKobo]),
                ]);
            }
            Log::error('Payment amount mismatch', [
                'reference' => $reference,
                'expected' => $expectedAmountInKobo,
                'received' => $verificationResult->amountInKobo,
            ]);
            throw new \InvalidArgumentException('Payment amount mismatch.');
        }

        if (strtoupper($verificationResult->currency) !== strtoupper($expectedCurrency)) {
            if ($payment) {
                $payment->update([
                    'status' => PaymentStatus::Failed,
                    'metadata' => array_merge($payment->metadata ?? [], ['currency_mismatch' => true, 'received' => $verificationResult->currency]),
                ]);
            }
            Log::error('Payment currency mismatch', [
                'reference' => $reference,
                'expected' => $expectedCurrency,
                'received' => $verificationResult->currency,
            ]);
            throw new \InvalidArgumentException('Payment currency mismatch.');
        }

        // 4. DB Transaction (Idempotent Activation)
        return DB::transaction(function () use ($payment, $connectionRequest, $verificationResult, $reference, $expectedAmountInKobo, $expectedCurrency) {
            if (!$payment) {
                $payment = Payment::create([
                    'user_id' => $connectionRequest->initiator_id,
                    'connection_request_id' => $connectionRequest->id,
                    'reference' => $reference,
                    'provider' => $verificationResult->provider,
                    'amount' => $expectedAmountInKobo,
                    'currency' => $expectedCurrency,
                    'status' => PaymentStatus::Successful,
                    'provider_reference' => $verificationResult->providerReference,
                    'paid_at' => $verificationResult->paidAt ? Carbon::parse($verificationResult->paidAt) : now(),
                    'metadata' => $verificationResult->metadata,
                ]);
            } else {
                $payment->update([
                    'status' => PaymentStatus::Successful,
                    'provider_reference' => $verificationResult->providerReference ?: $payment->provider_reference,
                    'paid_at' => $verificationResult->paidAt ? Carbon::parse($verificationResult->paidAt) : ($payment->paid_at ?? now()),
                    'metadata' => array_merge($payment->metadata ?? [], $verificationResult->metadata),
                ]);
            }

            // Update Connection Status
            $connectionRequest->update([
                'status' => ConnectionStatus::Connected,
                'connected_at' => $connectionRequest->connected_at ?? now(),
            ]);

            // Create Conversation idempotently
            $this->createConversationAction->execute($connectionRequest);

            // Dispatch activation event
            ConnectionActivated::dispatch($connectionRequest->id, $payment->id);

            return $connectionRequest->fresh(['conversation']);
        });
    }
}

