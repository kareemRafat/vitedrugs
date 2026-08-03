<?php

namespace App\Models\Drugs;

use App\Models\Disease;
use Database\Factories\MedicalArticleFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicalArticle extends Model
{
    use HasFactory;

    protected static function newFactory(): Factory
    {
        return MedicalArticleFactory::new();
    }

    protected $fillable = [
        'title',
        'slug',
        'content',
        'disease_id',
        'species',
        'article_type',
        'is_published',
        'summary',
    ];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    public function disease(): BelongsTo
    {
        return $this->belongsTo(Disease::class);
    }
}
