<?php

namespace App\Models;

use App\Enums\ProductStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
