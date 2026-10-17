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
        'products' => 'Drugs',
        'body_map' => 'Body Map',
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
            'diseases' => [
                'title' => 'Disease Library',
                'description' => 'Every bird disease on record — browse by name or jump to a letter, then open one for its clinical signs, post-mortem lesions and treatment.',
            ],
            'products' => [
                'title' => 'Drug Catalogue',
                'description' => 'Drugs with a recorded poultry dose — active ingredients, dosing by bird, and withdrawal periods for meat and eggs.',
            ],
            'body_map' => [
                'title' => 'Body Map',
                'description' => 'Click the part of the bird you are worried about and see the diseases, signs and post-mortem lesions recorded against its organs.',
            ],
            'atlas' => [
                'title' => 'Lesion Atlas',
                'description' => 'Opened a bird and found something you cannot name? Browse every post-mortem lesion recorded for poultry and see which diseases cause it.',
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

    'catalogue' => [
        'subtitle' => 'Drugs with a recorded dose for poultry, with their active ingredients, manufacturer and withdrawal periods.',
        'withdrawal_unrecorded' => 'No withdrawal period recorded for poultry — confirm with the manufacturer before treating birds for meat or eggs.',
    ],

    /*
    | The body map exists for poultry only, so the whole tree lives here rather
    | than in the shared base. The two tiers of attachment are named in the copy
    | because the page shows both and they are not equally trustworthy: "mapped"
    | is a link somebody made, "matched by name" is a string comparison.
    */
    'body_map' => [
        'title' => 'Poultry Body Map | VetPedia',
        'heading' => 'Poultry Body Map',
        'subtitle' => 'Click the part of the bird you are worried about and see what goes wrong there — the diseases, the signs you would observe, and the lesions you would find post-mortem.',
        'badge' => 'Body Map',
        'schematic_note' => 'Select any organ on the diagram to see the diseases, signs and lesions recorded against it.',

        'stats' => [
            'systems' => 'Body systems',
            'organs' => 'Organs mapped',
            'diseases' => 'Diseases reached',
        ],

        'diagram_view' => 'Diagram',
        'list_view' => 'List view',
        'diagram_label' => 'Anatomy of a chicken. Each marker is an organ; select one to see what is recorded against it.',
        'legend' => 'Body systems',

        'select_system' => 'Select a body system to see the organs it covers.',
        'select_organ' => 'Select an organ to see what is recorded against it.',
        'back' => 'All body systems',

        'diseases' => 'Diseases',
        'signs' => 'Clinical signs',
        'lesions' => 'Post-mortem lesions',
        'organs' => 'Organs',

        // These two are block headers, not labels. The disease list has one
        // heading ("Diseases") and each tier explains itself underneath it, so
        // there is no separate name for a curated link or a text match — only the
        // sentence that says which one you are looking at.
        'mapped_note' => 'Linked to this organ in the source data.',
        'derived_note' => 'Attached because a recorded sign or lesion names this organ. Matched by text, not reviewed by a veterinarian — treat it as a starting point.',

        // The caution that keeps a guessed match from reading as a curated one.
        // "Matched on" names the label the match was made on, which is the only
        // way a reader can tell a reasonable guess from a wrong one.
        'matched_because' => 'Matched on:',
        'and_more' => '+:count more',
        'zoonotic' => 'Zoonotic',
        'show_all' => 'Show all :count',
        'show_less' => 'Show fewer',

        'organ_empty' => 'Nothing is recorded against this organ yet.',
        'system_empty' => 'No organ in this system has anything recorded against it yet.',
        'no_system' => 'No body systems are mapped yet.',
    ],

    /*
    | The atlas shares the body map's tiering problem and inherits its honest
    | wording. No lesion carries an organ, so every placement on this page comes
    | from the lesion's name naming one, and the share that names none is listed
    | rather than dropped.
    */
    'atlas' => [
        'title' => 'Poultry Lesion Atlas | VetPedia',
        'heading' => 'Poultry Lesion Atlas',
        'subtitle' => 'What a disease looks like from the inside. Browse the post-mortem lesions recorded for poultry and see which diseases cause each one.',
        'badge' => 'Lesion Atlas',
        'grouping_note' => 'Lesions are filed under a body system when their name names an organ. That is a text match, not a link a veterinarian made, and lesions that name no organ are listed separately rather than hidden.',

        'stats' => [
            'lesions' => 'Lesions recorded',
            'diseases' => 'Diseases covered',
            'systems' => 'Body systems',
            'unclassified' => 'Not yet placed',
        ],

        'search_placeholder' => 'Search lesion names',
        'letter_nav' => 'Jump to letter',
        'all_letters' => 'All',
        'showing' => 'Showing :count lesions',
        'clear' => 'Clear filters',
        // Shown in place of the count while a request is in flight, so the reader
        // knows the number they are looking at is about to change.
        'loading' => 'Loading…',

        'unclassified' => 'Not yet placed',
        'organ' => 'Names',
        // A tile has to say something about a lesion that names no organ. Leaving
        // the line out reads as a rendering fault rather than as the honest answer.
        'names_no_organ' => 'Names no organ',
        'diseases_count' => ':count diseases',
        'caused_by' => 'Caused by',
        // The `+N` chip. An aria-label rather than a title attribute, because the
        // visible text is "+12" and that is not a label — a screen reader reads
        // the chip as twelve-something otherwise.
        'all_diseases' => 'Show all :count diseases',
        'no_diseases' => 'No poultry disease is linked to this lesion yet.',
        'close' => 'Close',

        'image_soon' => 'Photograph coming soon',
        'empty' => 'No lesion matches these filters. Try clearing the search or choosing a different body system.',

        // Read by the shared <x-pagination> component, which is handed this
        // namespace as its translationPrefix.
        //
        // Renamed from a second `showing`. The filter bar above wanted
        // "Showing :count lesions" and the pager wants a bare "Showing", but this
        // is one flat array, so the second `showing` silently overwrote the first
        // and the count never rendered — the page said "Showing" and nothing else.
        'pager_showing' => 'Showing',
        'of' => 'of',
        'previous_page' => 'Previous',
        'next_page' => 'Next',

        // Where a section's atlas-wide count exceeds the rows on this page.
        'count_on_page' => ':on on this page',
    ],

    /*
    | Some poultry diseases are legitimately linked to livestock hosts too
    | (salmonellosis, for one), so this stays "species" rather than "birds".
    */
    'disease' => [
        'host_species' => 'Affected species',
    ],

]);
