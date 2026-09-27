<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthRegistrationFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_with_email(): void
    {
        $response = $this->post('/register', [
            'name' => 'Amir Lee',
            'register_method' => 'email',
            'email' => 'amir@example.com',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ]);

        $response->assertRedirect('/analysis/business-type');

        $this->assertDatabaseHas('users', [
            'name' => 'Amir Lee',
            'email' => 'amir@example.com',
        ]);

        $this->post('/logout')->assertRedirect('/login');

        $this->post('/login', [
            'login_method' => 'email',
            'email' => 'amir@example.com',
            'password' => 'secret123',
        ])->assertRedirect('/analysis/business-type');
    }

    public function test_user_can_register_with_phone(): void
    {
        $response = $this->post('/register', [
            'name' => 'Nora',
            'register_method' => 'phone',
            'phone' => '+60123456789',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ]);

        $response->assertRedirect('/analysis/business-type');

        $this->assertDatabaseHas('users', [
            'name' => 'Nora',
            'phone' => '+60123456789',
        ]);

        $this->post('/logout')->assertRedirect('/login');

        $this->post('/login', [
            'login_method' => 'phone',
            'phone' => '+60123456789',
            'password' => 'secret123',
        ])->assertRedirect('/analysis/business-type');
    }

    public function test_existing_user_can_log_in_with_email_password(): void
    {
        User::factory()->create([
            'email' => 'existing@example.com',
            'password' => bcrypt('secret123'),
        ]);

        $this->post('/login', [
            'login_method' => 'email',
            'email' => 'existing@example.com',
            'password' => 'secret123',
        ])->assertRedirect('/analysis/business-type');
    }
}
