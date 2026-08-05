<?php

namespace App\Http\Controllers\LargeAnimals;

use App\Http\Controllers\Controller;
use App\Http\Requests\LargeAnimals\InitialDiagnosisRequest;
use App\Http\Requests\LargeAnimals\RefinedDiagnosisRequest;
use App\Models\LargeAnimals\ClinicalSign;
use App\Models\LargeAnimals\HostSpecies;
use App\Services\LargeAnimals\DiagnosisService;
use App\Services\LargeAnimals\DiagnosisShareService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DiagnosticController extends Controller
{
    public function __construct(
        private DiagnosisService $diagnosisService,
        private DiagnosisShareService $diagnosisShareService,
    ) {}

    public function index()
    {
        $hostSpecies = HostSpecies::query()
            ->where('is_domestic', true)
            ->orderBy('display_name')
            ->get();

        return view('large-animals.diagnosis.index', compact('hostSpecies'));
    }

    public function suggestions(Request $request): JsonResponse
    {
        $query = trim((string) $request->string('q'));

        if (mb_strlen($query) < 1) {
            return response()->json([]);
        }

        $isArabicQuery = (bool) preg_match('/\p{Arabic}/u', $query);

        $signs = ClinicalSign::query()
            ->where(function ($builder) use ($query): void {
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

    private function suggestionLabel(ClinicalSign $sign, bool $isArabicQuery): string
    {
        return $isArabicQuery && $sign->display_name_ar
            ? "{$sign->display_name_ar} ({$sign->display_name})"
            : $sign->display_name;
    }

    public function diagnose(InitialDiagnosisRequest $request)
    {
        $data = $request->validated();
        $results = $this->diagnosisService->rank($data['host_species_id'], $data['clinical_signs']);
        $refinementSignsPool = $this->diagnosisService->refinementSigns($results, $data['clinical_signs']);

        return view('large-animals.diagnosis.refinement', [
            'selectedSigns' => $data['clinical_signs'],
            'hostSpeciesId' => $data['host_species_id'],
            'refinementSignsPool' => $refinementSignsPool,
        ]);
    }

    public function refinedResults(RefinedDiagnosisRequest $request): RedirectResponse
    {
        $data = $request->validated();

        return redirect()->route('large-animals.diagnosis.results', [
            'assessment' => $this->diagnosisShareService->create(
                $data['host_species_id'],
                $data['clinical_signs'],
                $data['refinement_signs'] ?? [],
            ),
        ]);
    }

    public function showResults(Request $request)
    {
        $assessment = $this->diagnosisShareService->read((string) $request->query('assessment'));

        if (! $assessment) {
            return redirect()->route('large-animals.diagnosis')
                ->with('warning', __('large-animals.diagnosis.invalid_share_link'));
        }

        $allSigns = array_values(array_unique(array_merge(
            $assessment['clinical_signs'],
            $assessment['refinement_signs'],
        )));

        $groups = $this->diagnosisService->groups(
            $this->diagnosisService->rank($assessment['host_species_id'], $allSigns),
        );

        return view('large-animals.diagnosis.results', $groups);
    }
}
