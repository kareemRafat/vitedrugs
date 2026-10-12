<?php

namespace Database\Seeders;

use App\Models\LargeAnimals\AnatomicalStructure;
use App\Models\LargeAnimals\ClinicalSign;
use App\Models\LargeAnimals\Finding;
use App\Models\LargeAnimals\Modifier;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use RuntimeException;

class ClinicalSignSeeder extends Seeder
{
    /**
     * A clinical sign is identified by its composition of finding, modifier and
     * anatomical structure, because that triple is what the database makes
     * unique. Two rows sharing a finding and an anatomical structure are the
     * same sign as far as the schema is concerned, so genuine distinctions
     * (tongue vs oral vesicle, profuse vs hemorrhagic diarrhea) are expressed
     * by giving each its own finding or its own anatomical structure.
     *
     * @var list<array{canonical_name: string, display_name: string, display_name_ar: string, finding: string, anatomy: string|null}>
     */
    private const SIGNS = [
        ['canonical_name' => 'pyrexia', 'display_name' => 'Pyrexia', 'display_name_ar' => 'الحُمّى', 'finding' => 'pyrexia', 'anatomy' => null],
        ['canonical_name' => 'oral vesicle', 'display_name' => 'Oral vesicle', 'display_name_ar' => 'حويصلة فموية', 'finding' => 'vesicle', 'anatomy' => 'oral_cavity'],
        ['canonical_name' => 'tongue vesicle', 'display_name' => 'Tongue vesicle', 'display_name_ar' => 'حويصلة اللسان', 'finding' => 'vesicle', 'anatomy' => 'tongue'],
        ['canonical_name' => 'muzzle vesicle', 'display_name' => 'Muzzle vesicle', 'display_name_ar' => 'حويصلة المنخر', 'finding' => 'vesicle', 'anatomy' => 'muzzle'],
        ['canonical_name' => 'interdigital vesicle', 'display_name' => 'Interdigital vesicle', 'display_name_ar' => 'حويصلة بين الأصابع', 'finding' => 'vesicle', 'anatomy' => 'interdigital_space'],
        ['canonical_name' => 'coronary band vesicle', 'display_name' => 'Coronary band vesicle', 'display_name_ar' => 'حويصلة التاج', 'finding' => 'vesicle', 'anatomy' => 'coronary_band'],
        ['canonical_name' => 'hypersalivation', 'display_name' => 'Hypersalivation', 'display_name_ar' => 'فرط اللعاب', 'finding' => 'hypersalivation', 'anatomy' => null],
        ['canonical_name' => 'lameness', 'display_name' => 'Lameness', 'display_name_ar' => 'العرج', 'finding' => 'lameness', 'anatomy' => 'hooves'],
        ['canonical_name' => 'hoof sloughing', 'display_name' => 'Hoof sloughing', 'display_name_ar' => 'انفصال الحافر', 'finding' => 'hoof sloughing', 'anatomy' => 'hooves'],
        ['canonical_name' => 'depression', 'display_name' => 'Depression', 'display_name_ar' => 'الخمول والاكتئاب', 'finding' => 'depression', 'anatomy' => null],
        ['canonical_name' => 'inappetence', 'display_name' => 'Inappetence', 'display_name_ar' => 'فقدان الشهية', 'finding' => 'inappetence', 'anatomy' => null],
        ['canonical_name' => 'oral erosion', 'display_name' => 'Oral erosion', 'display_name_ar' => 'تآكل فموي', 'finding' => 'erosion', 'anatomy' => 'oral_cavity'],
        ['canonical_name' => 'diarrhea', 'display_name' => 'Diarrhea', 'display_name_ar' => 'الإسهال', 'finding' => 'diarrhea', 'anatomy' => 'intestines'],
        ['canonical_name' => 'hemorrhagic diarrhea', 'display_name' => 'Hemorrhagic diarrhea', 'display_name_ar' => 'إسهال نزفي', 'finding' => 'hemorrhagic diarrhea', 'anatomy' => 'intestines'],
        ['canonical_name' => 'profuse diarrhea', 'display_name' => 'Profuse diarrhea', 'display_name_ar' => 'إسهال غزير', 'finding' => 'profuse diarrhea', 'anatomy' => 'intestines'],
        ['canonical_name' => 'dehydration', 'display_name' => 'Dehydration', 'display_name_ar' => 'الجفاف', 'finding' => 'dehydration', 'anatomy' => null],
        ['canonical_name' => 'weight loss', 'display_name' => 'Weight loss', 'display_name_ar' => 'فقدان الوزن', 'finding' => 'weight loss', 'anatomy' => null],
        ['canonical_name' => 'anemia', 'display_name' => 'Anemia', 'display_name_ar' => 'فقر الدم', 'finding' => 'anemia', 'anatomy' => null],
        ['canonical_name' => 'mastitis', 'display_name' => 'Mastitis', 'display_name_ar' => 'التهاب الضرع', 'finding' => 'mastitis', 'anatomy' => 'mammary_gland'],
        ['canonical_name' => 'reduced milk production', 'display_name' => 'Reduced milk production', 'display_name_ar' => 'انخفاض إنتاج الحليب', 'finding' => 'reduced milk production', 'anatomy' => 'mammary_gland'],
        ['canonical_name' => 'mammary gland heat', 'display_name' => 'Mammary gland heat', 'display_name_ar' => 'سخونة الغدة الثديية', 'finding' => 'mammary gland heat', 'anatomy' => 'mammary_gland'],
        ['canonical_name' => 'teat vesicle', 'display_name' => 'Teat vesicle', 'display_name_ar' => 'حويصلة الحلمة', 'finding' => 'vesicle', 'anatomy' => 'teats'],
        ['canonical_name' => 'dyspnea', 'display_name' => 'Dyspnea', 'display_name_ar' => 'ضيق التنفس', 'finding' => 'dyspnea', 'anatomy' => 'lungs'],
        ['canonical_name' => 'respiratory distress', 'display_name' => 'Respiratory distress', 'display_name_ar' => 'اضطراب تنفسي', 'finding' => 'respiratory distress', 'anatomy' => 'lungs'],
        ['canonical_name' => 'tachypnea', 'display_name' => 'Tachypnea', 'display_name_ar' => 'تسرّع التنفس', 'finding' => 'tachypnea', 'anatomy' => 'lungs'],
        ['canonical_name' => 'nasal discharge', 'display_name' => 'Nasal discharge', 'display_name_ar' => 'إفرازات أنفية', 'finding' => 'nasal discharge', 'anatomy' => 'nasal_cavity'],
        ['canonical_name' => 'cough', 'display_name' => 'Cough', 'display_name_ar' => 'السعال', 'finding' => 'cough', 'anatomy' => 'trachea'],
        ['canonical_name' => 'lacrimation', 'display_name' => 'Lacrimation', 'display_name_ar' => 'الدمعان', 'finding' => 'lacrimation', 'anatomy' => null],
        ['canonical_name' => 'weakness', 'display_name' => 'Weakness', 'display_name_ar' => 'الضعف العام', 'finding' => 'weakness', 'anatomy' => null],
        ['canonical_name' => 'sudden death', 'display_name' => 'Sudden death', 'display_name_ar' => 'الموت المفاجئ', 'finding' => 'sudden death', 'anatomy' => null],
        ['canonical_name' => 'abortion', 'display_name' => 'Abortion', 'display_name_ar' => 'الإجهاض', 'finding' => 'abortion', 'anatomy' => 'uterus'],
        ['canonical_name' => 'tachycardia', 'display_name' => 'Tachycardia', 'display_name_ar' => 'تسرّع القلب', 'finding' => 'tachycardia', 'anatomy' => 'heart'],
        ['canonical_name' => 'recumbency', 'display_name' => 'Recumbency', 'display_name_ar' => 'الاستلقاء', 'finding' => 'recumbency', 'anatomy' => 'muscles'],
        ['canonical_name' => 'lymphadenitis', 'display_name' => 'Lymphadenitis', 'display_name_ar' => 'التهاب العقد اللمفاوية', 'finding' => 'lymphadenitis', 'anatomy' => 'lymph_nodes'],
        ['canonical_name' => 'submandibular lymphadenitis', 'display_name' => 'Submandibular lymphadenitis', 'display_name_ar' => 'التهاب العقد تحت الفك', 'finding' => 'submandibular lymphadenitis', 'anatomy' => 'lymph_nodes'],
        ['canonical_name' => 'infertility', 'display_name' => 'Infertility', 'display_name_ar' => 'العقم', 'finding' => 'infertility', 'anatomy' => 'uterus'],
        ['canonical_name' => 'temporary infertility', 'display_name' => 'Temporary infertility', 'display_name_ar' => 'عقم مؤقت', 'finding' => 'temporary infertility', 'anatomy' => 'uterus'],
    ];

