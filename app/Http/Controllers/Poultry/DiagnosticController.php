<?php

namespace App\Http\Controllers\Poultry;

use App\Http\Controllers\Controller;
use App\Http\Requests\Poultry\InitialDiagnosisRequest;
use App\Http\Requests\Poultry\RefinedDiagnosisRequest;
use App\Models\Disease;
use App\Models\LargeAnimals\ClinicalSign;
use App\Models\LargeAnimals\HostSpecies;
use App\Services\LargeAnimals\DiagnosisService;
use App\Services\LargeAnimals\DiagnosisShareService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * The poultry half of the differential diagnosis tool.
 *
 * Deliberately mirrors `LargeAnimals\DiagnosticController` — same three steps,
 * same scoring, same share mechanism — with two differences that matter:
 * the species list and the sign suggestions are restricted to poultry, and every
 * result is filtered to `diseases.domain = poultry`.
 *
 * @see \App\Http\Controllers\LargeAnimals\DiagnosticController
 */
class DiagnosticController extends Controller
{
    private const PART = 'poultry';

    public function __construct(
        private DiagnosisService $diagnosisService,
        private DiagnosisShareService $diagnosisShareService,
    ) {}

    public function index()
    {
        $hostSpecies = HostSpecies::query()
            ->forPart(self::PART)
            ->orderBy('display_name')
            ->get();

        return view('diagnosis.index', [
            'part' => self::PART,
            'hostSpecies' => $hostSpecies,
        ]);
    }

    public function suggestions(Request $request): JsonResponse
    {
        $query = trim((string) $request->string('q'));

        if (mb_strlen($query) < 1) {
            return response()->json([]);
        }

        $isArabicQuery = (bool) preg_match('/\p{Arabic}/u', $query);

        $signs = $this->signQuery()
            ->where(function (Builder $builder) use ($query): void {
                $builder->where('display_name', 'like', "%{$query}%")
                    ->orWhere('display_name_ar', 'like', "%{$query}%")
                    ->orWhere('canonical_name', 'like', "%{$query}%");
            })
            ->orderBy('display_name')
            ->limit(10)
            ->get()
            ->map(fn (ClinicalSign $sign): array => [
                'id' => $sign->id,
                'name' => $sign->localized_display_name,
                'label' => $this->suggestionLabel($sign, $isArabicQuery),
                'canonical_name' => $sign->canonical_name,
            ]);

        return response()->json($signs);
    }

    public function diagnose(InitialDiagnosisRequest $request)
    {
        $data = $request->validated();
        $results = $this->diagnosisService->rank($data['host_species_id'], $data['clinical_signs'], $this->domain());
        $refinementSignsPool = $this->diagnosisService->refinementSigns($results, $data['clinical_signs']);

        return view('diagnosis.refinement', [
            'part' => self::PART,
            'selectedSigns' => $data['clinical_signs'],
            'hostSpeciesId' => $data['host_species_id'],
            'refinementSignsPool' => $refinementSignsPool,
        ]);
    }

    public function refinedResults(RefinedDiagnosisRequest $request): RedirectResponse
    {
        $data = $request->validated();

        return redirect()->route('poultry.diagnosis.results', [
            'assessment' => $this->diagnosisShareService->create(
                $this->domain(),
                $data['host_species_id'],
                $data['clinical_signs'],
                $data['refinement_signs'] ?? [],
            ),
        ]);
    }

    public function showResults(Request $request)
    {
        $assessment = $this->diagnosisShareService->read((string) $request->query('assessment'), $this->domain());

        if (! $assessment) {
            return redirect()->route('poultry.diagnosis')
                ->with('warning', __('poultry.diagnosis.invalid_share_link'));
        }

        $allSigns = array_values(array_unique(array_merge(
            $assessment['clinical_signs'],
            $assessment['refinement_signs'],
        )));

        $groups = $this->diagnosisService->groups(
            $this->diagnosisService->rank($assessment['host_species_id'], $allSigns, $this->domain()),
        );

        return view('diagnosis.results', [
            'part' => self::PART,
            ...$groups,
        ]);
    }

    private function domain(): string
    {
        return (string) config('vetpedia.parts.poultry.domain');
    }

    /**
     * Clinical signs carry no domain column, so poultry signs are the ones
     * reachable through at least one poultry disease.
     */
    private function signQuery(): Builder
    {
        return ClinicalSign::query()
            ->whereHas('diseases', fn (Builder $diseases): Builder => $diseases
                ->where('domain', Disease::DOMAIN_POULTRY));
    }

    private function suggestionLabel(ClinicalSign $sign, bool $isArabicQuery): string
    {
        return $isArabicQuery && $sign->display_name_ar
            ? "{$sign->display_name_ar} ({$sign->display_name})"
            : $sign->display_name;
    }
}
