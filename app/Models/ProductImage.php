<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ProductImage extends Model
{
    use HasFactory;
    use HasUlids;

    protected $fillable = [
        'product_id',
        'image',
        'sort_order',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function url(): string
    {
        if (str_starts_with($this->image, 'http')) {
            return $this->image;
        }

        return Storage::disk('public')->url($this->image);
    }
}
