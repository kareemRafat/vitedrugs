@extends('layouts.master')

@section('css')
<style>

    .anatomy-hero{

        background: linear-gradient(135deg,#1d4350,#a43931);

        color:#fff;

        border-radius:20px;

        overflow:hidden;
    }

    .system-card{

        border:0;

        border-radius:15px;

        box-shadow:0 4px 20px rgba(0,0,0,.06);

        transition:all .3s ease;

        height:100%;
    }

    .system-card:hover{

        transform:translateY(-5px);

        box-shadow:0 10px 30px rgba(0,0,0,.12);
    }

    .system-header{

        background:#f8f9fa;

        border-bottom:1px solid #eee;

        border-radius:15px 15px 0 0;

        padding:18px;

        font-size:18px;

        font-weight:600;
    }

    .structure-btn{

        border-radius:10px;

        margin-bottom:10px;

        transition:.3s;
    }

    .structure-btn:hover{

        transform:translateX(5px);
    }

    .results-card{

        border:0;

        border-radius:15px;

        box-shadow:0 4px 20px rgba(0,0,0,.06);
    }

    .disease-card{

        border:0;

        border-radius:12px;

        box-shadow:0 2px 12px rgba(0,0,0,.05);

        transition:.3s;
    }

    .disease-card:hover{

        transform:translateY(-3px);

        box-shadow:0 8px 20px rgba(0,0,0,.1);
    }

    .empty-state{

        text-align:center;

        padding:60px 20px;

        color:#6c757d;
    }

    .loading-box{

        text-align:center;

        padding:50px 20px;
    }

</style>
@endsection


@section('page-header')

@include('layouts.breadcrumbs', [

    'items' => [

        [
            'title' => 'Knowledge Explorer',
            'url' => route('home')
        ],

        [
            'title' => 'Anatomical Navigator'
        ]

    ]

])

@endsection


@section('content')

<div class="row">

    <div class="col-12">

        <div class="card anatomy-hero">

            <div class="card-body py-5 text-center">

                <h1 class="mb-3">

                    Interactive Anatomical Navigator

                </h1>

                <p class="mb-0">

                    Explore diseases through anatomical structures and body systems.

                </p>

            </div>

        </div>

    </div>

</div>


<div class="row mt-4">

    @forelse($systems as $system)

        <div class="col-xl-4 col-lg-6 mb-4">

            <div class="card system-card">

                <div class="system-header">

                    {{ $system->name }}

                </div>

                <div class="card-body">

                    @forelse(
                        $system->anatomicalStructures
                        as $structure
                    )

                        <button

                            class="btn btn-outline-primary btn-block structure-btn"

                            onclick="
                                loadDiseases(
                                    '{{ $structure->id }}',
                                    '{{ addslashes($structure->name) }}'
                                )
                            ">

                            <i class="fa fa-heartbeat mr-2"></i>

                            {{ $structure->name }}

                        </button>

                    @empty

                        <div class="text-muted">

                            No anatomical structures available.

                        </div>

                    @endforelse

                </div>

            </div>

        </div>

    @empty

        <div class="col-12">

            <div class="empty-state">

                No body systems available.

            </div>

        </div>

    @endforelse

</div>


<div class="card results-card mt-4">

    <div class="card-header">

        <h4 class="mb-0">

            Associated Diseases

        </h4>

    </div>

    <div class="card-body">

        <div id="results">

            <div class="empty-state">

                Select an anatomical structure to explore related diseases.

            </div>

        </div>

    </div>

</div>

@endsection


@section('js')

<script>

async function loadDiseases(
    structureId,
    structureName
){

    const results =
        document.getElementById(
            'results'
        );

    results.innerHTML = `

        <div class="loading-box">

            <div class="spinner-border text-primary">

            </div>

            <p class="mt-3">

                Loading diseases associated with

                <strong>

                    ${structureName}

                </strong>

            </p>

        </div>

    `;

    try{

        let response = await fetch(

            `/api/v1/anatomy/${structureId}/diseases`

        );

        let diseases =
            await response.json();

        let html = `

            <h5 class="mb-4">

                Diseases associated with

                <strong>

                    ${structureName}

                </strong>

            </h5>

            <div class="row">

        `;

        if(!diseases.length){

            html += `

                <div class="col-12">

                    <div class="alert alert-light">

                        No diseases found for this anatomical structure.

                    </div>

                </div>

            `;

        }else{

            diseases.forEach(disease => {

                html += `

                    <div class="col-xl-4 col-lg-6 mb-4">

                        <div class="card disease-card">

                            <div class="card-body">

                                <h5>

                                    <a href="/diseases/${disease.slug}">

                                        ${disease.name}

                                    </a>

                                </h5>

                                <a href="/diseases/${disease.slug}"

                                   class="btn btn-sm btn-primary mt-3">

                                    View Disease

                                </a>

                            </div>

                        </div>

                    </div>

                `;

            });

        }

        html += `</div>`;

        results.innerHTML = html;

        window.scrollTo({

            top: results.offsetTop - 100,

            behavior: 'smooth'
        });

    }catch(error){

        results.innerHTML = `

            <div class="alert alert-danger">

                Unable to load diseases. Please try again.

            </div>

        `;
    }

}

</script>

@endsection

