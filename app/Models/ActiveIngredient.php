<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ActiveIngredient extends Model
{
    use HasFactory;
    use HasUlids;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'name_ar',
        'slug',
        'description',
        'description_ar',
        'indications',
        'contraindications',
        'precautions',
        'side_effects',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];


    public function drugClasses()
    {
        return $this->belongsToMany(
            DrugClass::class,
            'active_ingredient_drug_class'
        );
    }

    public function products()
    {
        return $this->belongsToMany(
            Product::class,
            'product_active_ingredient'
        )
            ->withPivot([
                'strength',
                'unit',
                'sort_order',
            ])
            ->withTimestamps();
    }

    public function drugInteractions()
    {
        return $this->hasMany(DrugInteraction::class);
    }
}
