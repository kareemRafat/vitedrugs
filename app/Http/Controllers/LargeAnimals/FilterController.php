<?php

namespace App\Http\Controllers\LargeAnimals;

use App\Http\Controllers\Controller;
use App\Http\Requests\LargeAnimals\FilterRequest;
use App\Models\Disease;
use App\Models\LargeAnimals\BodySystem;
use App\Models\LargeAnimals\ClinicalSign;
use App\Models\LargeAnimals\DiseaseClassification;
use App\Models\LargeAnimals\HostSpecies;
use App\Services\Knowledge\ToolPrefillService;
use App\Services\LargeAnimals\FilterService;
use App\Services\LargeAnimals\FilterShareService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class FilterController extends Controller
{
    private const PART = 'large-animals';

    public function __construct(
        private FilterService $filterService,
        private FilterShareService $filterShareService,
        private ToolPrefillService $prefill,
    ) {}

    public function index(Request $request)
    {
        return view('filter.index', [
            'part' => self::PART,
            'species' => $this->species(),
            'etiologies' => $this->etiologies(),
            'bodySystems' => $this->bodySystems(),
            'initialSpeciesId' => $this->prefill->species(self::PART, $request->query('host_species_id'))?->id,
        ]);
    }

    public function suggestions(Request $request): JsonResponse
    {
        $query = trim((string) $request->query('q', ''));

        if (mb_strlen($query) < 2) {
            return response()->json(['data' => []]);
        }

        $clinicalSigns = $this->signQuery()
            ->whereRaw('LOWER(display_name) LIKE ?', ['%'.mb_strtolower($query).'%'])
            ->limit(10)
            ->get()
            ->map(fn (ClinicalSign $item): array => [
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
                    'criteria' => $this->filterShareService->create($criteria, $this->domain()),
                ]),
            ]);
        }

        return redirect()->route('large-animals.filter.results', [
            'criteria' => $this->filterShareService->create($criteria, $this->domain()),
        ]);
    }

    public function showResults(Request $request)
    {
        $criteria = $this->filterShareService->read((string) $request->query('criteria'), $this->domain());

        if (! $criteria) {
            return redirect()->route('large-animals.filter')
                ->with('warning', __('large-animals.filter.invalid_link'));
        }

        $clinicalSigns = ClinicalSign::query()
            ->whereIn('id', $criteria['clinical_signs'])
            ->get();

        return view('filter.results', [
            'part' => self::PART,
            'criteria' => $criteria,
            'species' => $this->species(),
            'bodySystems' => $this->bodySystems(),
            'clinicalSigns' => $clinicalSigns,
            'diseases' => $this->filterService->filter($criteria, $this->domain()),
        ]);
    }

    private function domain(): string
    {
        return (string) config('vetpedia.parts.large-animals.domain');
    }

    private function species()
    {
        return HostSpecies::query()
            ->forPart(self::PART)
            ->orderBy('display_name')
            ->get();
    }

    private function etiologies()
    {
        return DiseaseClassification::query()
            ->select('etiology_type')
            ->whereNotNull('etiology_type')
            ->whereHas('disease', fn (Builder $disease): Builder => $disease
                ->forDomain(Disease::DOMAIN_LARGE_ANIMAL))
            ->groupBy('etiology_type')
            ->orderBy('etiology_type')
            ->get();
    }

    private function bodySystems()
    {
        return BodySystem::query()
            ->orderBy('display_name')
            ->get();
    }

    /**
     * Clinical signs carry no domain column, so a sign belongs to this part when
     * it is reachable through at least one disease in it.
     */
    private function signQuery(): Builder
    {
        return ClinicalSign::query()
            ->whereHas('diseases', fn (Builder $diseases): Builder => $diseases
                ->where('domain', Disease::DOMAIN_LARGE_ANIMAL));
    }
}
