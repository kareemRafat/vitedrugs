@extends('layouts.master')

@section('css')
<style>

    /* ==========================
       Products Table
    ========================== */

    .table thead th {
        background: #eef3f9;
        color: #1e293b;

        font-size: 15px;
        font-weight: 700;

        white-space: nowrap;
        vertical-align: middle;

        padding: 20px 18px;

        border-top: 0;
        border-bottom: 2px solid #dbe4f0;
        
    }

    .table thead tr {
        height: 72px;
    }

    .table tbody tr {
        height: 64px;
        transition: background-color .15s ease;
    }

    .table tbody tr:hover {
        background: #f8fbff;
    }

    .table tbody td {
        vertical-align: middle;
        padding: 16px 18px;
    }

    /* ==========================
       Product Link
    ========================== */

    .product-link {
        font-size: 16px;
        font-weight: 700;
        color: #0F4C81;
        text-decoration: none;
    }

    .product-link:hover {
        color: #0b3a62;
        text-decoration: none;
    }

    /* ==========================
       Search Card
    ========================== */

    .products-search-card {
        background: #f8fbff;
        border: 1px solid #e5e9f2;
    }

    .products-search-card .form-control,
    .products-search-card .custom-select,
    .products-search-card select {
        height: 50px;
    }

    .products-search-card .btn {
        height: 50px;
    }

    /* ==========================
       Pagination
    ========================== */

    .pagination {
        justify-content: center;
    }

    /* ==========================
       Small Screens
    ========================== */

    @media (max-width: 768px) {

        .table thead th {
            font-size: 13px;
            padding: 14px 12px;
        }

        .table tbody td {
            padding: 14px 12px;
        }

        .product-link {
            font-size: 15px;
        }
    }

</style>

@endsection

@section('page-header')
@endsection

@section('content')
    <div class="container py-4">

        {{-- Header --}}

        <div class="card border-0 shadow-sm mb-4 products-search-card">

            <div class="card-body">

                <div class="row align-items-center">

                    <div class="col-md-8">

                        <h2 class="mb-1">
                            Veterinary Products
                        </h2>

                        <p class="text-muted mb-0">
                            Browse veterinary pharmaceutical products and formulations.
                        </p>

                    </div>

                    <div class="col-md-4">

                        <div class="d-flex justify-content-md-end flex-wrap">

                            <span class="badge badge-primary mr-2 mb-1 p-2">
                                {{ number_format($products->total()) }} Products
                            </span>

                            <span class="badge badge-success mr-2 mb-1 p-2">
                                {{ $companies->count() }} Companies
                            </span>

                            <span class="badge badge-info mb-1 p-2">
                                {{ $dosageForms->count() }} Forms
                            </span>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- Search --}}

        <div class="card border-0 shadow-sm mb-4 products-search-card">


            <div class="card-body">

                <form method="GET">

                    <div class="row">

                        {{-- Search --}}

                        <div class="col-lg-4 mb-2">

                            <input type="text" name="search" class="form-control form-control-lg"
                                placeholder="Search products, ingredients, diseases..." value="{{ request('search') }}">

                        </div>

                        {{-- Company Filter --}}

                        <div class="col-lg-2 mb-2">

                            <select name="company" class="form-control form-control-lg">

                                <option value="">
                                    All Companies
                                </option>

                                @foreach ($companies as $company)
                                    <option value="{{ $company->id }}" @selected(request('company') == $company->id)>
                                        {{ $company->name }}
                                    </option>
                                @endforeach

                            </select>

                        </div>

                        {{-- Dosage Form Filter --}}

                        <div class="col-lg-2 mb-2">

                            <select name="dosage_form" class="form-control form-control-lg">

                                <option value="">
                                    All Forms
                                </option>

                                @foreach ($dosageForms as $form)
                                    <option value="{{ $form->id }}" @selected(request('dosage_form') == $form->id)>
                                        {{ $form->name }}
                                    </option>
                                @endforeach

                            </select>

                        </div>

                        {{-- Sort --}}

                        <div class="col-lg-2 mb-2">

                            <select name="sort" class="form-control form-control-lg">

                                <option value="latest" @selected(request('sort') == 'latest')>
                                    Latest
                                </option>

                                <option value="name" @selected(request('sort') == 'name')>
                                    Name A-Z
                                </option>

                                <option value="oldest" @selected(request('sort') == 'oldest')>
                                    Oldest
                                </option>

                            </select>

                        </div>

                        {{-- Buttons --}}

                        <div class="col-lg-2 mb-2">

                            <div class="d-flex">

                                <button type="submit" class="btn btn-primary flex-fill mr-1">
                                    Search
                                </button>

                                <a href="{{ route('products.index') }}" class="btn btn-light border">
                                    Reset
                                </a>

                            </div>

                        </div>

                    </div>

                </form>

            </div>


        </div>





        {{-- Results --}}

        <div class="card border-0 shadow-sm">


            <div class="card-header bg-white">

                <div class="d-flex justify-content-between align-items-center">

                    <h5 class="mb-0">

                        Products Directory

                    </h5>

                    <small class="text-muted">

                        Showing

                        {{ $products->firstItem() ?? 0 }}

                        -

                        {{ $products->lastItem() ?? 0 }}

                        of

                        {{ number_format($products->total()) }}

                        products

                    </small>

                </div>

            </div>

            <div class="table-responsive">

                <table class="table table-hover table-striped mb-0">

                    <thead>
                        <tr>
                            <th width="5%">#</th>
                            <th width="45%">Trade Name</th>
                            <th width="25%">Company</th>
                            <th width="15%">Dosage Form</th>
                            <th width="10%">Details</th>
                        </tr>

                    </thead>

                    <tbody>

                        @forelse($products as $product)
                            <tr>
                                <td>
                                    {{ $products->firstItem() + $loop->index }}
                                </td>

                                <td>

                                    <a href="{{ route('products.show', $product) }}" class="product-link">
                                        {{ $product->trade_name }}
                                    </a>

                                </td>
                                <td>
                                    <span class="text-muted">
                                        {{ $product->company?->name ?? 'Unknown' }}
                                    </span>
                                </td>

                                <td>
                                    <span class="text-muted">
                                        {{ $product->dosageForm?->name ?? 'N/A' }}
                                    </span>
                                </td>
                                <td class="text-center">

                                    <a href="{{ route('products.show', $product) }}" class="btn btn-sm btn-primary px-3">
                                        <i class="fe fe-arrow-right"></i>
                                    </a>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="5" class="text-center py-5">

                                    <i class="fe fe-package tx-40 text-muted d-block mb-3"></i>

                                    <h5>
                                        No Products Found
                                    </h5>

                                    <span class="text-muted">
                                        Try another search term.
                                    </span>

                                </td>

                            </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>


        </div>



        {{-- Pagination --}}

        @if ($products->hasPages())
            <div class="products-pagination mt-4">

                {{ $products->withQueryString()->links() }}

            </div>
        @endif

    </div>
@endsection

@section('js')
@endsection
