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
    | How many rows a bucket shows on the results page.
    */
    'limits' => [
        'results' => 20,
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
    */
    'fuzzy' => [
        'min_length' => 4,
        'min_score' => 0.3,
        'min_shared' => 2,

        // Upper bound on rows pulled into memory for the near-miss pass.
        'scan_limit' => 2000,
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
    | from. `columns` are every column the LIKE pass searches.
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
            'route' => '{part}.diagnosis',
            'route_key' => null,
            'route_params' => ['sign' => 'id'],
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
