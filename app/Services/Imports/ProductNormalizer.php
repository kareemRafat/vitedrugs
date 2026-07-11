<?php

namespace App\Services\Imports;

class ProductNormalizer
{
    public function normalize(array $item): array
    {
        $item['company'] = strtoupper(
            trim($item['company'] ?? '')
        );

        $item['dosage_form'] = trim(
            $item['dosage_form'] ?? ''
        );

        return $item;
    }
}