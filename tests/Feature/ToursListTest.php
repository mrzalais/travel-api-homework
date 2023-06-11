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
        $response->assertJsonFragment(['price' => (string)$tourPrice]);
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

    public function test_tours_list_sorts_by_starting_date_correctly(): void
    {
        /** @var Travel $travel */
        $travel = Travel::factory()->create();
        /** @var Tour $laterTour */
        $laterTour = Tour::factory()->create([
            'travel_id' => $travel->id,
            'starting_date' => now()->addDays(2),
            'ending_date' => now()->addDays(3),
        ]);
        /** @var Tour $earlierTour */
        $earlierTour = Tour::factory()->create([
            'travel_id' => $travel->id,
            'starting_date' => now(),
            'ending_date' => now()->addDays(1),
        ]);

        $response = $this->get('/api/v1/travels/' . $travel->slug . '/tours');

        $response->assertStatus(200);
        $response->assertJsonPath('data.0.id', $earlierTour->id);
        $response->assertJsonPath('data.1.id', $laterTour->id);
    }

    public function test_tours_list_sorts_by_price_correctly(): void
    {
        /** @var Travel $travel */
        $travel = Travel::factory()->create();
        /** @var Tour $expensiveTour */
        $expensiveTour = Tour::factory()->create([
            'travel_id' => $travel->id,
            'price' => 200,
        ]);
        /** @var Tour $cheapLaterTour */
        $cheapLaterTour = Tour::factory()->create([
            'travel_id' => $travel->id,
            'price' => 100,
            'starting_date' => now()->addDays(2),
            'ending_date' => now()->addDays(3),
        ]);
        /** @var Tour $cheapEarlierTour */
        $cheapEarlierTour = Tour::factory()->create([
            'travel_id' => $travel->id,
            'price' => 100,
            'starting_date' => now(),
            'ending_date' => now()->addDays(1),
        ]);

        $response = $this->get('/api/v1/travels/'.$travel->slug.'/tours?sortBy=price&sortOrder=asc');

        $response->assertStatus(200);
        $response->assertJsonPath('data.0.id', $cheapEarlierTour->id);
        $response->assertJsonPath('data.1.id', $cheapLaterTour->id);
        $response->assertJsonPath('data.2.id', $expensiveTour->id);
    }

    public function test_tours_list_filters_by_price_correctly(): void
    {
        /** @var Travel $travel */
        $travel = Travel::factory()->create();
        /** @var Tour $expensiveTour */
        $expensiveTour = Tour::factory()->create([
            'travel_id' => $travel->id,
            'price' => 200,
        ]);
        /** @var Tour $cheapTour */
        $cheapTour = Tour::factory()->create([
            'travel_id' => $travel->id,
            'price' => 100,
        ]);

        $endpoint = '/api/v1/travels/' . $travel->slug . '/tours';

        $response = $this->get($endpoint . '?priceFrom=100');
        $response->assertJsonCount(2, 'data');
        $response->assertJsonFragment(['id' => $cheapTour->id]);
        $response->assertJsonFragment(['id' => $expensiveTour->id]);

        $response = $this->get($endpoint . '?priceFrom=150');
        $response->assertJsonCount(1, 'data');
        $response->assertJsonMissing(['id' => $cheapTour->id]);
        $response->assertJsonFragment(['id' => $expensiveTour->id]);

        $response = $this->get($endpoint . '?priceFrom=250');
        $response->assertJsonCount(0, 'data');

        $response = $this->get($endpoint . '?priceTo=200');
        $response->assertJsonCount(2, 'data');
        $response->assertJsonFragment(['id' => $cheapTour->id]);
        $response->assertJsonFragment(['id' => $expensiveTour->id]);

        $response = $this->get($endpoint . '?priceTo=150');
        $response->assertJsonCount(1, 'data');
        $response->assertJsonMissing(['id' => $expensiveTour->id]);
        $response->assertJsonFragment(['id' => $cheapTour->id]);

        $response = $this->get($endpoint . '?priceTo=50');
        $response->assertJsonCount(0, 'data');

        $response = $this->get($endpoint . '?priceFrom=150&priceTo=250');
        $response->assertJsonCount(1, 'data');
        $response->assertJsonMissing(['id' => $cheapTour->id]);
        $response->assertJsonFragment(['id' => $expensiveTour->id]);
    }

    public function test_tours_list_filters_by_starting_date_correctly(): void
    {
        /** @var Travel $travel */
        $travel = Travel::factory()->create();
        /** @var Tour $laterTour */
        $laterTour = Tour::factory()->create([
            'travel_id' => $travel->id,
            'starting_date' => now()->addDays(2),
            'ending_date' => now()->addDays(3),
        ]);
        /** @var Tour $earlierTour */
        $earlierTour = Tour::factory()->create([
            'travel_id' => $travel->id,
            'starting_date' => date('Y-m-d'),
            'ending_date' => now()->addDays(1),
        ]);

        $endpoint = '/api/v1/travels/' . $travel->slug . '/tours';

        $response = $this->get($endpoint . '?dateFrom=' . date('Y-m-d'));
        $response->assertJsonCount(2, 'data');
        $response->assertJsonFragment(['id' => $earlierTour->id]);
        $response->assertJsonFragment(['id' => $laterTour->id]);

        $response = $this->get($endpoint . '?dateFrom=' . now()->addDay());
        $response->assertJsonCount(1, 'data');
        $response->assertJsonMissing(['id' => $earlierTour->id]);
        $response->assertJsonFragment(['id' => $laterTour->id]);

        $response = $this->get($endpoint . '?dateFrom=' . now()->addDays(5));
        $response->assertJsonCount(0, 'data');

        $response = $this->get($endpoint . '?dateTo=' . now()->addDays(5));
        $response->assertJsonCount(2, 'data');
        $response->assertJsonFragment(['id' => $earlierTour->id]);
        $response->assertJsonFragment(['id' => $laterTour->id]);

        $response = $this->get($endpoint . '?dateTo=' . now()->addDay());
        $response->assertJsonCount(1, 'data');
        $response->assertJsonMissing(['id' => $laterTour->id]);
        $response->assertJsonFragment(['id' => $earlierTour->id]);

        $response = $this->get($endpoint . '?dateTo=' . now()->subDay());
        $response->assertJsonCount(0, 'data');

        $response = $this->get($endpoint . '?dateFrom=' . now()->addDay() . '&dateTo=' . now()->addDays(5));
        $response->assertJsonCount(1, 'data');
        $response->assertJsonMissing(['id' => $earlierTour->id]);
        $response->assertJsonFragment(['id' => $laterTour->id]);
    }

    public function test_tour_list_returns_validation_errors(): void
    {
        /** @var Travel $travel */
        $travel = Travel::factory()->create();

        $response = $this->getJson('/api/v1/travels/' . $travel->slug . '/tours?dateFrom=abcde');
        $response->assertStatus(422);

        $response = $this->getJson('/api/v1/travels/' . $travel->slug . '/tours?priceFrom=abcde');
        $response->assertStatus(422);
    }
}
