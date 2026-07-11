<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DrugClass extends Model
{
    use HasUlids;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'name_ar',
        'slug',
        'description',
        'description_ar',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function activeIngredients()
    {
        return $this->belongsToMany(
            ActiveIngredient::class,
            'active_ingredient_drug_class'
        );
    }
}
