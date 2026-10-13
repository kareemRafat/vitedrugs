<?php

/*
| The poultry views are shared with large animals (resources/views/diagnosis and
| resources/views/filter) and read their copy from `$part.<section>.*`. Rather
| than duplicate ~144 keys per locale, this file starts from the large-animal
| tree and overrides only the wording that is actually wrong for birds.
|
| A key added to large-animals.php is picked up here automatically; a key that
| must read differently for poultry belongs in the overrides below. Both files
| must stay in step — the Arabic one overrides the same keys.
*/

$shared = require __DIR__.'/large-animals.php';

return array_replace_recursive($shared, [

    'nav' => [
        'home' => 'Poultry',
        'diagnosis' => 'Diagnosis',
        'filter' => 'Filter',
        'search' => 'Search',
        'diseases' => 'Diseases',
        'lesions' => 'Lesions',
    ],

    'landing' => [
        'badge' => 'Poultry',
        'heading' => 'The Poultry Knowledge Hub',
        'subtitle' => 'A clinical knowledge base for poultry veterinary practice — differential diagnosis, disease filtering, and a growing library of the conditions that affect chickens, turkeys, ducks and other birds.',

        'cta_primary' => 'Start Diagnosis',
        'cta_secondary' => 'Use the Filter',

        'services_heading' => 'Start here',
        'services_subtitle' => 'Both tools run against the poultry disease set only — every result you see is a bird disease.',
        'open' => 'Open',

        'services' => [
            'diagnosis' => [
                'title' => 'Differential Diagnosis',
                'description' => 'Pick the bird species and the signs you actually observed, and get a ranked list of the diseases most likely to explain them.',
            ],
            'filter' => [
                'title' => 'Clinical Filter',
                'description' => 'Narrow the list by bird species, body system and whether a disease can pass to humans, using the signs you select.',
            ],
            'search' => [
                'title' => 'Global Search',
                'description' => 'One box across diseases, clinical signs, bird species and products, with suggestions as you type and tolerance for near-misses.',
            ],
            'compare' => [
                'title' => 'Disease Comparison',
                'description' => 'Put up to four bird diseases side by side to see which signs tell them apart and which signs they all share.',
            ],
        ],

        'stats_heading' => 'Poultry knowledge base at a glance',
        'stats' => [
            'diseases' => 'Diseases',
            'findings' => 'Signs and lesions',
            'lesions' => 'Post-mortem lesions',
            'species' => 'Bird species',
        ],

        'cta_title' => 'Species covered',
        'cta_subtitle' => 'Chickens, turkeys, ducks, geese, pigeons, quail and ostriches — plus broilers and layers.',
        'cta_button' => 'Start a diagnosis',
    ],

    /*
    | Wording the large-animal pages say in terms of livestock. Everything not
    | listed here is inherited as-is.
    */
    'diagnosis' => [
        'subtitle' => 'Select the clinical signs you observed in the bird and we will rank the most likely poultry diseases.',
        'species_label' => 'Bird species',
        'species_placeholder' => 'Select bird species',
        'warning' => 'Please select at least three observed clinical signs before running differential diagnosis.',
        'no_results_desc' => 'Try selecting more or different clinical signs, or a different bird species.',
    ],

    'filter' => [
        'subtitle' => 'Narrow the poultry disease list by bird species, etiology, body system and zoonotic risk.',
        'species_label' => 'Bird species',
        'species_placeholder' => 'All bird species',
        'results_subtitle' => 'Poultry diseases matching the bird species, etiology, body system, zoonotic and clinical signs you selected.',
    ],

    'hero' => [
        'badge' => [
            'diagnosis' => 'Poultry Diagnostic Tool',
        ],
        'stats' => [
            'species' => 'Bird species',
        ],
    ],

    /*
    | Some poultry diseases are legitimately linked to livestock hosts too
    | (salmonellosis, for one), so this stays "species" rather than "birds".
    */
    'disease' => [
        'host_species' => 'Affected species',
    ],

]);
