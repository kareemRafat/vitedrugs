<?php

namespace App\Models;

use App\Enums\SubmissionStatus;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductSubmission extends Model
{
    use HasUlids;

    protected $fillable = [
        'user_id',
        'submitted_data',
        'submitted_by_name',
        'submitted_by_email',
        'submitted_by_phone',
        'status',
        'admin_notes',
    ];

    protected $casts = [
        'submitted_data' => 'array',
        'status' => SubmissionStatus::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', SubmissionStatus::Pending);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', SubmissionStatus::Approved);
    }

    public function scopeRejected($query)
    {
        return $query->where('status', SubmissionStatus::Rejected);
    }
}
