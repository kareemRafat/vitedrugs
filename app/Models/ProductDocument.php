<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

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

    public function url(): string
    {
        if (str_starts_with($this->file_path, 'http')) {
            return $this->file_path;
        }

        return Storage::disk('public')->url($this->file_path);
    }
}
