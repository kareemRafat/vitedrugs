@extends('layouts.master')

@section('css')

<style>
    .body-map-hero {
        background: linear-gradient(135deg, #1d4350, #a43931);
        color: #fff;
        border-radius: 15px;
        overflow: hidden;
    }

    .body-map-card {
        border: 0;
        border-radius: 15px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, .06);
    }

    .body-map-container {
        position: relative;
        text-align: center;
        max-width: 900px;
        margin: auto;
        padding: 20px;
    }

    .body-map-image {
        width: 100%;
        max-width: 700px;
        height: auto;
        user-select: none;
    }

    .organ-button {
        position: absolute;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        border: 3px solid #fff;
        background: #dc3545;
        cursor: pointer;
        transition: all .25s ease;
        box-shadow: 0 0 12px rgba(0, 0, 0, .25);
        z-index: 10;
    }

    .organ-button:hover {
        transform: scale(1.35);
        background: #28a745;
        box-shadow: 0 0 18px rgba(40, 167, 69, .6);
    }

    .organ-button:focus {
        outline: none;
        box-shadow: 0 0 0 4px rgba(1, 98, 232, .3);
    }

    .organ-label {
        position: absolute;
        transform: translate(-50%, -50%);
        background: rgba(0, 0, 0, .75);
        color: #fff;
        font-size: 12px;
        padding: 4px 10px;
        border-radius: 20px;
        white-space: nowrap;
        opacity: 0;
        pointer-events: none;
        transition: all .2s ease;
        z-index: 15;
    }

    .organ-button:hover + .organ-label {
        opacity: 1;
    }

    .legend-card {
        border: 0;
        border-radius: 15px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, .06);
        height: 100%;
    }

    .legend-item {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
    }

    .legend-dot {
        width: 18px;
        height: 18px;
        border-radius: 50%;
        background: #dc3545;
        margin-right: 10px;
        border: 2px solid #fff;
        box-shadow: 0 0 8px rgba(0,0,0,.2);
    }

    @media (max-width: 768px) {

        .organ-button {
            width: 22px;
            height: 22px;
        }

        .body-map-container {
            overflow-x: auto;
        }
    }
</style>

@endsection

@section('page-header')

@include('layouts.breadcrumbs', [
'items' => [
[
'title' => 'Anatomical Navigator',
'url' => route('anatomy.index'),
],
[
'title' => 'Interactive Body Map',
],
],
])

@endsection

@section('content')

<div class="row">

```
<div class="col-12">

    <div class="card body-map-hero">

        <div class="card-body text-center py-5">

            <h1 class="mb-3">

                Interactive Poultry Body Map

            </h1>

            <p class="mb-0">

                Explore poultry diseases anatomically by selecting an organ.

            </p>

        </div>

    </div>

</div>
```

</div>

<div class="row mt-4">

```
<div class="col-xl-9">

    <div class="card body-map-card">

        <div class="card-body">

            <div class="body-map-container">

                <img
                    src="{{ asset('assets/images/poultry-body-map.png') }}"
                    class="body-map-image"
                    alt="Interactive Poultry Body Map">

                {{-- Brain --}}
                <button class="organ-button"
                        style="top:18%;left:48%;"
                        onclick="goToOrgan('brain')"
                        aria-label="Brain"></button>

                <span class="organ-label"
                      style="top:14%;left:48%;">
                    Brain
                </span>

                {{-- Trachea --}}
                <button class="organ-button"
                        style="top:28%;left:47%;"
                        onclick="goToOrgan('trachea')"
                        aria-label="Trachea"></button>

                <span class="organ-label"
                      style="top:24%;left:47%;">
                    Trachea
                </span>

                {{-- Lung --}}
                <button class="organ-button"
                        style="top:37%;left:42%;"
                        onclick="goToOrgan('lung')"
                        aria-label="Lung"></button>

                <span class="organ-label"
                      style="top:33%;left:42%;">
                    Lung
                </span>

                {{-- Heart --}}
                <button class="organ-button"
                        style="top:45%;left:48%;"
                        onclick="goToOrgan('heart')"
                        aria-label="Heart"></button>

                <span class="organ-label"
                      style="top:41%;left:48%;">
                    Heart
                </span>

                {{-- Liver --}}
                <button class="organ-button"
                        style="top:52%;left:56%;"
                        onclick="goToOrgan('liver')"
                        aria-label="Liver"></button>

                <span class="organ-label"
                      style="top:48%;left:56%;">
                    Liver
                </span>

                {{-- Intestine --}}
                <button class="organ-button"
                        style="top:62%;left:52%;"
                        onclick="goToOrgan('intestine')"
                        aria-label="Intestine"></button>

                <span class="organ-label"
                      style="top:58%;left:52%;">
                    Intestine
                </span>

                {{-- Ceca --}}
                <button class="organ-button"
                        style="top:65%;left:58%;"
                        onclick="goToOrgan('ceca')"
                        aria-label="Ceca"></button>

                <span class="organ-label"
                      style="top:61%;left:58%;">
                    Ceca
                </span>

                {{-- Spleen --}}
                <button class="organ-button"
                        style="top:55%;left:38%;"
                        onclick="goToOrgan('spleen')"
                        aria-label="Spleen"></button>

                <span class="organ-label"
                      style="top:51%;left:38%;">
                    Spleen
                </span>

                {{-- Bursa --}}
                <button class="organ-button"
                        style="top:58%;left:44%;"
                        onclick="goToOrgan('bursa-of-fabricius')"
                        aria-label="Bursa of Fabricius"></button>

                <span class="organ-label"
                      style="top:54%;left:44%;">
                    Bursa
                </span>

                {{-- Kidney --}}
                <button class="organ-button"
                        style="top:57%;left:63%;"
                        onclick="goToOrgan('kidney')"
                        aria-label="Kidney"></button>

                <span class="organ-label"
                      style="top:53%;left:63%;">
                    Kidney
                </span>

            </div>

        </div>

    </div>

</div>

<div class="col-xl-3 mt-4 mt-xl-0">

    <div class="card legend-card">

        <div class="card-header">

            <h5 class="mb-0">

                Navigation Guide

            </h5>

        </div>

        <div class="card-body">

            <div class="legend-item">

                <span class="legend-dot"></span>

                Click any highlighted organ.

            </div>

            <div class="legend-item">

                <span class="legend-dot"></span>

                Explore associated diseases.

            </div>

            <div class="legend-item">

                <span class="legend-dot"></span>

                Review lesions and findings.

            </div>

            <div class="alert alert-light mt-4 mb-0">

                <strong>Tip:</strong>

                Hover over markers to identify anatomical structures.

            </div>

        </div>

    </div>

</div>
```

</div>

@endsection

@section('js')

<script>

    function goToOrgan(slug)
    {
        window.location.href =
            '/anatomy/' + slug;
    }

</script>

@endsection
