<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class Indication extends Model
{
    use HasUlids;

    protected $fillable = [
        'product_id',
        'description',
        'description_ar',
        'sort_order',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}