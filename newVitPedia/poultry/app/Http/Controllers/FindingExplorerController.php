<?php

namespace App\Http\Controllers;

use App\Models\Finding;

class FindingExplorerController extends Controller
{
    public function show(string $slug)
    {
        $finding = Finding::with([

            'diseases.species',
            'diseases.lesions',
            'diseases.diagnosticTests',
            'media'

        ])
            ->where('slug', $slug)
            ->firstOrFail();

        $relatedFindings =
            $this->getRelatedFindings(
                $finding
            );

        return view(
            'findings.show',
            compact(
                'finding',
                'relatedFindings'
            )
        );
    }

    protected function getRelatedFindings(
        Finding $finding
    ) {
        $related = collect();

        foreach ($finding->diseases as $disease) {

            $disease->loadMissing('findings');

            foreach ($disease->findings as $f) {

                if ($f->id === $finding->id) {
                    continue;
                }

                $related->push($f);
            }
        }

        return $related
            ->unique('id')
            ->take(20)
            ->values();
    }
}
