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
        return view('app.companies.index');
    }
}
