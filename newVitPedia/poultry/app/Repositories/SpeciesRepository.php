<?php

namespace App\Repositories;

use App\Models\Species;

class SpeciesRepository
{
    public function findByName(string $name): ?Species
    {
        return Species::where('name', $name)->first();
    }
}