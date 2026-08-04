<?php

namespace App\Http\Controllers\Drugs;

use App\Http\Controllers\Controller;
use App\Models\Disease;
use App\Models\Drugs\ClinicalSign;
use App\Models\Drugs\DifferentialSyndrome;
use App\Models\Drugs\HostSpecies;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class DiagnosticController extends Controller
{
    protected const PREFERRED_SPECIES_ORDER = [
        'Cattle',
        'Buffalo',
        'Sheep',
        'Goat',
        'Camel',
        'Equine',
        'Dogs',
        'Cats',
        'Poultry',
        'Turkey',
        'Calf',
    ];

    protected const GENERAL_SIGNS = [
        'anorexia',
        'pyrexia',
        'fever',
        'depression',
        'weakness',
        'weight loss',
        'dehydration',
        'lethargy',
        'inappetence',
        'emaciation',
    ];

    public function index()
    {
        $hostSpecies = HostSpecies::query()
            ->whereIn('display_name', self::PREFERRED_SPECIES_ORDER)
            ->get()
            ->sortBy(fn ($species) => array_search($species->display_name, self::PREFERRED_SPECIES_ORDER))
            ->values();

        $allSigns = ClinicalSign::query()
            ->with([
                'anatomicalStructure.bodySystem',
                'finding',
            ])
            ->orderBy('display_name')
            ->get()
            ->map(fn ($sign) => [
                'id' => $sign->id,
                'display_name' => $sign->display_name,
                'canonical_name' => $sign->canonical_name,
                'body_system' => $sign->anatomicalStructure?->bodySystem?->display_name ?? 'General Systemic Signs',
                'clinical_category' => $sign->finding?->category ?? 'General',
            ]);

        return view('drugs.diagnosis.index', compact('hostSpecies', 'allSigns'));
    }

    public function diagnose(Request $request)
    {
        $selectedSigns = $request->input('clinical_signs', []);
        $hostSpeciesId = $request->input('host_species_id');

        if (empty($selectedSigns)) {
            return redirect()
                ->back()
                ->with('warning', 'Please select at least one abnormal clinical finding before running differential diagnosis.');
        }

        $selectedClinicalSigns = ClinicalSign::query()
            ->with('anatomicalStructure.bodySystem')
            ->whereIn('id', $selectedSigns)
            ->get();

        $selectedSystemCounts = [];

        foreach ($selectedClinicalSigns as $selectedSign) {
            $system = $selectedSign->anatomicalStructure?->bodySystem?->display_name;

            if ($system) {
                $selectedSystemCounts[$system] = ($selectedSystemCounts[$system] ?? 0) + 1;
            }
        }

        $diseases = Disease::query()->with([
            'clinicalSigns.anatomicalStructure.bodySystem',
            'hostSpecies',
        ]);

        if ($hostSpeciesId) {
            $this->scopeToPrimaryHosts($diseases, $hostSpeciesId);
        }

        $diseases = $diseases->get();

        $differentialSyndromes = DifferentialSyndrome::query()
            ->with([
                'clinicalSigns',
                'diseases',
            ])
            ->get();

        $results = [];

        foreach ($diseases as $disease) {
            $score = $this->speciesBoost($disease, $hostSpeciesId);

            $matchedSigns = [];
            $reasons = [];

            $matchedSpecificSigns = 0;
            $matchedRequiredSigns = 0;
            $matchedHighWeightSigns = 0;
            $matchedPathognomonicSigns = 0;

            $priorityTier = 4;
            $matchedSyndromes = [];

            $confidence = 'Low';
            $matchType = 'Weak General Match';
            $boostedSystems = [];

            foreach ($disease->clinicalSigns as $sign) {
                if (! in_array($sign->id, $selectedSigns)) {
                    continue;
                }

                $pivot = $sign->pivot;
                $weight = $pivot->weight ?? 1;
                $canonicalName = strtolower(trim($sign->canonical_name));

                if ($pivot->is_pathognomonic) {
                    $score += 50;
                    $priorityTier = 1;
                    $reasons[] = 'Pathognomonic clinical sign strongly associated with this disease';
                    $matchedPathognomonicSigns++;
                }

                $isGeneralSign = (bool) ($sign->finding?->is_general_sign)
                    || in_array($canonicalName, self::GENERAL_SIGNS);

                if ($isGeneralSign && ! $pivot->is_pathognomonic) {
                    $score += min($weight, 1);
                    $score -= 2;
                    $score = max($score, 0);
                    $reasons[] = 'General systemic sign with low diagnostic specificity';
                } else {
                    $score += $weight;
                }

                $matchedSigns[strtolower(trim($sign->canonical_name))] = $sign->display_name;

                if ($weight >= 7 && ! $isGeneralSign && ($pivot->is_specific || $pivot->is_required)) {
                    $matchedHighWeightSigns++;
                    $score += 10;
                    $reasons[] = 'High-priority diagnostic sign: '.$sign->display_name;
                }

                if ($pivot->is_specific && ! in_array($canonicalName, self::GENERAL_SIGNS)) {
                    $matchedSpecificSigns++;
                    $score += 15;
                    $reasons[] = 'Specific clinical sign: '.$sign->display_name;
                }

                if ($pivot->is_required && ! in_array($canonicalName, self::GENERAL_SIGNS)) {
                    $matchedRequiredSigns++;
                    $score += 12;
                    $reasons[] = 'Required clinical sign: '.$sign->display_name;
                }

                $system = $sign->anatomicalStructure?->bodySystem?->display_name;

                if ($system && isset($selectedSystemCounts[$system]) && ! in_array($system, $boostedSystems)) {
                    if ($selectedSystemCounts[$system] >= 3) {
                        $score += 8;
                        $boostedSystems[] = $system;
                    } elseif ($selectedSystemCounts[$system] === 2) {
                        $score += 6;
                    } else {
                        $score += 2;
                    }
                }
            }

            foreach ($differentialSyndromes as $syndrome) {
                $syndromeSignIds = $syndrome->clinicalSigns->pluck('id')->toArray();

                $matchedSyndromeSigns = array_intersect($selectedSigns, $syndromeSignIds);

                $requiredMatchCount = max(2, (int) ceil(count($syndromeSignIds) * 0.6));

                if (count($matchedSyndromeSigns) >= $requiredMatchCount) {
                    $syndromeDisease = $syndrome->diseases->firstWhere('id', $disease->id);

                    if ($syndromeDisease) {
                        $boostScore = $syndromeDisease->pivot->boost_score ?? 15;

                        $score += $boostScore;
                        $matchedSyndromes[] = $syndrome->name;
                        $reasons[] = 'Matched differential syndrome: '.$syndrome->name;
                        $priorityTier = min($priorityTier, 2);
                    }
                }
            }

            $requiredSigns = $disease->clinicalSigns->filter(fn ($sign) => (bool) $sign->pivot->is_required);

            foreach ($requiredSigns as $requiredSign) {
                if (! in_array($requiredSign->id, $selectedSigns)) {
                    $score -= 4;
                }
            }

            $matchedCanonicalSigns = collect($disease->clinicalSigns)
                ->filter(fn ($sign) => in_array($sign->id, $selectedSigns))
                ->pluck('canonical_name')
                ->map(fn ($name) => strtolower(trim($name)))
                ->unique()
                ->values();

            $nonGeneralMatches = $matchedCanonicalSigns
                ->reject(fn ($sign) => in_array($sign, self::GENERAL_SIGNS));

            if ($matchedPathognomonicSigns === 0) {
                if (($matchedSpecificSigns >= 1 || $matchedRequiredSigns >= 1 || $matchedHighWeightSigns >= 1)
                    && count($nonGeneralMatches) >= 2) {
                    $priorityTier = 1;
                    $score += 20;
                }

                if (count($nonGeneralMatches) >= 3) {
                    $priorityTier = min($priorityTier, 2);
                    $score += 20;
                    $reasons[] = 'Three or more associated clinical signs matched.';
                } elseif (count($nonGeneralMatches) >= 2) {
                    $priorityTier = min($priorityTier, 3);
                    $score += 10;
                    $reasons[] = 'Two associated clinical signs matched.';
                } elseif (count($nonGeneralMatches) === 1) {
                    $priorityTier = 4;
                    $score += 2;
                }
            }

            $matchedCount = count($nonGeneralMatches);

            if ($matchedSpecificSigns >= 1 && $matchedCount >= 2) {
                $matchType = 'Strong Match';
            } elseif ($matchedCount >= 2) {
                $matchType = 'Moderate Match';
            }

            if ($matchedPathognomonicSigns > 0) {
                $score += 25;
                $confidence = 'High';
                $matchType = 'Pathognomonic Match';
                $priorityTier = 1;
            }

            if ($matchedPathognomonicSigns === 0) {
                if ($matchedCount >= 3 && $matchedSpecificSigns >= 1) {
                    $confidence = 'High';
                } elseif ($matchedCount >= 2) {
                    $confidence = 'Moderate';
                } else {
                    $confidence = 'Low';
                }
            }

            $isWeakGeneralMatch = $matchedCount <= 1
                && $matchedSpecificSigns === 0
                && $matchedRequiredSigns === 0
                && $matchedPathognomonicSigns === 0;

            $score = max($score, 0);

            if (count($matchedSigns) === 0) {
                continue;
            }

            $results[] = [
                'is_weak_general_match' => $isWeakGeneralMatch,
                'matched_specific_signs' => $matchedSpecificSigns,
                'matched_required_signs' => $matchedRequiredSigns,
                'matched_non_general_count' => $matchedCount,
                'disease' => $disease,
                'score' => $score,
                'priority_tier' => $priorityTier,
                'confidence' => $confidence,
                'match_type' => $matchType,
                'matched_signs' => array_values($matchedSigns),
                'reasons' => collect($reasons)->unique()->filter()->values()->toArray(),
                'missing_key_findings' => $this->missingKeyFindings($disease, $selectedSigns),
            ];
        }

        $maxScore = collect($results)->max('score');

        $results = collect($results)->map(function (array $result) use ($maxScore) {
            $relativeMatchStrength = $maxScore > 0 ? round(($result['score'] / $maxScore) * 100) : 0;

            if ($result['is_weak_general_match']) {
                $relativeMatchStrength = min($relativeMatchStrength, 35);
            }

            $matchedSignsCount = count($result['matched_signs']);

            if ($matchedSignsCount >= 3) {
                $relativeMatchStrength = min($relativeMatchStrength, 80);
            } elseif ($matchedSignsCount === 2) {
                $relativeMatchStrength = min($relativeMatchStrength, 65);
            } else {
                $relativeMatchStrength = min($relativeMatchStrength, 40);
            }

            if ($result['match_type'] === 'Pathognomonic Match') {
                $relativeMatchStrength = max($relativeMatchStrength, 95);
            }

            if ($matchedSignsCount === 1 && $result['match_type'] !== 'Pathognomonic Match') {
                $relativeMatchStrength = min($relativeMatchStrength, 45);
            }

            $result['relative_match_strength'] = max($relativeMatchStrength, 1);

            return $result;
        })->toArray();

        usort($results, function (array $a, array $b) {
            if ($a['priority_tier'] !== $b['priority_tier']) {
                return $a['priority_tier'] <=> $b['priority_tier'];
            }

            if ($a['relative_match_strength'] !== $b['relative_match_strength']) {
                return $b['relative_match_strength'] <=> $a['relative_match_strength'];
            }

            return $b['score'] <=> $a['score'];
        });

        $topDiseases = collect($results)->take(5);

        $refinementSignsPool = collect();

        foreach ($topDiseases as $result) {
            $disease = $result['disease'];

            $candidateSigns = $disease->clinicalSigns()
                ->wherePivot('is_pathognomonic', 1)
                ->whereNotIn('clinical_signs.id', $selectedSigns)
                ->get();

            if ($candidateSigns->isEmpty()) {
                $candidateSigns = $disease->clinicalSigns()
                    ->wherePivot('is_required', 1)
                    ->whereNotIn('clinical_signs.id', $selectedSigns)
                    ->orderByPivot('weight', 'desc')
                    ->get();
            }

            if ($candidateSigns->isEmpty()) {
                $candidateSigns = $disease->clinicalSigns()
                    ->whereNotIn('clinical_signs.id', $selectedSigns)
                    ->wherePivot('weight', '>=', 5)
                    ->orderByPivot('weight', 'desc')
                    ->limit(5)
                    ->get()
                    ->reject(fn ($sign) => in_array(strtolower(trim($sign->canonical_name)), self::GENERAL_SIGNS));
            }

            if ($candidateSigns->isEmpty()) {
                continue;
            }

            $refinementSignsPool = $refinementSignsPool->merge($candidateSigns);
        }

        $refinementSignsPool = $refinementSignsPool
            ->unique(fn ($sign) => strtolower(trim($sign->canonical_name)))
            ->values();

        $refinementSignsPool = $refinementSignsPool
            ->sortByDesc(fn ($sign) => $sign->pivot->weight ?? 0)
            ->take(15)
            ->values();

        return view('drugs.diagnosis.refinement', compact('results', 'selectedSigns', 'refinementSignsPool'));
    }

    public function refinedResults(Request $request)
    {
        $selectedSigns = $request->input('clinical_signs', []);
        $refinementSigns = $request->input('refinement_signs', []);
        $hostSpeciesId = $request->input('host_species_id');

        $allSigns = array_unique(array_merge($selectedSigns, $refinementSigns));

        $diseases = Disease::query()->with([
            'clinicalSigns',
            'hostSpecies',
        ]);

        if ($hostSpeciesId) {
            $this->scopeToPrimaryHosts($diseases, $hostSpeciesId);
        }

        $diseases = $diseases->get();

        $results = [];

        foreach ($diseases as $disease) {
            $score = $this->speciesBoost($disease, $hostSpeciesId);

            $matchedSigns = [];
            $reasons = [];

            $matchedPathognomonic = 0;
            $matchedRequired = 0;
            $matchedSpecific = 0;

            foreach ($disease->clinicalSigns as $sign) {
                if (! in_array($sign->id, $allSigns)) {
                    continue;
                }

                $pivot = $sign->pivot;
                $weight = $pivot->weight ?? 1;

                $score += min($weight, 10);

                if (in_array($sign->id, $refinementSigns)) {
                    if ($pivot->is_pathognomonic) {
                        $score += 50;
                        $matchedPathognomonic++;
                        $reasons[] = 'Pathognomonic clinical finding confirmed.';
                    }

                    if ($pivot->is_required) {
                        $score += 30;
                        $matchedRequired++;
                        $reasons[] = 'Core diagnostic feature identified.';
                    }

                    if ($pivot->is_specific) {
                        $score += 20;
                        $matchedSpecific++;
                        $reasons[] = 'Disease-specific clinical evidence identified.';
                    }

                    if ($weight >= 7) {
                        $score += 15;
                    }
                }

                $matchedSigns[strtolower(trim($sign->canonical_name))] = $sign->display_name;
            }

            if (count($matchedSigns) === 0) {
                continue;
            }

            $confidence = 'Low';

            if ($matchedPathognomonic >= 1) {
                $confidence = 'High';
            } elseif ($matchedRequired >= 1) {
                $confidence = 'Moderate';
            }

            $matchType = 'Refined Match';

            if ($matchedPathognomonic >= 1) {
                $matchType = 'Pathognomonic Reinforced Match';
            }

            $results[] = [
                'disease' => $disease,
                'score' => $score,
                'confidence' => $confidence,
                'match_type' => $matchType,
                'matched_signs' => array_values($matchedSigns),
                'reasons' => collect($reasons)->unique()->filter()->values()->toArray(),
                'missing_key_findings' => $this->missingKeyFindings($disease, $allSigns),
            ];
        }

        $maxScore = collect($results)->max('score');

        $results = collect($results)->map(function (array $result) use ($maxScore) {
            $result['relative_match_strength'] = $maxScore > 0
                ? max(1, min(round(($result['score'] / $maxScore) * 100), 100))
                : 0;

            return $result;
        })->toArray();

        usort($results, fn (array $a, array $b) => $b['score'] <=> $a['score']);

        $resultsCollection = collect($results)
            ->sortByDesc(fn ($result) => [
                $result['score'] ?? 0,
                $result['relative_match_strength'] ?? 0,
            ])
            ->values();

        $primaryResults = $resultsCollection->take(5);

        $secondaryResults = $resultsCollection->slice(5, 5)->values();

        $usedDiseaseIds = collect()
            ->merge($primaryResults->pluck('disease.id'))
            ->merge($secondaryResults->pluck('disease.id'))
            ->unique();

        $lowSupportResults = $resultsCollection
            ->reject(fn ($result) => $usedDiseaseIds->contains($result['disease']->id))
            ->sortBy('score')
            ->take(5)
            ->values();

        return view('drugs.diagnosis.results', compact('results', 'primaryResults', 'secondaryResults', 'lowSupportResults'));
    }

    private function scopeToPrimaryHosts(Builder $query, string $hostSpeciesId): Builder
    {
        return $query->whereHas('hostSpecies', function ($query) use ($hostSpeciesId) {
            $query->where('host_species.id', $hostSpeciesId)
                ->where(function ($query) {
                    $query->where('is_primary_host', true)
                        ->orWhere('susceptibility', 'high')
                        ->orWhere('susceptibility', 'moderate');
                });
        });
    }

    private function speciesBoost(Disease $disease, ?string $hostSpeciesId): int
    {
        if (! $hostSpeciesId) {
            return 0;
        }

        $hostRelation = $disease->hostSpecies->firstWhere('id', $hostSpeciesId);

        if (! $hostRelation) {
            return 0;
        }

        $score = 0;

        if ($hostRelation->pivot->is_primary_host) {
            $score += 40;
        }

        if ($hostRelation->pivot->is_reservoir) {
            $score += 10;
        }

        if ($hostRelation->pivot->is_carrier) {
            $score += 10;
        }

        if ($hostRelation->pivot->is_incidental_host) {
            $score -= 20;
        }

        $susceptibility = strtolower($hostRelation->pivot->susceptibility ?? '');

        if (str_contains($susceptibility, 'high')) {
            $score += 20;
        }

        if (str_contains($susceptibility, 'low')) {
            $score -= 10;
        }

        return $score;
    }

    private function missingKeyFindings(Disease $disease, array $selectedSigns): array
    {
        $missingKeyFindings = [];

        foreach ($disease->clinicalSigns as $diseaseSign) {
            if (in_array($diseaseSign->id, $selectedSigns)) {
                continue;
            }

            $pivot = $diseaseSign->pivot;

            if ($pivot->is_pathognomonic || $pivot->is_required || ($pivot->is_specific && $pivot->weight >= 7)) {
                $missingKeyFindings[] = $diseaseSign->display_name;
            }
        }

        return collect($missingKeyFindings)
            ->unique()
            ->take(5)
            ->values()
            ->toArray();
    }
}
