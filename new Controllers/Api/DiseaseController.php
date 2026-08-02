<?php

namespace App\Http\Controllers\Api;

use App\Models\Disease;
use App\Http\Controllers\Controller;
use App\Http\Resources\DiseaseListResource;
use App\Http\Resources\DiseaseArticleResource;
use App\Services\Narrative\DiseaseArticleBuilder;

class DiseaseController extends Controller
{
    public function index()
    {
        return DiseaseListResource::collection(

            Disease::query()

                ->orderBy('name_en')

                ->paginate(20)
        );
    }

    public function show(
        string $slug,
        DiseaseArticleBuilder $builder
    ) {
        $disease = Disease::where(
            'slug',
            $slug
        )->firstOrFail();

        $article = $builder->build(
            $disease
        );

        return DiseaseArticleResource::make(
            $article
        );
    }
}