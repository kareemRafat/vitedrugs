<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class DiagnosticSession extends Model
{
    use HasUlids;

    protected $fillable = [

        'species_id',

        'age_days',

        'positive_findings',

        'negative_findings',

        'asked_questions',

        'current_results',

        'is_finished'
    ];

    protected $casts = [

        'positive_findings' => 'array',

        'negative_findings' => 'array',

        'asked_questions' => 'array',

        'current_results' => 'array',

        'is_finished' => 'boolean'
    ];
}