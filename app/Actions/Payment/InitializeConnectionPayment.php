<?php

namespace App\Actions\Payment;

use App\Contracts\PaymentGateway;
use Illuminate\Support\Facades\DB;

/**
 * Initialize a payment for a connection request.
 *
 * Creates a Payment record and delegates to the PaymentGateway contract
 * for provider-specific initialization (Paystack checkout URL, etc.).
 *
 * Used by: Livewire components, future API controllers.
 */
class InitializeConnectionPayment
{
    public function __construct(
        private readonly PaymentGateway $gateway,
    ) {}

    /**
     * @return array{authorization_url: string, reference: string}
     */
    public function execute(int $connectionRequestId, int $payerUserId): array
    {
        // Placeholder — full implementation after models/migrations are approved.
        //
        // Intended flow:
        //   1. Load ConnectionRequest, verify status is PaymentPending
        //   2. Authorize that payer is the initiator
        //   3. Generate unique payment reference
        //   4. Read connection fee from config('marketplace.connection_fee')
        //   5. DB::transaction — create Payment record (pending)
        //   6. Call $this->gateway->initialize(...)
        //   7. Return authorization URL + reference

        throw new \RuntimeException('InitializeConnectionPayment action is not yet implemented.');
    }
}
