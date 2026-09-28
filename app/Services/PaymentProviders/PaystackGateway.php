<?php

namespace App\Services\PaymentProviders;

use App\Contracts\PaymentGateway;
use App\DataTransferObjects\PaymentInitiationData;
use App\DataTransferObjects\PaymentVerificationResult;
use App\Enums\PaymentStatus;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Paystack implementation of the PaymentGateway contract.
 *
 * All Paystack-specific HTTP calls and response parsing live here.
 * The rest of the application interacts only with the PaymentGateway
 * interface, so replacing Paystack with another provider requires only
 * a new implementation + a binding change in AppServiceProvider.
 */
class PaystackGateway implements PaymentGateway
{
    private string $secretKey;
    private string $baseUrl;

    public function __construct()
    {
        $this->secretKey = config('services.paystack.secret_key', '');
        $this->baseUrl = config('services.paystack.base_url', 'https://api.paystack.co');
    }

    public function initialize(PaymentInitiationData $data): array
    {
        $response = Http::withToken($this->secretKey)
            ->acceptJson()
            ->post("{$this->baseUrl}/transaction/initialize", array_filter([
                'email' => $data->email,
                'amount' => $data->amountInKobo,
                'reference' => $data->reference,
                'currency' => $data->currency,
                'callback_url' => $data->callbackUrl ?: null,
                'metadata' => $data->metadata,
            ]));

        if ($response->failed() || !$response->json('status')) {
            $errorMessage = $response->json('message') ?? 'Paystack transaction initialization failed.';
            Log::error('Paystack initialization failed', [
                'reference' => $data->reference,
                'status' => $response->status(),
                'error' => $errorMessage,
            ]);
            throw new \RuntimeException($errorMessage);
        }

        return [
            'authorization_url' => (string) $response->json('data.authorization_url'),
            'reference' => (string) $response->json('data.reference'),
            'access_code' => $response->json('data.access_code') ? (string) $response->json('data.access_code') : null,
        ];
    }

    public function verify(string $reference): PaymentVerificationResult
    {
        $response = Http::withToken($this->secretKey)
            ->acceptJson()
            ->get("{$this->baseUrl}/transaction/verify/" . rawurlencode($reference));

        if ($response->failed() || !$response->json('status')) {
            $errorMessage = $response->json('message') ?? 'Paystack transaction verification failed.';
            Log::warning('Paystack verification returned non-success response', [
                'reference' => $reference,
                'status' => $response->status(),
                'error' => $errorMessage,
            ]);

            return new PaymentVerificationResult(
                successful: false,
                status: PaymentStatus::Failed,
                amountInKobo: 0,
                currency: 'NGN',
                reference: $reference,
                providerReference: '',
                provider: $this->provider(),
                paidAt: null,
                metadata: [],
            );
        }

        $data = $response->json('data') ?? [];
        $statusStr = strtolower((string) ($data['status'] ?? 'failed'));

        $paymentStatus = match ($statusStr) {
            'success' => PaymentStatus::Successful,
            'abandoned' => PaymentStatus::Cancelled,
            'failed' => PaymentStatus::Failed,
            default => PaymentStatus::Pending,
        };

        return new PaymentVerificationResult(
            successful: $statusStr === 'success',
            status: $paymentStatus,
            amountInKobo: (int) ($data['amount'] ?? 0),
            currency: (string) ($data['currency'] ?? 'NGN'),
            reference: (string) ($data['reference'] ?? $reference),
            providerReference: (string) ($data['id'] ?? ''),
            provider: $this->provider(),
            paidAt: isset($data['paid_at']) ? (string) $data['paid_at'] : null,
            metadata: (array) ($data['metadata'] ?? []),
        );
    }

    public function provider(): string
    {
        return 'paystack';
    }
}

