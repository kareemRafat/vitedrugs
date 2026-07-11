<?php

namespace App\Filament\Pages;

use App\Models\ActiveIngredient;
use App\Models\Company;
use App\Models\Disease;
use App\Models\DosageForm;
use App\Models\Product;
use Filament\Pages\Page;

class Statistics extends Page
{
    protected string $view = 'filament.pages.statistics';

    public array $stats = [];

    public array $topCompanies = [];

    public array $topDiseases = [];

    public array $topIngredients = [];

    public array $topDosageForms = [];

    public function mount(): void
    {
        $this->stats = [

            'products' => Product::count(),

            'companies' => Company::count(),

            'diseases' => Disease::count(),

            'ingredients' => ActiveIngredient::count(),
        ];

        $this->topCompanies = Company::withCount('products')
            ->orderByDesc('products_count')
            ->take(10)
            ->get()
            ->toArray();

        $this->topDiseases = Disease::withCount('products')
            ->orderByDesc('products_count')
            ->take(10)
            ->get()
            ->toArray();

        $this->topIngredients = ActiveIngredient::withCount('products')
            ->orderByDesc('products_count')
            ->take(10)
            ->get()
            ->toArray();

        $this->topDosageForms = DosageForm::withCount('products')
            ->orderByDesc('products_count')
            ->take(10)
            ->get()
            ->toArray();
    }
}
