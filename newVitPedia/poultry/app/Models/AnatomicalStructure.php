<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AnatomicalStructure extends Model
{
    use HasFactory;

    protected $fillable = [

        'body_system_id',

        'parent_id',

        'name',

        'display_name',

        'canonical_name',

        'slug',

        'description',

        'anatomical_region',

        'hierarchy_level',

        'sort_order',

        'is_vital',

        'is_active',
    ];

    protected $casts = [

        'is_vital' => 'boolean',

        'is_active' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function bodySystem()
    {
        return $this->belongsTo(
            BodySystem::class
        );
    }

    public function parent()
    {
        return $this->belongsTo(
            AnatomicalStructure::class,
            'parent_id'
        );
    }

    public function children()
    {
        return $this->hasMany(
            AnatomicalStructure::class,
            'parent_id'
        );
    }

    public function lesions()
    {
        return $this->hasMany(
            Lesion::class
        );
    }

    public function findings()
    {
        return $this->hasMany(
            Finding::class
        );
    }

    public function media()
    {
        return $this->morphMany(
            MediaAsset::class,
            'mediable'
        )->orderBy('sort_order');
    }
}
