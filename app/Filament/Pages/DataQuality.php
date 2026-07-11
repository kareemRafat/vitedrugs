<?php

namespace App\Filament\Pages;

use App\Models\Product;
use App\Models\Company;
use App\Models\Disease;
use App\Models\ActiveIngredient;
use Filament\Pages\Page;

class DataQuality extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shield-check';

    protected static string|\UnitEnum|null $navigationGroup = 'System';

   public string $view = 'filament.pages.data-quality';

    public array $stats = [];

    public function mount(): void
    {
        $this->stats = [

            'products_without_diseases' =>
            Product::doesntHave('diseases')->count(),

            'products_without_ingredients' =>
            Product::doesntHave('activeIngredients')->count(),

            'companies_without_products' =>
            Company::doesntHave('products')->count(),

            'diseases_without_products' =>
            Disease::doesntHave('products')->count(),

            'ingredients_without_products' =>
            ActiveIngredient::doesntHave('products')->count(),
        ];
    }
}
