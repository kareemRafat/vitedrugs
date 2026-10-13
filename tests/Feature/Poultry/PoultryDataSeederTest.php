<?php

namespace Tests\Feature\Poultry;

use App\Models\Disease;
use App\Models\LargeAnimals\AnatomicalStructure;
use App\Models\LargeAnimals\BodySystem;
use App\Models\LargeAnimals\ClinicalSign;
use App\Models\LargeAnimals\Finding;
use App\Models\LargeAnimals\HostSpecies;
use App\Models\Species;
use App\Services\LargeAnimals\DiagnosisService;
use Database\Seeders\PoultryDataSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Exercises PoultryDataSeeder against a miniature dump that reproduces the
 * awkward shapes of the real file: snake_case, hyphenated and plain canonical
 * names for the same finding, a NULL slug, and a self-referencing anatomical
 * structure.
 */
class PoultryDataSeederTest extends TestCase
{
    use RefreshDatabase;

    private string $dumpPath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->dumpPath = sys_get_temp_dir().'/poultry-dump-'.getmypid().'.sql';
        file_put_contents($this->dumpPath, $this->dump());

        config()->set('vetpedia.poultry.sql_dump', $this->dumpPath);
    }

    protected function tearDown(): void
    {
        @unlink($this->dumpPath);

        parent::tearDown();
    }

    #[Test]
    public function it_loads_the_dump_into_the_shared_tables(): void
    {
        $this->seed(PoultryDataSeeder::class);

        $this->assertSame(1, Disease::query()->poultry()->count());
        $this->assertSame(1, Finding::query()->where('ontology_type', 'lesion')->count());
        // Six dump rows collapse to four findings: the three spellings of
        // "sudden death" become one, leaving two other signs plus that one.
        $this->assertSame(3, Finding::query()->where('ontology_type', 'sign')->count());
        $this->assertSame(2, HostSpecies::query()->where('taxonomy_group', 'poultry')->count());
    }

    #[Test]
    public function it_tags_every_poultry_row_with_the_poultry_domain(): void
    {
        $this->seed(PoultryDataSeeder::class);

        $this->assertSame(Disease::DOMAIN_POULTRY, Disease::first()->domain);
        $this->assertSame(Disease::DOMAIN_POULTRY, Species::first()->domain);
    }

    #[Test]
    public function it_maps_finding_types_onto_ontology_type_and_category(): void
    {
        $this->seed(PoultryDataSeeder::class);

        $lesion = Finding::query()->where('canonical_name', 'enlarged spleen')->first();

        $this->assertSame('lesion', $lesion->ontology_type);
        $this->assertSame('postmortem', $lesion->category);

        $sign = Finding::query()->where('canonical_name', 'reduced egg production')->first();

        $this->assertSame('sign', $sign->ontology_type);
        $this->assertSame('clinical', $sign->category);
    }

    #[Test]
    public function it_merges_snake_case_and_hyphenated_duplicates_onto_one_canonical_finding(): void
    {
        $this->seed(PoultryDataSeeder::class);

        // The dump spells "sudden death" three ways; all three collapse.
        $this->assertSame(1, Finding::query()->where('slug', 'sudden-death')->count());
        $this->assertSame(
            1,
            Finding::query()->where('canonical_name', 'sudden death')->count(),
            'Snake_case and hyphenated spellings must normalise to the same canonical name.',
        );
    }

    #[Test]
    public function it_generates_a_slug_when_the_dump_leaves_it_null(): void
    {
        $this->seed(PoultryDataSeeder::class);

        $this->assertSame('enlarged-spleen', Finding::query()->where('canonical_name', 'enlarged spleen')->value('slug'));
    }

    #[Test]
    public function it_maps_specificity_and_required_flags_onto_the_engine_pivot(): void
    {
        $this->seed(PoultryDataSeeder::class);

        $disease = Disease::query()->firstOrFail();
        $links = DB::table('disease_clinical_sign')->where('disease_id', $disease->id)->get();

        // Six dump links collapse to four, because all three "sudden death" rows
        // resolve to the same clinical sign on the same disease.
        $this->assertCount(4, $links);
        $this->assertSame(
            [true, true, true, false],
            $links->pluck('is_specific')->map(fn ($v) => (bool) $v)->sortDesc()->values()->all(),
            'Only high and very_high specificity count as specific.',
        );
        $this->assertSame(1, $links->where('is_required', 1)->count());
        $this->assertSame(1, $links->where('is_pathognomonic', 1)->count());
    }

    #[Test]
    public function it_links_each_disease_to_host_species_with_one_primary(): void
    {
        $this->seed(PoultryDataSeeder::class);

        $disease = Disease::query()->firstOrFail();

        $links = DB::table('disease_host_species')->where('disease_id', $disease->id)->get();

        $this->assertCount(2, $links);
        $this->assertSame(1, $links->where('is_primary_host', 1)->count());
    }

    #[Test]
    public function it_wires_anatomical_structure_parents(): void
    {
        $this->seed(PoultryDataSeeder::class);

        $structure = AnatomicalStructure::query()->where('canonical_name', 'upper respiratory tract')->firstOrFail();

        $this->assertNotNull($structure->parent_id);
        $this->assertSame('respiratory tract', AnatomicalStructure::find($structure->parent_id)->canonical_name);
        $this->assertSame('respiratory', BodySystem::find($structure->body_system_id)->canonical_name);
    }

    #[Test]
    public function it_is_idempotent(): void
    {
        $this->seed(PoultryDataSeeder::class);

        $before = $this->counts();

        $this->seed(PoultryDataSeeder::class);
        $this->seed(PoultryDataSeeder::class);

        $this->assertSame($before, $this->counts(), 'Re-running the seeder must not duplicate rows.');
    }

    #[Test]
    public function it_leaves_large_animal_rows_untouched(): void
    {
        Disease::create(['name' => 'Strangles', 'slug' => 'strangles', 'domain' => Disease::DOMAIN_LARGE_ANIMAL]);

        $this->seed(PoultryDataSeeder::class);

        $this->assertSame(1, Disease::query()->largeAnimals()->count());
        $this->assertSame('large_animal', Disease::query()->where('slug', 'strangles')->value('domain'));
    }

    #[Test]
    public function it_gives_each_finding_a_clinical_sign_the_engine_can_reach(): void
    {
        $this->seed(PoultryDataSeeder::class);

        $signs = ClinicalSign::query()->pluck('id');

        $this->assertGreaterThan(0, $signs->count());
        $this->assertSame(
            1,
            ClinicalSign::query()
                ->where('canonical_name', 'reduced egg production')
                ->whereNull('modifier_id')
                ->whereNull('anatomical_structure_id')
                ->count(),
        );
    }

    #[Test]
    public function the_diagnosis_engine_can_rank_poultry_diseases(): void
    {
        $this->seed(PoultryDataSeeder::class);

        $chicken = HostSpecies::query()->where('canonical_name', 'chicken')->firstOrFail();

        $signIds = ClinicalSign::query()
            ->whereHas('finding', fn ($query) => $query->where('canonical_name', 'reduced egg production'))
            ->pluck('id')
            ->all();

        $results = app(DiagnosisService::class)->rank($chicken->id, $signIds, Disease::DOMAIN_POULTRY);

        $this->assertCount(1, $results);
        $this->assertSame('Newcastle Disease', $results->first()['disease']->name);
        $this->assertGreaterThan(0, $results->first()['probability']);
    }

    #[Test]
    public function it_does_nothing_when_the_dump_is_missing(): void
    {
        config()->set('vetpedia.poultry.sql_dump', '/does/not/exist.sql');

        $this->seed(PoultryDataSeeder::class);

        $this->assertSame(0, Disease::query()->count());
    }

    /**
     * @return array<string, int>
     */
    private function counts(): array
    {
        return [
            'diseases' => Disease::query()->count(),
            'findings' => Finding::query()->count(),
            'clinical_signs' => ClinicalSign::query()->count(),
            'body_systems' => BodySystem::query()->count(),
            'anatomical_structures' => AnatomicalStructure::query()->count(),
            'host_species' => HostSpecies::query()->count(),
            'species' => Species::query()->count(),
            'disease_clinical_sign' => DB::table('disease_clinical_sign')->count(),
            'disease_host_species' => DB::table('disease_host_species')->count(),
        ];
    }

    /**
     * A miniature stand-in for the real phpMyAdmin dump.
     */
    private function dump(): string
    {
        $statements = [];

        $statements[] = 'CREATE TABLE `body_systems` (`id` bigint(20) UNSIGNED NOT NULL, `canonical_name` varchar(255) NOT NULL, `display_name` varchar(255) DEFAULT NULL) ENGINE=InnoDB;';

        $statements[] = "INSERT INTO `body_systems` (`id`, `canonical_name`, `display_name`) VALUES\n"
            ."(1,'respiratory','Respiratory System'),\n"
            ."(2,'digestive','Digestive System');";

        $statements[] = 'CREATE TABLE `anatomical_structures` (`id` bigint(20) UNSIGNED NOT NULL, `body_system_id` bigint(20) UNSIGNED NOT NULL, `parent_id` bigint(20) UNSIGNED DEFAULT NULL, `canonical_name` varchar(255) NOT NULL, `display_name` varchar(255) DEFAULT NULL) ENGINE=InnoDB;';

        $statements[] = "INSERT INTO `anatomical_structures` (`id`, `body_system_id`, `parent_id`, `canonical_name`, `display_name`) VALUES\n"
            ."(1,1,NULL,'respiratory tract','Respiratory tract'),\n"
            ."(2,1,1,'upper respiratory tract','Upper respiratory tract');";

        $statements[] = 'CREATE TABLE `species` (`id` char(26) NOT NULL, `name` varchar(255) NOT NULL) ENGINE=InnoDB;';

        $statements[] = "INSERT INTO `species` (`id`, `name`) VALUES\n"
            ."('01AAAAAAAAAAAAAAAAAAAAAAAA','Chicken'),\n"
            ."('01BBBBBBBBBBBBBBBBBBBBBBBB','Turkey');";

        $statements[] = 'CREATE TABLE `diseases` (`id` char(26) NOT NULL, `name` varchar(255) NOT NULL, `slug` varchar(255) NOT NULL, `description` text DEFAULT NULL, `zoonotic` tinyint(1) NOT NULL DEFAULT 0, `deleted_at` timestamp NULL DEFAULT NULL) ENGINE=InnoDB;';

        $statements[] = "INSERT INTO `diseases` (`id`, `name`, `slug`, `description`, `zoonotic`) VALUES\n"
            ."('01CCCCCCCCCCCCCCCCCCCCCCCC','Newcastle Disease','newcastle-disease','A viral disease of poultry, it''s highly contagious.',0);";

        $statements[] = "CREATE TABLE `findings` (`id` char(26) NOT NULL, `canonical_name` varchar(255) NOT NULL, `display_name` varchar(255) NOT NULL, `slug` varchar(255) DEFAULT NULL, `type` enum('clinical_sign','gross_lesion') NOT NULL, `body_system` varchar(255) DEFAULT NULL) ENGINE=InnoDB;";

        // The same finding spelled three ways, plus a NULL slug on purpose.
        $statements[] = "INSERT INTO `findings` (`id`, `canonical_name`, `display_name`, `slug`, `type`, `body_system`) VALUES\n"
            ."('01DDDDDDDDDDDDDDDDDDDDDDDD','sudden death','Sudden death',NULL,'clinical_sign','general_systemic'),\n"
            ."('01EEEEEEEEEEEEEEEEEEEEEEEE','sudden_death','Sudden_death',NULL,'clinical_sign','general_systemic'),\n"
            ."('01FFFFFFFFFFFFFFFFFFFFFFFFFF','sudden-death','Sudden-Death',NULL,'clinical_sign','general_systemic'),\n"
            ."('01000000000000000000000001','reduced egg production','Reduced_egg_production',NULL,'clinical_sign','reproductive'),\n"
            ."('01000000000000000000000002','ruffled_feathers','Ruffled_feathers',NULL,'clinical_sign','integumentary'),\n"
            ."('01000000000000000000000003','enlarged_spleen','Enlarged_spleen',NULL,'gross_lesion',NULL);";

        $statements[] = "CREATE TABLE `disease_finding` (`id` bigint(20) UNSIGNED NOT NULL, `disease_id` char(26) NOT NULL, `finding_id` char(26) NOT NULL, `weight` tinyint(3) UNSIGNED NOT NULL DEFAULT 5, `specificity` enum('low','medium','high','very_high') NOT NULL DEFAULT 'medium', `is_pathognomonic` tinyint(1) NOT NULL DEFAULT 0, `required_for_diagnosis` tinyint(1) NOT NULL DEFAULT 0) ENGINE=InnoDB;";

        // Two of the three "sudden death" rows point at the same disease, which
        // is what forces the pivot to collapse them.
        $statements[] = "INSERT INTO `disease_finding` (`id`, `disease_id`, `finding_id`, `weight`, `specificity`, `is_pathognomonic`, `required_for_diagnosis`) VALUES\n"
            ."(1,'01CCCCCCCCCCCCCCCCCCCCCCCC','01DDDDDDDDDDDDDDDDDDDDDDDD',10,'very_high',1,1),\n"
            ."(2,'01CCCCCCCCCCCCCCCCCCCCCCCC','01EEEEEEEEEEEEEEEEEEEEEEEE',10,'very_high',1,1),\n"
            ."(3,'01CCCCCCCCCCCCCCCCCCCCCCCC','01FFFFFFFFFFFFFFFFFFFFFFFFFF',10,'very_high',1,1),\n"
            ."(4,'01CCCCCCCCCCCCCCCCCCCCCCCC','01000000000000000000000001',8,'medium',0,0),\n"
            ."(5,'01CCCCCCCCCCCCCCCCCCCCCCCC','01000000000000000000000002',7,'high',0,0),\n"
            ."(6,'01CCCCCCCCCCCCCCCCCCCCCCCC','01000000000000000000000003',8,'high',0,0);";

        $statements[] = 'CREATE TABLE `disease_species` (`id` bigint(20) UNSIGNED NOT NULL, `disease_id` char(26) NOT NULL, `species_id` char(26) NOT NULL) ENGINE=InnoDB;';

        $statements[] = "INSERT INTO `disease_species` (`id`, `disease_id`, `species_id`) VALUES\n"
            ."(1,'01CCCCCCCCCCCCCCCCCCCCCCCC','01AAAAAAAAAAAAAAAAAAAAAAAA'),\n"
            ."(2,'01CCCCCCCCCCCCCCCCCCCCCCCC','01BBBBBBBBBBBBBBBBBBBBBBBB');";

        return "-- phpMyAdmin SQL Dump\nSET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n\n"
            .implode("\n\n", $statements)."\n";
    }
}
