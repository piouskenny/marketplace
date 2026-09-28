<?php

namespace App\Actions\Payment;

use App\Contracts\PaymentGateway;
use App\DataTransferObjects\PaymentInitiationData;
use App\Enums\ConnectionStatus;
use App\Enums\PaymentStatus;
use App\Models\ConnectionRequest;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Initialize a payment for a connection request.
 *
 * Creates a Payment record and delegates to the PaymentGateway contract
 * for provider-specific initialization (Paystack checkout URL, etc.).
 *
 * Used by: Controllers, Livewire components, API endpoints.
 */
class InitializeConnectionPayment
{
    public function __construct(
        private readonly PaymentGateway $gateway,
    ) {}

    /**
     * @return array{authorization_url: string, reference: string, access_code: string|null}
     */
    public function execute(int $connectionRequestId, int $payerUserId): array
    {
        $connectionRequest = ConnectionRequest::findOrFail($connectionRequestId);

        // Authorize payer is initiator
        if ((int) $connectionRequest->initiator_id !== (int) $payerUserId) {
            throw new \Illuminate\Auth\Access\AuthorizationException('Only the applicant who initiated the connection request is authorized to pay.');
        }

        // Validate status (must be Accepted or PaymentPending)
        $statusValue = $connectionRequest->status instanceof ConnectionStatus
            ? $connectionRequest->status->value
            : (string) $connectionRequest->status;

        if ($statusValue !== ConnectionStatus::Accepted->value && $statusValue !== ConnectionStatus::PaymentPending->value) {
            throw new \InvalidArgumentException('Connection request is not in a payable status (current status: ' . $statusValue . ').');
        }

        $payer = User::findOrFail($payerUserId);
        $amountInKobo = (int) config('marketplace.connection_fee', 100000);
        $currency = (string) config('marketplace.currency', 'NGN');

        // Check for existing pending payment record
        $existingPayment = Payment::where('connection_request_id', $connectionRequest->id)
            ->where('user_id', $payer->id)
            ->where('status', PaymentStatus::Pending)
            ->latest()
            ->first();

        if ($existingPayment) {
            $reference = $existingPayment->reference;
        } else {
            $reference = 'CONN-FEE-' . strtoupper(Str::random(12));
        }

        $callbackUrl = route('connections.pay.callback');

        $initiationData = new PaymentInitiationData(
            email: $payer->email,
            amountInKobo: $amountInKobo,
            reference: $reference,
            currency: $currency,
            callbackUrl: $callbackUrl,
            metadata: [
                'connection_request_id' => $connectionRequest->id,
                'payer_id' => $payer->id,
                'custom_fields' => [
                    [
                        'display_name' => 'Connection Request ID',
                        'variable_name' => 'connection_request_id',
                        'value' => (string) $connectionRequest->id,
                    ],
                ],
            ],
        );

        return DB::transaction(function () use ($connectionRequest, $payer, $reference, $amountInKobo, $currency, $existingPayment, $initiationData) {
            $statusValue = $connectionRequest->status instanceof ConnectionStatus
                ? $connectionRequest->status->value
                : (string) $connectionRequest->status;

            if ($statusValue === ConnectionStatus::Accepted->value) {
                $connectionRequest->update(['status' => ConnectionStatus::PaymentPending]);
            }

            if (!$existingPayment) {
                Payment::create([
                    'user_id' => $payer->id,
                    'connection_request_id' => $connectionRequest->id,
                    'reference' => $reference,
                    'provider' => $this->gateway->provider(),
                    'amount' => $amountInKobo,
                    'currency' => $currency,
                    'status' => PaymentStatus::Pending,
                    'metadata' => $initiationData->metadata,
                ]);
            }

            return $this->gateway->initialize($initiationData);
        });
    }
}

