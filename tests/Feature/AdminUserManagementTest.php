<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminUserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_filter_by_employee()
    {
        $admin = User::factory()->create(['user_type' => 'A', 'gender' => 'M']);
        
        // Create an employee with type F
        $employee = User::factory()->create([
            'name' => 'Logistics Employee',
            'user_type' => 'F',
            'gender' => 'F'
        ]);

        // Create a customer
        $customer = User::factory()->create([
            'name' => 'John Customer',
            'user_type' => 'C',
            'gender' => 'M'
        ]);

        $response = $this->actingAs($admin)
                         ->get(route('admin.users.index') . '?user_type=E');

        $response->assertStatus(200);
        $response->assertSee('Logistics Employee');
        $response->assertSee('Funcionário');
        $response->assertDontSee('John Customer');
    }

    public function test_admin_can_create_user_with_photo()
    {
        Storage::fake('public');

        $admin = User::factory()->create(['user_type' => 'A', 'gender' => 'M']);

        $photo = UploadedFile::fake()->image('user_photo.jpg');

        $response = $this->actingAs($admin)
                         ->post(route('admin.users.store'), [
                             'name' => 'New Staff Member',
                             'email' => 'staff@example.com',
                             'password' => 'secret123',
                             'password_confirmation' => 'secret123',
                             'user_type' => 'E',
                             'gender' => 'M',
                             'photo' => $photo,
                         ]);

        $response->assertRedirect(route('admin.users.index'));
        
        $user = User::where('email', 'staff@example.com')->first();
        $this->assertNotNull($user);
        $this->assertNotEquals('anonymous.png', $user->photo_url);
        
        // Assert file exists in storage
        Storage::disk('public')->assertExists('photos/' . $user->photo_url);
    }

    public function test_admin_can_edit_user_with_photo()
    {
        Storage::fake('public');

        $admin = User::factory()->create(['user_type' => 'A', 'gender' => 'M']);

        // Create user with previous dummy photo
        $user = User::factory()->create([
            'user_type' => 'C',
            'gender' => 'F',
            'photo_url' => 'old_avatar.jpg',
        ]);
        
        // Place initial file
        Storage::disk('public')->put('photos/old_avatar.jpg', 'fake content');

        $newPhoto = UploadedFile::fake()->image('new_avatar.png');

        $response = $this->actingAs($admin)
                         ->post(route('admin.users.update', $user), [
                             'name' => 'Updated Name',
                             'email' => $user->email,
                             'user_type' => 'E',
                             'photo' => $newPhoto,
                         ]);

        $response->assertRedirect(route('admin.users.index'));
        
        $user = $user->fresh();
        $this->assertEquals('Updated Name', $user->name);
        $this->assertEquals('F', $user->user_type); // E is mutated to F
        $this->assertNotEquals('old_avatar.jpg', $user->photo_url);

        // Assert old file is deleted and new file is created
        Storage::disk('public')->assertMissing('photos/old_avatar.jpg');
        Storage::disk('public')->assertExists('photos/' . $user->photo_url);
    }
}
