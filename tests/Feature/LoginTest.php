<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class LoginTest extends TestCase
{
    public function test_login_returns_token_with_valid_credentials(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $response = $this->postJson('/api/v1/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['access_token']);
    }

    public function test_login_returns_error_with_invalid_credentials(): void
    {
        $response = $this->postJson('/api/v1/login', [
            'email' => 'nonexisting@user.com',
            'password' => 'password',
        ]);

        $response->assertStatus(422);
    }
}
