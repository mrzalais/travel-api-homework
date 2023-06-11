<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ToursListRequest;
use App\Http\Resources\TourResource;
use App\Models\Travel;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TourController extends Controller
{
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
