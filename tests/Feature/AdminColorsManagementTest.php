<?php

namespace Tests\Feature;

use App\Models\Color;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminColorsManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_list_colors()
    {
        $admin = User::factory()->create(['user_type' => 'A', 'gender' => 'M']);

        $color1 = Color::create(['code' => 'red', 'name' => 'Vermelho']);
        $color2 = Color::create(['code' => 'blue', 'name' => 'Azul']);

        $response = $this->actingAs($admin)
                         ->get(route('admin.colors.index'));

        $response->assertStatus(200);
        $response->assertSee('Vermelho');
        $response->assertSee('red');
        $response->assertSee('Azul');
        $response->assertSee('blue');
    }

    public function test_admin_can_create_color_with_base_image()
    {
        Storage::fake('public');

        $admin = User::factory()->create(['user_type' => 'A', 'gender' => 'M']);
        $image = UploadedFile::fake()->image('base_tshirt.jpg');

        $response = $this->actingAs($admin)
                         ->post(route('admin.colors.store'), [
                             'code' => '#123456',
                             'name' => 'Custom Indigo',
                             'image' => $image,
                         ]);

        $response->assertRedirect(route('admin.colors.index'));

        $color = Color::find('#123456');
        $this->assertNotNull($color);
        $this->assertEquals('Custom Indigo', $color->name);

        // Verify that the file was stored as public/tshirt_base/#123456.jpg
        Storage::disk('public')->assertExists('tshirt_base/#123456.jpg');
    }

    public function test_admin_can_edit_color_and_update_base_image()
    {
        Storage::fake('public');

        $admin = User::factory()->create(['user_type' => 'A', 'gender' => 'M']);
        $color = Color::create(['code' => 'green', 'name' => 'Verde']);

        // Upload new image
        $image = UploadedFile::fake()->image('green_new.jpg');

        $response = $this->actingAs($admin)
                         ->post(route('admin.colors.update', $color), [
                             'name' => 'Verde Limão',
                             'image' => $image,
                         ]);

        $response->assertRedirect(route('admin.colors.index'));

        $color = $color->fresh();
        $this->assertEquals('Verde Limão', $color->name);
        Storage::disk('public')->assertExists('tshirt_base/green.jpg');
    }

    public function test_admin_can_soft_delete_color()
    {
        $admin = User::factory()->create(['user_type' => 'A', 'gender' => 'M']);
        $color = Color::create(['code' => 'purple', 'name' => 'Roxo']);

        $response = $this->actingAs($admin)
                         ->post(route('admin.colors.destroy', $color));

        $response->assertRedirect(route('admin.colors.index'));

        $this->assertSoftDeleted('colors', [
            'code' => 'purple',
        ]);
    }

    public function test_non_admin_cannot_access_colors_management()
    {
        $customer = User::factory()->create(['user_type' => 'C', 'gender' => 'F']);

        $response = $this->actingAs($customer)
                         ->get(route('admin.colors.index'));

        $response->assertStatus(403);
    }
}
