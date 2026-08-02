<?php

namespace App\Http\Controllers\Api;

use App\Models\MedicalArticle;
use App\Http\Controllers\Controller;
use App\Http\Resources\MedicalArticleResource;
use App\Http\Resources\MedicalArticleListResource;

class MedicalArticleController extends Controller
{
    public function index()
    {
        return MedicalArticleListResource::collection(

            MedicalArticle::query()

                ->where('is_published', true)

                ->latest()

                ->paginate(20)
        );
    }

    public function show(string $slug)
    {
        $article = MedicalArticle::query()

            ->where('slug', $slug)

            ->where('is_published', true)

            ->with('disease')

            ->firstOrFail();

        return MedicalArticleResource::make(
            $article
        );
    }
}