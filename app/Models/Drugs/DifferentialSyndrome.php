<?php

namespace App\Models\Drugs;

use App\Models\Disease;
use Database\Factories\DifferentialSyndromeFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class DifferentialSyndrome extends Model
{
    use HasFactory;

    protected static function newFactory(): Factory
    {
        return DifferentialSyndromeFactory::new();
    }

    protected $fillable = [
        'name',
        'description',
    ];

    public function clinicalSigns(): BelongsToMany
    {
        return $this->belongsToMany(ClinicalSign::class, 'differential_syndrome_clinical_sign');
    }

    public function diseases(): BelongsToMany
    {
        return $this->belongsToMany(Disease::class, 'differential_syndrome_disease')
            ->withPivot('boost_score');
    }
}
