<?php

namespace Tests\Feature;

use App\Models\Travel;
use Tests\TestCase;

class TravelsListTest extends TestCase
{
    public function test_travels_list_returns_paginated_data_correctly(): void
    {
        Travel::factory(16)->create(['is_public' => true]);

        $response = $this->get('/api/v1/travels');

        $response->assertStatus(200);
        $response->assertJsonCount(15, 'data');
        $response->assertJsonPath('meta.per_page', 15);
        $this->assertGreaterThan(1, data_get($response->json(), 'meta.last_page'));
        $this->assertGreaterThan(15, data_get($response->json(), 'meta.total'));
    }
}
