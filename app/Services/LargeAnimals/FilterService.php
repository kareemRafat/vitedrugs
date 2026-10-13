<?php

namespace App\Services\LargeAnimals;

use App\Models\Disease;
use App\Models\LargeAnimals\BodySystem;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class FilterService
{
    public function filter(array $criteria, string $domain): Collection
    {
        $query = Disease::query()
            ->where('is_active', true)
            ->forDomain($domain)
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
                ->find($criteria['body_system_id'])
                ?->anatomicalStructures()
                ->pluck('id');

            // A system with no structures matched nothing and used to fall
            // through to the unfiltered result set, so picking "Systemic" from
            // the dropdown returned every disease in the part. "No structures"
            // means no diseases; it must not mean "ignore the filter".
            //
            // `find` rather than `findOrFail` because the id arrives inside an
            // encrypted share token. A token minted before the duplicate body
            // systems were merged can carry an id that no longer exists, and a
            // dead link should degrade to an empty result rather than a 500.
            if ($structureIds === null || $structureIds->isEmpty()) {
                return collect();
            }

            $query->whereHas('clinicalSigns', function (Builder $builder) use ($structureIds): void {
                $builder->whereIn('clinical_signs.anatomical_structure_id', $structureIds);
            });
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
