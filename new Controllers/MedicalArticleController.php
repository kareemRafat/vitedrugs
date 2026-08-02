<?php

namespace App\Http\Controllers;

use App\Models\MedicalArticle;
use App\Http\Resources\MedicalArticleResource;

class MedicalArticleController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Index
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $articles =

            MedicalArticle::query()

            ->where('is_published', true)

            ->latest()

            ->paginate(12);


        return view(

            'medical-articles.fmd',

            compact('articles')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Show
    |--------------------------------------------------------------------------
    */

    public function show($slug)
    {
        $article =

            MedicalArticle::query()

            ->where('slug', $slug)

            ->where('is_published', true)

            ->with('disease')

            ->firstOrFail();


        /*
        |--------------------------------------------------------------------------
        | Generate Table Of Contents
        |--------------------------------------------------------------------------
        */

        $tableOfContents =

            $this->extractTableOfContents(
                $article->content
            );


        return view(

            'medical-articles.show',

            [

                'article' => $article,

                'tableOfContents' => $tableOfContents,
            ]
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Extract TOC
    |--------------------------------------------------------------------------
    */

    protected function extractTableOfContents($content)
    {
        preg_match_all(

            '/<h2.*?>(.*?)<\/h2>/i',

            $content,

            $matches
        );


        return

            $matches[1] ?? [];
    }
}
