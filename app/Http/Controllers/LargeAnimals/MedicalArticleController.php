<?php

namespace App\Http\Controllers\LargeAnimals;

use App\Http\Controllers\Controller;
use App\Models\LargeAnimals\MedicalArticle;

class MedicalArticleController extends Controller
{
    public function index()
    {
        $articles = MedicalArticle::query()
            ->where('is_published', true)
            ->latest()
            ->paginate(12);

        return view('large-animals.medical-articles.index', compact('articles'));
    }

    public function show(string $slug)
    {
        $article = MedicalArticle::query()
            ->where('slug', $slug)
            ->where('is_published', true)
            ->with('disease')
            ->firstOrFail();

        $tableOfContents = $this->extractTableOfContents($article->content);

        return view('large-animals.medical-articles.show', compact('article', 'tableOfContents'));
    }

    protected function extractTableOfContents(?string $content): array
    {
        preg_match_all('/<h2.*?>(.*?)<\/h2>/i', $content ?? '', $matches);

        return $matches[1] ?? [];
    }
}
