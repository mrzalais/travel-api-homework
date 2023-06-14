<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\TravelRequest;
use App\Http\Resources\TravelResource;
use App\Models\Travel;
use Illuminate\Http\Request;

class TravelController extends Controller
{
    public function store(TravelRequest $request): TravelResource
    {
        $travel = Travel::create($request->validated());

        return new TravelResource($travel);
    }

    public function update(Travel $travel, TravelRequest $request): TravelResource
    {
        $travel->update($request->validated());

        return new TravelResource($travel);
    }
}
