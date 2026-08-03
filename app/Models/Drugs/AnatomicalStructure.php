<?php

namespace App\Models\Drugs;

use Database\Factories\AnatomicalStructureFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AnatomicalStructure extends Model
{
    use HasFactory;

    protected static function newFactory(): Factory
    {
        return AnatomicalStructureFactory::new();
    }

    protected $fillable = [
        'body_system_id',
        'parent_id',
        'canonical_name',
        'display_name',
        'type',
    ];

    public function bodySystem(): BelongsTo
    {
        return $this->belongsTo(BodySystem::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }
}
