<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BodySystem extends Model
{
    use HasFactory;

    protected $fillable = [

        'name',

        'display_name',

        'canonical_name',

        'slug',

        'description',

        'icon',

        'sort_order',

        'is_active',
    ];

    protected $casts = [

        'is_active' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function anatomicalStructures()
    {
        return $this->hasMany(
            AnatomicalStructure::class
        );
    }

    public function lesions()
    {
        return $this->hasMany(
            Lesion::class
        );
    }

    public function media()
    {
        return $this->morphMany(
            MediaAsset::class,
            'mediable'
        );
    }
}
