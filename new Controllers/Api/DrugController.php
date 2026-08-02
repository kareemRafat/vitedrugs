<?php

namespace App\Http\Controllers\Api;

use App\Models\Drug;
use App\Http\Controllers\Controller;
use App\Http\Resources\DrugResource;
use App\Http\Resources\DrugListResource;

class DrugController extends Controller
{
    public function index()
    {
        return DrugListResource::collection(

            Drug::query()

                ->orderBy('name_en')

                ->paginate(20)
        );
    }

    public function show(string $slug)
    {
        $drug = Drug::query()

            ->with([
                'category',
                'therapeuticUses',
            ])

            ->where(
                'slug',
                $slug
            )

            ->firstOrFail();

        return DrugResource::make(
            $drug
        );
    }
}