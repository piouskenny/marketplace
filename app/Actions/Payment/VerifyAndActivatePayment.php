<?php

namespace App\Actions\Payment;

use App\Contracts\PaymentGateway;
use Illuminate\Support\Facades\DB;

/**
 * Verify a payment via the gateway and activate the connection if successful.
 *
 * This is the ONLY path that transitions a connection to Connected status.
 * Called from both the callback controller and the webhook handler.
 * Must be idempotent — safe to call multiple times for the same reference.
 *
 * Used by: Webhook controller, callback controller, future API controllers.
 */
class VerifyAndActivatePayment
{
    public function __construct(
        private readonly PaymentGateway $gateway,
    ) {}

    public function execute(string $reference): mixed
    {
        // Placeholder — full implementation after models/migrations are approved.
        //
        // Intended flow:
        //   1. Find Payment by reference
        //   2. If already successful → return early (idempotent)
        //   3. Call $this->gateway->verify($reference)
        //   4. Validate: amount matches, currency matches
        //   5. DB::transaction:
        //      a. Update Payment status to successful
        //      b. Update ConnectionRequest status to Connected, set connected_at
        //      c. Create Conversation record
        //   6. Dispatch PaymentVerified event (triggers notifications)
        //   7. Dispatch ConnectionActivated event (triggers contact unlock)
        //   8. Return the updated ConnectionRequest

        throw new \RuntimeException('VerifyAndActivatePayment action is not yet implemented.');
    }
}
