<?php

namespace App\Models\Drugs;

use Database\Factories\AbbreviationFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Abbreviation extends Model
{
    use HasFactory;

    protected static function newFactory(): Factory
    {
        return AbbreviationFactory::new();
    }

    protected $fillable = [
        'abbreviation',
        'full_term',
        'description',
        'category',
    ];
}
