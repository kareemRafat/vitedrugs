<?php

namespace App\Models\LargeAnimals;

use App\Models\Disease;
use Database\Factories\DiseaseClassificationFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DiseaseClassification extends Model
{
    use HasFactory;

    protected static function newFactory(): Factory
    {
        return DiseaseClassificationFactory::new();
    }

    protected $fillable = [
        'disease_id',
        'infectiousness',
        'transmissibility',
        'etiology_type',
        'occurrence_patterns',
        'disease_courses',
        'notifiable',
        'zoonotic',
        'oie_category',
    ];

    protected $casts = [
        'occurrence_patterns' => 'array',
        'disease_courses' => 'array',
        'notifiable' => 'boolean',
        'zoonotic' => 'boolean',
    ];

    public function disease(): BelongsTo
    {
        return $this->belongsTo(Disease::class);
    }
}
