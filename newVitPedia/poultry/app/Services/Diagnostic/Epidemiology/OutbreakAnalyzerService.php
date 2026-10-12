<?php

namespace App\Services\Diagnostic\Epidemiology;

class OutbreakAnalyzerService
{
    public function analyze(array $data): array
    {
        $morbidity = 0;
        $mortality = 0;
        $fatality = 0;

        if ($data['flock_size'] > 0) {

            $morbidity = round(
                ($data['affected_birds']
                    / $data['flock_size']) * 100,
                2
            );

            $mortality = round(
                ($data['dead_birds']
                    / $data['flock_size']) * 100,
                2
            );
        }

        if ($data['affected_birds'] > 0) {

            $fatality = round(
                ($data['dead_birds']
                    / $data['affected_birds']) * 100,
                2
            );
        }

        $alerts = [];

        if ($mortality > 20) {

            $alerts[] =
                'High mortality outbreak';
        }

        if (
            !$data['vaccinated']
        ) {

            $alerts[] =
                'Vaccination gap detected';
        }

        if (
            $data['new_birds_introduced']
        ) {

            $alerts[] =
                'Recent bird introduction may indicate infectious disease';
        }

        if (
            $data['wet_litter']
        ) {

            $alerts[] =
                'Wet litter predisposes to enteric diseases';
        }

        return [

            'morbidity_rate' => $morbidity,

            'mortality_rate' => $mortality,

            'case_fatality_rate' => $fatality,

            'alerts' => $alerts
        ];
    }
}