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

        $pattern = '/<h2([^>]*)>(.*?)<\/h2>|<p([^>]*)>\s*<strong([^>]*)>((?:(?!<\/strong>).)*?)<\/strong>\s*<\/p>/is';

        return preg_replace_callback($pattern, function (array $match) use (&$index) {
            $id = 'section-'.$index;
            $index++;

            if (str_starts_with(strtolower($match[0]), '<h2')) {
                $attrs = $match[1];

                if (preg_match('/\bid\s*=\s*["\']/i', $attrs)) {
                    return $match[0];
                }

                return '<h2'.$attrs.' id="'.$id.'">'.$match[2].'</h2>';
            }

            $pAttrs = $match[3];

            if (preg_match('/\bid\s*=\s*["\']/i', $pAttrs)) {
                return $match[0];
            }

            return '<p'.$pAttrs.' id="'.$id.'"><strong'.$match[4].'>'.$match[5].'</strong></p>';
        }, $content ?? '');
    }

    protected function extractTableOfContents(?string $content): array
    {
        $pattern = '/<h2[^>]*>(.*?)<\/h2>|<p[^>]*>\s*<strong[^>]*>((?:(?!<\/strong>).)*?)<\/strong>\s*<\/p>/is';

        preg_match_all($pattern, $content ?? '', $matches, PREG_SET_ORDER);

        return array_map(
            fn (array $match): string => $match[1] !== '' ? $match[1] : $match[2],
            $matches,
        );
    }
}
