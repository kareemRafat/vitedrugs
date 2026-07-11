@extends('layouts.master')

@section('css')
@endsection

@section('page-header')
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">
                    Companies
                </h4>

                <span class="text-muted mt-1 tx-13 mr-2 mb-0">
                    / Directory
                </span>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="row">

        <div class="col-xl-12">

            <div class="card">

                <div class="card-header">
                    Veterinary Companies
                </div>
                <form method="GET" class="mb-3">

                    <div class="input-group">

                        <input type="text" name="search" class="form-control" placeholder="Search companies..."
                            value="{{ request('search') }}">

                        <button class="btn btn-primary" type="submit">
                            Search
                        </button>

                    </div>

                </form>
                <div class="card-body">

                    <div class="table-responsive">

                        <table class="table table-hover">

                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Type</th>
                                    <th>Country</th>
                                    <th>Products</th>
                                    <th></th>
                                </tr>
                            </thead>

                            <tbody>

                                @foreach ($companies as $company)
                                    <tr>

                                        <td>
                                            {{ $company->name }}
                                        </td>

                                        <td>
                                            {{ ucfirst($company->company_type) }}
                                        </td>

                                        <td>
                                            {{ $company->country ?? 'Not Available' }}
                                        </td>

                                        <td>
                                            {{ $company->products_count }}
                                        </td>

                                        <td>

                                            <a href="{{ route('companies.show', $company) }}"
                                                class="btn btn-sm btn-primary">
                                                View
                                            </a>

                                        </td>

                                    </tr>
                                @endforeach

                            </tbody>

                        </table>

                    </div>

                    <div class="mt-3">
                        {{ $companies->links() }}
                    </div>

                </div>

            </div>

        </div>

    </div>
@endsection

@section('js')
@endsection
