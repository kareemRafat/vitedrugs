@extends('layouts.master')

@section('css')
    <style>
        .hero-disease {
            background: linear-gradient(135deg, #1d4350, #a43931);
            color: #fff;
            border: 0;
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, .12);
        }


        .hero-disease .card-body {
            padding: 35px;
        }

        .disease-title {
            font-size: 2.3rem;
            font-weight: 700;
            margin-bottom: 15px;
        }

        .info-badge {
            font-size: 13px;
            padding: 8px 14px;
            margin: 3px;
            border-radius: 30px;
        }

        .section-card {
            border: 0;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, .06);
            margin-bottom: 25px;
        }

        .section-card .card-header {
            background: #fff;
            font-weight: 700;
            border-bottom: 1px solid #eef1f7;
            padding: 15px 20px;
        }

        .finding-item {
            padding: 14px 18px;
            border-bottom: 1px solid #eef1f7;
            transition: .2s;
        }

        .finding-item:last-child {
            border-bottom: 0;
        }

        .finding-item:hover {
            background: #f8f9fb;
        }

        .nav-tabs {
            border-bottom: 2px solid #eef1f7;
        }

        .nav-tabs .nav-link {
            border: 0;
            color: #6c757d;
            font-weight: 600;
            padding: 14px 18px;
        }

        .nav-tabs .nav-link.active {
            color: #0162e8;
            border-bottom: 3px solid #0162e8;
            background: transparent;
        }

        .quick-stat {
            text-align: center;
            padding: 20px 10px;
        }

        .quick-stat h2 {
            color: #0162e8;
            margin-bottom: 5px;
            font-weight: 700;
        }

        .quick-stat span {
            color: #6c757d;
            font-size: 13px;
        }

        .similar-disease-item {
            padding: 12px 0;
            border-bottom: 1px solid #eef1f7;
        }

        .similar-disease-item:last-child {
            border-bottom: 0;
        }

        .disease-actions .btn {
            margin-right: 10px;
            margin-top: 10px;
        }

        .image-card img {
            height: 240px;
            object-fit: cover;
        }

        @media (max-width: 768px) {

            .disease-title {
                font-size: 1.7rem;
            }

            .hero-disease .card-body {
                padding: 25px;
            }

            .nav-tabs .nav-link {
                padding: 10px;
                font-size: 13px;
            }
        }
    </style>
@endsection

@section('page-header')
    @include('layouts.breadcrumbs', [
        'items' => [
            [
                'title' => 'Disease Explorer',
                'url' => route('diseases.index'),
            ],
            [
                'title' => $disease->name,
            ],
        ],
    ])
@endsection

