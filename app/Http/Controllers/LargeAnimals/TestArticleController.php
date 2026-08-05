<?php

namespace App\Http\Controllers\LargeAnimals;

use App\Http\Controllers\Controller;
use App\Services\MedicalKnowledge\DiseaseDataLoader;
use App\Services\Narrative\DiseaseArticleBuilder;

class TestArticleController extends Controller
{
    public function show(DiseaseDataLoader $loader, DiseaseArticleBuilder $builder)
    {
        $disease = $loader->load('foot-and-mouth-disease');

        $article = $builder->build($disease);

        return view('large-animals.medical.article', compact('article', 'disease'));
    }
}
