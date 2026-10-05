<?php

namespace Tests\Feature;

use App\Enums\ConnectionStatus;
use App\Enums\ConnectionType;
use App\Enums\PaymentStatus;
use App\Models\ConnectionRequest;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HistoryAndPaymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_history_hub()
    {
        $response = $this->get(route('dashboard.history'));
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_connection_history_tab()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $conn = ConnectionRequest::create([
            'initiator_id' => $user1->id,
            'recipient_id' => $user2->id,
            'type' => ConnectionType::ProfessionalRequest,
            'status' => ConnectionStatus::Accepted,
            'initial_message' => 'I would like to hire your services.',
        ]);

        $response = $this->actingAs($user1)->get(route('dashboard.history', ['tab' => 'connections']));

        $response->assertStatus(200);
        $response->assertSee('Connection History');
        $response->assertSee('Accepted — Awaiting Payment');
    }

    public function test_authenticated_user_can_view_payment_history_tab()
    {
        $user = User::factory()->create();
        $recipient = User::factory()->create();

        $conn = ConnectionRequest::create([
            'initiator_id' => $user->id,
            'recipient_id' => $recipient->id,
            'type' => ConnectionType::ProfessionalRequest,
            'status' => ConnectionStatus::Accepted,
        ]);

        $payment = Payment::create([
            'user_id' => $user->id,
            'connection_request_id' => $conn->id,
            'reference' => 'CONN-FEE-TEST1234',
            'provider' => 'paystack',
            'amount' => 100000,
            'currency' => 'NGN',
            'status' => PaymentStatus::Pending,
        ]);

        $response = $this->actingAs($user)->get(route('dashboard.history', ['tab' => 'payments']));

        $response->assertStatus(200);
        $response->assertSee('Transaction Reference');
        $response->assertSee('CONN-FEE-TEST1234');
        $response->assertSee('Retry Payment');
    }

    public function test_retry_payment_generates_paystack_authorization_url()
    {
        $user = User::factory()->create();
        $recipient = User::factory()->create();

        $conn = ConnectionRequest::create([
            'initiator_id' => $user->id,
            'recipient_id' => $recipient->id,
            'type' => ConnectionType::ProfessionalRequest,
            'status' => ConnectionStatus::Accepted,
        ]);

        // Mock payment gateway response
        $mockGateway = \Mockery::mock(\App\Contracts\PaymentGateway::class);
        $mockGateway->shouldReceive('provider')->andReturn('paystack');
        $mockGateway->shouldReceive('initialize')->once()->andReturn([
            'authorization_url' => 'https://checkout.paystack.com/mock-retry-url',
            'reference' => 'CONN-FEE-RETRY123',
            'access_code' => 'mock_access_code',
        ]);

        $this->app->instance(\App\Contracts\PaymentGateway::class, $mockGateway);

        $response = $this->actingAs($user)
            ->postJson(route('connections.pay', ['connection' => $conn->id]));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'authorization_url' => 'https://checkout.paystack.com/mock-retry-url',
            'status' => 'payment_pending',
        ]);
    }
}
