<?php

namespace App\Http\Controllers;

use App\Models\Company;

class CompanyController extends Controller
{
    public function show(Company $company)
    {
        return view(
            'app.companies.show',
            compact('company')
        );
    }

    public function index()
    {
        $query = Company::query()->withCount('products');

        if ($search = request('search')) {
            $terms = preg_split('/\s+/', trim($search));
            $terms = array_filter($terms, fn ($t) => strlen($t) > 0);
            $booleanQuery = implode(' ', array_map(fn ($t) => '+'.$t.'*', $terms));

            $query->where(function ($q) use ($booleanQuery, $search) {
                $q->whereFullText(['name', 'name_ar'], $booleanQuery, ['mode' => 'boolean'])
                    ->orWhere('country', 'like', "%{$search}%");
            });
        }

        if ($type = request('type')) {
            $query->where('company_type', $type);
        }

        if ($letter = request('letter')) {
            $query->where('name', 'LIKE', $letter.'%');
        }

        $query->orderBy('name');

        $companies = $query->paginate(21)->withQueryString();

        $typeCounts = [];
        foreach (['manufacturer', 'agent', 'distributor', 'marketing'] as $type) {
            $typeCounts[$type] = Company::where('company_type', $type)->count();
        }

        $lettersQuery = Company::query();
        if ($type = request('type')) {
            $lettersQuery->where('company_type', $type);
        }
        $availableLetters = $lettersQuery
            ->selectRaw('UPPER(LEFT(name, 1)) as letter')
            ->distinct()
            ->orderBy('letter')
            ->pluck('letter')
            ->toArray();

        return view('app.companies.index', compact(
            'companies', 'typeCounts', 'availableLetters'
        ));
    }
}
