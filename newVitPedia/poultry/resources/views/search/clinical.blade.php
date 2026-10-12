@extends('layouts.master')

@section('content')

<div class="row">

    <div class="col-xl-12">

        <div class="card">

            <div class="card-body">

                <h2>

                    Advanced Clinical Search

                </h2>

                <form>

                    <input
                        type="text"
                        name="q"
                        class="form-control mb-3"
                        placeholder="green diarrhea nervous signs sudden death"
                        value="{{ request('q') }}"
                    >

                    <button class="btn btn-primary">

                        Search

                    </button>

                </form>

                <hr>

                @foreach($results as $result)

                    <div class="card mb-3">

                        <div class="card-body">

                            <h4>

                                <a href="{{ route(
                                    'diseases.show',
                                    $result['disease']->slug
                                ) }}">

                                    {{ $result['disease']->name }}

                                </a>

                            </h4>

                            <strong>

                                Score:

                            </strong>

                            {{ $result['score'] }}

                            <br><br>

                            <strong>

                                Matched Terms:

                            </strong>

                            {{ implode(
                                ', ',
                                $result['matched_terms']
                            ) }}

                        </div>

                    </div>

                @endforeach

            </div>

        </div>

    </div>

</div>

@endsection