<?php

namespace App\DataTransferObjects;

/**
 * Immutable data object for initiating a payment with an external gateway.
 */
final readonly class PaymentInitiationData
{
    public function __construct(
        public string $email,
        public int $amountInKobo,
        public string $reference,
        public string $currency = 'NGN',
        public string $callbackUrl = '',
        public array $metadata = [],
    ) {}
}
