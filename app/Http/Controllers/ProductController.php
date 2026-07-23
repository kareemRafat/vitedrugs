<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\DosageForm;
use App\Models\Product;

class ProductController extends Controller
{
    public function index()
    {
        $query = Product::query()
            ->with([
                'manufacturer',
                'dosageForm',
                'activeIngredients',
            ]);

        // Search
        if ($search = request('search')) {
            $query->where(function ($q) use ($search) {
                $q->whereFullText(['trade_name', 'trade_name_ar'], $search.'*', ['mode' => 'boolean'])
                    ->orWhereHas('activeIngredients', fn ($q) => $q->whereFullText(['name', 'name_ar'], $search.'*', ['mode' => 'boolean']))
                    ->orWhereHas('companies', fn ($q) => $q->whereFullText(['name', 'name_ar'], $search.'*', ['mode' => 'boolean']))
                    ->orWhereHas('diseases', fn ($q) => $q->whereFullText(['name', 'name_ar'], $search.'*', ['mode' => 'boolean']));

                if (mb_strlen($search) < 3) {
                    $q->orWhere('trade_name', 'like', "%{$search}%")
                        ->orWhere('trade_name_ar', 'like', "%{$search}%");
                }
            });
        }

        // Company filter
        if ($companyId = request('company')) {
            $query->whereHas('companies', fn ($q) => $q->where('companies.id', $companyId));
        }

        // Dosage form filter
        if ($dosageFormId = request('dosage_form')) {
            $query->where('dosage_form_id', $dosageFormId);
        }

        // Sorting
        switch (request('sort')) {
            case 'name':
                $query->orderBy('trade_name');
                break;
            case 'oldest':
                $query->oldest();
                break;
            default:
                $query->latest();
                break;
        }

        $products = $query->paginate(21)->withQueryString();
        $companies = Company::query()->orderBy('name')->get();
        $dosageForms = DosageForm::query()->orderBy('name')->get();

        return view('app.products.index', compact('products', 'companies', 'dosageForms'));
    }

    public function show(Product $product)
    {
        $product->load([
            'companies', 'dosageForm', 'activeIngredients',
            'indications', 'contraindications', 'precautions', 'sideEffects',
            'dosages.species', 'withdrawalPeriods.species',
            'diseases', 'images', 'documents',
            'alternatives.alternativeProduct',
        ]);

        $relatedProducts = Product::where('id', '!=', $product->id)
            ->whereHas('diseases', fn ($q) => $q->whereIn('diseases.id', $product->diseases->pluck('id')))
            ->take(10)->get();

        $relatedByIngredients = Product::where('id', '!=', $product->id)
            ->whereHas('activeIngredients', fn ($q) => $q->whereIn('active_ingredients.id', $product->activeIngredients->pluck('id')))
            ->take(10)->get();

        return view('app.products.show', compact('product', 'relatedProducts', 'relatedByIngredients'));
    }
}
