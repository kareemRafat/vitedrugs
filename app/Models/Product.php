<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory;
    use HasUlids;
    use SoftDeletes;

    protected $fillable = [
        'company_id',
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
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
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
        return $this->hasMany(ProductImage::class);
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
