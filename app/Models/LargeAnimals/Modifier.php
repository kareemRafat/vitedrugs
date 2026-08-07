<?php

namespace App\Models\LargeAnimals;

use Database\Factories\ModifierFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Modifier extends Model
{
    use HasFactory;

    public const DIMENSIONS = [
        'laterality' => ['laterality', 'bilateral', 'direction', 'sided'],
        'course' => ['course', 'chronicity', 'onset', 'duration', 'progression', 'progresses_to', 'healing', 'recurrence', 'chronic', 'peracute', 'persistent', 'recurrent'],
        'severity' => ['severity', 'severe', 'mild', 'marked', 'moderate', 'extent', 'reduced', 'profuse'],
        'temporal' => ['timing', 'intermittent', 'temporal', 'occurrence'],
        'pain' => ['pain', 'painful', 'painless', 'elicits', 'sensation'],
        'location' => ['location', 'locations', 'location_preference', 'common_location', 'common_site', 'site', 'depth', 'affected', 'affected_organs', 'position'],
        'temperature' => ['temperature', 'temperature_range', 'temperature_range_c', 'temp'],
        'appearance' => ['appearance', 'color', 'consistency', 'shape', 'size', 'size_cm', 'diameter_cm', 'diameter_mm', 'form', 'discharge', 'discharge_consistency', 'odor', 'pus_color', 'discoloration', 'swelling', 'enlargement', 'erythematous', 'hemorrhagic', 'suppurative', 'cyanosis', 'hyperemia', 'warm', 'moist', 'fluid', 'blood_stained', 'necrosis', 'muscle_tone', 'composition', 'contains', 'contains_gas', 'crepitation', 'watery', 'purulent', 'serous', 'mucopurulent'],
        'distribution' => ['distribution', 'pattern', 'systemic_extension', 'mucosal_involvement', 'all_quarters_affected', 'diffuse', 'multifocal', 'generalized'],
        'association' => ['associated_with', 'associated_structure', 'associated_stage', 'association', 'secondary', 'secondary_bacterial', 'secondary_to', 'response_to_treatment', 'cause', 'etiology', 'trigger'],
        'host' => ['host', 'species', 'species_association', 'species_specific', 'age_group', 'sex'],
        'behavior' => ['behavior', 'audible', 'groaning', 'grunting', 'exercise_induced', 'detectable_rectally', 'detected_by', 'mobility'],
        'congenital' => ['congenital', 'immune_mediated'],
        'type' => ['type', 'stage', 'disease_form', 'lay_term', 'common_name'],
    ];

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
        $data = $this->decodeData();

        if ($data === null) {
            return $this->display_name;
        }

        return self::formatData($data);
    }

    public static function formatData(array $data): string
    {
        return collect($data)
            ->map(function ($value, string $key): string {
                $label = self::humanize($key);

                if (is_bool($value)) {
                    return $value ? $label : self::negate($label);
                }

                if (is_array($value)) {
                    return collect($value)->map(fn (mixed $item): string => self::humanize((string) $item))->implode(', ');
                }

                return $label.': '.self::humanize((string) $value);
            })
            ->implode(', ');
    }

    public static function dimensionFor(string $key): string
    {
        foreach (self::DIMENSIONS as $dimension => $keys) {
            if (in_array($key, $keys, true)) {
                return $dimension;
            }
        }

        return Str::slug($key);
    }

    public static function groupsForData(array $data): ?string
    {
        $groups = collect(array_keys($data))
            ->map(fn (string $key): string => self::dimensionFor($key))
            ->unique()
            ->filter()
            ->sort()
            ->values();

        return $groups->isNotEmpty() ? $groups->implode(', ') : null;
    }

    public static function canonicalForData(array $data): string
    {
        $slug = collect($data)
            ->map(fn (mixed $value, string $key): string => Str::slug((string) $key, ' ').' '.Str::slug((string) $value, ' '))
            ->implode(' ');

        return Str::slug($slug);
    }

    public function decodeData(): ?array
    {
        $name = $this->display_name;

        if (! is_string($name) || ! str_starts_with($name, '{')) {
            return null;
        }

        $data = json_decode($name, true);

        return is_array($data) && $data !== [] ? $data : null;
    }

    private static function humanize(string $value): string
    {
        return ucfirst(str_replace('_', ' ', $value));
    }

    private static function negate(string $label): string
    {
        return 'not '.strtolower($label);
    }

    public function clinicalSigns(): HasMany
    {
        return $this->hasMany(ClinicalSign::class);
    }
}
