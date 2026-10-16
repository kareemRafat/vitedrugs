<?php

return [

    'title' => 'Search | VetPedia',
    'heading' => 'Search',
    'subtitle' => 'Search diseases, clinical signs, host species and veterinary products.',
    'badge' => 'Global Search',

    'placeholder' => 'Search diseases, signs, species, products...',
    'button' => 'Search',

    'no_query' => 'Type a term to search diseases, clinical signs, host species and products.',
    'results_for' => 'Results for ":q"',
    'no_results_title' => 'Nothing matched ":q"',
    'no_results' => 'Check the spelling, or try a shorter or more general term.',

    // Read by the shared <x-pagination> component, one per bucket card.
    // `pager_showing` rather than `showing`, matching every other namespace that
    // feeds that component.
    'pager_showing' => 'Showing',
    'of' => 'of',
    'previous_page' => 'Previous',
    'next_page' => 'Next',

    'browse_by_sign' => 'Browse by clinical sign',
    'run_diagnosis' => 'Run a diagnosis',

    'buckets' => [
        'diseases' => [
            'label' => 'Diseases',
        ],
        'clinical_signs' => [
            'label' => 'Clinical Signs',
        ],
        'host_species' => [
            'label' => 'Host Species',
        ],
        'products' => [
            'label' => 'Products',
        ],
        'blog_articles' => [
            'label' => 'Blog Articles',
        ],
        'microorganisms' => [
            'label' => 'Microorganisms',
        ],
        'medical_articles' => [
            'label' => 'Medical Articles',
        ],
    ],

];
