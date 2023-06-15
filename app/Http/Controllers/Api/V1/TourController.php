<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ToursListRequest;
use App\Http\Resources\TourResource;
use App\Models\Travel;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * @group Public endpoints
 */
class TourController extends Controller
{
    /**
     * GET Travel Tours
     *
     * Returns paginated list of tours by travel slug.
     *
     * @urlParam travel_slug string Travel slug. Example: "first-travel"
     *
     * @bodyParam priceFrom number. Example: "123.45"
     * @bodyParam priceTo number. Example: "234.56"
     * @bodyParam dateFrom date. Example: "2023-06-01"
     * @bodyParam dateTo date. Example: "2023-07-01"
     * @bodyParam sortBy string. Example: "price"
     * @bodyParam sortOrder string. Example: "asc" or "desc"
     *
     * @response {"data":[{"id":"9958e389-5edf-48eb-8ecd-e058985cf3ce","name":"Tour on Sunday","starting_date":"2023-06-11","ending_date":"2023-06-16", ...}
     *
     */
    public function index(Travel $travel, ToursListRequest $request): AnonymousResourceCollection
    {
        $tours = $travel->tours()
            ->when($request->input('priceFrom'), function ($query) use ($request) {
                /** @var Builder $query */
                $query->where('price', '>=', $request->input('priceFrom') * 100);
            })
            ->when($request->input('priceTo'), function ($query) use ($request) {
                /** @var Builder $query */
                $query->where('price', '<=', $request->input('priceTo') * 100);
            })
            ->when($request->input('dateFrom'), function ($query) use ($request) {
                /** @var Builder $query */
                $query->where('starting_date', '>=', $request->input('dateFrom'));
            })
            ->when($request->input('dateTo'), function ($query) use ($request) {
                /** @var Builder $query */
                $query->where('starting_date', '<=', $request->input('dateTo'));
            })
            ->when($request->input('sortBy') && $request->input('sortOrder'), function ($query) use ($request) {
                /** @var Builder $query */
                $query->orderBy($request->input('sortBy'), $request->input('sortOrder'));
            })
            ->orderBy('starting_date')
            ->paginate();

        return TourResource::collection($tours);
    }
}