    public function run(): void
    {
        $modifier = Modifier::where('canonical_name', 'acute')->first();

        $findings = Finding::query()->pluck('id', 'canonical_name');
        $structures = AnatomicalStructure::query()->pluck('id', 'canonical_name');

        $this->guardAgainstUnresolvedNames($findings, $structures);

        $created = 0;
        $updated = 0;

        foreach (self::SIGNS as $sign) {
            $attributes = [
                'finding_id' => $findings[$sign['finding']],
                'modifier_id' => $modifier?->id,
                'anatomical_structure_id' => $sign['anatomy'] === null
                    ? null
                    : $structures[$sign['anatomy']],
            ];

            $existing = ClinicalSign::query()->where($attributes)->first();

            if ($existing !== null) {
                // Only write when something actually differs, so re-seeding
                // does not bump updated_at on every row.
                if ($existing->display_name !== $sign['display_name']
                    || $existing->canonical_name !== $sign['canonical_name']
                    || $existing->display_name_ar !== $sign['display_name_ar']) {
                    $existing->update([
                        'canonical_name' => $sign['canonical_name'],
                        'display_name' => $sign['display_name'],
                        'display_name_ar' => $sign['display_name_ar'],
                    ]);

                    $updated++;
                }

                continue;
            }

            ClinicalSign::query()->create([
                ...$attributes,
                'canonical_name' => $sign['canonical_name'],
                'display_name' => $sign['display_name'],
                'display_name_ar' => $sign['display_name_ar'],
                'stage' => 'clinical',
            ]);

            $created++;
        }

        $this->command?->info(sprintf(
            'Clinical signs: %d created, %d updated.',
            $created,
            $updated,
        ));
    }

    /**
     * Fail loudly rather than writing a clinical sign with a null finding.
     *
     * @param  Collection<string, mixed>  $findings
     * @param  Collection<string, mixed>  $structures
     */
    private function guardAgainstUnresolvedNames($findings, $structures): void
    {
        $missingFindings = [];
        $missingStructures = [];

        foreach (self::SIGNS as $sign) {
            if (! $findings->has($sign['finding'])) {
                $missingFindings[$sign['finding']] = $sign['canonical_name'];
            }

            if ($sign['anatomy'] !== null && ! $structures->has($sign['anatomy'])) {
                $missingStructures[$sign['anatomy']] = $sign['canonical_name'];
            }
        }

        if ($missingFindings !== [] || $missingStructures !== []) {
            $lines = [];

            foreach ($missingFindings as $name => $sign) {
                $lines[] = "  finding: {$name} (needed by \"{$sign}\")";
            }

            foreach ($missingStructures as $name => $sign) {
                $lines[] = "  anatomical structure: {$name} (needed by \"{$sign}\")";
            }

            throw new RuntimeException(
                "ClinicalSignSeeder could not resolve:\n".implode("\n", $lines)
            );
        }
    }
}
