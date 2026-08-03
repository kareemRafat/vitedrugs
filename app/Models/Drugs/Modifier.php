<?php

namespace App\Models\Drugs;

use Database\Factories\ModifierFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Modifier extends Model
{
    use HasFactory;

    protected static function newFactory(): Factory
    {
        return ModifierFactory::new();
    }

    protected $fillable = [
        'canonical_name',
        'display_name',
        'type',
        'modifier_group',
        'is_noisy',
    ];

    protected $casts = [
        'is_noisy' => 'boolean',
    ];

    public function clinicalSigns(): HasMany
    {
        return $this->hasMany(ClinicalSign::class);
    }
}
