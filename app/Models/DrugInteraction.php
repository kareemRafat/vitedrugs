<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DrugInteraction extends Model
{
    use HasFactory;
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
