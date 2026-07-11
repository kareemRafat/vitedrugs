<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductDocument extends Model
{
    use HasFactory;
    use HasUlids;

    protected $fillable = [
        'product_id',
        'title',
        'file_path',
        'type',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}