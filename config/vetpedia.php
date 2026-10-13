<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Poultry Data Sources
    |--------------------------------------------------------------------------
    |
    | Locations of the legacy datasets used to populate the Poultry section.
    | Both paths are overridable through environment variables so the loaders
    | never need to be edited when the source files move or are replaced with
    | a corrected revision.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Site parts
    |--------------------------------------------------------------------------
    |
    | A "part" is a themed section of the site: drugs, large-animals, poultry.
    | `SetPartTheme` resolves the part from the first URL segment, and the
    | header, footer and shared views all key off it.
    |
    | This map connects each part to the data that belongs to it. Two different
    | vocabularies meet here and they are deliberately kept apart:
    |
    |   domain           the value stored on `diseases` and `species`. Underscored
    |                    (`large_animal`), see the HasDomain trait.
    |   taxonomy_groups  the values stored on `host_species`. That table has no
    |                    `domain` column at all, so part membership has to be
    |                    expressed through its taxonomy instead.
    |
    | Every taxonomy group currently present in `host_species` is listed
    | explicitly rather than derived with a `!= 'poultry'` exclusion, so a group
    | added later has to be placed deliberately instead of silently joining the
    | large-animal part.
    |
    */

    'parts' => [

        'large-animals' => [
            'domain' => 'large_animal',
            'taxonomy_groups' => ['ruminant', 'equine', 'porcine', 'canine', 'feline', 'fish'],
        ],

        'poultry' => [
            'domain' => 'poultry',
            'taxonomy_groups' => ['poultry'],
        ],

    ],

    'poultry' => [

        /*
         | The phpMyAdmin SQL dump holding the relational poultry ontology:
         | diseases, findings, species, body systems, anatomical structures
         | and the disease/finding pivot.
         */
        'sql_dump' => env(
            'VETPEDIA_POULTRY_SQL_DUMP',
            base_path('newVitPedia/vetpedia_poultry (1).sql')
        ),

        /*
         | Directory of per-disease knowledge documents (one JSON file per
         | disease) written to diseases.knowledge_payload. Loaded through the
         | admin panel rather than the seeder.
         */
        'knowledge_directory' => env(
            'VETPEDIA_POULTRY_KNOWLEDGE_DIR',
            base_path('newVitPedia/poultry/storage/app/diseases')
        ),

        /*
         | The domain value written onto every poultry row.
         */
        'domain' => 'poultry',

        /*
         | The domain every row that predates the Poultry section belongs to.
         */
        'default_domain' => 'large_animal',

        /*
         | Knowledge documents whose filename does not match the Disease slug.
         |
         | Five of the 154 documents are worded differently from the disease they
         | describe. Each entry maps the document's filename onto the Disease slug
         | that should receive it. Add to this list rather than renaming files, so
         | the mapping stays reviewable in one place.
         |
         | Three documents are intentionally absent because they have no single
         | target disease and are surfaced as unmatched by the importer:
         |
         |   duck-viral-hepatitis          the dump splits this into type 1, 2 and 3
         |   duck-virus-enteritis          no such Disease exists
         |   mycotoxicosis-family-profile  a cross-disease profile, not a Disease
         */
        'knowledge_file_aliases' => [
            'ostrich-codiostomum-infection' => 'codiostomum-struthionis-infection',
            'ostrich-feather-pecking-and-cannibalism' => 'feather-pecking-and-cannibalism',
            'ostrich-ingested-toxins' => 'ostrich-ingested-toxicosis',
            'ostrich-trauma' => 'trauma',
            'oosporein' => 'oosporein-toxicosis',
        ],

    ],

];