@section('content')


    <div class="row">

        <div class="col-12">

            <div class="card hero-disease">

                <div class="card-body">

                    <h1 class="disease-title">

                        {{ $disease->name }}

                    </h1>

                    <div class="mb-3">

                        @foreach ($disease->species as $species)
                            <span class="badge badge-light info-badge">

                                {{ $species->name }}

                            </span>
                        @endforeach

                    </div>

                    <div class="disease-actions">

                        <a href="{{ route('diseases.graph', $disease->slug) }}" class="btn btn-light">

                            <i class="fa fa-project-diagram"></i>

                            Knowledge Graph

                        </a>

                    </div>

                </div>

            </div>

        </div>

    </div>


    <div class="row mt-4">

        {{-- LEFT SIDEBAR --}}

        <div class="col-xl-3">

            <div class="card section-card">

                <div class="card-header">

                    Quick Facts

                </div>

                <div class="card-body p-0">

                    <div class="row no-gutters">

                        <div class="col-12 quick-stat">

                            <h2>

                                {{ $disease->diagnosticTests->count() }}

                            </h2>

                            <span>

                                Diagnostic Tests

                            </span>

                        </div>

                        <div class="col-12 quick-stat border-top">

                            <h2>

                                {{ $disease->findings->count() }}

                            </h2>

                            <span>

                                Clinical Findings

                            </span>

                        </div>

                        <div class="col-12 quick-stat border-top">

                            <h2>

                                {{ $disease->lesions->count() }}

                            </h2>

                            <span>

                                Necropsy Lesions

                            </span>

                        </div>

                    </div>

                </div>

            </div>


            <div class="card section-card">

                <div class="card-header">

                    Similar Diseases

                </div>

                <div class="card-body">

                    @forelse($similarDiseases as $similar)
                        @php

                            $relatedDisease = $similar['disease'];

                            $similarity =
                                $similar['similarity'] ??
                                ($similar['similarity_percentage'] ?? ($similar['score'] ?? 0));
                        @endphp

                        <div class="similar-disease-item">

                            <a href="{{ route('diseases.show', $relatedDisease->slug) }}">

                                <strong>

                                    {{ $relatedDisease->name }}

                                </strong>

                            </a>

                            <div class="progress mt-2">

                                <div class="progress-bar" role="progressbar" style="width: {{ min(100, $similarity) }}%">

                                    {{ round($similarity) }}%

                                </div>

                            </div>

                        </div>

                    @empty

                        <div class="alert alert-light mb-0">

                            No similar diseases found.

                        </div>
                    @endforelse

                </div>

            </div>

        </div>


        {{-- MAIN CONTENT --}}

        <div class="col-xl-9">

            <div class="card section-card">

                <div class="card-body">

                    <ul class="nav nav-tabs">

                        <li class="nav-item">

                            <a class="nav-link active" data-toggle="tab" href="#overview">

                                Overview

                            </a>

                        </li>

                        <li class="nav-item">

                            <a class="nav-link" data-toggle="tab" href="#clinical">

                                Clinical Signs

                            </a>

                        </li>

                        <li class="nav-item">

                            <a class="nav-link" data-toggle="tab" href="#necropsy">

                                Necropsy

                            </a>

                        </li>

                        <li class="nav-item">

                            <a class="nav-link" data-toggle="tab" href="#diagnosis">

                                Diagnosis

                            </a>

                        </li>

                        <li class="nav-item">

                            <a class="nav-link" data-toggle="tab" href="#differential">

                                Differential

                            </a>

                        </li>

                        <li class="nav-item">

                            <a class="nav-link" data-toggle="tab" href="#images">

                                Images

                                @if ($disease->media->count())
                                    <span class="badge badge-primary">

                                        {{ $disease->media->count() }}

                                    </span>
                                @endif

                            </a>

                        </li>

                    </ul>


                    <div class="tab-content pt-4">

                        {{-- OVERVIEW --}}

                        <div id="overview" class="tab-pane fade show active">

                            <div class="row">

                                <div class="col-md-6">

                                    <div class="card border">

                                        <div class="card-header">
                                            <strong>Transmission Routes</strong>
                                        </div>

                                        <div class="card-body">

                                            @if (isset($disease->transmissionRoutes) && $disease->transmissionRoutes->count())
                                                @foreach ($disease->transmissionRoutes as $route)
                                                    <span class="badge badge-primary m-1">

                                                        {{ $route->name ?? 'Unknown' }}

                                                    </span>
                                                @endforeach
                                            @else
                                                <div class="alert alert-light mb-0">

                                                    No information available yet.

                                                </div>
                                            @endif

                                        </div>

                                    </div>

                                </div>


                                <div class="col-md-6">

                                    <div class="card border">

                                        <div class="card-header">
                                            <strong>Affected Species</strong>
                                        </div>

                                        <div class="card-body">

                                            @if (isset($disease->species) && $disease->species->count())
                                                @foreach ($disease->species as $species)
                                                    <span class="badge badge-success m-1">

                                                        {{ $species->name ?? 'Unknown' }}

                                                    </span>
                                                @endforeach
                                            @else
                                                <div class="alert alert-light mb-0">

                                                    No information available yet.

                                                </div>
                                            @endif

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>


                        {{-- CLINICAL SIGNS --}}

                        <div id="clinical" class="tab-pane fade">

                            @if (isset($disease->findings) && $disease->findings->count())
                                @foreach ($disease->findings as $finding)
                                    <div class="finding-item">

                                        <div class="d-flex justify-content-between">

                                            <strong>

                                                {{ $finding->display_name ?? 'Unknown Finding' }}

                                            </strong>

                                            @if (optional($finding->pivot)->is_pathognomonic)
                                                <span class="badge badge-danger">

                                                    Pathognomonic

                                                </span>
                                            @endif

                                        </div>

                                    </div>
                                @endforeach
                            @else
                                <div class="alert alert-light">

                                    No information available yet.

                                </div>
                            @endif

                        </div>


                        {{-- NECROPSY LESIONS --}}

                        <div id="necropsy" class="tab-pane fade">

                            @if (isset($disease->lesions) && $disease->lesions->count())
                                @foreach ($disease->lesions as $lesion)
                                    <div class="finding-item">

                                        <strong>

                                            {{ $lesion->display_name ?? 'Unknown Lesion' }}

                                        </strong>

                                    </div>
                                @endforeach
                            @else
                                <div class="alert alert-light">

                                    No information available yet.

                                </div>
                            @endif

                        </div>


                        {{-- DIAGNOSTIC TESTS --}}

                        <div id="diagnosis" class="tab-pane fade">

                            @if (isset($disease->diagnosticTests) && $disease->diagnosticTests->count())
                                @foreach ($disease->diagnosticTests as $test)
                                    <div class="finding-item">

                                        <strong>

                                            {{ $test->name ?? 'Unnamed Test' }}

                                        </strong>

                                    </div>
                                @endforeach
                            @else
                                <div class="alert alert-light">

                                    No information available yet.

                                </div>
                            @endif

                        </div>


                        {{-- DIFFERENTIAL DIAGNOSIS --}}

                        <div id="differential" class="tab-pane fade">

                            @if (isset($disease->differentialDiagnoses) && $disease->differentialDiagnoses->count())
                                @foreach ($disease->differentialDiagnoses as $diff)
                                    <div class="finding-item">

                                        @if (!empty($diff->slug))
                                            <a href="{{ route('diseases.show', $diff->slug) }}">

                                                {{ $diff->name ?? 'Unnamed Disease' }}

                                            </a>
                                        @else
                                            {{ $diff->name ?? 'Unnamed Disease' }}
                                        @endif

                                    </div>
                                @endforeach
                            @else
                                <div class="alert alert-light">

                                    No information available yet.

                                </div>
                            @endif

                        </div>


                        {{-- IMAGES --}}

                        <div id="images" class="tab-pane fade">

                            <div class="row">

                                @forelse($disease->media as $image)
                                    <div class="col-xl-4 col-lg-6">

                                        <div class="card mb-4">

                                            <a href="{{ $image->image_url }}" target="_blank">

                                                <img src="{{ $image->image_url }}" class="card-img-top"
                                                    style="
                                height:250px;
                                object-fit:cover;
                            ">

                                            </a>

                                            <div class="card-body">

                                                <h5>

                                                    {{ $image->title }}

                                                </h5>

                                                <span class="badge badge-info">

                                                    {{ str_replace('_', ' ', ucfirst($image->image_type)) }}

                                                </span>

                                                @if ($image->caption)
                                                    <hr>

                                                    <p class="mb-0">

                                                        {{ $image->caption }}

                                                    </p>
                                                @endif

                                                @if ($image->source)
                                                    <small class="text-muted d-block mt-3">

                                                        Source:

                                                        {{ $image->source }}

                                                    </small>
                                                @endif

                                            </div>

                                        </div>

                                    </div>

                                @empty

                                    <div class="col-12">

                                        <div class="alert alert-light">

                                            No images available for this disease.

                                        </div>

                                    </div>
                                @endforelse

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>


@endsection

@section('js')
    <script>
        document.addEventListener(

            'DOMContentLoaded',

            function() {

                $(document).on(

                    'click',

                    '[data-toggle="lightbox"]',

                    function(event) {

                        event.preventDefault();

                        $(this).ekkoLightbox();

                    }
                );

            }

        );
    </script>
@endsection
