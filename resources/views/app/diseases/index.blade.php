@extends('app.layouts.master')

@section('content')
    <div class="row">

        <div class="col-xl-12">

            <div class="card">

                <div class="card-header">
                    Diseases
                </div>
                <form method="GET" class="mb-3">

                    <div class="input-group">

                        <input type="text" name="search" class="form-control" placeholder="Search diseases..."
                            value="{{ request('search') }}">

                        <button type="submit" class="btn btn-primary">
                            Search
                        </button>

                    </div>

                </form>
                <div class="card-body">

                    <table class="table table-hover">

                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Products</th>
                                <th></th>
                            </tr>
                        </thead>

                        <tbody>

                            @foreach ($diseases as $disease)
                                <tr>

                                    <td>
                                        {{ $disease->name }}
                                    </td>

                                    <td>
                                        {{ $disease->products_count }}
                                    </td>

                                    <td>

                                        <a href="{{ route('diseases.show', $disease) }}" class="btn btn-sm btn-primary">
                                            View
                                        </a>

                                    </td>

                                </tr>
                            @endforeach

                        </tbody>

                    </table>

                    {{ $diseases->links() }}

                </div>

            </div>

        </div>

    </div>
@endsection
