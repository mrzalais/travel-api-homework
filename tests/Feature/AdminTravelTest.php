<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\Travel;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Tests\TestCase;

class AdminTravelTest extends TestCase
{
    public function test_public_user_cannot_access_adding_travel(): void
    {
        $response = $this->postJson('/api/v1/admin/travels');

        $response->assertStatus(401);
    }

    public function test_non_admin_user_cannot_access_adding_travel(): void
    {
        $this->seed(RoleSeeder::class);
        /** @var User $user */
        $user = User::factory()->create();
        $user->roles()->attach(Role::query()->where('name', 'editor')->value('id'));
        $response = $this->actingAs($user)->postJson('/api/v1/admin/travels');

        $response->assertStatus(403);
    }

    public function test_saves_travel_successfully_with_valid_data(): void
    {
        $this->seed(RoleSeeder::class);
        /** @var User $user */
        $user = User::factory()->create();
        $user->roles()->attach(Role::query()->where('name', 'admin')->value('id'));

        $travelName = $this->faker->name;
        $response = $this->actingAs($user)->postJson('/api/v1/admin/travels', [
            'name' => $travelName,
        ]);
        $response->assertStatus(422);

        $response = $this->actingAs($user)->postJson('/api/v1/admin/travels', [
            'name' => $travelName,
            'is_public' => 1,
            'description' => 'Some description',
            'number_of_days' => 5,
        ]);

        $response->assertStatus(201);
    }

    public function test_updates_travel_successfully_with_valid_data(): void
    {
        $this->seed(RoleSeeder::class);
        /** @var User $user */
        $user = User::factory()->create();
        $user->roles()->attach(Role::query()->where('name', 'editor')->value('id'));
        /** @var Travel $travel */
        $travel = Travel::factory()->create();

        $response = $this->actingAs($user)->putJson('/api/v1/admin/travels/'.$travel->id, [
            'name' => 'Travel name',
        ]);
        $response->assertStatus(422);

        $response = $this->actingAs($user)->putJson('/api/v1/admin/travels/'.$travel->id, [
            'name' => 'Travel name updated',
            'is_public' => 1,
            'description' => 'Some description',
            'number_of_days' => 5,
        ]);

        $response->assertStatus(200);
    }
}
