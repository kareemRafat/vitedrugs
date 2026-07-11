<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class DrugInteraction extends Model
{
    use HasUlids;

    protected $fillable = [
        'active_ingredient_id',
        'interacting_drug',
        'severity',
        'effect',
        'recommendation',
    ];

    public function activeIngredient()
    {
        return $this->belongsTo(ActiveIngredient::class);
    }
}