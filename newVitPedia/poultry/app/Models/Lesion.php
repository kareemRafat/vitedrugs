<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lesion extends Model
{
    protected $fillable = [

        'canonical_name',

        'display_name',

        'body_system_id',

        'anatomical_structure_id',

        'description'
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function diseases()
    {
        return $this->belongsToMany(
            Disease::class
        )
            ->withPivot([

                'weight',

                'specificity',

                'is_pathognomonic',

                'is_confirmatory'
            ]);
    }

    public function bodySystem()
    {
        return $this->belongsTo(
            BodySystem::class
        );
    }

    public function anatomicalStructure()
    {
        return $this->belongsTo(
            AnatomicalStructure::class
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
