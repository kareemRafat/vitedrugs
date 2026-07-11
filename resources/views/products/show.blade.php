@extends('layouts.master')
@section('css')
    <style>
        body {
            background: #f8fafc;
        }

        .product-page {
            width: 100%;
        }

        .product-hero {
            background: #fff;
            border-radius: 18px;
            padding: 1rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, .06);
        }

        .product-title {
            font-size: 2.25rem;
            font-weight: 700;
            line-height: 1.2;
            margin-bottom: .5rem;
        }

        .product-subtitle {
            color: #6b7280;
            font-size: .95rem;
        }

        .meta-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 1rem;
            height: 100%;
        }

        .meta-label {
            display: block;
            font-size: .75rem;
            text-transform: uppercase;
            color: #6b7280;
            margin-bottom: .35rem;
        }

        .meta-value {
            font-weight: 600;
            color: #111827;
        }

        .vp-card {
            background: #fff;
            border: 0;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 16px rgba(0, 0, 0, .05);
            transition: .2s ease;
        }

        .vp-card:hover {
            box-shadow: 0 10px 24px rgba(0, 0, 0, .08);
        }

        .vp-card .card-header {
            background: #fff;
            border-bottom: 1px solid #eef2f7;
            padding: 1rem 1.25rem;
        }

        .vp-table {
            min-width: 650px;
            margin-bottom: 0;
        }

        .vp-table thead th {
            border-top: 0;
            background: #f8fafc;
            font-size: .85rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .3px;
            white-space: nowrap;
        }

        .vp-table tbody td {
            vertical-align: middle;
            padding: .85rem;
        }

        .vp-table tbody tr:hover {
            background: #fafcff;
        }

        .vp-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .vp-list li {
            padding: .75rem 0;
            border-bottom: 1px solid #f1f5f9;
        }

        .vp-list li:last-child {
            border-bottom: 0;
        }

        .vp-list a {
            color: #111827;
            font-weight: 500;
        }

        .vp-list a:hover {
            color: #2563eb;
            text-decoration: none;
        }

        .empty-state {
            text-align: center;
            padding: 2rem;
            color: #6b7280;
        }

        .table-responsive {
            overflow-x: auto;
        }

        @media (min-width:992px) {
            .sidebar-sticky {
                position: sticky;
                top: 20px;
            }
        }

        @media (max-width:768px) {

            .product-title {
                font-size: 1.75rem;
            }

        }
    </style>
@endsection

@section('title')
    {{ $product->trade_name }} | VetPedia
@endsection

@section('meta_description')
    {{ \Illuminate\Support\Str::limit(strip_tags($product->description ?? ''), 160) }}
@endsection

@section('meta_keywords')
    {{ $product->trade_name }},
    veterinary drug,
    {{ $product->dosageForm?->name }}
@endsection
@section('og_title')
    {{ $product->trade_name }}
@endsection

@section('og_description')
    {{ \Illuminate\Support\Str::limit($product->description, 160) }}
@endsection
@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">

        <div>

            <nav aria-label="breadcrumb">

                <ol class="breadcrumb bg-transparent p-0 mb-2">

                    <li class="breadcrumb-item">
                        <a href="{{ route('home') }}">
                            Home
                        </a>
                    </li>

                    <li class="breadcrumb-item">
                        <a href="{{ route('products.index') }}">
                            Products
                        </a>
                    </li>

                    <li class="breadcrumb-item active">
                        {{ $product->trade_name }}
                    </li>

                </ol>

            </nav>

            <h2 class="mb-0 font-weight-bold">
                {{ $product->trade_name }}
            </h2>

            <div class="mt-3">

                @if ($product->product_type)
                    <span class="badge badge-primary">
                        {{ $product->product_type }}
                    </span>
                @endif

                @if ($product->dosageForm?->name)
                    <span class="badge badge-light border">
                        {{ $product->dosageForm->name }}
                    </span>
                @endif

            </div>

        </div>

    </div>



    <!-- breadcrumb -->
@endsection

