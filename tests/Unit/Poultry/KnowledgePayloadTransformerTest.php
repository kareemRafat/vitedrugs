<?php

namespace Tests\Unit\Poultry;

use App\Services\Knowledge\DiseaseKnowledgePayloadTransformer;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class KnowledgePayloadTransformerTest extends TestCase
{
    #[Test]
    public function it_always_returns_every_key_even_for_degenerate_input(): void
    {
        foreach ([null, 'string', 42, [], ['disease' => 'not-an-array']] as $input) {
            $payload = DiseaseKnowledgePayloadTransformer::transform($input);

            $this->assertSame(
                DiseaseKnowledgePayloadTransformer::KEYS,
                array_keys($payload),
                'A degenerate payload should still expose every key.',
            );
        }
    }

    #[Test]
    public function it_maps_the_poultry_vocabulary_onto_the_public_page_keys(): void
    {
        $payload = DiseaseKnowledgePayloadTransformer::transform([
            'gross_lesions' => [['canonical_name' => 'splenomegaly', 'display_name' => 'Enlarged spleen']],
            'diagnostic_tests' => [['test_name' => 'Blood smear', 'sample' => 'Peripheral blood', 'purpose' => 'Detect parasites.']],
            'prevention_and_control' => ['Control tick infestation.'],
            'clinical_manifestations' => [['canonical_name' => 'anemia', 'weight' => 9, 'specificity' => 'high']],
        ]);

        $this->assertSame('splenomegaly', $payload['postmortem_findings'][0]['canonical_name']);
        $this->assertSame('Blood smear', $payload['diagnosis'][0]['method']);
        $this->assertSame('Peripheral blood', $payload['diagnosis'][0]['sample']);
        $this->assertSame('Control tick infestation.', $payload['prevention_control'][0]['description']);
        $this->assertSame('anemia', $payload['clinical_signs'][0]['canonical_name']);
    }

    #[Test]
    public function it_adds_the_eight_newly_mapped_keys(): void
    {
        $payload = DiseaseKnowledgePayloadTransformer::transform([
            'microscopic_lesions' => ['Trophozoites inside erythrocytes'],
            'pathognomonic_findings' => ['Caseous core in the bursa'],
            'field_diagnosis_clues' => ['Drop in egg production'],
            'epidemiological_clues' => ['New birds introduced 10 days prior'],
            'important_organs_affected' => ['spleen', 'liver'],
            'differential_diagnoses' => ['Plasmodiosis'],
        ]);

        $this->assertSame(['Trophozoites inside erythrocytes'], $payload['microscopic_lesions']);
        $this->assertSame(['Caseous core in the bursa'], $payload['pathognomonic_findings']);
        $this->assertSame(['Drop in egg production'], $payload['field_diagnosis_clues']);
        $this->assertSame(['New birds introduced 10 days prior'], $payload['epidemiological_clues']);
        $this->assertSame(['spleen', 'liver'], $payload['important_organs_affected']);
        $this->assertSame(['Plasmodiosis'], $payload['differential_diagnoses']);
    }

    #[Test]
    public function it_normalizes_morbidity_and_mortality_into_a_range(): void
    {
        $payload = DiseaseKnowledgePayloadTransformer::transform([
            'morbidity' => ['minimum_percent' => 40, 'maximum_percent' => 60, 'notes' => 'Outbreak scale.'],
            'mortality' => ['minimum_percent' => null, 'maximum_percent' => 90, 'notes' => null],
        ]);

        $this->assertSame(40, $payload['morbidity']['min']);
        $this->assertSame(60, $payload['morbidity']['max']);
        $this->assertSame('Outbreak scale.', $payload['morbidity']['notes']);
        $this->assertSame([], $payload['morbidity']['breakdown']);

        $this->assertNull($payload['mortality']['min']);
        $this->assertSame(90, $payload['mortality']['max']);
    }

    #[Test]
    public function it_preserves_per_age_group_mortality_breakdowns(): void
    {
        $payload = DiseaseKnowledgePayloadTransformer::transform([
            'mortality' => [
                'first_week_old_ducklings' => 'up to 95%',
                'one_to_three_weeks_old' => 'up to 50%',
            ],
        ]);

        $this->assertNull($payload['mortality']['min']);
        $this->assertNull($payload['mortality']['max']);
        $this->assertSame(['up to 95%', 'up to 50%'], $payload['mortality']['breakdown']);
    }

    #[Test]
    public function it_swaps_an_inverted_range(): void
    {
        $payload = DiseaseKnowledgePayloadTransformer::transform([
            'mortality' => ['minimum_percent' => 90, 'maximum_percent' => 10],
        ]);

        $this->assertSame(10, $payload['mortality']['min']);
        $this->assertSame(90, $payload['mortality']['max']);
    }

    #[Test]
    public function it_preserves_lesion_detail_from_the_source(): void
    {
        $payload = DiseaseKnowledgePayloadTransformer::transform([
            'gross_lesions' => [[
                'canonical_name' => 'hepatic_degeneration',
                'display_name' => 'Degeneration of the liver',
                'anatomical_location' => 'liver',
                'weight' => 8,
                'specificity' => 'high',
                'is_pathognomonic' => false,
                'notes' => 'Liver appears grayish-yellow.',
            ]],
        ]);

        $lesion = $payload['postmortem_findings'][0];

        $this->assertSame('liver', $lesion['anatomical_location']);
        $this->assertSame(8, $lesion['weight']);
        $this->assertSame('high', $lesion['specificity']);
        $this->assertSame('Liver appears grayish-yellow.', $lesion['notes']);
    }

    #[Test]
    public function it_treats_high_specificity_as_specific(): void
    {
        $payload = DiseaseKnowledgePayloadTransformer::transform([
            'clinical_manifestations' => [
                ['canonical_name' => 'a', 'specificity' => 'very_high'],
                ['canonical_name' => 'b', 'specificity' => 'high'],
                ['canonical_name' => 'c', 'specificity' => 'medium'],
                ['canonical_name' => 'd', 'specificity' => 'low'],
            ],
        ]);

        $this->assertTrue($payload['clinical_signs'][0]['is_specific']);
        $this->assertTrue($payload['clinical_signs'][1]['is_specific']);
        $this->assertFalse($payload['clinical_signs'][2]['is_specific']);
        $this->assertFalse($payload['clinical_signs'][3]['is_specific']);
    }

    #[Test]
    public function it_drops_blank_and_duplicate_scalar_entries(): void
    {
        $payload = DiseaseKnowledgePayloadTransformer::transform([
            'microscopic_lesions' => ['  Necrotic foci  ', 'Necrotic foci', '', '   '],
        ]);

        $this->assertSame(['Necrotic foci'], $payload['microscopic_lesions']);
    }

    #[Test]
    public function it_accepts_plain_string_lists_for_entry_style_keys(): void
    {
        $payload = DiseaseKnowledgePayloadTransformer::transform([
            'gross_lesions' => ['Enlarged spleen'],
            'prevention_and_control' => ['Vaccinate'],
        ]);

        $this->assertSame([['description' => 'Enlarged spleen']], $payload['postmortem_findings']);
        $this->assertSame([['description' => 'Vaccinate']], $payload['prevention_control']);
    }

    #[Test]
    public function it_unwraps_a_disease_keyed_document(): void
    {
        $payload = DiseaseKnowledgePayloadTransformer::transform([
            'disease' => ['gross_lesions' => [['canonical_name' => 'x']], 'slug' => 'x'],
        ]);

        $this->assertSame('x', $payload['postmortem_findings'][0]['canonical_name']);
    }

    #[Test]
    public function it_leaves_an_already_flat_payload_untouched(): void
    {
        $payload = DiseaseKnowledgePayloadTransformer::transform([
            'postmortem_findings' => [['canonical_name' => 'y']],
        ]);

        $this->assertSame('y', $payload['postmortem_findings'][0]['canonical_name']);
    }
}
