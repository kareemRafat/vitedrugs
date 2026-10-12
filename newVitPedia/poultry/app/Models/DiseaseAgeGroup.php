<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DiseaseAgeGroup extends Model
{
    protected $fillable = [
        'disease_id',
        'min_days',
        'max_days',
        'description'
    ];

    public function disease(): BelongsTo
    {
        return $this->belongsTo(Disease::class);
    }
}