<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Disease;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Diagnostic\Comparator\DiseaseComparatorService;

class DiseaseComparisonController extends Controller
{
    public function __construct(
        protected DiseaseComparatorService $comparator
    ) {
    }

    public function compare(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'disease_a' => [
                'required',
                'string',
                'exists:diseases,id',
                'different:disease_b',
            ],

            'disease_b' => [
                'required',
                'string',
                'exists:diseases,id',
            ],
        ], [
            'disease_a.different' =>
                'Please select two different diseases for comparison.',
        ]);

        $diseases = Disease::findMany([
            $validated['disease_a'],
            $validated['disease_b'],
        ]);

        if ($diseases->count() !== 2) {

            return response()->json([
                'message' => 'One or both diseases could not be found.'
            ], 404);
        }

        $diseaseA = $diseases->firstWhere(
            'id',
            $validated['disease_a']
        );

        $diseaseB = $diseases->firstWhere(
            'id',
            $validated['disease_b']
        );

        return response()->json(
            $this->comparator->compare(
                $diseaseA,
                $diseaseB
            )
        );
    }
}