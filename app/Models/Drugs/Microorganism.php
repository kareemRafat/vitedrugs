<?php

namespace App\Models\Drugs;

use App\Models\Disease;
use Database\Factories\MicroorganismFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Microorganism extends Model
{
    use HasFactory;

    protected static function newFactory(): Factory
    {
        return MicroorganismFactory::new();
    }

    protected $fillable = [
        'name',
        'normalized_name',
        'kingdom',
        'phylum',
        'class',
        'order',
        'family',
        'genus',
        'species',
        'microorganism_type',
        'is_pathogenic',
        'searchable_text',
        'json_data',
        'source_json_file',
        'tags',
        'slug',
    ];

    protected $casts = [
        'is_pathogenic' => 'boolean',
        'json_data' => 'array',
        'tags' => 'array',
    ];

    public function diseases(): BelongsToMany
    {
        return $this->belongsToMany(Disease::class, 'disease_microorganism')
            ->withPivot('role')
            ->withTimestamps();
    }
}
