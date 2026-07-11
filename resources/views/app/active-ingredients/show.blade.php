@extends('app.layouts.master')
@section('title')
{{ $activeIngredient->name }} | VetPedia
@endsection

@section('meta_description')
{{ Str::limit(strip_tags($activeIngredient->description), 160) }}
@endsection

@section('meta_keywords')
{{ $activeIngredient->name }},
active ingredient,
veterinary pharmacology
@endsection
@section('og_title')
{{ $activeIngredient->name }}
@endsection

@section('og_description')
{{ \Illuminate\Support\Str::limit($activeIngredient->description, 160) }}
@endsection

@section('page-header')

<div class="breadcrumb-header justify-content-between">

    <div class="my-auto">

        <div class="d-flex">

            <h4 class="content-title mb-0 my-auto">
                {{ $activeIngredient->name }}
            </h4>

            <span class="text-muted mt-1 tx-13 mr-2 mb-0">

                <a href="{{ route('home') }}">
                    Home
                </a>

                /

                <a href="{{ route('active-ingredients.index') }}">
                    Active Ingredients
                </a>

                /

                {{ $activeIngredient->name }}

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

        {{-- Overview --}}
        <div class="card mb-4">

            <div class="card-body">

                <h1 class="mb-1">
                    {{ $activeIngredient->name }}
                </h1>

                @if($activeIngredient->name_ar)
                    <div class="text-muted mb-3">
                        {{ $activeIngredient->name_ar }}
                    </div>
                @endif

                <hr>

                <p>
                    {{ $activeIngredient->description ?: 'No description available.' }}
                </p>

                <div class="row mt-4">

                    <div class="col-md-6">

                        <div class="border rounded p-3">

                            <strong>
                                Products Using This Ingredient
                            </strong>

                            <div class="h3 mb-0 mt-2">
                                {{ $activeIngredient->products->count() }}
                            </div>

                        </div>

                    </div>

                    <div class="col-md-6">

                        <div class="border rounded p-3">

                            <strong>
                                Drug Classes
                            </strong>

                            <div class="h3 mb-0 mt-2">
                                {{ $activeIngredient->drugClasses->count() }}
                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

        {{-- Indications --}}
        @if($activeIngredient->indications)

            <div class="card mb-4">

                <div class="card-header">
                    Indications
                </div>

                <div class="card-body">

                    {!! nl2br(e($activeIngredient->indications)) !!}

                </div>

            </div>

        @endif

        {{-- Contraindications --}}
        @if($activeIngredient->contraindications)

            <div class="card mb-4">

                <div class="card-header">
                    Contraindications
                </div>

                <div class="card-body">

                    {!! nl2br(e($activeIngredient->contraindications)) !!}

                </div>

            </div>

        @endif

        {{-- Precautions --}}
        @if($activeIngredient->precautions)

            <div class="card mb-4">

                <div class="card-header">
                    Precautions
                </div>

                <div class="card-body">

                    {!! nl2br(e($activeIngredient->precautions)) !!}

                </div>

            </div>

        @endif

        {{-- Side Effects --}}
        @if($activeIngredient->side_effects)

            <div class="card mb-4">

                <div class="card-header">
                    Side Effects
                </div>

                <div class="card-body">

                    {!! nl2br(e($activeIngredient->side_effects)) !!}

                </div>

            </div>

        @endif

        {{-- Related Products --}}
        <div class="card mb-4">

            <div class="card-header">
                Related Products
            </div>

            <div class="card-body">

                @if($activeIngredient->products->count())

                    <div class="table-responsive">

                        <table class="table table-hover table-bordered">

                            <thead>

                                <tr>

                                    <th>Trade Name</th>

                                    <th>Strength</th>

                                    <th>Company</th>

                                    <th width="120">
                                        Action
                                    </th>

                                </tr>

                            </thead>

                            <tbody>

                            @foreach($activeIngredient->products as $product)

                                <tr>

                                    <td>

                                        <a href="{{ route('products.show', $product) }}">

                                            {{ $product->trade_name }}

                                        </a>

                                    </td>

                                    <td>

                                        {{ $product->pivot->strength }}
                                        {{ $product->pivot->unit }}

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
                Drug Classes
            </div>

            <div class="card-body">

                @if($activeIngredient->drugClasses->count())

                    <ul class="mb-0">

                        @foreach($activeIngredient->drugClasses as $class)

                            <li>

                                {{ $class->name }}

                            </li>

                        @endforeach

                    </ul>

                @else

                    <span class="text-muted">
                        No drug classes assigned.
                    </span>

                @endif

            </div>

        </div>
        <div class="card mb-4">

    <div class="card-header">
        Related Diseases
    </div>

    <div class="card-body">

        @if($diseases->count())

            <ul class="mb-0">

                @foreach($diseases as $disease)

                    <li>

                        <a href="{{ route('diseases.show', $disease) }}">

                            {{ $disease->name }}

                        </a>

                    </li>

                @endforeach

            </ul>

        @else

            <span class="text-muted">
                No diseases found.
            </span>

        @endif

    </div>

</div>

    </div>

</div>


</div>

@endsection
