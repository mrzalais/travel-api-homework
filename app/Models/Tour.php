<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\Tour
 *
 * @property string $id
 * @property string $travel_id
 * @property string $name
 * @property string $starting_date
 * @property string $ending_date
 * @property int $price
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|Tour newModelQuery()
 * @method static Builder|Tour newQuery()
 * @method static Builder|Tour query()
 * @method static Builder|Tour whereCreatedAt($value)
 * @method static Builder|Tour whereEndingDate($value)
 * @method static Builder|Tour whereId($value)
 * @method static Builder|Tour whereName($value)
 * @method static Builder|Tour wherePrice($value)
 * @method static Builder|Tour whereStartingDate($value)
 * @method static Builder|Tour whereTravelId($value)
 * @method static Builder|Tour whereUpdatedAt($value)
 * @mixin Eloquent
 */
class Tour extends Model
{
    use HasFactory;
    use HasUuids;

    protected $fillable = [
        'travel_id',
        'name',
        'starting_date',
        'ending_date',
        'price',
    ];

    public function price(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value / 100,
            set: fn ($value) => $value * 100
        );
    }
}
