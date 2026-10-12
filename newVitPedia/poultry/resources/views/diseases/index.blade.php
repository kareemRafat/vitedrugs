@extends('layouts.master')

@section('css')
<style>
    .hero-diseases {
        background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
        color: #fff;
        border: 0;
        border-radius: 18px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0,0,0,.12);
    }

    .hero-diseases .card-body {
        padding: 50px 35px;
    }

    .disease-card {
        border: 0;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0,0,0,.06);
        transition: .25s;
        height: 100%;
    }

    .disease-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,.12);
    }

    .disease-title {
        font-size: 18px;
        font-weight: 700;
        color: #1c273c;
    }

    .agent-badge {
        font-size: 12px;
        border-radius: 30px;
        padding: 6px 12px;
    }

    .empty-box {
        padding: 80px 20px;
        text-align: center;
    }

    .search-box {
        border-radius: 12px;
        height: 50px;
    }

    .zoonotic-badge {
        position: absolute;
        top: 15px;
        right: 15px;
    }
    
</style>
@endsection


@section('page-header')

@include('layouts.breadcrumbs', [

    'items' => [

        [
            'title' => 'Disease Explorer'
        ]

    ]

])

@endsection


@section('content')

<div class="row">

    <div class="col-12">

        <div class="card hero-diseases">

            <div class="card-body text-center">

                <h1 class="mb-3">

                    Disease Explorer

                </h1>

                <p class="mb-0">

                    Explore poultry diseases, clinical findings, lesions and diagnostic information.

                </p>

            </div>

        </div>

    </div>

</div>


<div class="row mt-4">

    <div class="col-md-6">

        <input
            type="text"
            id="diseaseSearch"
            class="form-control search-box"
            placeholder="Search diseases...">

    </div>

</div>


<div class="row mt-4" id="diseaseContainer">

    @forelse($diseases as $disease)

        <div class="col-xl-4 col-lg-6 mb-4 disease-item">

            <div class="card disease-card">

                <div class="card-body position-relative">

                    @if($disease->zoonotic)

                        <span class="badge badge-danger zoonotic-badge">

                            Zoonotic

                        </span>

                    @endif

                    <h5 class="disease-title mb-3">

                        {{ $disease->name }}

                    </h5>

                    @if($disease->agent_type)

                        <span class="badge badge-primary agent-badge">

                            {{ ucfirst($disease->agent_type) }}

                        </span>

                    @endif

                    <hr>

                    <a href="{{ route('diseases.show',$disease->slug) }}"
                       class="btn btn-primary btn-block">

                        <i class="fa fa-book-open"></i>

                        Explore Disease

                    </a>

                </div>

            </div>

        </div>

    @empty

        <div class="col-12">

            <div class="card">

                <div class="card-body empty-box">

                    <i class="fa fa-folder-open fa-4x text-muted mb-4"></i>

                    <h3>

                        No diseases found

                    </h3>

                </div>

            </div>

        </div>

    @endforelse

</div>


@if($diseases->hasPages())

<div class="row">

    <div class="col-12">

        <div class="d-flex justify-content-center">

            {{ $diseases->links() }}

        </div>

    </div>

</div>

@endif

@endsection


@section('js')

<script>

document.addEventListener(

    'DOMContentLoaded',

    function(){

        let searchInput =
            document.getElementById(
                'diseaseSearch'
            );

        searchInput.addEventListener(

            'keyup',

            function(){

                let value =
                    this.value.toLowerCase();

                document
                    .querySelectorAll(
                        '.disease-item'
                    )

                    .forEach(function(card){

                        let text =
                            card.innerText
                                .toLowerCase();

                        card.style.display =
                            text.includes(value)
                            ? ''
                            : 'none';
                    });

            });

    });

</script>

@endsection

