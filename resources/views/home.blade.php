@extends('layouts.master')
@section('css')
    <style>
        .card.text-decoration-none {
            transition: all .25s ease;
        }

        .card.text-decoration-none:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, .12) !important;
        }

        .product-card,
        .company-card {
            transition: all .25s ease;
        }

        .product-card:hover,
        .company-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, .08);
        }
    </style>
@endsection

@section('content')
    <div class="row">

        <div class="col-xl-12">

            <div class="card bg-primary text-white">

                <div class="card-body text-center py-5">

                    <h1 class="display-4 text-white mb-3">

                        VetPedia

                    </h1>

                    <p class="lead mb-4">

                        Veterinary Diseases, Drugs, Active Ingredients
                        and Clinical Knowledge Platform

                    </p>

                    <div class="row justify-content-center">

                        <div class="col-lg-6">

                            <form action="#" method="GET">

                                <div class="input-group">

                                    <input type="text" class="form-control"
                                        placeholder="Search diseases, drugs, ingredients...">

                                    <div class="input-group-append">

                                        <button class="btn btn-light" type="submit">
                                            Search
                                        </button>

                                    </div>

                                </div>

                            </form>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

    <div class="row">

        {{-- Products --}}
        <div class="col-xl-3 col-md-6 mb-4">

            <a href="{{ route('products.index') }}" class="card border-0 shadow-sm h-100 text-decoration-none">

                <div class="card-body text-center">

                    <div class="mb-3">
                        <i class="fe fe-package tx-40 text-info"></i>
                    </div>

                    <h1 class="font-weight-bold text-info mb-2">
                        {{ $productsCount }}
                    </h1>

                    <h3 class="font-weight-bold text-dark mb-2">
                        Products
                    </h3>

                    <p class="text-muted small">
                        Browse veterinary pharmaceutical products
                    </p>

                </div>

            </a>

        </div>

        {{-- Companies --}}
        <div class="col-xl-3 col-md-6 mb-4">

            <a href="{{ route('companies.index') }}" class="card border-0 shadow-sm h-100 text-decoration-none">

                <div class="card-body text-center">

                    <div class="mb-3">
                        <i class="fe fe-briefcase tx-40 text-success"></i>
                    </div>

                    <h1 class="font-weight-bold text-success mb-2">
                        {{ $companiesCount }}
                    </h1>

                    <h3 class="font-weight-bold text-dark mb-2">
                        Companies
                    </h3>

                    <p class="text-muted small">
                        Veterinary manufacturers and suppliers
                    </p>

                </div>

            </a>

        </div>

        {{-- Diseases --}}
        <div class="col-xl-3 col-md-6 mb-4">

            <a href="{{ route('diseases.index') }}" class="card border-0 shadow-sm h-100 text-decoration-none">

                <div class="card-body text-center">

                    <div class="mb-3">
                        <i class="fe fe-activity tx-40 text-danger"></i>
                    </div>

                    <h1 class="font-weight-bold text-danger mb-2">
                        {{ $diseasesCount }}
                    </h1>

                    <h3 class="font-weight-bold text-dark mb-2">
                        Diseases
                    </h3>

                    <p class="text-muted small">
                        Veterinary disease knowledge database
                    </p>

                </div>

            </a>

        </div>

        {{-- Active Ingredients --}}
        <div class="col-xl-3 col-md-6 mb-4">

            <a href="{{ route('active-ingredients.index') }}" class="card border-0 shadow-sm h-100 text-decoration-none">

                <div class="card-body text-center">

                    <div class="mb-3">
                        <i class="fe fe-droplet tx-40 text-primary"></i>
                    </div>

                    <h1 class="font-weight-bold text-primary mb-2">
                        {{ $ingredientsCount }}
                    </h1>

                    <h3 class="font-weight-bold text-dark mb-2">
                        Active Ingredients
                    </h3>

                    <p class="text-muted small">
                        Explore active pharmaceutical substances
                    </p>

                </div>

            </a>

        </div>

    </div>

    <div class="row">

        {{-- Latest Products --}}
        <div class="col-xl-6 mb-4">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-header bg-white border-0">

                    <div class="d-flex align-items-center justify-content-between">

                        <h4 class="mb-0 font-weight-bold">
                            <i class="fe fe-package text-info mr-2"></i>
                            Latest Veterinary Products
                        </h4>

                        <a href="{{ route('products.index') }}" class="btn btn-sm btn-outline-info">
                            View All
                        </a>

                    </div>

                </div>

                <div class="card-body pt-0">

                    @forelse($latestProducts as $product)
                        <a href="{{ route('products.show', $product) }}" class="d-block text-decoration-none mb-3">

                            <div class="border rounded p-3 product-card">

                                <div class="d-flex align-items-center">

                                    <div class="mr-3">

                                        <div class="bg-info text-white rounded-circle d-flex align-items-center justify-content-center"
                                            style="width:50px;height:50px;">

                                            <i class="fe fe-package"></i>

                                        </div>

                                    </div>

                                    <div class="flex-grow-1">

                                        <h5 class="mb-1 text-dark">
                                            {{ $product->trade_name }}
                                        </h5>

                                        <small class="text-muted">

                                            {{ $product->dosageForm?->name ?? 'Dosage Form N/A' }}

                                        </small>

                                    </div>

                                    <div>

                                        <i class="fe fe-chevron-left text-muted"></i>

                                    </div>

                                </div>

                            </div>

                        </a>

                    @empty

                        <div class="text-center text-muted py-4">
                            No products available.
                        </div>
                    @endforelse

                </div>

            </div>

        </div>

        {{-- Featured Companies --}}
        <div class="col-xl-6 mb-4">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-header bg-white border-0">

                    <div class="d-flex align-items-center justify-content-between">

                        <h4 class="mb-0 font-weight-bold">
                            <i class="fe fe-briefcase text-success mr-2"></i>
                            Featured Companies
                        </h4>

                        <a href="{{ route('companies.index') }}" class="btn btn-sm btn-outline-success">
                            View All
                        </a>

                    </div>

                </div>

                <div class="card-body pt-0">

                    @forelse($latestCompanies as $company)
                        <a href="{{ route('companies.show', $company) }}" class="d-block text-decoration-none mb-3">

                            <div class="border rounded p-3 company-card">

                                <div class="d-flex align-items-center">

                                    <div class="mr-3">

                                        <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center"
                                            style="width:50px;height:50px;">

                                            <i class="fe fe-briefcase"></i>

                                        </div>

                                    </div>

                                    <div class="flex-grow-1">

                                        <h5 class="mb-1 text-dark">
                                            {{ $company->name }}
                                        </h5>

                                        <small class="text-muted">

                                            {{ ucfirst($company->company_type ?? 'Veterinary Company') }}

                                        </small>

                                    </div>

                                    <div>

                                        <i class="fe fe-chevron-left text-muted"></i>

                                    </div>

                                </div>

                            </div>

                        </a>

                    @empty

                        <div class="text-center text-muted py-4">
                            No companies available.
                        </div>
                    @endforelse

                </div>

            </div>

        </div>

    </div>
@endsection
