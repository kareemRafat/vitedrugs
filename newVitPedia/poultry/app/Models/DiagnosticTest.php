<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class DiagnosticTest extends Model
{
    use HasUlids;

    protected $fillable = [
        'disease_id',
        'test_name',
        'sample',
        'purpose'
    ];

    public function disease(): BelongsTo
    {
        return $this->belongsTo(Disease::class);
    }
}