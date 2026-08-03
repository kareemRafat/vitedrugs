<?php

namespace Database\Seeders;

use App\Models\Drugs\VeterinaryProject;
use Illuminate\Database\Seeder;

class VeterinaryProjectSeeder extends Seeder
{
    public function run(): void
    {
        if (VeterinaryProject::exists()) {
            return;
        }

        $projects = [
            [
                'title' => 'Broiler Poultry Farm Feasibility Study',
                'slug' => 'broiler-poultry-farm-feasibility-study',
                'summary' => 'A step-by-step economic feasibility study for a 5,000-bird broiler production unit, including capital, operating costs, and projected returns.',
                'content' => "## Overview\n\nThis feasibility study evaluates a 5,000-bird broiler unit under intensive management.\n\n## Investment\n\n- Land and housing: 40%\n- Day-old chicks: 20%\n- Feed: 30%\n- Labor and utilities: 10%\n\n## Expected Returns\n\nWith an average feed conversion ratio of 1.7 and a 35-day cycle, the projected return on investment is approximately 18-25% per cycle.",
                'project_type' => 'feasibility_study',
                'sector' => 'poultry',
                'featured' => true,
                'is_published' => true,
            ],
            [
                'title' => 'Dairy Cattle Investment Guide',
                'slug' => 'dairy-cattle-investment-guide',
                'summary' => 'An investment guide for establishing a 50-head dairy herd covering genetics, nutrition, housing, and milk marketing.',
                'content' => "## Overview\n\nA guide for investors entering dairy production with a 50-head Holstein herd.\n\n## Key Considerations\n\n- Genetic selection and replacement heifers\n- Balanced ration formulation\n- Milking parlor and cooling infrastructure\n- Milk collection and pricing contracts\n\n## Financial Outlook\n\nThe payback period is typically 5-7 years under stable milk prices.",
                'project_type' => 'investment_guide',
                'sector' => 'ruminant',
                'featured' => true,
                'is_published' => true,
            ],
            [
                'title' => 'Fish Farming Starter Guideline',
                'slug' => 'fish-farming-starter-guideline',
                'summary' => 'Practical guidelines for starting a small-scale tilapia pond operation, including site selection, stocking, feeding, and disease control.',
                'content' => "## Overview\n\nSmall-scale tilapia culture is a low-barrier entry point for aquaculture.\n\n## Site Selection\n\n- Reliable water supply\n- Adequate drainage\n- Proximity to markets\n\n## Stocking and Feeding\n\nStock 10-15 fingerlings per square meter and feed a floating pelleted ration twice daily.",
                'project_type' => 'guideline',
                'sector' => 'fish',
                'featured' => false,
                'is_published' => true,
            ],
        ];

        foreach ($projects as $project) {
            VeterinaryProject::create($project);
        }
    }
}
