@extends('layouts.master')

@section('css')
    <style>
        .finding-hero {
            background: linear-gradient(135deg, #283c86, #45a247);
            color: #fff;
            border-radius: 20px;
            overflow: hidden;
        }

        .info-box {
            border: 0;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, .06);
        }

        .related-item {
            padding: 12px 18px;
            border-bottom: 1px solid #f1f1f1;
        }

        .related-item:last-child {
            border-bottom: 0;
        }

        .related-item a {
            color: #495057;
            text-decoration: none;
        }

        .related-item a:hover {
            color: #0162e8;
        }

        .disease-card {
            border: 0;
            border-radius: 15px;
            transition: .3s;
            box-shadow: 0 2px 12px rgba(0, 0, 0, .05);
            height: 100%;
        }

        .disease-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, .12);
        }

        .stat-box {
            text-align: center;
            padding: 15px;
            border-radius: 12px;
            background: #f8f9fa;
            margin-bottom: 15px;
        }

        .stat-value {
            font-size: 28px;
            font-weight: 700;
            color: #0162e8;
        }

        .image-card img {
            height: 220px;
            object-fit: cover;
            border-radius: 10px;
            cursor: pointer;
        }

        .empty-state {
            text-align: center;
            padding: 50px 20px;
            color: #6c757d;
        }
    </style>
@endsection


@section('page-header')
    @include('layouts.breadcrumbs', [
        'items' => [
            [
                'title' => 'Finding Explorer',
                'url' => route('home'),
            ],
    
            [
                'title' => $finding->display_name,
            ],
        ],
    ])
@endsection


@section('content')

    <div class="row">

        {{-- HERO --}}

        <div class="col-12">

            <div class="card finding-hero">

                <div class="card-body py-5">

                    <h1 class="mb-3">

                        {{ $finding->display_name }}

                    </h1>

                    <p class="mb-0">

                        Clinical finding associated with

                        <strong>

                            {{ $finding->diseases->count() }}

                        </strong>

                        diseases.

                    </p>

                </div>

            </div>

        </div>

    </div>


    <div class="row mt-4">

        {{-- SIDEBAR --}}

        <div class="col-xl-3">

            <div class="card info-box">

                <div class="card-header">

                    Quick Statistics

                </div>

                <div class="card-body">

                    <div class="stat-box">

                        <div class="stat-value">

                            {{ $finding->diseases->count() }}

                        </div>

                        <small>

                            Associated Diseases

                        </small>

                    </div>

                    <div class="stat-box">

                        <div class="stat-value">

                            {{ $relatedFindings->count() }}

                        </div>

                        <small>

                            Related Findings

                        </small>

                    </div>

                    @if (isset($finding->media))
                        <div class="stat-box mb-0">

                            <div class="stat-value">

                                {{ $finding->media->count() }}

                            </div>

                            <small>

                                Clinical Images

                            </small>

                        </div>
                    @endif

                </div>

            </div>


            <div class="card info-box mt-4">

                <div class="card-header">

                    Related Findings

                </div>

                <div class="card-body p-0">

                    @forelse($relatedFindings as $related)
                        <div class="related-item">

                            <a href="{{ route('findings.show', $related->slug) }}">
                                {{ $related->display_name }}

                            </a>

                        </div>

                    @empty

                        <div class="p-4 text-muted">

                            No related findings available.

                        </div>
                    @endforelse

                </div>

            </div>

        </div>


        {{-- MAIN CONTENT --}}

        <div class="col-xl-9">

            <div class="card info-box">

                <div class="card-header">

                    Diseases Associated With

                    "{{ $finding->display_name }}"

                </div>

                <div class="card-body">

                    <div class="row">

                        @forelse($finding->diseases as $disease)
                            <div class="col-lg-6 mb-4">

                                <div class="card disease-card">

                                    <div class="card-body">

                                        <h5>

                                            <a href="{{ route('diseases.show', $disease->slug) }}">

                                                {{ $disease->name }}

                                            </a>

                                        </h5>

                                        <hr>

                                        @foreach ($disease->species as $species)
                                            <span class="badge badge-primary">

                                                {{ $species->name }}

                                            </span>
                                        @endforeach

                                        <br><br>

                                        @if ($disease->pivot->is_pathognomonic)
                                            <span class="badge badge-danger">

                                                Pathognomonic

                                            </span>
                                        @endif

                                        @if ($disease->pivot->weight)
                                            <span class="badge badge-info">

                                                Weight:

                                                {{ $disease->pivot->weight }}

                                            </span>
                                        @endif

                                        @if ($disease->pivot->specificity)
                                            <span class="badge badge-success">

                                                {{ ucfirst($disease->pivot->specificity) }}

                                            </span>
                                        @endif

                                        <hr>

                                        <strong>

                                            Diagnostic Tests

                                        </strong>

                                        <ul class="mt-2 mb-0">

                                            @forelse($disease->diagnosticTests->take(3) as $test)
                                                <li>

                                                    {{ $test->name }}

                                                </li>

                                            @empty

                                                <li>

                                                    Not available

                                                </li>
                                            @endforelse

                                        </ul>

                                    </div>

                                </div>

                            </div>

                        @empty

                            <div class="col-12">

                                <div class="empty-state">

                                    No diseases associated with this finding.

                                </div>

                            </div>
                        @endforelse

                    </div>

                </div>

            </div>


            {{-- IMAGES --}}

            @if (isset($finding->media))
                <div class="card info-box mt-4">

                    <div class="card-header">

                        Clinical Images

                    </div>

                    <div class="card-body">

                        <div class="row">

                            @forelse($finding->media as $image)
                                <div class="col-lg-4 col-md-6 mb-4">

                                    <div class="image-card">

                                        <a href="{{ $image->image_url }}" target="_blank">

                                            <img src="{{ $image->image_url }}" class="img-fluid w-100">

                                        </a>

                                        <div class="mt-3">

                                            <h6>

                                                {{ $image->title }}

                                            </h6>

                                            <span class="badge badge-info">

                                                {{ str_replace('_', ' ', ucfirst($image->image_type)) }}

                                            </span>

                                            @if ($image->caption)
                                                <p class="small text-muted mt-2 mb-0">

                                                    {{ $image->caption }}

                                                </p>
                                            @endif

                                        </div>

                                    </div>

                                </div>

                            @empty

                                <div class="col-12">

                                    <div class="empty-state">

                                        No images available.

                                    </div>

                                </div>
                            @endforelse

                        </div>

                    </div>

                </div>
            @endif

        </div>

    </div>

@endsection


@section('js')
@endsection
