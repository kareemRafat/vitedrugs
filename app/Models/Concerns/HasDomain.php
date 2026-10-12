<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Marks a model as belonging to one animal group.
 *
 * The site is split into parts — Drugs, Large Animals and Poultry — but they
 * share tables rather than being forked per part. The `domain` column is what
 * keeps them apart, so any model that exists once per part needs it.
 *
 * Domain values use underscores (`large_animal`). That is deliberate and
 * separate from the UI theme parts, which use hyphens (`large-animals`); the
 * two vocabularies must never be mixed.
 *
 * @mixin Model
 */
trait HasDomain
{
    public const DOMAIN_LARGE_ANIMAL = 'large_animal';

    public const DOMAIN_POULTRY = 'poultry';

    /**
     * Domain values with their admin-facing labels.
     *
     * @return array<string, string>
     */
    public static function domainOptions(): array
    {
        return [
            self::DOMAIN_LARGE_ANIMAL => 'Large Animals',
            self::DOMAIN_POULTRY => 'Poultry',
        ];
    }

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
