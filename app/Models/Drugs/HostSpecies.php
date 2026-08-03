<?php

namespace App\Models\Drugs;

use App\Models\Disease;
use Database\Factories\HostSpeciesFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class HostSpecies extends Model
{
    use HasFactory;

    protected static function newFactory(): Factory
    {
        return HostSpeciesFactory::new();
    }

    protected $fillable = [
        'canonical_name',
        'display_name',
        'taxonomy_group',
        'is_domestic',
        'is_wildlife',
        'is_human',
    ];

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
