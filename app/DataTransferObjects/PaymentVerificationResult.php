<?php

namespace App\DataTransferObjects;

use App\Enums\PaymentStatus;

/**
 * Immutable data object returned after verifying a payment with an external gateway.
 */
final readonly class PaymentVerificationResult
{
    public function __construct(
        public bool $successful,
        public PaymentStatus $status,
        public int $amountInKobo,
        public string $currency,
        public string $reference,
        public string $providerReference,
        public string $provider,
        public ?string $paidAt = null,
        public array $metadata = [],
    ) {}
}
