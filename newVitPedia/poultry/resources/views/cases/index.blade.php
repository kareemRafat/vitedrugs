@extends('layouts.master')

@section('page-header')

@include('layouts.breadcrumbs', [

    'items' => [

        [
            'title' => 'Clinical Cases'
        ]

    ]

])

@endsection

@section('content')
    <div class="card">

        <div class="card-body">

            <h2>

                Clinical Case Library

            </h2>

            @foreach ($cases as $case)
                <div class="card mb-3">

                    <div class="card-body">

                        <h4>

                            <a
                                href="{{ route('cases.show', $case->slug) }}">

                                {{ $case->title }}

                            </a>

                        </h4>

                        <p>

                            {{ $case->species?->name }}

                        </p>

                        <p>

                            Final Diagnosis:

                            {{ $case->disease?->name }}

                        </p>

                    </div>

                </div>
            @endforeach

            {{ $cases->links() }}

        </div>

    </div>
@endsection
