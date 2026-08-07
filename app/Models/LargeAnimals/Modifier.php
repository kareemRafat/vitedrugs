<?php

namespace App\Models\LargeAnimals;

use Database\Factories\ModifierFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Modifier extends Model
{
    use HasFactory;

    protected static function newFactory(): Factory
    {
        return ModifierFactory::new();
    }

    protected $fillable = [
        'canonical_name',
        'display_name',
        'display_name_ar',
        'type',
        'modifier_group',
        'is_noisy',
    ];

    protected $casts = [
        'is_noisy' => 'boolean',
    ];

    public function getLocalizedDisplayNameAttribute(): string
    {
        return app()->getLocale() === 'ar' && $this->display_name_ar
            ? $this->display_name_ar
            : $this->display_name;
    }

    public function getDisplayLabelAttribute(): string
    {
        $name = $this->display_name;

        if (! str_starts_with($name, '{')) {
            return $name;
        }

        $data = json_decode($name, true);

        if (! is_array($data) || $data === []) {
            return $name;
        }

        return collect($data)
            ->map(function ($value, string $key): string {
                $label = $this->humanize($key);

                if (is_bool($value)) {
                    return $value ? $label : $this->negate($label);
                }

                if (is_array($value)) {
                    return collect($value)->map(fn (mixed $item): string => $this->humanize((string) $item))->implode(', ');
                }

                return $label.': '.$this->humanize((string) $value);
            })
            ->implode(', ');
    }

    private function humanize(string $value): string
    {
        return ucfirst(str_replace('_', ' ', $value));
    }

    private function negate(string $label): string
    {
        return 'not '.strtolower($label);
    }

    public function clinicalSigns(): HasMany
    {
        return $this->hasMany(ClinicalSign::class);
    }
}
