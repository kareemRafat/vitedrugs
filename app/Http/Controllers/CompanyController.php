<?php

namespace App\Http\Controllers;

use App\Models\Company;

class CompanyController extends Controller
{
    public function show(Company $company)
    {
        $company->load([
            'products.dosageForm',
            'parentCompany',
            'subsidiaries',
        ]);

        return view(
            'app.companies.show',
            compact('company')
        );
    }

    public function index()
    {
        $query = Company::query()
            ->withCount('products');

        if ($search = request('search')) {

            $query->where(function ($q) use ($search) {

                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('name_ar', 'like', "%{$search}%")
                    ->orWhere('country', 'like', "%{$search}%");
            });
        }

        $companies = $query
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view(
            'app.companies.index',
            compact('companies')
        );
    }
}
