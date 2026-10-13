<?php

namespace App\Http\Controllers\Poultry;

use App\Http\Controllers\Controller;
use App\Http\Requests\Poultry\FilterRequest;
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

/**
 * The poultry half of the clinical findings filter — the mirror image of the
 * diagnosis tool: given observed signs, what could be causing them?
 *
 * @see \App\Http\Controllers\LargeAnimals\FilterController
 */
class FilterController extends Controller
{
    private const PART = 'poultry';

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
            'initialSignTokens' => $this->initialSignTokens($request),
        ]);
    }

    /**
     * The signs to show already picked, in the shape the token picker holds them.
     *
     * Reached by clicking a clinical sign in search. The picker is Alpine state,
     * so a pre-selected sign has to enter as a token or it would render without
     * a chip and without the hidden input behind it — present on screen, absent
     * from the submission.
     *
     * `localized_display_name` rather than the raw column, so the chip reads the
     * same name the search result the user just clicked showed them.
     *
     * @return array<int, array{id: int, title: string}>
     */
    private function initialSignTokens(Request $request): array
    {
        $sign = $this->prefill->sign(self::PART, $request->query('sign'));

        return $sign === null
            ? []
            : [['id' => $sign->id, 'title' => $sign->localized_display_name]];
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
                'redirect' => route('poultry.filter.results', [
                    'criteria' => $this->filterShareService->create($criteria, $this->domain()),
                ]),
            ]);
        }

        return redirect()->route('poultry.filter.results', [
            'criteria' => $this->filterShareService->create($criteria, $this->domain()),
        ]);
    }

    public function showResults(Request $request)
    {
        $criteria = $this->filterShareService->read((string) $request->query('criteria'), $this->domain());

        if (! $criteria) {
            return redirect()->route('poultry.filter')
                ->with('warning', __('poultry.filter.invalid_link'));
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
        return (string) config('vetpedia.parts.poultry.domain');
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
                ->forDomain(Disease::DOMAIN_POULTRY))
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
     * Clinical signs carry no domain column, so poultry signs are the ones
     * reachable through at least one poultry disease.
     */
    private function signQuery(): Builder
    {
        return ClinicalSign::query()
            ->whereHas('diseases', fn (Builder $diseases): Builder => $diseases
                ->where('domain', Disease::DOMAIN_POULTRY));
    }
}
