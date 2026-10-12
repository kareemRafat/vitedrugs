<?php

namespace Tests\Feature\LargeAnimals;

use App\Models\LargeAnimals\ClinicalSign;
use Database\Seeders\AnatomicalStructureSeeder;
use Database\Seeders\BodySystemSeeder;
use Database\Seeders\ClinicalSignSeeder;
use Database\Seeders\FindingSeeder;
use Database\Seeders\ModifierSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Test;
use RuntimeException;
use Tests\TestCase;

/**
 * Clinical signs are unique on (finding, modifier, anatomical structure), so a
 * seeder that describes two signs sharing a finding and an anatomy will hit a
 * duplicate-entry error, and one that leaves a finding unset will hit a NOT NULL
 * error. These cover both failure modes.
 */
class ClinicalSignSeederTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            BodySystemSeeder::class,
            AnatomicalStructureSeeder::class,
            FindingSeeder::class,
            ModifierSeeder::class,
        ]);
    }

    #[Test]
    public function it_creates_every_sign_without_violating_the_composition_index(): void
    {
        $this->seed(ClinicalSignSeeder::class);

        $this->assertSame(37, ClinicalSign::query()->count());
    }

    #[Test]
    public function every_sign_has_a_finding(): void
    {
        $this->seed(ClinicalSignSeeder::class);

        $this->assertSame(0, ClinicalSign::query()->whereNull('finding_id')->count());
    }

    #[Test]
    public function no_two_signs_share_a_composition(): void
    {
        $this->seed(ClinicalSignSeeder::class);

        $duplicates = DB::table('clinical_signs')
            ->select('finding_id', 'modifier_id', 'anatomical_structure_id')
            ->groupBy('finding_id', 'modifier_id', 'anatomical_structure_id')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        $this->assertCount(0, $duplicates, 'Two clinical signs collapsed onto one composition.');
    }

    #[Test]
    public function signs_that_look_similar_are_still_distinct_rows(): void
    {
        $this->seed(ClinicalSignSeeder::class);

        $pairs = [
            ['oral vesicle', 'tongue vesicle'],
            ['interdigital vesicle', 'coronary band vesicle'],
            ['diarrhea', 'hemorrhagic diarrhea'],
            ['diarrhea', 'profuse diarrhea'],
            ['dyspnea', 'respiratory distress'],
            ['dyspnea', 'tachypnea'],
            ['mastitis', 'reduced milk production'],
            ['mastitis', 'mammary gland heat'],
        ];

        foreach ($pairs as [$first, $second]) {
            $this->assertSame(
                1,
                ClinicalSign::query()->where('canonical_name', $first)->count(),
                "{$first} should exist exactly once.",
            );

            $this->assertSame(
                1,
                ClinicalSign::query()->where('canonical_name', $second)->count(),
                "{$second} should exist exactly once.",
            );
        }
    }

    #[Test]
    public function it_is_idempotent(): void
    {
        $this->seed(ClinicalSignSeeder::class);

        $before = $this->snapshot();

        $this->seed(ClinicalSignSeeder::class);
        $this->seed(ClinicalSignSeeder::class);

        $this->assertSame($before, $this->snapshot(), 'Re-seeding must not add, remove or rewrite rows.');
    }

    #[Test]
    public function it_matches_on_composition_rather_than_canonical_name(): void
    {
        $this->seed(ClinicalSignSeeder::class);

        // A poultry loader also writes modifier-less signs. One of them must not
        // be mistaken for the curated sign of the same canonical name.
        $sign = ClinicalSign::query()->where('canonical_name', 'pyrexia')->firstOrFail();

        ClinicalSign::query()->create([
            'finding_id' => $sign->finding_id,
            'modifier_id' => null,
            'anatomical_structure_id' => null,
            'canonical_name' => 'pyrexia',
            'display_name' => 'Pyrexia (poultry)',
            'stage' => 'clinical',
        ]);

        $this->seed(ClinicalSignSeeder::class);

        $curated = ClinicalSign::query()
            ->where('canonical_name', 'pyrexia')
            ->whereNotNull('modifier_id')
            ->firstOrFail();

        $poultry = ClinicalSign::query()
            ->where('canonical_name', 'pyrexia')
            ->whereNull('modifier_id')
            ->firstOrFail();

        $this->assertSame('Pyrexia', $curated->display_name, 'The curated sign must not be overwritten.');
        $this->assertSame('Pyrexia (poultry)', $poultry->fresh()->display_name, 'The poultry sign must be left alone.');
    }

    #[Test]
    public function it_fails_loudly_when_a_finding_is_missing(): void
    {
        DB::table('findings')->where('canonical_name', 'dyspnea')->delete();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('dyspnea');

        $this->seed(ClinicalSignSeeder::class);
    }

    #[Test]
    public function it_fails_loudly_when_an_anatomical_structure_is_missing(): void
    {
        DB::table('anatomical_structures')->where('canonical_name', 'hooves')->delete();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('hooves');

        $this->seed(ClinicalSignSeeder::class);
    }

    /**
     * @return array<string, mixed>
     */
    private function snapshot(): array
    {
        return ClinicalSign::query()
            ->orderBy('canonical_name')
            ->get(['canonical_name', 'display_name', 'display_name_ar', 'finding_id', 'modifier_id', 'anatomical_structure_id'])
            ->map(fn (ClinicalSign $sign): array => [
                $sign->canonical_name,
                $sign->display_name,
                $sign->display_name_ar,
                $sign->finding_id,
                $sign->modifier_id,
                $sign->anatomical_structure_id,
            ])
            ->all();
    }
}
