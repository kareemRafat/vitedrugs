<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class MediaAsset extends Model
{
    use HasUlids;

    protected $fillable = [

        'image_path',

        'title',

        'caption',

        'image_type',

        'featured',

        'sort_order',

        'source',

        'copyright',
    ];

    protected $casts = [

        'featured' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function mediable(): MorphTo
    {
        return $this->morphTo();
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getImageUrlAttribute(): string
    {
        return asset(
            'storage/' . $this->image_path
        );
    }

    public function getThumbnailUrlAttribute(): string
    {
        return $this->image_url;
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeFeatured($query)
    {
        return $query->where(
            'featured',
            true
        );
    }

    public function scopeClinical($query)
    {
        return $query->where(
            'image_type',
            'clinical'
        );
    }

    public function scopeNecropsy($query)
    {
        return $query->where(
            'image_type',
            'necropsy'
        );
    }

    public function scopeHistopathology($query)
    {
        return $query->where(
            'image_type',
            'histopathology'
        );
    }
}