<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class Finding extends Model
{
    use HasUlids;

    protected $fillable = [
        'canonical_name',
        'display_name',
        'type',
        'body_system'
    ];

    public function diseases(): BelongsToMany
    {
        return $this->belongsToMany(Disease::class)
            ->withPivot([
                'weight',
                'specificity',
                'is_pathognomonic'
            ]);
    }
    public function media()
    {
        return $this->morphMany(
            MediaAsset::class,
            'mediable'
        )->orderBy('sort_order');
    }
}
