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

        $content = $this->injectSectionIds($article->localized_content);
        $tableOfContents = $this->extractTableOfContents($content);

        return view('large-animals.medical-articles.show', compact('article', 'tableOfContents', 'content'));
    }

    protected function injectSectionIds(?string $content): string
    {
        $index = 0;

        return preg_replace_callback('/<h2([^>]*)>/i', function (array $match) use (&$index) {
            $attrs = $match[1];
            $id = 'section-'.$index;
            $index++;

            if (preg_match('/\bid\s*=\s*["\']/i', $attrs)) {
                return $match[0];
            }

            return '<h2'.$attrs.' id="'.$id.'">';
        }, $content ?? '');
    }

    protected function extractTableOfContents(?string $content): array
    {
        preg_match_all('/<h2.*?>(.*?)<\/h2>/i', $content ?? '', $matches);

        return $matches[1] ?? [];
    }
}
