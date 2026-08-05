<?php

namespace App\Models\LargeAnimals;

use Database\Factories\VeterinaryProjectFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VeterinaryProject extends Model
{
    use HasFactory;
    use HasUlids;
    use SoftDeletes;

    protected static function newFactory(): Factory
    {
        return VeterinaryProjectFactory::new();
    }

    protected $fillable = [
        'title',
        'slug',
        'summary',
        'content',
        'project_type',
        'sector',
        'featured',
        'is_published',
    ];

    protected $casts = [
        'featured' => 'boolean',
        'is_published' => 'boolean',
    ];
}
