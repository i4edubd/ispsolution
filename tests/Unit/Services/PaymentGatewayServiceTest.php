<?php

namespace Tests\Unit\Services;

use App\Models\Invoice;
use App\Models\NetworkUser;
use App\Models\Tenant;
use App\Services\PaymentGatewayService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PaymentGatewayServiceTest extends TestCase
{
    use RefreshDatabase;

    protected PaymentGatewayService $paymentGatewayService;

    protected Tenant $tenant;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Mock BillingService
        $billingService = $this->createMock(\App\Services\BillingService::class);
        $this->paymentGatewayService = new PaymentGatewayService($billingService);

        // Create a test tenant
        $this->tenant = Tenant::factory()->create();

        // Fake HTTP requests
        Http::fake();
    }

    public function test_can_initiate_bkash_payment()
    {
        $networkUser = NetworkUser::factory()->create([
            'tenant_id' => $this->tenant->id,
        ]);

        $invoice = Invoice::factory()->create([
            'tenant_id' => $this->tenant->id,
            'network_user_id' => $networkUser->id,
            'total_amount' => 1000,
        ]);

        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
        $this->paymentGatewayService->initiatePayment($invoice, 'bkash');
    }

    public function test_can_initiate_nagad_payment()
    {
        $networkUser = NetworkUser::factory()->create([
            'tenant_id' => $this->tenant->id,
        ]);

        $invoice = Invoice::factory()->create([
            'tenant_id' => $this->tenant->id,
            'network_user_id' => $networkUser->id,
            'total_amount' => 1000,
        ]);

        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
        $this->paymentGatewayService->initiatePayment($invoice, 'nagad');
    }

    public function test_can_initiate_sslcommerz_payment()
    {
        $networkUser = NetworkUser::factory()->create([
            'tenant_id' => $this->tenant->id,
        ]);

        $invoice = Invoice::factory()->create([
            'tenant_id' => $this->tenant->id,
            'network_user_id' => $networkUser->id,
            'total_amount' => 1000,
        ]);

        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
        $this->paymentGatewayService->initiatePayment($invoice, 'sslcommerz');
    }

    public function test_can_initiate_stripe_payment()
    {
        $networkUser = NetworkUser::factory()->create([
            'tenant_id' => $this->tenant->id,
        ]);

        $invoice = Invoice::factory()->create([
            'tenant_id' => $this->tenant->id,
            'network_user_id' => $networkUser->id,
            'total_amount' => 1000,
        ]);

        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
        $this->paymentGatewayService->initiatePayment($invoice, 'stripe');
    }

    public function test_handles_unsupported_gateway()
    {
        $networkUser = NetworkUser::factory()->create([
            'tenant_id' => $this->tenant->id,
        ]);

        $invoice = Invoice::factory()->create([
            'tenant_id' => $this->tenant->id,
            'network_user_id' => $networkUser->id,
            'total_amount' => 1000,
        ]);

        $this->expectException(\Exception::class);
        $this->paymentGatewayService->initiatePayment($invoice, 'unsupported');
    }

    public function test_can_verify_payment()
    {
        $this->expectException(\Exception::class);
        $this->paymentGatewayService->verifyPayment('test-transaction-id', 'bkash', $this->tenant->id);
    }

    public function test_can_process_webhook()
    {
        $webhookData = [
            'transaction_id' => 'test-txn-123',
            'status' => 'success',
            'amount' => 1000,
        ];

        $result = $this->paymentGatewayService->processWebhook('bkash', $webhookData);

        $this->assertIsBool($result);
    }
}
