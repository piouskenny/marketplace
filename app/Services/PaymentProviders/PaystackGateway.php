<?php

namespace App\Services\PaymentProviders;

use App\Contracts\PaymentGateway;
use App\DataTransferObjects\PaymentInitiationData;
use App\DataTransferObjects\PaymentVerificationResult;
use App\Enums\PaymentStatus;
use Illuminate\Support\Facades\Http;

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
        // Placeholder — will POST to /transaction/initialize
        //
        // $response = Http::withToken($this->secretKey)
        //     ->post("{$this->baseUrl}/transaction/initialize", [
        //         'email'        => $data->email,
        //         'amount'       => $data->amountInKobo,
        //         'reference'    => $data->reference,
        //         'currency'     => $data->currency,
        //         'callback_url' => $data->callbackUrl,
        //         'metadata'     => $data->metadata,
        //     ]);
        //
        // return [
        //     'authorization_url' => $response->json('data.authorization_url'),
        //     'reference'         => $response->json('data.reference'),
        //     'access_code'       => $response->json('data.access_code'),
        // ];

        throw new \RuntimeException('PaystackGateway::initialize is not yet implemented.');
    }

    public function verify(string $reference): PaymentVerificationResult
    {
        // Placeholder — will GET /transaction/verify/{reference}
        //
        // $response = Http::withToken($this->secretKey)
        //     ->get("{$this->baseUrl}/transaction/verify/{$reference}");
        //
        // $data = $response->json('data');
        //
        // return new PaymentVerificationResult(
        //     successful:        $data['status'] === 'success',
        //     status:            $data['status'] === 'success' ? PaymentStatus::Successful : PaymentStatus::Failed,
        //     amountInKobo:      $data['amount'],
        //     currency:          $data['currency'],
        //     reference:         $data['reference'],
        //     providerReference: $data['id'],
        //     provider:          $this->provider(),
        //     paidAt:            $data['paid_at'] ?? null,
        //     metadata:          $data['metadata'] ?? [],
        // );

        throw new \RuntimeException('PaystackGateway::verify is not yet implemented.');
    }

    public function provider(): string
    {
        return 'paystack';
    }
}
