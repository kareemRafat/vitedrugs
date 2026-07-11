@extends('app.layouts.master')
@section('title')
{{ $company->name }} | VetPedia
@endsection

@section('meta_description')
Veterinary company profile for {{ $company->name }}
@endsection

@section('meta_keywords')
{{ $company->name }},
veterinary company
@endsection
@section('og_title')
{{ $company->name }}
@endsection

@section('og_description')
{{ \Illuminate\Support\Str::limit($company->description, 160) }}
@endsection
@section('css')
@endsection

@section('page-header')

<div class="breadcrumb-header justify-content-between">

    <div class="my-auto">

        <div class="d-flex">

            <h4 class="content-title mb-0 my-auto">
                {{ $company->name }}
            </h4>

            <span class="text-muted mt-1 tx-13 mr-2 mb-0">

                <a href="{{ route('home') }}">
                    Home
                </a>

                /

                <a href="{{ route('companies.index') }}">
                    Companies
                </a>

                /

                {{ $company->name }}

            </span>

        </div>

    </div>

</div>

@endsection
@section('content')
    <!-- row -->
    <div class="row">

        <div class="col-xl-12">
            <div class="card">

                <div class="card-body">

                    <h1>{{ $company->name }}</h1>

                    @if ($company->name_ar)
                        <p>{{ $company->name_ar }}</p>
                    @endif

                    <hr>

                    <div class="row">

                        <div class="col-md-6">
                            <strong>Type:</strong>
                            {{ ucfirst($company->company_type) }}
                        </div>

                        <div class="col-md-6">
                            <strong>Country:</strong>
                            {{ $company->country }}
                        </div>

                    </div>

                </div>

            </div>

            <div class="card mb-4">

                <div class="card-header">
                    Contact Information
                </div>

                <div class="card-body">

                    <p><strong>Phone:</strong> {{ $company->phone ?? 'Not Available' }}</p>

                    <p><strong>Mobile:</strong> {{ $company->mobile ?? 'Not Available' }}</p>

                    <p><strong>Whatsapp:</strong> {{ $company->whatsapp ?? 'Not Available' }}</p>

                    <p><strong>Email:</strong> {{ $company->email ?? 'Not Available' }}</p>

                    <p><strong>Website:</strong> {{ $company->website ?? 'Not Available' }}</p>

                    <p><strong>Governorate:</strong> {{ $company->governorate ?? 'Not Available' }}</p>

                    <p><strong>Coverage Area:</strong> {{ $company->coverage_area ?? 'Not Available' }}</p>

                    <p><strong>Address:</strong> {{ $company->address ?? 'Not Available' }}</p>

                </div>

            </div>

            <div class="card">

                <div class="card-header">
                    Products
                </div>

                <div class="table-responsive">

                    <table class="table table-hover">

                        <thead>
                            <tr>
                                <th>Trade Name</th>
                            </tr>
                        </thead>

                        <tbody>

                            @foreach ($company->products as $product)
                                <tr>
                                    <td>
                                        <a href="{{ route('products.show', $product) }}">
                                            {{ $product->trade_name }}
                                        </a>
                                    </td>
                                </tr>
                            @endforeach

                        </tbody>

                    </table>

                </div>

            </div>






            <!-- row closed -->
        </div>
    @endsection
    @section('js')
    @endsection
