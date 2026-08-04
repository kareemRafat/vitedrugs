<?php

namespace App\Http\Controllers\Drugs;

use App\Http\Controllers\Controller;
use App\Models\Drugs\MedicalArticle;

class MedicalArticleController extends Controller
{
    public function index()
    {
        $articles = MedicalArticle::query()
            ->where('is_published', true)
            ->latest()
            ->paginate(12);

        return view('drugs.medical-articles.index', compact('articles'));
    }

    public function show(string $slug)
    {
        $article = MedicalArticle::query()
            ->where('slug', $slug)
            ->where('is_published', true)
            ->with('disease')
            ->firstOrFail();

        $tableOfContents = $this->extractTableOfContents($article->content);

        return view('drugs.medical-articles.show', compact('article', 'tableOfContents'));
    }

    protected function extractTableOfContents(?string $content): array
    {
        preg_match_all('/<h2.*?>(.*?)<\/h2>/i', $content ?? '', $matches);

        return $matches[1] ?? [];
    }
}
