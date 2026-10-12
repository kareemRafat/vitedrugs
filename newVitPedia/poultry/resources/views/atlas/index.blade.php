@extends('layouts.master')

@section('css')

<style>

.atlas-hero{

    background:linear-gradient(
        135deg,
        #3a1c71,
        #d76d77,
        #ffaf7b
    );

    color:#fff;

    border-radius:15px;
}

.organ-card{

    transition:.3s;
}

.organ-card:hover{

    transform:translateY(-4px);

    box-shadow:0 8px 20px rgba(0,0,0,.1);
}

.lesion-item{

    padding:10px;

    border-bottom:1px solid #eee;
}

.lesion-item:last-child{

    border-bottom:0;
}

</style>

@endsection

@section('content')

<div class="row">

    <div class="col-12">

        <div class="card atlas-hero">

            <div class="card-body text-center py-5">

                <h1>

                    Poultry Lesion Atlas

                </h1>

                <p>

                    Explore diseases through necropsy lesions

                </p>

            </div>

        </div>

    </div>

</div>


<div class="accordion mt-4"
     id="atlasAccordion">

@foreach($systems as $system)

<div class="card">

    <div class="card-header">

        <button
            class="btn btn-link"
            data-toggle="collapse"
            data-target="#system{{ $system->id }}">

            {{ $system->display_name }}

        </button>

    </div>

    <div id="system{{ $system->id }}"
         class="collapse"
         data-parent="#atlasAccordion">

        <div class="card-body">

            <div class="row">

                @foreach(
                    $system->anatomicalStructures
                    as $structure
                )

                <div class="col-xl-4">

                    <div class="card organ-card mb-3">

                        <div class="card-header">

                            <strong>

                                {{ $structure->display_name }}

                            </strong>

                        </div>

                        <div class="card-body p-0">

                            @forelse(
                                $structure->lesions
                                as $lesion
                            )

                            <div class="lesion-item">

                                <strong>

                                    {{ $lesion->display_name }}

                                </strong>

                                <br>

                                <small>

                                    Diseases:

                                </small>

                                <ul class="mt-2">

                                    @foreach(
                                        $lesion->diseases
                                        ->take(5)
                                        as $disease
                                    )

                                    <li>

                                        <a href="{{ route(
                                            'diseases.show',
                                            $disease->slug
                                        ) }}">

                                            {{ $disease->name }}

                                        </a>

                                    </li>

                                    @endforeach

                                </ul>

                            </div>

                            @empty

                            <div class="p-3 text-muted">

                                No lesions available

                            </div>

                            @endforelse

                        </div>

                    </div>

                </div>

                @endforeach

            </div>

        </div>

    </div>

</div>

@endforeach

</div>

@endsection