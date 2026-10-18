<?php

namespace App\Models;

use App\Enums\ContactCategory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactSubmission extends Model
{
    use HasFactory;
    use HasUlids;

    protected $fillable = [
        'name',
        'email',
        'role',
        'subject',
        'category',
        'message',
        // Never visitor-supplied: the form writes the active app locale server-side.
        'locale',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'category' => ContactCategory::class,
            'is_read' => 'boolean',
            'read_at' => 'datetime',
        ];
    }

    public function scopeUnread(Builder $query): Builder
    {
        return $query->where('is_read', false);
    }

    public function scopeRead(Builder $query): Builder
    {
        return $query->where('is_read', true);
    }

    public function markAsRead(): void
    {
        if ($this->is_read) {
            return;
        }

        $this->forceFill([
            'is_read' => true,
            'read_at' => now(),
        ])->save();
    }

    public function markAsUnread(): void
    {
        if (! $this->is_read) {
            return;
        }

        $this->forceFill([
            'is_read' => false,
            'read_at' => null,
        ])->save();
    }
}
