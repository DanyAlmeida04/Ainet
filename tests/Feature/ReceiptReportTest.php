<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Customer;
use App\Models\Order;
use App\Models\ReceiptReport;
use Illuminate\Support\Facades\DB;

class ReceiptReportTest extends TestCase
{
    use RefreshDatabase;

    private User $customerUser;
    private User $adminUser;
    private Order $order;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a customer user
        $this->customerUser = User::create([
            'name' => 'John Customer',
            'email' => 'customer@test.com',
            'password' => bcrypt('password'),
            'user_type' => 'C',
            'gender' => 'M',
            'blocked' => 0,
        ]);

        // Create matching customer entry
        DB::table('customers')->insert([
            'id' => $this->customerUser->id,
            'nif' => '123456789',
            'address' => 'Customer Street 123',
            'default_payment_type' => 'Visa',
            'default_payment_ref' => '1234567890123654',
        ]);

        // Create an admin user
        $this->adminUser = User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'user_type' => 'A',
            'gender' => 'M',
            'blocked' => 0,
        ]);

        // Create a closed order for the customer
        $this->order = Order::create([
            'status' => 'closed',
            'customer_id' => $this->customerUser->id,
            'date' => now(),
            'total_price' => 100.00,
            'nif' => '123456789',
            'address' => 'Customer Street 123',
            'payment_type' => 'Visa',
            'payment_ref' => '1234567890123654',
            'receipt_url' => 'receipt_test.pdf',
        ]);
    }

    public function test_customer_can_submit_receipt_report_for_closed_order(): void
    {
        $response = $this->actingAs($this->customerUser)
            ->post(route('orders.reportReceipt', $this->order));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('receipt_reports', [
            'order_id' => $this->order->id,
            'user_id' => $this->customerUser->id,
            'status' => 'pending',
            'notified' => false,
        ]);
    }

    public function test_customer_cannot_submit_report_if_order_is_not_closed(): void
    {
        $pendingOrder = Order::create([
            'status' => 'pending',
            'customer_id' => $this->customerUser->id,
            'date' => now(),
            'total_price' => 50.00,
            'nif' => '123456789',
            'address' => 'Customer Street 123',
            'payment_type' => 'Visa',
            'payment_ref' => '1234567890123654',
        ]);

        $response = $this->actingAs($this->customerUser)
            ->post(route('orders.reportReceipt', $pendingOrder));

        $response->assertRedirect();
        $response->assertSessionHasErrors();
        $this->assertDatabaseCount('receipt_reports', 0);
    }

    public function test_customer_cannot_submit_multiple_concurrent_pending_reports(): void
    {
        // First report
        $this->actingAs($this->customerUser)
            ->post(route('orders.reportReceipt', $this->order));

        $this->assertDatabaseCount('receipt_reports', 1);

        // Second report (should fail since first is pending)
        $response = $this->actingAs($this->customerUser)
            ->post(route('orders.reportReceipt', $this->order));

        $response->assertRedirect();
        $response->assertSessionHasErrors();
        $this->assertDatabaseCount('receipt_reports', 1);
    }

    public function test_customer_can_submit_up_to_three_reports_if_previous_ones_resolved(): void
    {
        // 1st report: pending -> reject
        $report1 = ReceiptReport::create([
            'order_id' => $this->order->id,
            'user_id' => $this->customerUser->id,
            'status' => 'pending',
            'notified' => false,
        ]);

        // Second report attempt should fail because there is a pending report
        $response = $this->actingAs($this->customerUser)
            ->post(route('orders.reportReceipt', $this->order));
        $response->assertSessionHasErrors();

        // Reject 1st report
        $report1->update(['status' => 'rejected']);

        // 2nd report: submit successfully
        $response = $this->actingAs($this->customerUser)
            ->post(route('orders.reportReceipt', $this->order));
        $response->assertSessionHas('success');
        $this->assertDatabaseCount('receipt_reports', 2);

        // Resolve 2nd report
        $report2 = ReceiptReport::where('order_id', $this->order->id)->where('status', 'pending')->first();
        $report2->update(['status' => 'rejected']);

        // 3rd report: submit successfully
        $response = $this->actingAs($this->customerUser)
            ->post(route('orders.reportReceipt', $this->order));
        $response->assertSessionHas('success');
        $this->assertDatabaseCount('receipt_reports', 3);

        // Resolve 3rd report
        $report3 = ReceiptReport::where('order_id', $this->order->id)->where('status', 'pending')->first();
        $report3->update(['status' => 'rejected']);

        // 4th report: should fail because limit is 3
        $response = $this->actingAs($this->customerUser)
            ->post(route('orders.reportReceipt', $this->order));
        $response->assertSessionHasErrors();
        $this->assertDatabaseCount('receipt_reports', 3);
    }

    public function test_admin_can_accept_and_reject_reports(): void
    {
        $report = ReceiptReport::create([
            'order_id' => $this->order->id,
            'user_id' => $this->customerUser->id,
            'status' => 'pending',
            'notified' => false,
        ]);

        // Reject report as admin
        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.reports.handle', $report), ['action' => 'reject']);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertEquals('rejected', $report->fresh()->status);
        $this->assertFalse($report->fresh()->notified);

        // Reset to pending
        $report->update(['status' => 'pending']);

        // Accept report as admin
        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.reports.handle', $report), ['action' => 'accept']);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertEquals('accepted', $report->fresh()->status);
        $this->assertFalse($report->fresh()->notified);
    }
}
