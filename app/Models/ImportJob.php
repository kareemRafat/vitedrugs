<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class ImportJob extends Model
{
    use HasUlids;

    protected $fillable = [
        'source_file',
        'source_type',
        'status',
        'is_approved',
        'total_products',
        'imported_products',
        'failed_products',
        'extracted_json',
        'error_message',
        'started_at',
        'completed_at',
    ];
    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];
}
