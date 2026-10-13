<?php

use App\Models\Blog;
use App\Models\Disease;
use App\Models\LargeAnimals\ClinicalSign;
use App\Models\LargeAnimals\HostSpecies;
use App\Models\LargeAnimals\MedicalArticle;
use App\Models\LargeAnimals\Microorganism;
use App\Models\Product;

/*
|--------------------------------------------------------------------------
| Global Search
|--------------------------------------------------------------------------
|
| One search page serves both the large-animals and the poultry parts. The
| parts differ only in which result buckets they show and in the domain those
| buckets are restricted to, so both live here rather than in two controllers.
|
| Buckets that exist only as editorial or reference content (blog posts,
| microorganisms, medical articles) are marked with scope `none`: they carry
| no domain and are large-animal-only, so the poultry part simply omits them
| from its bucket list below.
|
*/

return [

    /*
    | How many rows a bucket shows per page, and the most it will hold in memory.
    |
    | `per_page` matches /drugs/search so both parts paginate identically.
    |
    | `pool` caps the rows a bucket collects before slicing. Ranking happens in
    | PHP — the LIKE pass cannot sort by relevance — so a bucket pages through an
    | in-memory collection rather than asking the database for a page at a time.
    | The pool is therefore a real ceiling: a term matching more rows than this
    | reports and pages only what was collected. It is set well above a page so
    | that reaching it is not something a normal search does.
    */
    'limits' => [
        'per_page' => 10,
        'pool' => 500,
    ],

    /*
    | Near-miss matching, used only when the LIKE pass comes back short.
    |
    | Scores candidates by the Dice coefficient over character trigrams, which
    | absorbs the doubled-letter deletions ("cocido" -> "coccidiosis") that a
    | plain edit distance cannot reach without a threshold loose enough to match
    | noise outright.
    |
    | A match must also be *anchored* at the front of the candidate. This is a
    | hard requirement, not a relaxed alternative, and it is what does the real
    | work: "chicken" and "thickened" share the interior trigram run
    | hic/ick/cke/ken and score 0.667, far above any threshold worth setting, yet
    | they only begin matching at the second trigram. They are a shared suffix,
    | not a typo, and no score cutoff separates them from real matches.
    |
    | min_score stays at 0.30 because "cocido" -> "coccidiosis" is anchored but
    | only scores 0.308. Raising it to 0.50 would buy nothing — anchoring has
    | already removed the false positives — while throwing that case away.
    |
    | min_shared stops a single common trigram such as "the" from dragging in
    | unrelated words.
    |
    | scan_limit bounds the rows pulled into memory by the near-miss pass. It is
    | deliberately well under `limits.pool`: the pass runs for every bucket whose
    | LIKE pass came up short of a full page, so on a narrow query all seven
    | buckets scan at once and this is the number multiplied by seven.
    */
    'fuzzy' => [
        'min_length' => 4,
        'min_score' => 0.3,
        'min_shared' => 2,

        // Upper bound on rows pulled into memory for the near-miss pass.
        'scan_limit' => 500,
    ],

    /*
    | Buckets shown per part, in display order.
    */
    'parts' => [
        'large-animals' => [
            'diseases',
            'clinical_signs',
            'host_species',
            'products',
            'blog_articles',
            'microorganisms',
            'medical_articles',
        ],

        'poultry' => [
            'diseases',
            'clinical_signs',
            'host_species',
            'products',
        ],
    ],

    /*
    | Bucket definitions.
    |
    | scope:
    |   domain         a `domain` column of its own
    |   part           membership expressed as a taxonomy group (host_species)
    |   through_disease  no domain of its own; scoped via its diseases
    |   none           unscoped editorial or reference content
    |
    | title/secondary name the columns the row label and its sub-line read
    | from. `columns` are every column the LIKE pass searches. `relations`
    | additionally searches related tables through `orWhereHas`, which is how the
    | products bucket reaches a manufacturer, an active ingredient or a disease
    | name the way /drugs/search does.
    */
    'buckets' => [

        'diseases' => [
            'model' => Disease::class,
            'label' => 'diseases',
            'icon' => 'activity',
            'scope' => 'domain',
            'columns' => ['name', 'name_ar'],
            'title' => 'name',
            'title_ar' => 'name_ar',
            'secondary' => 'description',
            'route' => '{part}.diseases.show',
            'route_key' => 'slug',
            'order_by' => 'name',
        ],

        'clinical_signs' => [
            'model' => ClinicalSign::class,
            'label' => 'clinical_signs',
            'icon' => 'stethoscope',
            'scope' => 'through_disease',
            'columns' => ['display_name', 'display_name_ar', 'canonical_name'],
            'title' => 'display_name',
            'title_ar' => 'display_name_ar',
            'secondary' => 'canonical_name',
            // One row per concept, not per record.
            //
            // A canonical name is not unique — `ClinicalSign` is a finding plus an
            // anatomy plus a modifier, and the same concept is sometimes recorded
            // twice (five names do this on the poultry side). Without this, two
            // identical-looking rows appeared in the results pointing at one URL,
            // and the page behind it resolved to a single record and showed half
            // the diseases. Applied before the limit, so the cap and the badge
            // count concepts rather than records.
            //
            // Data-driven rather than special-cased, like `columns` and
            // `relations` above.
            'dedupe_by' => 'canonical_name',
            // The sign's own page, which lists the diseases carrying it.
            //
            // It pointed at the diagnosis tool first, which was a dead end dressed
            // as a hand-off: `InitialDiagnosisRequest` requires a species and
            // `min:3` signs, so `?sign=93` opened a form that could not be
            // submitted with the one sign it had been given. The filter tool was
            // the next attempt and answered the question a level later than it
            // needed to.
            //
            // A sign is a real entry in the knowledge base — the filter tool reads
            // the same rows — so it gets a real page, and the page offers both
            // hand-offs onward at the bottom.
            //
            // Addressed by `canonical_name`: `clinical_signs` has no slug column,
            // and the canonical name is the stable normalised key.
            'route' => '{part}.signs.show',
            'route_key' => null,
            'route_params' => ['sign' => 'canonical_name'],
            'order_by' => 'display_name',
        ],

        'host_species' => [
            'model' => HostSpecies::class,
            'label' => 'host_species',
            'icon' => 'feather',
            'scope' => 'part',
            'columns' => ['display_name', 'display_name_ar', 'canonical_name'],
            'title' => 'display_name',
            'title_ar' => 'display_name_ar',
            'secondary' => 'taxonomy_group',
            'route' => '{part}.filter',
            'route_key' => null,
            'route_params' => ['host_species_id' => 'id'],
            'order_by' => 'display_name',
        ],

        'products' => [
            'model' => Product::class,
            'label' => 'products',
            'icon' => 'package',
            'scope' => 'through_disease',
            'columns' => ['trade_name', 'trade_name_ar'],
            'relations' => [
                'activeIngredients' => ['name', 'name_ar'],
                'companies' => ['name', 'name_ar'],
                'diseases' => ['name', 'name_ar'],
            ],
            'title' => 'trade_name',
            'title_ar' => 'trade_name_ar',
            'secondary' => null,
            'route' => 'products.show',
            'route_key' => null,
            'route_params' => ['product' => 'slug'],
            'order_by' => 'trade_name',
        ],

        'blog_articles' => [
            'model' => Blog::class,
            'label' => 'blog_articles',
            'icon' => 'newspaper',
            'scope' => 'none',
            'columns' => ['title', 'title_ar', 'excerpt'],
            'title' => 'title',
            'title_ar' => 'title_ar',
            'secondary' => 'excerpt',
            'route' => 'blog.show',
            'route_key' => null,
            'route_params' => ['blog' => 'slug'],
            'order_by' => 'title',
        ],

        'microorganisms' => [
            'model' => Microorganism::class,
            'label' => 'microorganisms',
            'icon' => 'microscope',
            'scope' => 'none',
            'extra_scope' => 'catalogue',
            'columns' => ['name', 'normalized_name', 'searchable_text'],
            'title' => 'name',
            'secondary' => 'kingdom',
            'route' => '{part}.microorganisms.show',
            'route_key' => 'slug',
            'order_by' => 'name',
        ],

        'medical_articles' => [
            'model' => MedicalArticle::class,
            'label' => 'medical_articles',
            'icon' => 'file-text',
            'scope' => 'none',
            'columns' => ['title', 'summary'],
            'title' => 'title',
            'secondary' => 'summary',
            'route' => '{part}.medical-articles.show',
            'route_key' => 'slug',
            'order_by' => 'title',
        ],

    ],

];
