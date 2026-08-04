<?php

namespace App\Http\Controllers\Drugs;

use App\Http\Controllers\Controller;
use App\Models\Disease;
use App\Services\Narrative\DiseaseArticleBuilder;
use Illuminate\Http\Request;

class DiseaseArticleController extends Controller
{
    public function show(string $slug, DiseaseArticleBuilder $builder)
    {
        $disease = Disease::where('slug', $slug)->firstOrFail();

        $article = $builder->build($disease);

        return view('drugs.medical.article', compact('article', 'disease'));
    }

    public function search(Request $request)
    {
        $query = trim((string) $request->query('q', ''));

        $disease = Disease::where('name', 'like', "%{$query}%")
            ->orWhere('name_ar', 'like', "%{$query}%")
            ->orWhere('slug', 'like', "%{$query}%")
            ->first();

        abort_if(! $disease, 404);

        return redirect()->route('drugs.diseases.show', $disease->slug);
    }
}
