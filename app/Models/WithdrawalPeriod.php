<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WithdrawalPeriod extends Model
{
    use HasFactory;
    use HasUlids;

    protected $fillable = [
        'product_id',
        'species_id',
        'meat_days',
        'milk_days',
        'egg_days',
        'notes',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function species()
    {
        return $this->belongsTo(Species::class);
    }
}