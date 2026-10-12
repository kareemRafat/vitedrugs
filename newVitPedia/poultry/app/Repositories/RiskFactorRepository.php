<?php

namespace App\Repositories;

use App\Models\RiskFactor;

class RiskFactorRepository
{
    public function findByName(string $name): ?RiskFactor
    {
        return RiskFactor::where('name', $name)->first();
    }
}