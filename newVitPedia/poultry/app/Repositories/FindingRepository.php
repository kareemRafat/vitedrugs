<?php

namespace App\Repositories;

use App\Models\Finding;
use App\Support\TerminologyNormalizer;

class FindingRepository
{
    public function __construct(
        protected TerminologyNormalizer $normalizer
    ) {
    }

    public function firstOrCreate(
        string $name,
        string $type = 'clinical_sign',
        ?string $bodySystem = null
    ): Finding {

        $canonical = $this->normalizer->normalize($name);

        return Finding::firstOrCreate(
            [
                'canonical_name' => $canonical,
            ],
            [
                'display_name' => ucwords($canonical),
                'type' => $type,
                'body_system' => $bodySystem,
            ]
        );
    }
}