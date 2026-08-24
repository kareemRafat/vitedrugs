<?php

namespace App\Http\Controllers\LargeAnimals;

use App\Http\Controllers\Controller;
use App\Http\Requests\LargeAnimals\FilterRequest;
use App\Models\LargeAnimals\BodySystem;
use App\Models\LargeAnimals\ClinicalSign;
use App\Models\LargeAnimals\DiseaseClassification;
use App\Models\LargeAnimals\HostSpecies;
use App\Services\LargeAnimals\FilterService;
use App\Services\LargeAnimals\FilterShareService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class FilterController extends Controller
{
    public function __construct(
        private FilterService $filterService,
        private FilterShareService $filterShareService,
    ) {}

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

    public function index()
    {
        $species = HostSpecies::query()
            ->whereIn('display_name', self::PREFERRED_SPECIES_ORDER)
            ->get()
            ->sortBy(fn ($species) => array_search($species->display_name, self::PREFERRED_SPECIES_ORDER))
            ->values();

        $etiologies = DiseaseClassification::query()
            ->select('etiology_type')
            ->whereNotNull('etiology_type')
            ->groupBy('etiology_type')
            ->orderBy('etiology_type')
            ->get();

        $bodySystems = BodySystem::query()
            ->orderBy('display_name')
            ->get();

        return view('large-animals.filter.index', compact(
            'species',
            'etiologies',
            'bodySystems'
        ));
    }

    public function suggestions(Request $request): JsonResponse
    {
        $query = trim((string) $request->query('q', ''));

        if (mb_strlen($query) < 2) {
            return response()->json(['data' => []]);
        }

        $clinicalSigns = ClinicalSign::query()
            ->whereRaw('LOWER(display_name) LIKE ?', ['%'.mb_strtolower($query).'%'])
            ->limit(10)
            ->get()
            ->map(fn ($item) => [
                'id' => 'clinical_sign_'.$item->id,
                'title' => $item->display_name,
                'type' => 'Clinical Sign',
                'entity_type' => 'clinical_sign',
                'real_id' => $item->id,
            ]);

        return response()->json(['data' => $clinicalSigns->values()]);
    }

    public function store(FilterRequest $request): RedirectResponse|JsonResponse
    {
        $criteria = $request->validated();

        if ($request->wantsJson()) {
            return response()->json([
                'redirect' => route('large-animals.filter.results', [
                    'criteria' => $this->filterShareService->create($criteria),
                ]),
            ]);
        }

        return redirect()->route('large-animals.filter.results', [
            'criteria' => $this->filterShareService->create($criteria),
        ]);
    }

    public function showResults(Request $request)
    {
        $criteria = $this->filterShareService->read((string) $request->query('criteria'));

        if (! $criteria) {
            return redirect()->route('large-animals.filter')
                ->with('warning', __('large-animals.filter.invalid_link'));
        }

        $species = HostSpecies::query()
            ->whereIn('display_name', self::PREFERRED_SPECIES_ORDER)
            ->get()
            ->sortBy(fn ($species) => array_search($species->display_name, self::PREFERRED_SPECIES_ORDER))
            ->values();

        $bodySystems = BodySystem::query()
            ->orderBy('display_name')
            ->get();

        $clinicalSigns = ClinicalSign::query()
            ->whereIn('id', $criteria['clinical_signs'])
            ->get();

        $diseases = $this->filterService->filter($criteria);

        return view('large-animals.filter.results', compact(
            'criteria',
            'species',
            'bodySystems',
            'clinicalSigns',
            'diseases'
        ));
    }
}
