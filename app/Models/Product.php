<?php

namespace App\Models;

use App\Enums\ProductStatus;
use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory;
    use HasUlids;
    use SoftDeletes;

    protected $fillable = [
        'trade_name',
        'trade_name_ar',
        'slug',
        'dosage_form_id',
        'product_type',
        'description',
        'description_ar',
        'package_size',
        'storage_conditions',
        'is_active',
        'status',
        'created_by',
        'admin_notes',
        'reviewed_at',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'status' => ProductStatus::class,
            'reviewed_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::addGlobalScope('approved', fn (Builder $builder) => $builder->where('status', ProductStatus::Approved)->where('is_active', true));
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', ProductStatus::Pending);
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', ProductStatus::Approved);
    }

    public function scopeRejected(Builder $query): Builder
    {
        return $query->where('status', ProductStatus::Rejected);
    }

    public function scopePendingOrApproved(Builder $query): Builder
    {
        return $query->whereIn('status', [ProductStatus::Pending, ProductStatus::Approved]);
    }

    public function isPending(): bool
    {
        return $this->status === ProductStatus::Pending;
    }

    public function isApproved(): bool
    {
        return $this->status === ProductStatus::Approved;
    }

    public function isRejected(): bool
    {
        return $this->status === ProductStatus::Rejected;
    }

    /**
     * Only the products a part has a dosing instruction for.
     *
     * `products` is one shared table for the whole site and carries no species
     * column, so "is this drug for poultry?" is not recorded anywhere directly.
     * Three indirect signals disagree on how to answer it:
     *
     *   dosages             → 15 of 30 have a dose written for a bird
     *   disease_product     → 12 treat a poultry disease
     *   withdrawal_periods  →  6 carry a poultry withdrawal figure
     *
     * Dosing wins because it is the only one of the three that is a statement
     * *about the animal*: a drug that treats a poultry disease but has no poultry
     * dose has not been shown to be dosable in a bird, and a withdrawal period
     * with no dose describes a use we cannot reproduce.
     *
     * This is narrower than the union of all three (24 of 30), which is the
     * point — an inclusion rule wide enough to include almost everything is not
     * a filter, and the six products left out have no poultry record at all.
     *
     * @param  Builder<self>  $query
     */
    public function scopeApplicableTo(Builder $query, string $domain): Builder
    {
        // The closure is typed on `Relation` as well as `Builder` because the
        // dotted form hands it the `Dosage` relation rather than its query. Both
        // forward `where()` to the same underlying builder.
        return $query->whereHas(
            'dosages.species',
            fn (Builder|Relation $q) => $q->where('species.domain', $domain),
        );
    }

    /**
     * Eager-loads what a catalogue card renders, with the part's own rows only.
     *
     * The relations are constrained rather than filtered afterwards. Doses and
     * withdrawal periods belong to one species each, and a card on the poultry
     * page that listed a cattle dose would be offering a number nobody asked
     * for — the same cross-part leak as the Phase 6 and Phase 7 pickers.
     *
     * @param  Builder<self>  $query
     */
    public function scopeWithPartRelations(Builder $query, string $domain): Builder
    {
        // The two hops here are handed different things and the difference is
        // not guessable: an eager-load constraint receives the *relation*
        // (`Relation`), while a `whereHas` nested inside it receives a query
        // *builder* (`Builder`). Both forward `where()` to the same underlying
        // query, so the union below accepts either and the call site reads
        // normally whichever Laravel hands it.
        $partSpecies = fn (): Closure => fn (Builder|Relation $s) => $s->where('domain', $domain);

        return $query->with([
            'manufacturer',
            'dosageForm',
            'activeIngredients',
            'diseases' => fn (Relation $q) => $q->where('diseases.domain', $domain),
            'dosages' => fn (Relation $q) => $q->whereHas('species', $partSpecies())->with('species'),
            'withdrawalPeriods' => fn (Relation $q) => $q->whereHas('species', $partSpecies())->with('species'),
        ]);
    }

    public function companies()
    {
        return $this->belongsToMany(
            Company::class,
            'product_company'
        )
            ->withPivot('role')
            ->withTimestamps();
    }

    public function dosageForm()
    {
        return $this->belongsTo(DosageForm::class);
    }

    public function activeIngredients()
    {
        return $this->belongsToMany(
            ActiveIngredient::class,
            'product_active_ingredient'
        )
            ->withPivot([
                'strength',
                'unit',
                'sort_order',
            ])
            ->withTimestamps();
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function documents()
    {
        return $this->hasMany(ProductDocument::class);
    }

    public function dosages()
    {
        return $this->hasMany(Dosage::class);
    }

    public function withdrawalPeriods()
    {
        return $this->hasMany(WithdrawalPeriod::class);
    }

    public function alternatives()
    {
        return $this->hasMany(Alternative::class);
    }

    public function indications()
    {
        return $this->hasMany(Indication::class);
    }

    public function contraindications()
    {
        return $this->hasMany(Contraindication::class);
    }

    public function precautions()
    {
        return $this->hasMany(Precaution::class);
    }

    public function sideEffects()
    {
        return $this->hasMany(SideEffect::class);
    }

    public function diseases()
    {
        return $this->belongsToMany(
            Disease::class,
            'disease_product'
        )
            ->withPivot([
                'sort_order',
            ])
            ->withTimestamps();
    }

    public function manufacturer()
    {
        return $this->companies()
            ->wherePivot(
                'role',
                'manufacturer'
            );
    }

    public function agents()
    {
        return $this->companies()
            ->wherePivot(
                'role',
                'agent'
            );
    }

    public function distributors()
    {
        return $this->companies()
            ->wherePivot(
                'role',
                'distributor'
            );
    }
}
