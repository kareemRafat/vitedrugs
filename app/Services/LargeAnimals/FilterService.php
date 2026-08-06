<?php

namespace App\Services\LargeAnimals;

use App\Models\Disease;
use App\Models\LargeAnimals\BodySystem;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class FilterService
{
    public function filter(array $criteria): Collection
    {
        $query = Disease::query()
            ->where('is_active', true)
            ->with([
                'clinicalSigns.anatomicalStructure.bodySystem',
                'hostSpecies',
                'diseaseClassification',
                'microorganisms',
            ]);

        if (! empty($criteria['host_species_id'])) {
            $query->whereHas('hostSpecies', function (Builder $builder) use ($criteria): void {
                $builder->whereKey($criteria['host_species_id'])
                    ->where(function (Builder $builder): void {
                        $builder->where('is_primary_host', true)
                            ->orWhereIn('susceptibility', ['high', 'moderate']);
                    });
            });
        }

        if (! empty($criteria['etiology_type'])) {
            $query->whereHas('diseaseClassification', function (Builder $builder) use ($criteria): void {
                $builder->where('etiology_type', $criteria['etiology_type']);
            });
        }

        if (! empty($criteria['zoonotic'])) {
            $query->whereHas('diseaseClassification', function (Builder $builder): void {
                $builder->where('zoonotic', true);
            });
        }

        if (! empty($criteria['body_system_id'])) {
            $structureIds = BodySystem::query()
                ->findOrFail($criteria['body_system_id'])
                ->anatomicalStructures()
                ->pluck('id');

            if ($structureIds->isNotEmpty()) {
                $query->whereHas('clinicalSigns', function (Builder $builder) use ($structureIds): void {
                    $builder->whereIn('clinical_signs.anatomical_structure_id', $structureIds);
                });
            }
        }

        $clinicalSignIds = $criteria['clinical_signs'] ?? [];

        if ($clinicalSignIds !== []) {
            $query->whereHas('clinicalSigns', function (Builder $builder) use ($clinicalSignIds): void {
                $builder->whereIn('clinical_signs.id', $clinicalSignIds);
            });
        }

        return $query->get()
            ->map(function (Disease $disease) use ($clinicalSignIds): array {
                $matched = $disease->clinicalSigns
                    ->filter(fn ($sign): bool => in_array($sign->id, $clinicalSignIds))
                    ->pluck('localized_display_name')
                    ->values();

                return [
                    'disease' => $disease,
                    'matched_signs' => $matched,
                    'match_count' => $matched->count(),
                ];
            })
            ->sortBy(fn (array $result): string => $result['disease']->name)
            ->sortByDesc('match_count')
            ->values();
    }
}
