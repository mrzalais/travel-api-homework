<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * App\Models\Travel
 *
 * @property string $id
 * @property int $is_public
 * @property string $slug
 * @property string $name
 * @property string $description
 * @property int $number_of_days
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, Tour> $tours
 * @property-read int|null $tours_count
 * @property-read int $number_of_nights
 *
 * @method static Builder|Travel findSimilarSlugs(string $attribute, array $config, string $slug)
 * @method static Builder|Travel newModelQuery()
 * @method static Builder|Travel newQuery()
 * @method static Builder|Travel query()
 * @method static Builder|Travel whereCreatedAt($value)
 * @method static Builder|Travel whereDescription($value)
 * @method static Builder|Travel whereId($value)
 * @method static Builder|Travel whereIsPublic($value)
 * @method static Builder|Travel whereName($value)
 * @method static Builder|Travel whereNumberOfDays($value)
 * @method static Builder|Travel whereSlug($value)
 * @method static Builder|Travel whereUpdatedAt($value)
 * @method static Builder|Travel withUniqueSlugConstraints(Model $model, string $attribute, array $config, string $slug)
 *
 * @mixin Eloquent
 */
class Travel extends Model
{
    use HasFactory;
    use HasUuids;
    use Sluggable;

    protected $table = 'travels';

    protected $fillable = [
        'is_public',
        'slug',
        'name',
        'description',
        'number_of_days',
    ];

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name',
            ],
        ];
    }

    public function tours(): HasMany
    {
        return $this->hasMany(Tour::class);
    }

    public function numberOfNights(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => data_get($attributes, 'number_of_days') - 1
        );
    }
}
