<?php

namespace App\Http\Controllers;

use App\Models\Drug;
use App\Services\Ontology\OntologyLinkService;

class DrugController extends Controller
{
    public function show(
        string $slug,
        OntologyLinkService $linker
    ) {
        $drug = Drug::query()
            ->with([
                'category',
                'therapeuticUses',
            ])
            ->where('slug', $slug)
            ->firstOrFail();

        $drug->description =
            $linker->link($drug->description);

        $drug->mechanism_of_action =
            $linker->link($drug->mechanism_of_action);

        return view(
            'drugs.show',
            compact('drug')
        );
    }


}
