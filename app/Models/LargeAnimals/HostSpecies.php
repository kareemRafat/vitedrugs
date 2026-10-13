<?php

namespace App\Models\LargeAnimals;

use App\Models\Disease;
use Database\Factories\HostSpeciesFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use InvalidArgumentException;

class HostSpecies extends Model
{
    use HasFactory;

    /**
     * Restrict to the species that belong to a themed site part.
     *
     * This table has no `domain` column — part membership lives in
     * `taxonomy_group` and is mapped through `config('vetpedia.parts')`.
     *
     * @param  Builder<self>  $query
     */
    public function scopeForPart(Builder $query, string $part): Builder
    {
        $groups = config("vetpedia.parts.{$part}.taxonomy_groups");

        if (! is_array($groups)) {
            throw new InvalidArgumentException("Unknown site part [{$part}]. Check config/vetpedia.php.");
        }

        return $query->whereIn('taxonomy_group', $groups);
    }

    protected static function newFactory(): Factory
    {
        return HostSpeciesFactory::new();
    }

    protected $fillable = [
        'canonical_name',
        'display_name',
        'display_name_ar',
        'taxonomy_group',
        'is_domestic',
        'is_wildlife',
        'is_human',
    ];

    public function getLocalizedDisplayNameAttribute(): string
    {
        return app()->getLocale() === 'ar' && $this->display_name_ar
            ? $this->display_name_ar
            : $this->display_name;
    }

    protected $casts = [
        'is_domestic' => 'boolean',
        'is_wildlife' => 'boolean',
        'is_human' => 'boolean',
    ];

    public function diseases(): BelongsToMany
    {
        return $this->belongsToMany(Disease::class, 'disease_host_species')
            ->withPivot([
                'role',
                'susceptibility',
                'is_primary_host',
                'is_reservoir',
                'is_vector',
                'is_carrier',
                'is_incidental_host',
                'notes',
            ])
            ->withTimestamps();
    }
}
