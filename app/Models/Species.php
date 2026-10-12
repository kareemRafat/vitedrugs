<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Species extends Model
{
    use HasFactory;
    use HasUlids;
    use SoftDeletes;

    public const DOMAIN_LARGE_ANIMAL = 'large_animal';

    public const DOMAIN_POULTRY = 'poultry';

    protected $fillable = [
        'name',
        'name_ar',
        'slug',
        'description',
        'description_ar',
        'is_active',
        'domain',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Restrict to a single domain.
     *
     * @param  Builder<self>  $query
     */
    public function scopeForDomain(Builder $query, string $domain): Builder
    {
        return $query->where('domain', $domain);
    }

    /**
     * @param  Builder<self>  $query
     */
    public function scopePoultry(Builder $query): Builder
    {
        return $query->where('domain', self::DOMAIN_POULTRY);
    }

    /**
     * @param  Builder<self>  $query
     */
    public function scopeLargeAnimals(Builder $query): Builder
    {
        return $query->where('domain', self::DOMAIN_LARGE_ANIMAL);
    }
}
