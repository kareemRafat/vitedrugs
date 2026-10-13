<?php

/*
| The poultry views are shared with large animals (resources/views/diagnosis and
| resources/views/filter) and read their copy from `$part.<section>.*`. Rather
| than duplicate ~144 keys per locale, this file starts from the large-animal
| tree and overrides only the wording that is actually wrong for birds.
|
| A key added to large-animals.php is picked up here automatically; a key that
| must read differently for poultry belongs in the overrides below. Both files
| must stay in step — the English one overrides the same keys.
|
| Note for translators: these overrides are unreviewed by a native speaker.
*/

$shared = require __DIR__.'/large-animals.php';

return array_replace_recursive($shared, [

    'nav' => [
        'home' => 'الدواجن',
        'diagnosis' => 'التشخيص',
        'filter' => 'التصفية',
        'search' => 'البحث',
        'diseases' => 'الأمراض',
        'lesions' => 'الآفات',
        'products' => 'الأدوية',
        'body_map' => 'خريطة الجسم',
    ],

    'landing' => [
        'badge' => 'الدواجن',
        'heading' => 'مركز معرفة الدواجن',
        'subtitle' => 'قاعدة معرفة سريرية للممارسة البيطرية في مجال الدواجن — التشخيص التفريقي، والتصفية السريرية، ومكتبة متنامية من الأمراض التي تصيب الدجاج والرومي والبط وغيرها من الطيور.',

        'cta_primary' => 'ابدأ التشخيص',
        'cta_secondary' => 'استخدم التصفية',

        'services_heading' => 'ابدأ من هنا',
        'services_subtitle' => 'تعمل الأداتان على مجموعة أمراض الدواجن فقط — وكل نتيجة تراها هي مرض يصيب الطيور.',
        'open' => 'افتح',

        'services' => [
            'diagnosis' => [
                'title' => 'التشخيص التفريقي',
                'description' => 'اختر نوع الطائر والعلامات التي لاحظتها فعلياً، واحصل على قائمة مرتبة بالأمراض الأكثر احتمالاً في تفسيرها.',
            ],
            'filter' => [
                'title' => 'التصفية السريرية',
                'description' => 'ضيّق قائمة الأمراض حسب نوع الطائر والمنظومة الجسدية ومدى إمكانية انتقال المرض إلى الإنسان، باستخدام العلامات التي تختارها.',
            ],
            'search' => [
                'title' => 'البحث الشامل',
                'description' => 'مربع بحث واحد يجمع الأمراض والعلامات الإكلينيكية وأنواع الطيور والمستحضيرات، مع اقتراحات أثناء الكتابة وتسامح مع الأخطاء الإملائية.',
            ],
            'compare' => [
                'title' => 'مقارنة الأمراض',
                'description' => 'ضع حتى أربعة أمراض جنبًا إلى جنب لترى العلامات التي تفرّق بينها والعلامات التي تشترك فيها جميعًا.',
            ],
            'products' => [
                'title' => 'دليل الأدوية',
                'description' => 'أدوية مسجَّل لها جرعة للدواجن — المواد الفعّالة، والجرعات حسب الطائر، وفترات السحب للحم والبيض.',
            ],
            'body_map' => [
                'title' => 'خريطة الجسم',
                'description' => 'اضغط على جزء الطائر الذي يقلقك، واعرض الأمراض والعلامات وآفات ما بعد الوفاة المسجَّلة مقابل أعضائه.',
            ],
        ],

        'stats_heading' => 'قاعدة معرفة الدواجن في لمحة',
        'stats' => [
            'diseases' => 'الأمراض',
            'findings' => 'العلامات والآفات',
            'lesions' => 'آفات ما بعد الوفاة',
            'species' => 'أنواع الطيور',
        ],

        'cta_title' => 'الأنواع المشمولة',
        'cta_subtitle' => 'الدجاج، والرومي، والبط، والإوز، والحمام، والسمان، والنعام — إضافة إلى بداري التسمين ودجاج البيض.',
        'cta_button' => 'ابدأ تشخيصاً',
    ],

    /*
    | Wording the large-animal pages say in terms of livestock. Everything not
    | listed here is inherited as-is.
    */
    'diagnosis' => [
        'subtitle' => 'اختر العلامات السريرية التي لاحظتها على الطائر وسنرتب قائمة بأمراض الدواجن الأكثر احتمالاً في تفسيرها.',
        'species_label' => 'نوع الطائر',
        'species_placeholder' => 'اختر نوع الطائر',
        'warning' => 'يرجى اختيار ثلاث علامات سريرية لاحظتها على الأقل قبل تشغيل التشخيص التفريقي.',
        'no_results_desc' => 'جرّب اختيار علامات سريرية أكثر أو مختلفة، أو نوع طائر آخر.',
    ],

    'filter' => [
        'subtitle' => 'ضيّق قائمة أمراض الدواجن حسب نوع الطائر، والسبب، والمنظومة الجسدية، وخطر نقل المرض إلى الإنسان.',
        'species_label' => 'نوع الطائر',
        'species_placeholder' => 'كل أنواع الطيور',
        'results_subtitle' => 'أمراض الدواجن المطابقة لنوع الطائر، والسبب، والمنظومة الجسدية، وخطر نقل المرض، والعلامات السريرية التي اخترتها.',
    ],

    'hero' => [
        'badge' => [
            'diagnosis' => 'أداة التشخيص البيطري للدواجن',
        ],
        'stats' => [
            'species' => 'أنواع الطيور',
        ],
    ],

    'catalogue' => [
        'subtitle' => 'أدوية مسجَّل لها جرعة للدواجن، مع المواد الفعّالة والشركة المصنّعة وفترات السحب.',
        'withdrawal_unrecorded' => 'لا توجد فترة سحب مسجّلة للدواجن — تأكّد من الشركة المصنّعة قبل معالجة طيور مخصّصة للحم أو البيض.',
    ],

    'body_map' => [
        'title' => 'الخريطة الجسدية للدواجن | VetPedia',
        'heading' => 'الخريطة الجسدية للدواجن',
        'subtitle' => 'اضغط على جزء الطائر الذي يقلقك لتعرف ما يحدث فيه — الأمراض والعلامات التي ستلاحظها والآفات التي تجده بعد النفوق.',
        'badge' => 'خريطة الجسم',
        'schematic_note' => 'الرسم تخطيطي؛ مواضع العلامات إرشادية وليست تشريحية دقيقة.',

        'stats' => [
            'systems' => 'الجهات الجسدية',
            'organs' => 'الأعضاء الموثّقة',
            'diseases' => 'الأمراض المرتبطة',
        ],

        'diagram_view' => 'الرسم',
        'list_view' => 'عرض القائمة',
        'diagram_label' => 'رسم تخطيطي للدجاجة. كل علامة تمثل جهة جسدية؛ اخترها لعرض أعضائها.',
        'legend' => 'الجهات الجسدية',

        'select_system' => 'اختر جهة جسدية لعرض الأعضاء التي تشملها.',
        'select_organ' => 'اختر عضواً لعرض ما هو مسجَّل مقابله.',
        'back' => 'كل الجهات الجسدية',

        'diseases' => 'الأمراض',
        'signs' => 'العلامات الإكلينيكية',
        'lesions' => 'آفات ما بعد الوفاة',
        'organs' => 'الأعضاء',

        'mapped' => 'مرتبطة',
        'derived' => 'مطابقة بالاسم',
        'mapped_note' => 'مرتبطة بهذا العضو في البيانات المصدرية.',
        'derived_note' => 'أُضيفت لأن علامة أو آفة مسجَّلة تحمل اسم هذا العضو. المطابقة نصية ولم يراجعها طبيب بيطري — تعاملها كنقطة بداية.',

        'organ_empty' => 'لا يوجد أي شيء مسجَّل مقابل هذا العضو بعد.',
        'system_empty' => 'لا يوجد في هذه الجهة عضو مسجَّل مقابله شيء بعد.',
        'no_system' => 'لم يتم توثيق أي جهة جسدية بعد.',
    ],

    /*
    | Some poultry diseases are legitimately linked to livestock hosts too
    | (salmonellosis, for one), so this stays "species" rather than "birds".
    */
    'disease' => [
        'host_species' => 'الأنواع المصابة',
    ],

]);
