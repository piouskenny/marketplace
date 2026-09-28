<?php

namespace Tests\Feature;

use App\Enums\ConnectionStatus;
use App\Enums\ConnectionType;
use App\Enums\OpportunityStatus;
use App\Enums\PaymentStatus;
use App\Models\Category;
use App\Models\ConnectionRequest;
use App\Models\Conversation;
use App\Models\Opportunity;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PaystackPaymentTest extends TestCase
{
    use RefreshDatabase;

    protected User $jobPoster;
    protected User $applicant;
    protected ConnectionRequest $connection;

    protected function setUp(): void
    {
        parent::setUp();

        $this->jobPoster = User::factory()->create([
            'name' => 'Job Poster User',
            'email' => 'poster@example.com',
            'onboarding_completed' => true,
        ]);

        $this->applicant = User::factory()->create([
            'name' => 'Applicant User',
            'email' => 'applicant@example.com',
            'onboarding_completed' => true,
        ]);

        $category = Category::create([
            'name' => 'General Service',
            'slug' => 'general-service',
        ]);

        $opportunity = Opportunity::create([
            'user_id' => $this->jobPoster->id,
            'category_id' => $category->id,
            'title' => 'Test Opportunity',
            'description' => 'Description',
            'status' => OpportunityStatus::Open,
        ]);

        $this->connection = ConnectionRequest::create([
            'initiator_id' => $this->applicant->id,
            'recipient_id' => $this->jobPoster->id,
            'opportunity_id' => $opportunity->id,
            'type' => ConnectionType::OpportunityApplication,
            'status' => ConnectionStatus::Accepted,
        ]);
    }

    /** @test */
    public function applicant_can_initialize_paystack_payment()
    {
        Http::fake([
            'https://api.paystack.co/transaction/initialize' => Http::response([
                'status' => true,
                'message' => 'Authorization URL created',
                'data' => [
                    'authorization_url' => 'https://checkout.paystack.com/test-checkout-code',
                    'access_code' => 'test-access-code',
                    'reference' => 'CONN-FEE-TESTREF123',
                ],
            ], 200),
        ]);

        $response = $this->actingAs($this->applicant)
            ->postJson("/connections/{$this->connection->id}/pay");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'authorization_url' => 'https://checkout.paystack.com/test-checkout-code',
                'status' => 'payment_pending',
            ]);

        $this->assertDatabaseHas('connection_requests', [
            'id' => $this->connection->id,
            'status' => ConnectionStatus::PaymentPending->value,
        ]);

        $this->assertDatabaseHas('payments', [
            'connection_request_id' => $this->connection->id,
            'user_id' => $this->applicant->id,
            'amount' => 100000,
            'status' => PaymentStatus::Pending->value,
        ]);
    }

    /** @test */
    public function job_owner_cannot_initialize_payment_for_application()
    {
        $response = $this->actingAs($this->jobPoster)
            ->postJson("/connections/{$this->connection->id}/pay");

        $response->assertStatus(403);
    }

    /** @test */
    public function successful_payment_verification_activates_connection_and_creates_conversation()
    {
        $reference = 'CONN-FEE-SUCCESSREF123';

        $payment = Payment::create([
            'user_id' => $this->applicant->id,
            'connection_request_id' => $this->connection->id,
            'reference' => $reference,
            'provider' => 'paystack',
            'amount' => 100000,
            'currency' => 'NGN',
            'status' => PaymentStatus::Pending,
        ]);

        Http::fake([
            'https://api.paystack.co/transaction/verify/*' => Http::response([
                'status' => true,
                'message' => 'Verification successful',
                'data' => [
                    'status' => 'success',
                    'id' => 99887766,
                    'amount' => 100000,
                    'currency' => 'NGN',
                    'reference' => $reference,
                    'paid_at' => now()->toISOString(),
                    'metadata' => ['connection_request_id' => $this->connection->id],
                ],
            ], 200),
        ]);

        $response = $this->actingAs($this->applicant)
            ->get("/connections/pay/callback?reference={$reference}");

        $response->assertRedirect();

        $this->assertDatabaseHas('connection_requests', [
            'id' => $this->connection->id,
            'status' => ConnectionStatus::Connected->value,
        ]);

        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'status' => PaymentStatus::Successful->value,
            'provider_reference' => '99887766',
        ]);

        $this->assertDatabaseHas('conversations', [
            'connection_request_id' => $this->connection->id,
        ]);
    }

    /** @test */
    public function failed_paystack_payment_does_not_activate_connection()
    {
        $reference = 'CONN-FEE-FAILREF123';

        $payment = Payment::create([
            'user_id' => $this->applicant->id,
            'connection_request_id' => $this->connection->id,
            'reference' => $reference,
            'provider' => 'paystack',
            'amount' => 100000,
            'currency' => 'NGN',
            'status' => PaymentStatus::Pending,
        ]);

        Http::fake([
            'https://api.paystack.co/transaction/verify/*' => Http::response([
                'status' => true,
                'message' => 'Verification completed',
                'data' => [
                    'status' => 'failed',
                    'id' => 99887767,
                    'amount' => 100000,
                    'currency' => 'NGN',
                    'reference' => $reference,
                    'metadata' => ['connection_request_id' => $this->connection->id],
                ],
            ], 200),
        ]);

        $response = $this->actingAs($this->applicant)
            ->get("/connections/pay/callback?reference={$reference}");

        $response->assertRedirect();

        $this->assertDatabaseHas('connection_requests', [
            'id' => $this->connection->id,
            'status' => ConnectionStatus::Accepted->value,
        ]);

        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'status' => PaymentStatus::Failed->value,
        ]);

        $this->assertDatabaseMissing('conversations', [
            'connection_request_id' => $this->connection->id,
        ]);
    }

    /** @test */
    public function incorrect_payment_amount_is_rejected_and_fails_payment()
    {
        $reference = 'CONN-FEE-WRONGAMT123';

        $payment = Payment::create([
            'user_id' => $this->applicant->id,
            'connection_request_id' => $this->connection->id,
            'reference' => $reference,
            'provider' => 'paystack',
            'amount' => 100000,
            'currency' => 'NGN',
            'status' => PaymentStatus::Pending,
        ]);

        Http::fake([
            'https://api.paystack.co/transaction/verify/*' => Http::response([
                'status' => true,
                'message' => 'Verification successful',
                'data' => [
                    'status' => 'success',
                    'id' => 99887768,
                    'amount' => 50000, // 500 NGN instead of 1000 NGN
                    'currency' => 'NGN',
                    'reference' => $reference,
                    'metadata' => ['connection_request_id' => $this->connection->id],
                ],
            ], 200),
        ]);

        $response = $this->actingAs($this->applicant)
            ->get("/connections/pay/callback?reference={$reference}");

        $response->assertRedirect();

        $this->assertDatabaseHas('connection_requests', [
            'id' => $this->connection->id,
            'status' => ConnectionStatus::Accepted->value,
        ]);

        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'status' => PaymentStatus::Failed->value,
        ]);
    }

    /** @test */
    public function duplicate_callback_requests_are_processed_idempotently()
    {
        $reference = 'CONN-FEE-DUPCALLBACK123';

        $payment = Payment::create([
            'user_id' => $this->applicant->id,
            'connection_request_id' => $this->connection->id,
            'reference' => $reference,
            'provider' => 'paystack',
            'amount' => 100000,
            'currency' => 'NGN',
            'status' => PaymentStatus::Pending,
        ]);

        Http::fake([
            'https://api.paystack.co/transaction/verify/*' => Http::response([
                'status' => true,
                'message' => 'Verification successful',
                'data' => [
                    'status' => 'success',
                    'id' => 99887769,
                    'amount' => 100000,
                    'currency' => 'NGN',
                    'reference' => $reference,
                    'metadata' => ['connection_request_id' => $this->connection->id],
                ],
            ], 200),
        ]);

        // First callback execution
        $this->actingAs($this->applicant)
            ->get("/connections/pay/callback?reference={$reference}")
            ->assertRedirect();

        $this->assertEquals(1, Conversation::where('connection_request_id', $this->connection->id)->count());

        // Second callback execution (duplicate)
        $this->actingAs($this->applicant)
            ->get("/connections/pay/callback?reference={$reference}")
            ->assertRedirect();

        // Ensure single conversation record still exists
        $this->assertEquals(1, Conversation::where('connection_request_id', $this->connection->id)->count());
        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'status' => PaymentStatus::Successful->value,
        ]);
    }

    /** @test */
    public function valid_paystack_webhook_activates_connection_idempotently()
    {
        $reference = 'CONN-FEE-WEBHOOK123';
        $secretKey = 'sk_test_demo1234567890abcdef1234567890';
        config(['services.paystack.secret_key' => $secretKey]);

        $payment = Payment::create([
            'user_id' => $this->applicant->id,
            'connection_request_id' => $this->connection->id,
            'reference' => $reference,
            'provider' => 'paystack',
            'amount' => 100000,
            'currency' => 'NGN',
            'status' => PaymentStatus::Pending,
        ]);

        Http::fake([
            'https://api.paystack.co/transaction/verify/*' => Http::response([
                'status' => true,
                'message' => 'Verification successful',
                'data' => [
                    'status' => 'success',
                    'id' => 99887770,
                    'amount' => 100000,
                    'currency' => 'NGN',
                    'reference' => $reference,
                    'metadata' => ['connection_request_id' => $this->connection->id],
                ],
            ], 200),
        ]);

        $payloadData = [
            'event' => 'charge.success',
            'data' => [
                'reference' => $reference,
                'amount' => 100000,
                'currency' => 'NGN',
                'status' => 'success',
            ],
        ];

        $rawPayload = json_encode($payloadData);
        $signature = hash_hmac('sha512', $rawPayload, $secretKey);

        $response = $this->call(
            'POST',
            '/paystack/webhook',
            [],
            [],
            [],
            [
                'HTTP_X-PAYSTACK-SIGNATURE' => $signature,
                'CONTENT_TYPE' => 'application/json',
            ],
            $rawPayload
        );

        $response->assertStatus(200);

        $this->assertDatabaseHas('connection_requests', [
            'id' => $this->connection->id,
            'status' => ConnectionStatus::Connected->value,
        ]);

        // Repeat webhook delivery
        $responseDuplicate = $this->call(
            'POST',
            '/paystack/webhook',
            [],
            [],
            [],
            [
                'HTTP_X-PAYSTACK-SIGNATURE' => $signature,
                'CONTENT_TYPE' => 'application/json',
            ],
            $rawPayload
        );

        $responseDuplicate->assertStatus(200);
        $this->assertEquals(1, Conversation::where('connection_request_id', $this->connection->id)->count());
    }

    /** @test */
    public function invalid_webhook_signature_is_rejected()
    {
        $payloadData = ['event' => 'charge.success', 'data' => ['reference' => 'INVALID']];
        $rawPayload = json_encode($payloadData);

        $response = $this->call(
            'POST',
            '/paystack/webhook',
            [],
            [],
            [],
            [
                'HTTP_X-PAYSTACK-SIGNATURE' => 'invalid_signature_string',
                'CONTENT_TYPE' => 'application/json',
            ],
            $rawPayload
        );

        $response->assertStatus(400);
    }
}
