<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class Species extends Model
{
    use HasUlids;

    protected $fillable = ['name'];

    public function diseases(): BelongsToMany
    {
        return $this->belongsToMany(Disease::class);
    }
}