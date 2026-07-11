@extends('app.layouts.master')

@section('content')

<div class="row">

    <div class="col-xl-12">

        <div class="card">

            <div class="card-header">
                Active Ingredients
            </div>

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

                    @foreach($ingredients as $ingredient)

                        <tr>

                            <td>
                                {{ $ingredient->name }}
                            </td>

                            <td>
                                {{ $ingredient->products_count }}
                            </td>

                            <td>

                                <a
                                    href="{{ route('active-ingredients.show', $ingredient) }}"
                                    class="btn btn-sm btn-primary"
                                >
                                    View
                                </a>

                            </td>

                        </tr>

                    @endforeach

                    </tbody>

                </table>

                {{ $ingredients->links() }}

            </div>

        </div>

    </div>

</div>

@endsection
