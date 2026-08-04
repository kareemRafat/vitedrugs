<?php

namespace App\Http\Controllers\Drugs;

use App\Http\Controllers\Controller;
use App\Models\Drugs\BodySystem;
use App\Models\Drugs\ClinicalSign;
use App\Models\Drugs\DiseaseClassification;
use App\Models\Drugs\HostSpecies;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FilterController extends Controller
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

        return view('drugs.filter.index', compact(
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
}
