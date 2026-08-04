<?php

namespace App\Models\Drugs;

use Database\Factories\SynonymFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Synonym extends Model
{
    use HasFactory;

    protected static function newFactory(): Factory
    {
        return SynonymFactory::new();
    }

    protected $fillable = [
        'term',
        'normalized_term',
        'source_type',
        'synonymable_type',
        'synonymable_id',
    ];

    public function synonymable(): MorphTo
    {
        return $this->morphTo();
    }
}
