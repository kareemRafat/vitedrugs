<?php

namespace App\Services;

use App\Models\Disease;
use App\Repositories\FindingRepository;
use App\Repositories\RiskFactorRepository;
use App\Repositories\SpeciesRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
// use Illuminate\Support\Str;

class DiseaseImporterService
{
    public function __construct(
        protected FindingRepository $findingRepository,
        protected SpeciesRepository $speciesRepository,
        protected RiskFactorRepository $riskFactorRepository,
    ) {}

    public function import(string $path): Disease
    {
        $data = json_decode(File::get($path), true);

        return DB::transaction(function () use ($data) {

            $disease = Disease::updateOrCreate(

                [
                    'slug' => $data['slug']
                ],

                [
                    'name'                => $data['name'],
                    'description'         => $data['description'] ?? null,

                    'agent_type'          => $data['etiology']['agent_type'] ?? null,
                    'agent_name'          => $data['etiology']['agent_name'] ?? null,
                    'family'              => $data['etiology']['family'] ?? null,
                    'genus'               => $data['etiology']['genus'] ?? null,

                    'incubation_period'   => $data['incubation_period'] ?? null,

                    'morbidity_min'       => $data['morbidity']['minimum_percent'] ?? null,
                    'morbidity_max'       => $data['morbidity']['maximum_percent'] ?? null,

                    'mortality_min'       => $data['mortality']['minimum_percent'] ?? null,
                    'mortality_max'       => $data['mortality']['maximum_percent'] ?? null,
                ]
            );

            $this->attachSpecies($disease, $data);

            $this->attachFindings($disease, $data);

            $this->attachRiskFactors($disease, $data);

            $this->attachDiagnosticTests($disease, $data);

            $this->attachAgeGroups($disease, $data);

            return $disease;
        });
    }

    protected function attachSpecies(Disease $disease, array $data): void
    {
        foreach ($data['host_species'] ?? [] as $name) {

            $species = $this->speciesRepository
                ->findByName($name);

            if ($species) {
                $disease->species()->syncWithoutDetaching($species->id);
            }
        }
    }

    protected function attachFindings(Disease $disease, array $data): void
    {
        foreach ($data['clinical_manifestations'] ?? [] as $item) {

            $finding = $this->findingRepository->firstOrCreate(
                $item['canonical_name'],
                'clinical_sign',
                $item['body_system'] ?? null
            );

            $disease->findings()->syncWithoutDetaching([
                $finding->id => [

                    'weight' =>
                    $item['weight'] ?? 5,

                    'specificity' =>
                    $item['specificity'] ?? 'medium',

                    'is_pathognomonic' =>
                    $item['is_pathognomonic'] ?? false,

                    'required_for_diagnosis' =>
                    $item['required_for_diagnosis'] ?? false,

                    'negative_finding' =>
                    $item['negative_finding'] ?? false,

                    'likelihood_ratio_positive' =>
                    $item['likelihood_ratio_positive'] ?? null,

                    'likelihood_ratio_negative' =>
                    $item['likelihood_ratio_negative'] ?? null,
                ]
            ]);
        }

        foreach ($data['gross_lesions'] ?? [] as $item) {

            $finding = $this->findingRepository->firstOrCreate(
                $item['canonical_name'],
                'gross_lesion'
            );

            $disease->findings()->syncWithoutDetaching([
                $finding->id => [

                    'weight' =>
                    $item['weight'] ?? 5,

                    'specificity' =>
                    $item['specificity'] ?? 'medium',

                    'is_pathognomonic' =>
                    $item['is_pathognomonic'] ?? false,

                    'required_for_diagnosis' =>
                    $item['required_for_diagnosis'] ?? false,

                    'negative_finding' =>
                    $item['negative_finding'] ?? false,

                    'likelihood_ratio_positive' =>
                    $item['likelihood_ratio_positive'] ?? null,

                    'likelihood_ratio_negative' =>
                    $item['likelihood_ratio_negative'] ?? null,
                ]
            ]);
        }
    }

    protected function attachRiskFactors(Disease $disease, array $data): void
    {
        foreach ($data['risk_factors'] ?? [] as $name) {

            $factor = $this->riskFactorRepository
                ->findByName($name);

            if ($factor) {
                $disease->riskFactors()->syncWithoutDetaching($factor->id);
            }
        }
    }

    protected function attachDiagnosticTests(
        Disease $disease,
        array $data
    ): void {

        foreach ($data['diagnostic_tests'] ?? [] as $test) {

            $disease->diagnosticTests()->create([
                'test_name' => $test['test_name'],
                'sample'    => $test['sample'] ?? null,
                'purpose'   => $test['purpose'] ?? null,
            ]);
        }
    }

    protected function attachAgeGroups(
        Disease $disease,
        array $data
    ): void {

        foreach ($data['susceptible_age_groups'] ?? [] as $age) {

            $disease->ageGroups()->create([
                'min_days'   => $age['min_days'] ?? null,
                'max_days'   => $age['max_days'] ?? null,
                'description' => $age['description'] ?? null,
            ]);
        }
    }
}
