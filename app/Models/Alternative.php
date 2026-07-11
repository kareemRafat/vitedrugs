<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Alternative extends Model
{
    use HasFactory;
    use HasUlids;

    protected $fillable = [
        'product_id',
        'alternative_product_id',
        'type',
        'notes',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function alternativeProduct()
    {
        return $this->belongsTo(
            Product::class,
            'alternative_product_id'
        );
    }
}