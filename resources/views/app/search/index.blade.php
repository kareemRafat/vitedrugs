@extends('app.layouts.master')

@section('content')

<div class="row">

    <div class="col-xl-12">

        <div class="card">

            <div class="card-body">

                <form method="GET">

                    <div class="input-group">

                        <input
                            type="text"
                            name="q"
                            class="form-control"
                            placeholder="Search products, companies, diseases, ingredients..."
                            value="{{ $q }}"
                        >

                        <button
                            type="submit"
                            class="btn btn-primary"
                        >
                            Search
                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

</div>

@if($q)

<div class="row">

    <div class="col-xl-6">

        <div class="card">

            <div class="card-header">
                Products
            </div>

            <div class="card-body">

                <ul>

                    @forelse($products as $item)

                        <li>
                            <a href="{{ route('products.show',$item) }}">
                                {{ $item->trade_name }}
                            </a>
                        </li>

                    @empty

                        <li>No results</li>

                    @endforelse

                </ul>

            </div>

        </div>

    </div>

    <div class="col-xl-6">

        <div class="card">

            <div class="card-header">
                Companies
            </div>

            <div class="card-body">

                <ul>

                    @forelse($companies as $item)

                        <li>
                            <a href="{{ route('companies.show',$item) }}">
                                {{ $item->name }}
                            </a>
                        </li>

                    @empty

                        <li>No results</li>

                    @endforelse

                </ul>

            </div>

        </div>

    </div>

</div>

<div class="row">

    <div class="col-xl-6">

        <div class="card">

            <div class="card-header">
                Diseases
            </div>

            <div class="card-body">

                <ul>

                    @forelse($diseases as $item)

                        <li>
                            <a href="{{ route('diseases.show',$item) }}">
                                {{ $item->name }}
                            </a>
                        </li>

                    @empty

                        <li>No results</li>

                    @endforelse

                </ul>

            </div>

        </div>

    </div>

    <div class="col-xl-6">

        <div class="card">

            <div class="card-header">
                Active Ingredients
            </div>

            <div class="card-body">

                <ul>

                    @forelse($ingredients as $item)

                        <li>
                            <a href="{{ route('active-ingredients.show',$item) }}">
                                {{ $item->name }}
                            </a>
                        </li>

                    @empty

                        <li>No results</li>

                    @endforelse

                </ul>

            </div>

        </div>

    </div>

</div>

@endif

@endsection
