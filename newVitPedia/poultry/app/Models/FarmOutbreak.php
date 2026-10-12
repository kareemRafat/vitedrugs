<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FarmOutbreak extends Model
{
    use HasFactory;
    use HasUlids;

    protected $fillable = [

        'farm_name',
        'species_id',
        'age_days',
        'flock_size',
        'affected_birds',
        'dead_birds',
        'morbidity_rate',
        'mortality_rate',
        'case_fatality_rate',
        'vaccinated',
        'mixed_species',
        'poor_ventilation',
        'wet_litter',
        'recent_feed_change',
        'new_birds_introduced',
        'history',
        'clinical_findings',
        'suspected_diseases',
        'final_diagnosis'
    ];

    protected $casts = [

        'clinical_findings' => 'array',
        'suspected_diseases' => 'array',
        'final_diagnosis' => 'array',

        'vaccinated' => 'boolean',
        'mixed_species' => 'boolean',
        'poor_ventilation' => 'boolean',
        'wet_litter' => 'boolean',
        'recent_feed_change' => 'boolean',
        'new_birds_introduced' => 'boolean',
    ];
}
