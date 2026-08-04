<?php

namespace App\Http\Controllers\Drugs;

use App\Http\Controllers\Controller;
use App\Http\Requests\Drugs\InitialDiagnosisRequest;
use App\Http\Requests\Drugs\RefinedDiagnosisRequest;
use App\Models\Drugs\ClinicalSign;
use App\Models\Drugs\HostSpecies;
use App\Services\Drugs\DiagnosisService;
use App\Services\Drugs\DiagnosisShareService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

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

        return view('drugs.diagnosis.index', compact('hostSpecies'));
    }

    public function suggestions(Request $request): JsonResponse
    {
        $query = trim((string) $request->string('q'));

        if (mb_strlen($query) < 1) {
            return response()->json([]);
        }

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
                'canonical_name' => $sign->canonical_name,
            ]);

        return response()->json($signs);
    }

    public function diagnose(InitialDiagnosisRequest $request)
    {
        $data = $request->validated();
        $results = $this->diagnosisService->rank($data['host_species_id'], $data['clinical_signs']);
        $refinementSignsPool = $this->diagnosisService->refinementSigns($results, $data['clinical_signs']);

        return view('drugs.diagnosis.refinement', [
            'selectedSigns' => $data['clinical_signs'],
            'hostSpeciesId' => $data['host_species_id'],
            'refinementSignsPool' => $refinementSignsPool,
        ]);
    }

    public function refinedResults(RefinedDiagnosisRequest $request): RedirectResponse
    {
        $data = $request->validated();

        return redirect()->route('drugs.diagnosis.results', [
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
            return redirect()->route('drugs.diagnosis')
                ->with('warning', __('drugs.diagnosis.invalid_share_link'));
        }

        $allSigns = array_values(array_unique(array_merge(
            $assessment['clinical_signs'],
            $assessment['refinement_signs'],
        )));

        $groups = $this->diagnosisService->groups(
            $this->diagnosisService->rank($assessment['host_species_id'], $allSigns),
        );

        return view('drugs.diagnosis.results', $groups);
    }
}
