<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\Travel;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Tests\TestCase;

class AdminTourTest extends TestCase
{
    public function test_public_user_cannot_access_adding_tour(): void
    {
        /** @var Travel $travel */
        $travel = Travel::factory()->create();
        $response = $this->postJson('/api/v1/admin/travels/' . $travel->id . '/tours');

        $response->assertStatus(401);
    }

    public function test_non_admin_user_cannot_access_adding_tour(): void
    {
        $this->seed(RoleSeeder::class);
        /** @var User $user */
        $user = User::factory()->create();
        $user->roles()->attach(Role::where('name', 'editor')->value('id'));
        /** @var Travel $travel */
        $travel = Travel::factory()->create();
        $response = $this->actingAs($user)->postJson('/api/v1/admin/travels/' . $travel->id . '/tours');

        $response->assertStatus(403);
    }

    public function test_saves_tour_successfully_with_valid_data(): void
    {
        $this->seed(RoleSeeder::class);
        /** @var User $user */
        $user = User::factory()->create();
        $user->roles()->attach(Role::where('name', 'admin')->value('id'));
        /** @var Travel $travel */
        $travel = Travel::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/v1/admin/travels/' . $travel->id . '/tours', [
            'name' => 'Tour name',
        ]);
        $response->assertStatus(422);

        $response = $this->actingAs($user)->postJson('/api/v1/admin/travels/' . $travel->id . '/tours', [
            'name' => 'Tour name',
            'starting_date' => now()->toDateString(),
            'ending_date' => now()->addDay()->toDateString(),
            'price' => 123.45,
        ]);

        $response->assertStatus(201);
    }
}
