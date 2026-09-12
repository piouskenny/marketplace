<?php

namespace App\Contracts;

use App\DataTransferObjects\PaymentInitiationData;
use App\DataTransferObjects\PaymentVerificationResult;

/**
 * Contract for payment gateway providers.
 *
 * Implement this interface for each payment provider (Paystack, Flutterwave, etc.)
 * so the core marketplace logic never depends on a specific provider's SDK or API.
 */
interface PaymentGateway
{
    /**
     * Initialize a payment transaction with the provider.
     *
     * @return array{authorization_url: string, reference: string, access_code: string|null}
     */
    public function initialize(PaymentInitiationData $data): array;

    /**
     * Verify a payment transaction by its reference.
     */
    public function verify(string $reference): PaymentVerificationResult;

    /**
     * Return the provider identifier (e.g. 'paystack', 'flutterwave').
     */
    public function provider(): string;
}
