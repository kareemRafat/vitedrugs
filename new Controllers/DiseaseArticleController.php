<?php

namespace App\Http\Controllers;

use App\Models\Disease;
use App\Services\Narrative\DiseaseArticleBuilder;

class DiseaseArticleController extends Controller
{
    public function show(
        string $slug,
        DiseaseArticleBuilder $builder
    ) {
        $disease = Disease::where('slug', $slug)->firstOrFail();

        $article = $builder->build($disease);

        return view('medical.article', compact('article', 'disease'));
    }



    public function search()
    {
        $disease = Disease::where('name_en', 'like', '%' . request('q') . '%')
            ->orWhere('name_ar', 'like', '%' . request('q') . '%')
            ->orWhere('slug', 'like', '%' . request('q') . '%')
            ->first();

        abort_if(! $disease, 404);

        return redirect()->route('disease.show', $disease->slug);
    }
}
