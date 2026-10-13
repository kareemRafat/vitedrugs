<?php

namespace App\Models\LargeAnimals;

use Database\Factories\AnatomicalStructureFactory;
use Illuminate\Database\Eloquent\Builder;
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
        'display_name_ar',
        'type',
        'domain',
    ];

    /**
     * The structures that can exist in one species.
     *
     * `domain` is null on the great majority of rows, and null means "every
     * part" rather than "unknown": liver, gizzard, air sacs, comb and syrinx are
     * shared anatomy, and claiming them for one species would be a statement the
     * table cannot support. Only the organs a bird does not have — a rumen, a
     * udder, a urinary bladder — carry a value.
     *
     * @param  Builder<AnatomicalStructure>  $query
     * @return Builder<AnatomicalStructure>
     */
    public function scopeForPart(Builder $query, string $domain): Builder
    {
        return $query->where(function (Builder $builder) use ($domain): void {
            $builder->whereNull('domain')->orWhere('domain', $domain);
        });
    }

    public function getLocalizedDisplayNameAttribute(): string
    {
        return app()->getLocale() === 'ar' && $this->display_name_ar
            ? $this->display_name_ar
            : $this->display_name;
    }

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
