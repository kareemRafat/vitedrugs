<?php

namespace App\Http\Controllers;

use App\Services\Narrative\DiseaseArticleBuilder;
use App\Services\MedicalKnowledge\DiseaseDataLoader;

class TestArticleController extends Controller
{
    public function show(
        DiseaseDataLoader $loader,
        DiseaseArticleBuilder $builder
    ) {
        $disease =
            $loader->load(
                'foot-and-mouth-disease'
            );

        $article =
            $builder->build($disease);

            

        return view(
            'medical.article',
            compact('article')
        );
    }
}
