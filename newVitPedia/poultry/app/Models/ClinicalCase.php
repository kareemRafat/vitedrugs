<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClinicalCase extends Model
{
    protected $fillable = [

        'title',
        'slug',
        'species_id',
        'disease_id',
        'age_days',
        'flock_size',
        'affected_birds',
        'dead_birds',
        'history',
        'clinical_findings',
        'necropsy_findings',
        'laboratory_results',
        'diagnosis',
        'treatment',
        'outcome',
        'images'
    ];

    protected $casts = [

        'images' => 'array'
    ];

    public function disease()
    {
        return $this->belongsTo(
            Disease::class
        );
    }

    public function species()
    {
        return $this->belongsTo(
            Species::class
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
