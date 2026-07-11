@extends('app.layouts.master')
@section('title')
{{ $disease->name }} | VetPedia
@endsection

@section('meta_description')
{{ Str::limit(strip_tags($disease->description), 160) }}
@endsection

@section('meta_keywords')
{{ $disease->name }},
veterinary disease
@endsection
@section('og_title')
{{ $disease->name }}
@endsection

@section('og_description')
{{ \Illuminate\Support\Str::limit($disease->description, 160) }}
@endsection
@section('page-header')

<div class="breadcrumb-header justify-content-between">

    <div class="my-auto">

        <div class="d-flex">

            <h4 class="content-title mb-0 my-auto">
                {{ $disease->name }}
            </h4>

            <span class="text-muted mt-1 tx-13 mr-2 mb-0">

                <a href="{{ route('home') }}">
                    Home
                </a>

                /

                <a href="{{ route('diseases.index') }}">
                    Diseases
                </a>

                /

                {{ $disease->name }}

            </span>

        </div>

    </div>

</div>

@endsection

@section('content')

<div class="container py-4">

<div class="row">

    {{-- Main Content --}}
    <div class="col-lg-8">

        {{-- Disease Overview --}}
        <div class="card mb-4">

            <div class="card-body">

                <h1 class="mb-1">
                    {{ $disease->name }}
                </h1>

                @if($disease->name_ar)

                    <div class="text-muted mb-3">
                        {{ $disease->name_ar }}
                    </div>

                @endif

                <hr>

                <p>
                    {{ $disease->description ?: 'No description available.' }}
                </p>

                <div class="row mt-4">

                    <div class="col-md-6">

                        <div class="border rounded p-3">

                            <strong>
                                Related Products
                            </strong>

                            <div class="h3 mt-2 mb-0">
                                {{ $disease->products->count() }}
                            </div>

                        </div>

                    </div>

                    <div class="col-md-6">

                        <div class="border rounded p-3">

                            <strong>
                                Active Ingredients
                            </strong>

                            <div class="h3 mt-2 mb-0">

                                @php

                                    $ingredients = collect();

                                    foreach ($disease->products as $product) {
                                        foreach ($product->activeIngredients as $ingredient) {
                                            $ingredients->push($ingredient);
                                        }
                                    }

                                    $ingredients = $ingredients->unique('id');

                                @endphp

                                {{ $ingredients->count() }}

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

        {{-- Related Products --}}
        <div class="card mb-4">

            <div class="card-header">
                Related Products
            </div>

            <div class="card-body">

                @if($disease->products->count())

                    <div class="table-responsive">

                        <table class="table table-hover table-bordered">

                            <thead>

                                <tr>

                                    <th>Trade Name</th>

                                    <th>Dosage Form</th>

                                    <th>Manufacturer</th>

                                    <th width="120">
                                        Action
                                    </th>

                                </tr>

                            </thead>

                            <tbody>

                            @foreach($disease->products as $product)

                                <tr>

                                    <td>

                                        <a href="{{ route('products.show', $product) }}">

                                            {{ $product->trade_name }}

                                        </a>

                                    </td>

                                    <td>
                                        {{ $product->dosageForm?->name ?? 'N/A' }}
                                    </td>

                                    <td>

                                        @if($manufacturer = $product->manufacturer()->first())

                                            <a href="{{ route('companies.show', $manufacturer) }}">
                                                {{ $manufacturer->name }}
                                            </a>

                                        @else

                                            N/A

                                        @endif

                                    </td>

                                    <td>

                                        <a
                                            href="{{ route('products.show', $product) }}"
                                            class="btn btn-sm btn-primary"
                                        >
                                            View
                                        </a>

                                    </td>

                                </tr>

                            @endforeach

                            </tbody>

                        </table>

                    </div>

                @else

                    <p class="text-muted mb-0">
                        No related products found.
                    </p>

                @endif

            </div>

        </div>

    </div>

    {{-- Sidebar --}}
    <div class="col-lg-4">

        <div class="card mb-4">

            <div class="card-header">
                Active Ingredients
            </div>

            <div class="card-body">

                @if($ingredients->count())

                    <ul class="mb-0">

                        @foreach($ingredients as $ingredient)

                            <li>

                                <a href="{{ route('active-ingredients.show', $ingredient) }}">
                                    {{ $ingredient->name }}
                                </a>

                            </li>

                        @endforeach

                    </ul>

                @else

                    <span class="text-muted">
                        No active ingredients found.
                    </span>

                @endif

            </div>

        </div>

    </div>

</div>


</div>

@endsection
