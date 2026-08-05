<?php

namespace Database\Seeders;

use App\Models\Disease;
use App\Models\LargeAnimals\ClinicalSign;
use Illuminate\Database\Seeder;

class DiseaseClinicalSignSeeder extends Seeder
{
    public function run(): void
    {
        if (Disease::count() === 0) {
            return;
        }

        $byName = [
            'Foot and Mouth Disease' => [
                ['pyrexia', 9, true, true, false],
                ['oral vesicle', 10, true, true, true],
                ['tongue vesicle', 10, true, true, true],
                ['muzzle vesicle', 9, true, false, true],
                ['interdigital vesicle', 9, true, false, true],
                ['coronary band vesicle', 9, true, false, true],
                ['hypersalivation', 8, true, false, false],
                ['lameness', 7, true, false, false],
                ['hoof sloughing', 8, true, false, false],
                ['depression', 5, false, false, false],
                ['inappetence', 5, false, false, false],
                ['oral erosion', 6, true, false, false],
            ],
            'Coccidiosis' => [
                ['diarrhea', 9, true, true, false],
                ['hemorrhagic diarrhea', 9, true, false, false],
                ['dehydration', 7, true, false, false],
                ['weight loss', 6, true, false, false],
                ['depression', 5, false, false, false],
                ['inappetence', 5, false, false, false],
                ['pyrexia', 5, false, false, false],
                ['anemia', 5, false, false, false],
            ],
            'Mastitis' => [
                ['mastitis', 10, true, true, true],
                ['reduced milk production', 9, true, true, false],
                ['mammary gland heat', 8, true, false, false],
                ['teat vesicle', 7, false, false, false],
                ['pyrexia', 6, false, false, false],
                ['inappetence', 4, false, false, false],
                ['depression', 4, false, false, false],
            ],
            'Bacterial pneumonia' => [
                ['dyspnea', 9, true, true, false],
                ['respiratory distress', 8, true, false, false],
                ['tachypnea', 8, true, false, false],
                ['nasal discharge', 7, true, false, false],
                ['cough', 7, true, false, false],
                ['pyrexia', 7, true, false, false],
                ['depression', 5, false, false, false],
                ['inappetence', 5, false, false, false],
            ],
            'Bronchopneumonia' => [
                ['dyspnea', 9, true, true, false],
                ['cough', 8, true, false, false],
                ['tachypnea', 7, true, false, false],
                ['pyrexia', 7, true, false, false],
                ['nasal discharge', 7, true, false, false],
                ['depression', 5, false, false, false],
            ],
            'Pneumonia' => [
                ['dyspnea', 9, true, true, false],
                ['cough', 8, true, false, false],
                ['respiratory distress', 8, true, false, false],
                ['tachypnea', 7, true, false, false],
                ['pyrexia', 6, true, false, false],
                ['nasal discharge', 6, true, false, false],
            ],
            'CRD' => [
                ['cough', 8, true, false, false],
                ['dyspnea', 7, true, false, false],
                ['nasal discharge', 7, true, false, false],
                ['lacrimation', 6, true, false, false],
                ['weight loss', 6, false, false, false],
            ],
            'Colibacillosis' => [
                ['diarrhea', 9, true, true, false],
                ['dehydration', 7, true, false, false],
                ['pyrexia', 6, false, false, false],
                ['depression', 5, false, false, false],
                ['weakness', 5, false, false, false],
                ['sudden death', 6, false, false, false],
            ],
            'Colisepticaemia' => [
                ['pyrexia', 8, true, false, false],
                ['depression', 6, false, false, false],
                ['sudden death', 7, false, false, false],
                ['diarrhea', 6, false, false, false],
                ['dehydration', 6, false, false, false],
            ],
            'Salmonellosis' => [
                ['diarrhea', 9, true, true, false],
                ['profuse diarrhea', 8, true, false, false],
                ['pyrexia', 8, true, false, false],
                ['dehydration', 8, true, false, false],
                ['depression', 6, false, false, false],
                ['inappetence', 6, false, false, false],
                ['abortion', 5, false, false, false],
            ],
            'Mycoplasmosis' => [
                ['dyspnea', 7, true, false, false],
                ['cough', 6, true, false, false],
                ['nasal discharge', 6, true, false, false],
                ['reduced milk production', 6, false, false, false],
                ['mastitis', 6, false, false, false],
            ],
            'Septicaemia' => [
                ['pyrexia', 9, true, true, false],
                ['depression', 7, true, false, false],
                ['tachycardia', 7, true, false, false],
                ['tachypnea', 7, true, false, false],
                ['sudden death', 6, false, false, false],
                ['recumbency', 5, false, false, false],
            ],
            'Septicemia' => [
                ['pyrexia', 9, true, true, false],
                ['depression', 7, true, false, false],
                ['tachycardia', 7, true, false, false],
                ['tachypnea', 7, true, false, false],
                ['sudden death', 6, false, false, false],
                ['recumbency', 5, false, false, false],
            ],
            'Diarrhea' => [
                ['diarrhea', 10, true, true, false],
                ['dehydration', 7, true, false, false],
                ['weakness', 5, false, false, false],
                ['inappetence', 5, false, false, false],
            ],
            'Enteritis' => [
                ['diarrhea', 9, true, true, false],
                ['dehydration', 6, true, false, false],
                ['pyrexia', 6, false, false, false],
                ['inappetence', 5, false, false, false],
            ],
            'Gastroenteritis' => [
                ['diarrhea', 9, true, true, false],
                ['dehydration', 6, true, false, false],
                ['pyrexia', 6, false, false, false],
                ['inappetence', 5, false, false, false],
            ],
            'Strangles' => [
                ['lymphadenitis', 9, true, true, true],
                ['submandibular lymphadenitis', 9, true, true, true],
                ['pyrexia', 7, true, false, false],
                ['nasal discharge', 7, true, false, false],
                ['dyspnea', 6, false, false, false],
                ['inappetence', 5, false, false, false],
            ],
            'Metritis' => [
                ['pyrexia', 7, true, false, false],
                ['inappetence', 5, false, false, false],
                ['depression', 5, false, false, false],
                ['reduced milk production', 6, true, false, false],
            ],
            'Cervicitis' => [
                ['infertility', 7, true, false, false],
                ['temporary infertility', 6, true, false, false],
                ['pyrexia', 5, false, false, false],
            ],
            'Foot rot' => [
                ['lameness', 9, true, true, false],
                ['hoof sloughing', 7, true, false, false],
                ['pyrexia', 5, false, false, false],
                ['inappetence', 5, false, false, false],
            ],
        ];

        foreach ($byName as $diseaseName => $signs) {
            $disease = Disease::where('name', $diseaseName)->first();

            if (! $disease) {
                continue;
            }

            foreach ($signs as [$canonical, $weight, $isSpecific, $isRequired, $isPathognomonic]) {
                $sign = ClinicalSign::where('canonical_name', $canonical)->first();

                if (! $sign) {
                    continue;
                }

                $disease->clinicalSigns()->syncWithoutDetaching([
                    $sign->id => [
                        'weight' => $weight,
                        'is_specific' => $isSpecific,
                        'is_required' => $isRequired,
                        'is_pathognomonic' => $isPathognomonic,
                    ],
                ]);
            }
        }
    }
}