@section('content')
    <div class="container-fluid product-page py-4">


        <div class="product-hero mb-4">

            <div class="product-hero mb-4">

                <div class="p-4 p-lg-5">

                    <div class="row align-items-center">

                        <div class="col-xl-8 col-lg-7">

                            <h1 class="product-title">
                                {{ $product->trade_name }}
                            </h1>

                            @if ($product->trade_name_ar)
                                <div class="product-subtitle mb-3">
                                    {{ $product->trade_name_ar }}
                                </div>
                            @endif

                            <p class="text-muted mb-0">
                                {{ $product->description ?? 'No description available.' }}
                            </p>

                        </div>

                        <div class="col-lg-4 mt-4 mt-lg-0">

                            <div class="row">

                                <div class="col-6 mb-3">
                                    <div class="meta-card">
                                        <span class="meta-label">
                                            Dosage Form
                                        </span>

                                        <div class="meta-value">
                                            {{ $product->dosageForm?->name ?? 'N/A' }}
                                        </div>
                                    </div>
                                </div>

                                <div class="col-6 mb-3">
                                    <div class="meta-card">
                                        <span class="meta-label">
                                            Product Type
                                        </span>

                                        <div class="meta-value">
                                            {{ $product->product_type ?? 'N/A' }}
                                        </div>
                                    </div>
                                </div>

                                <div class="col-6">
                                    <div class="meta-card">

                                        <span class="meta-label">
                                            Manufacturer
                                        </span>

                                        <div class="meta-value">

                                            @if ($manufacturer = $product->companies->first(fn($company) => $company->pivot?->role === 'manufacturer'))
                                                <a href="{{ route('companies.show', $manufacturer) }}">
                                                    {{ $manufacturer->name }}
                                                </a>
                                            @else
                                                N/A
                                            @endif

                                        </div>

                                    </div>
                                </div>

                                <div class="col-6">
                                    <div class="meta-card">

                                        <span class="meta-label">
                                            Package
                                        </span>

                                        <div class="meta-value">
                                            {{ $product->package_size ?? 'N/A' }}
                                        </div>

                                    </div>
                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>
            <div class="row">

                <div class="col-xl-8 col-lg-7">


                    <div class="card vp-card mb-4">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <h2 class="h6 mb-0 text-dark font-weight-bold">
                                <i class="fas fa-syringe mr-2"></i>
                                Dosages
                            </h2>
                        </div>

                        <div class="card-body">
                            @if ($product->dosages->isEmpty())
                                <div class="empty-state">
                                    No dosage information available.
                                </div>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-hover vp-table">

                                        <thead>
                                            <tr>
                                                <th>Species</th>
                                                <th>Dosage</th>
                                                <th>Route</th>
                                                <th>Duration</th>
                                            </tr>
                                        </thead>

                                        <tbody>

                                            @foreach ($product->dosages as $dosage)
                                                <tr>
                                                    <td>{{ $dosage->species?->name }}</td>
                                                    <td>{{ $dosage->dosage }}</td>
                                                    <td>{{ $dosage->route }}</td>
                                                    <td>{{ $dosage->duration }}</td>
                                                </tr>
                                            @endforeach

                                        </tbody>

                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="card vp-card mb-4">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <h2 class="h6 mb-0 text-dark font-weight-bold">
                                <i class="fas fa-clock mr-2"></i>
                                Withdrawal Periods
                            </h2>
                        </div>

                        <div class="card-body">
                            @if ($product->withdrawalPeriods->isEmpty())
                                <div class="empty-state">
                                    No withdrawal information available.
                                </div>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-hover vp-table">

                                        <thead>
                                            <tr>
                                                <th>Species</th>
                                                <th>Meat</th>
                                                <th>Milk</th>
                                                <th>Eggs</th>
                                            </tr>
                                        </thead>

                                        <tbody>

                                            @foreach ($product->withdrawalPeriods as $item)
                                                <tr>
                                                    <td>{{ $item->species?->name }}</td>
                                                    <td>{{ $item->meat_days }}</td>
                                                    <td>{{ $item->milk_days }}</td>
                                                    <td>{{ $item->egg_days }}</td>
                                                </tr>
                                            @endforeach

                                        </tbody>

                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-xl-4 col-lg-5">
                    <div class="sidebar-sticky">
                        <div class="card vp-card mb-4">
                            <div class="card-header d-flex align-items-center justify-content-between">
                                <h2 class="h6 mb-0 text-dark font-weight-bold">
                                    <i class="fas fa-pills mr-2"></i>
                                    Active Ingredients
                                </h2>
                            </div>

                            <div class="card-body">

                                @if ($product->activeIngredients->isEmpty())
                                    <div class="empty-state">
                                        No active ingredients available.
                                    </div>
                                @else
                                    <ul class="vp-list">

                                        @foreach ($product->activeIngredients as $ingredient)
                                            <li>

                                                <a href="{{ route('active-ingredients.show', $ingredient) }}">
                                                    {{ $ingredient->name }}
                                                </a>

                                                @if ($ingredient->pivot->strength)
                                                    <div class="small text-muted mt-1">
                                                        {{ $ingredient->pivot->strength }}
                                                        {{ $ingredient->pivot->unit }}
                                                    </div>
                                                @endif

                                            </li>
                                        @endforeach

                                    </ul>
                                @endif

                            </div>
                        </div>


                        <div class="card vp-card mb-4">
                            <div class="card-header d-flex align-items-center justify-content-between">
                                <h2 class="h6 mb-0 text-dark font-weight-bold">
                                    <i class="fas fa-virus mr-2"></i>
                                    Diseases
                                </h2>
                            </div>

                            <div class="card-body">

                                @if ($product->diseases->isEmpty())
                                    <div class="empty-state">
                                        No diseases linked.
                                    </div>
                                @else
                                    <ul class="vp-list">

                                        @foreach ($product->diseases as $disease)
                                            <li>

                                                <a href="{{ route('diseases.show', $disease) }}">
                                                    {{ $disease->name }}
                                                </a>

                                            </li>
                                        @endforeach

                                    </ul>
                                @endif

                            </div>
                        </div>

                        <div class="card vp-card mb-4">

                            <div class="card-header d-flex align-items-center justify-content-between">
                                <h2 class="h6 mb-0 text-dark font-weight-bold">
                                    <i class="fas fa-link mr-2"></i>
                                    Related Products
                                </h2>
                            </div>

                            <div class="card-body">

                                @if ($relatedProducts->count())
                                    <ul class="vp-list">

                                        @foreach ($relatedProducts as $related)
                                            <li>

                                                <a href="{{ route('products.show', $related) }}">

                                                    {{ $related->trade_name }}

                                                </a>

                                            </li>
                                        @endforeach

                                    </ul>
                                @else
                                    <div class="empty-state">

                                        <div class="font-weight-bold mb-2">
                                            No data available
                                        </div>

                                        <div class="small">
                                            Information has not been added yet.
                                        </div>

                                    </div>
                                @endif

                            </div>

                        </div>
                        <div class="card vp-card mb-4">

                            <div class="card-header d-flex align-items-center justify-content-between">
                                <h2 class="h6 mb-0 text-dark font-weight-bold">
                                    <i class="fas fa-link mr-2"></i>
                                    Related Products By Active Ingredient
                                </h2>
                            </div>

                            <div class="card-body">

                                @if ($relatedByIngredients->count())
                                    <ul class="vp-list">

                                        @foreach ($relatedByIngredients as $related)
                                            <li>

                                                <a href="{{ route('products.show', $related) }}">
                                                    {{ $related->trade_name }}
                                                </a>

                                            </li>
                                        @endforeach

                                    </ul>
                                @else
                                    <div class="empty-state">

                                        <div class="font-weight-bold mb-2">
                                            No data available
                                        </div>

                                        <div class="small">
                                            Information has not been added yet.
                                        </div>

                                    </div>
                                @endif

                            </div>

                        </div>

                        <div class="card vp-card mb-4">
                            <div class="card-header d-flex align-items-center justify-content-between">
                                <h2 class="h6 mb-0 text-dark font-weight-bold">
                                    <i class="fas fa-building mr-2"></i>
                                    Companies
                                </h2>
                            </div>

                            <div class="card-body">

                                @if ($product->companies->isEmpty())
                                    <div class="empty-state">
                                        No companies available.
                                    </div>
                                @else
                                    <ul class="vp-list">

                                        @foreach ($product->companies as $company)
                                            <li>

                                                <a href="{{ route('companies.show', $company) }}">
                                                    {{ $company->name }}
                                                </a>

                                                <div class="small text-muted mt-1">
                                                    {{ ucfirst($company->pivot->role) }}
                                                </div>

                                            </li>
                                        @endforeach

                                    </ul>
                                @endif

                            </div>
                        </div>

                    </div>
                </div>
            </div> {{-- col-lg-4 --}}

        </div> {{-- container --}}
    @endsection
    @section('js')
    @endsection
