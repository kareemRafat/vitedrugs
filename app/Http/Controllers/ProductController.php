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
                'company',
                'dosageForm',
            ]);


        /*
|--------------------------------------------------------------------------
| Search
|--------------------------------------------------------------------------
*/

        if ($search = request('search')) {

            $query->where(function ($q) use ($search) {

                $q->where(
                    'trade_name',
                    'like',
                    "%{$search}%"
                )

                    ->orWhereHas(
                        'activeIngredients',
                        fn($q) => $q->where(
                            'name',
                            'like',
                            "%{$search}%"
                        )
                    )

                    ->orWhereHas(
                        'companies',
                        fn($q) => $q->where(
                            'name',
                            'like',
                            "%{$search}%"
                        )
                    )

                    ->orWhereHas(
                        'diseases',
                        fn($q) => $q->where(
                            'name',
                            'like',
                            "%{$search}%"
                        )
                    );
            });
        }

        /*
|--------------------------------------------------------------------------
| Company Filter
|--------------------------------------------------------------------------
*/

        if ($companyId = request('company')) {

            $query->where('company_id', $companyId);
        }

        /*
|--------------------------------------------------------------------------
| Dosage Form Filter
|--------------------------------------------------------------------------
*/

        if ($dosageFormId = request('dosage_form')) {

            $query->where('dosage_form_id', $dosageFormId);
        }

        /*
|--------------------------------------------------------------------------
| Sorting
|--------------------------------------------------------------------------
*/

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

        $products = $query
            ->paginate(20)
            ->withQueryString();

        $companies = Company::query()
            ->orderBy('name')
            ->get();

        $dosageForms = DosageForm::query()
            ->orderBy('name')
            ->get();

        return view(
            'products.index',
            compact(
                'products',
                'companies',
                'dosageForms'
            )
        );
    }

    public function show(Product $product)
    {
        $product->load([
            'company',
            'companies',
            'dosageForm',
            'activeIngredients',
            'indications',
            'contraindications',
            'precautions',
            'sideEffects',
            'dosages.species',
            'withdrawalPeriods.species',
            'diseases',
            'images',
            'documents',
        ]);

        $relatedProducts = Product::where('id', '!=', $product->id)
            ->whereHas('diseases', function ($query) use ($product) {
                $query->whereIn(
                    'diseases.id',
                    $product->diseases->pluck('id')
                );
            })
            ->take(10)
            ->get();

        $relatedByIngredients = Product::where('id', '!=', $product->id)
            ->whereHas('activeIngredients', function ($query) use ($product) {
                $query->whereIn(
                    'active_ingredients.id',
                    $product->activeIngredients->pluck('id')
                );
            })
            ->take(10)
            ->get();

        return view(
            'products.show',

            compact('product', 'relatedProducts', 'relatedByIngredients')
        );
    }
}
