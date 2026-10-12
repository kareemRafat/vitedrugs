<?php

namespace App\Support;

class TerminologyNormalizer
{
    protected array $dictionary = [

        // Appetite
        'off food'                 => 'anorexia',
        'off feed'                 => 'anorexia',
        'loss of appetite'         => 'anorexia',
        'inappetence'              => 'anorexia',

        // Depression
        'depressed'                => 'depression',
        'depression'               => 'depression',
        'dullness'                 => 'depression',
        'listlessness'             => 'depression',

        // Respiratory
        'difficult breathing'      => 'dyspnea',
        'difficulty breathing'     => 'dyspnea',
        'labored breathing'        => 'dyspnea',
        'gasping'                  => 'dyspnea',

        // Diarrhea
        'white diarrhea'           => 'whitish diarrhea',
        'green diarrhea'           => 'greenish diarrhea',
        'yellow diarrhea'          => 'yellowish diarrhea',

        // General
        'ruffling'                 => 'ruffled feathers',
        'ruffled plumage'          => 'ruffled feathers',

        // Musculoskeletal
        'slipped tendon'           => 'achilles tendon displacement',

        // Nervous
        'twisted neck'             => 'torticollis',
        'wry neck'                 => 'torticollis',

        // Head lesions
        'swollen head'             => 'head swelling',
        'bluish head'              => 'cyanotic head',

    ];

    public function normalize(?string $term): ?string
    {
        if (!$term) {
            return null;
        }

        $term = strtolower(trim($term));

        return $this->dictionary[$term] ?? $term;
    }
}