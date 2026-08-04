<?php

namespace App\Services\Drugs;

use App\Models\Disease;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class DiagnosisService
{
    public function rank(string $hostSpeciesId, array $clinicalSignIds): Collection
    {
        $diseases = Disease::query()
            ->where('is_active', true)
            ->whereHas('hostSpecies', function (Builder $query) use ($hostSpeciesId): void {
                $query->whereKey($hostSpeciesId)
                    ->where(function (Builder $query): void {
                        $query->where('is_primary_host', true)
                            ->orWhereIn('susceptibility', ['high', 'moderate']);
                    });
            })
            ->with(['clinicalSigns', 'hostSpecies'])
            ->get();

        $results = $diseases->map(function (Disease $disease) use ($clinicalSignIds): ?array {
            $totalWeight = max(1, $disease->clinicalSigns->sum(fn ($sign): int => max(1, (int) $sign->pivot->weight)));
            $matchedSigns = $disease->clinicalSigns->filter(fn ($sign): bool => in_array($sign->id, $clinicalSignIds));

            if ($matchedSigns->isEmpty()) {
                return null;
            }

            $matchedWeight = $matchedSigns->sum(fn ($sign): int => max(1, (int) $sign->pivot->weight));
            $evidenceScore = $matchedSigns->sum(function ($sign): int {
                $pivot = $sign->pivot;

                return max(1, (int) $pivot->weight)
                    + ($pivot->is_pathognomonic ? 50 : 0)
                    + ($pivot->is_required ? 20 : 0)
                    + ($pivot->is_specific ? 10 : 0);
            });

            return [
                'disease' => $disease,
                'evidence_score' => $evidenceScore,
                'match_score' => (int) round(($matchedWeight / $totalWeight) * 100),
                'matched_signs' => $matchedSigns->pluck('localized_display_name')->values()->all(),
                'missing_key_findings' => $disease->clinicalSigns
                    ->reject(fn ($sign): bool => in_array($sign->id, $clinicalSignIds))
                    ->filter(fn ($sign): bool => $sign->pivot->is_pathognomonic
                        || $sign->pivot->is_required
                        || ($sign->pivot->is_specific && $sign->pivot->weight >= 7))
                    ->sortByDesc(fn ($sign): int => ($sign->pivot->is_pathognomonic ? 100 : 0)
                        + ($sign->pivot->is_required ? 50 : 0)
                        + ($sign->pivot->is_specific ? 20 : 0)
                        + $sign->pivot->weight)
                    ->pluck('localized_display_name')
                    ->unique()
                    ->take(5)
                    ->values()
                    ->all(),
            ];
        })->filter()->values();

        $highestScore = max(1, (int) $results->max('evidence_score'));

        return $results->map(function (array $result) use ($highestScore): array {
            $result['probability'] = (int) round(($result['evidence_score'] / $highestScore) * 100);

            return $result;
        })->sortByDesc('probability')->values();
    }

    public function refinementSigns(Collection $results, array $clinicalSignIds): Collection
    {
        return $results->take(3)
            ->flatMap(fn (array $result) => $result['disease']->clinicalSigns)
            ->reject(fn ($sign): bool => in_array($sign->id, $clinicalSignIds))
            ->filter(fn ($sign): bool => $sign->pivot->is_pathognomonic
                || $sign->pivot->is_required
                || ($sign->pivot->is_specific && $sign->pivot->weight >= 7))
            ->sortByDesc(fn ($sign): int => ($sign->pivot->is_pathognomonic ? 100 : 0)
                + ($sign->pivot->is_required ? 50 : 0)
                + ($sign->pivot->is_specific ? 20 : 0)
                + $sign->pivot->weight)
            ->unique('id')
            ->values();
    }

    public function groups(Collection $results): array
    {
        return [
            'primaryResults' => $results->filter(fn (array $result): bool => $result['probability'] >= 50)->take(5)->values(),
            'secondaryResults' => $results->filter(fn (array $result): bool => $result['probability'] >= 30 && $result['probability'] < 50)->take(5)->values(),
            'lowSupportResults' => $results->filter(fn (array $result): bool => $result['probability'] < 30)->take(5)->values(),
        ];
    }
}
