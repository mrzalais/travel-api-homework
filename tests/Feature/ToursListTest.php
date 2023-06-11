<?php

namespace Tests\Feature;

use App\Models\Tour;
use App\Models\Travel;
use Tests\TestCase;

class ToursListTest extends TestCase
{
    public function test_tour_price_is_shown_correctly(): void
    {
        /** @var Travel $travel */
        $travel = Travel::factory()->create();
        $tourPrice = $this->faker->randomFloat(2, 10, 999);
        Tour::factory()->create([
            'travel_id' => $travel->id,
            'price' => $tourPrice,
        ]);

        $response = $this->get('/api/v1/travels/' . $travel->slug . '/tours');

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonFragment(['price' => (string) $tourPrice]);
    }

    public function test_tours_list_returns_pagination(): void
    {
        $toursPerPage = config('app.paginationPerPage.tours');

        /** @var Travel $travel */
        $travel = Travel::factory()->create();
        Tour::factory($toursPerPage + 1)->create(['travel_id' => $travel->id]);

        $response = $this->get('/api/v1/travels/' . $travel->slug . '/tours');

        $response->assertStatus(200);
        $response->assertJsonCount($toursPerPage, 'data');
        $response->assertJsonPath('meta.last_page', 2);
    }
}
