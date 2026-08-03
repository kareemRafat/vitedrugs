<?php

namespace App\Models\Drugs;

use Database\Factories\FindingFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Finding extends Model
{
    use HasFactory;

    protected static function newFactory(): Factory
    {
        return FindingFactory::new();
    }

    protected $fillable = [
        'canonical_name',
        'display_name',
        'category',
        'ontology_type',
        'parent_id',
        'is_noisy',
        'is_general_sign',
        'slug',
    ];

    protected $casts = [
        'is_noisy' => 'boolean',
        'is_general_sign' => 'boolean',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function clinicalSigns(): HasMany
    {
        return $this->hasMany(ClinicalSign::class);
    }
}
