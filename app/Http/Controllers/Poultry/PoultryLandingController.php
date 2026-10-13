<?php

namespace App\Http\Controllers\Poultry;

use App\Http\Controllers\Controller;
use App\Models\Disease;
use App\Models\LargeAnimals\Finding;
use App\Models\LargeAnimals\HostSpecies;
use Illuminate\Database\Eloquent\Builder;

class PoultryLandingController extends Controller
{
    public function __invoke()
    {
        return view('poultry.landing', [
            'stats' => [
                'diseases' => Disease::query()
                    ->poultry()
                    ->where('is_active', true)
                    ->count(),

                // Findings and lesions are counted through the disease links
                // rather than straight off the table. A bare Finding::count()
                // would include large-animal-only findings, because the two
                // sections share one table and some findings belong to both.
                'findings' => Finding::query()
                    ->whereHas('clinicalSigns.diseases', fn (Builder $diseases): Builder => $diseases
                        ->where('domain', Disease::DOMAIN_POULTRY))
                    ->count(),

                'lesions' => Finding::query()
                    ->where('ontology_type', 'lesion')
                    ->whereHas('clinicalSigns.diseases', fn (Builder $diseases): Builder => $diseases
                        ->where('domain', Disease::DOMAIN_POULTRY))
                    ->count(),

                // Counted by taxonomy rather than by disease link: a handful of
                // cross-host diseases are legitimately linked to cattle, sheep
                // and pigs too, which would inflate this as a bird count.
                'species' => HostSpecies::query()
                    ->where('taxonomy_group', 'poultry')
                    ->count(),
            ],
        ]);
    }
}
