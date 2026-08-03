<?php

namespace App\Models\Drugs;

use Database\Factories\BodySystemFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BodySystem extends Model
{
    use HasFactory;

    protected static function newFactory(): Factory
    {
        return BodySystemFactory::new();
    }

    protected $fillable = [
        'canonical_name',
        'display_name',
    ];

    public function anatomicalStructures(): HasMany
    {
        return $this->hasMany(AnatomicalStructure::class);
    }
}
