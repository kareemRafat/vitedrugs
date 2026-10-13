<?php

namespace App\Models\LargeAnimals;

use Database\Factories\BodySystemFactory;
use Illuminate\Database\Eloquent\Builder;
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
        'display_name_ar',
    ];

    public function getLocalizedDisplayNameAttribute(): string
    {
        return app()->getLocale() === 'ar' && $this->display_name_ar
            ? $this->display_name_ar
            : $this->display_name;
    }

    public function anatomicalStructures(): HasMany
    {
        return $this->hasMany(AnatomicalStructure::class);
    }

    /**
     * Only the systems that hold at least one structure.
     *
     * Twelve systems in the table are empty — `Systemic`, `Metabolic`, `Otic`,
     * `Ophthalmic` and the rest. They are kept because they are real taxonomy a
     * lesion atlas will want, but every one of them is a filter option that can
     * only ever return nothing, and a body system with no organs has nothing to
     * draw on a diagram.
     *
     * @param  Builder<BodySystem>  $query
     * @return Builder<BodySystem>
     */
    public function scopeWithStructures(Builder $query): Builder
    {
        return $query->whereHas('anatomicalStructures');
    }
}
