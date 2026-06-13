<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Order;
use App\Models\TshirtImage;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\NewDesignNotificationMailable;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class PremiumWorkflowFeaturesTest extends TestCase
{
    use RefreshDatabase;

    private User $customerUser;
    private User $adminUser;
    private User $employeeUser;
    private Order $order1;
    private Order $order2;

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

        DB::table('customers')->insert([
            'id' => $this->customerUser->id,
            'nif' => '123456789',
            'address' => 'Customer Street 123',
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

        // Create an employee user
        $this->employeeUser = User::create([
            'name' => 'Employee User',
            'email' => 'employee@test.com',
            'password' => bcrypt('password'),
            'user_type' => 'F',
            'gender' => 'M',
            'blocked' => 0,
        ]);

        // Create two pending orders for the customer
        $this->order1 = Order::create([
            'status' => 'pending',
            'customer_id' => $this->customerUser->id,
            'date' => now()->subDay(),
            'total_price' => 100.00,
            'nif' => '123456789',
            'address' => 'Customer Street 123',
            'payment_type' => 'Visa',
            'payment_ref' => '4123456789012365',
        ]);

        $this->order2 = Order::create([
            'status' => 'pending',
            'customer_id' => $this->customerUser->id,
            'date' => now(),
            'total_price' => 50.00,
            'nif' => '123456789',
            'address' => 'Customer Street 123',
            'payment_type' => 'Visa',
            'payment_ref' => '4123456789012365',
        ]);
    }

    public function test_admin_can_export_stats_to_csv(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.dashboard.export', ['range' => 'lifetime']));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        
        $content = $response->streamedContent();
        $this->assertStringContainsString('ID Encomenda', $content);
        $this->assertStringContainsString('4123456789012365', $content);
    }

    public function test_employee_can_process_next_pending_order(): void
    {
        // Should redirect to order1 (the older pending order)
        $response = $this->actingAs($this->employeeUser)
            ->get(route('employee.orders.next'));

        $response->assertRedirect(route('employee.orders.show', $this->order1));

        // Let's close order1
        $this->order1->update(['status' => 'closed']);

        // Now next should redirect to order2
        $response = $this->actingAs($this->employeeUser)
            ->get(route('employee.orders.next'));

        $response->assertRedirect(route('employee.orders.show', $this->order2));

        // Let's close order2
        $this->order2->update(['status' => 'closed']);

        // No pending orders left, should redirect back to index with info message
        $response = $this->actingAs($this->employeeUser)
            ->get(route('employee.orders.next'));

        $response->assertRedirect(route('employee.orders.index'));
        $response->assertSessionHas('info', 'Excelente! Não há mais encomendas pendentes para processar.');
    }

    public function test_admin_creating_design_with_newsletter_sends_email(): void
    {
        Mail::fake();
        \Illuminate\Support\Facades\Storage::fake('public');

        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.designs.store'), [
                'name' => 'Awesome New Design',
                'description' => 'A test design newsletter description.',
                'image' => UploadedFile::fake()->image('design.png'),
                'notify_customers' => '1',
            ]);

        $response->assertRedirect(route('admin.designs.index'));
        $response->assertSessionHas('success');

        // Check if design was created
        $this->assertDatabaseHas('tshirt_images', [
            'name' => 'Awesome New Design',
            'customer_id' => null,
        ]);

        // Verify that Mail was sent to active customer
        Mail::assertSent(NewDesignNotificationMailable::class, function ($mail) {
            return $mail->hasTo($this->customerUser->email) &&
                   $mail->design->name === 'Awesome New Design';
        });

        // Mail should not go to admin or employee
        Mail::assertNotSent(NewDesignNotificationMailable::class, function ($mail) {
            return $mail->hasTo($this->adminUser->email) || $mail->hasTo($this->employeeUser->email);
        });
    }

    public function test_admin_can_create_new_user_and_customer_record_is_created_if_type_is_c(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.users.create'));

        $response->assertStatus(200);

        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.users.store'), [
                'name' => 'New Customer',
                'email' => 'newcustomer@test.com',
                'password' => 'newpassword123',
                'password_confirmation' => 'newpassword123',
                'user_type' => 'C',
                'gender' => 'M',
            ]);

        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('users', [
            'name' => 'New Customer',
            'email' => 'newcustomer@test.com',
            'user_type' => 'C',
        ]);
        $newUser = User::where('email', 'newcustomer@test.com')->first();
        $this->assertDatabaseHas('customers', [
            'id' => $newUser->id,
        ]);
    }

    public function test_admin_can_create_new_employee_without_customer_record(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.users.store'), [
                'name' => 'New Employee',
                'email' => 'newemployee@test.com',
                'password' => 'newpassword123',
                'password_confirmation' => 'newpassword123',
                'user_type' => 'E',
                'gender' => 'F',
            ]);

        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('users', [
            'name' => 'New Employee',
            'email' => 'newemployee@test.com',
            'user_type' => 'F',
        ]);
        $newUser = User::where('email', 'newemployee@test.com')->first();
        $this->assertDatabaseMissing('customers', [
            'id' => $newUser->id,
        ]);
    }

    public function test_non_admin_cannot_access_create_user(): void
    {
        $response = $this->actingAs($this->customerUser)
            ->get(route('admin.users.create'));
        $response->assertStatus(403);

        $response = $this->actingAs($this->employeeUser)
            ->get(route('admin.users.create'));
        $response->assertStatus(403);
    }
}
