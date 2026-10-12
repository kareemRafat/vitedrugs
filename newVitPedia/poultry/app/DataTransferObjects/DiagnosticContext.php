<?php

namespace App\DataTransferObjects;

class DiagnosticContext
{
    public function __construct(

        /*
        |--------------------------------------------------------------------------
        | Clinical Information
        |--------------------------------------------------------------------------
        */

        public array $findings = [],

        public array $lesions = [],

        /*
        |--------------------------------------------------------------------------
        | Bird Information
        |--------------------------------------------------------------------------
        */

        public ?string $speciesId = null,

        public ?int $ageDays = null,

        public ?bool $vaccinated = null,

        /*
        |--------------------------------------------------------------------------
        | Management / Biosecurity
        |--------------------------------------------------------------------------
        */

        public bool $wetLitter = false,

        public bool $poorVentilation = false,

        public bool $mixedSpecies = false,

        public bool $newBirdsIntroduced = false,

        public ?bool $feedRecentlyChanged = null,

        /*
        |--------------------------------------------------------------------------
        | Mortality Information
        |--------------------------------------------------------------------------
        */

        public ?float $mortalityRate = null,

        public ?string $mortalityPattern = null,

        public bool $suddenDeaths = false,

        public ?int $peakDay = null,

        /*
        |--------------------------------------------------------------------------
        | Geographic Information
        |--------------------------------------------------------------------------
        */

        public ?string $country = 'Egypt',

        public ?string $region = null,

        public ?int $month = null,

        /*
        |--------------------------------------------------------------------------
        | Flock Information
        |--------------------------------------------------------------------------
        */

        public ?int $flockSize = null,

        public ?int $affectedBirds = null,

        public ?int $deadBirds = null,

        public ?float $productionDrop = null,

        public ?string $farmType = null,

        public ?string $waterSource = null,

        /*
        |--------------------------------------------------------------------------
        | Outbreak Timeline
        |--------------------------------------------------------------------------
        */

        public ?int $outbreakDurationDays = null,

        public ?string $outbreakSpeed = null,

        /*
        |--------------------------------------------------------------------------
        | Environmental Factors
        |--------------------------------------------------------------------------
        */

        public ?float $houseTemperature = null,

        public ?float $houseHumidity = null,

        public ?string $season = null,

        /*
        |--------------------------------------------------------------------------
        | Vaccination History
        |--------------------------------------------------------------------------
        */

        public array $vaccinesAdministered = [],

        /*
        |--------------------------------------------------------------------------
        | Laboratory Information (Future Use)
        |--------------------------------------------------------------------------
        */

        public array $laboratoryFindings = [],

    ) {
    }
}