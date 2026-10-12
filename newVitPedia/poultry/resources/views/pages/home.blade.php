@extends('layouts.master')

@section('css')
<style>
    .hero-card {
        background: linear-gradient(135deg, #1e3c72, #2a5298);
        color: #fff;
        border-radius: 15px;
        overflow: hidden;
    }

    .hero-card h1 {
        font-size: 42px;
        font-weight: 700;
    }

    .hero-card p {
        font-size: 18px;
        opacity: .9;
    }

    .feature-card {
        transition: all .3s ease;
        cursor: pointer;
        border: 0;
    }

    .feature-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,.12);
    }

    .stat-card {
        text-align: center;
        border: 0;
        transition: all .3s ease;
    }

    .stat-card:hover {
        transform: translateY(-3px);
    }

    .stat-number {
        font-size: 36px;
        font-weight: bold;
        color: #0162e8;
    }

    .section-title {
        font-size: 24px;
        font-weight: 600;
        margin-bottom: 25px;
    }

    .disease-card,
    .case-card {
        transition: all .3s ease;
    }

    .disease-card:hover,
    .case-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 20px rgba(0,0,0,.1);
    }

    .cta-box {
        background: linear-gradient(135deg, #0f9b0f, #00c851);
        color: white;
        border-radius: 15px;
    }
</style>
@endsection


@section('page-header')
<div class="breadcrumb-header justify-content-between">
    <div class="my-auto">
        <div class="d-flex">
            <h4 class="content-title mb-0 my-auto">

                VetPedia Poultry

            </h4>

            <span class="text-muted mt-1 tx-13 mr-2 mb-0">

                / Intelligent Poultry Platform

            </span>
        </div>
    </div>
</div>
@endsection


@section('content')

<div class="row">

    {{-- HERO SECTION --}}
    <div class="col-12">

        <div class="card hero-card">

            <div class="card-body text-center py-5">

                <h1>

                    VetPedia Poultry

                </h1>

                <p class="mt-3 mb-4">

                    Intelligent Poultry Clinical Decision Support System

                </p>

                <a href="{{ url('/diagnostic') }}"
                   class="btn btn-light btn-lg mr-2">

                    <i class="fa fa-stethoscope"></i>

                    Start Diagnosis

                </a>

                <a href="{{ route('clinical.search') }}"
                   class="btn btn-outline-light btn-lg">

                    <i class="fa fa-search"></i>

                    Clinical Search

                </a>

            </div>

        </div>

    </div>


    {{-- QUICK MODULES --}}
    <div class="col-12 mt-4">

        <h3 class="section-title">

            Platform Modules

        </h3>

    </div>

    <div class="col-xl-3 col-md-6">

        <a href="{{ url('/diagnostic') }}">

            <div class="card feature-card">

                <div class="card-body text-center">

                    <i class="fa fa-stethoscope fa-3x text-primary mb-3"></i>

                    <h5>Diagnosis Engine</h5>

                    <p class="text-muted">

                        AI-powered poultry diagnosis

                    </p>

                </div>

            </div>

        </a>

    </div>

    <div class="col-xl-3 col-md-6">

        <a href="{{ route('clinical.search') }}">

            <div class="card feature-card">

                <div class="card-body text-center">

                    <i class="fa fa-search fa-3x text-success mb-3"></i>

                    <h5>Clinical Search</h5>

                    <p class="text-muted">

                        Search diseases by findings

                    </p>

                </div>

            </div>

        </a>

    </div>

    <div class="col-xl-3 col-md-6">

        <a href="{{ route('anatomy.index') }}">

            <div class="card feature-card">

                <div class="card-body text-center">

                    <i class="fa fa-heartbeat fa-3x text-danger mb-3"></i>

                    <h5>Anatomical Navigator</h5>

                    <p class="text-muted">

                        Explore diseases anatomically

                    </p>

                </div>

            </div>

        </a>

    </div>

    <div class="col-xl-3 col-md-6">

        <a href="{{ route('cases.index') }}">

            <div class="card feature-card">

                <div class="card-body text-center">

                    <i class="fa fa-folder-open fa-3x text-warning mb-3"></i>

                    <h5>Case Library</h5>

                    <p class="text-muted">

                        Browse real clinical cases

                    </p>

                </div>

            </div>

        </a>

    </div>


    {{-- STATISTICS --}}
    <div class="col-12 mt-5">

        <h3 class="section-title">

            Knowledge Base Statistics

        </h3>

    </div>

    <div class="col-xl-3 col-md-6">

        <div class="card stat-card">
            <div class="card-body">
                <div class="stat-number">
                    {{ number_format($stats['diseases']) }}
                </div>

                <h6>Diseases</h6>
            </div>
        </div>

    </div>

    <div class="col-xl-3 col-md-6">

        <div class="card stat-card">
            <div class="card-body">
                <div class="stat-number">
                    {{ number_format($stats['findings']) }}
                </div>

                <h6>Clinical Findings</h6>
            </div>
        </div>

    </div>

    <div class="col-xl-3 col-md-6">

        <div class="card stat-card">
            <div class="card-body">
                <div class="stat-number">
                    {{ number_format($stats['lesions']) }}
                </div>

                <h6>Necropsy Lesions</h6>
            </div>
        </div>

    </div>

    <div class="col-xl-3 col-md-6">

        <div class="card stat-card">
            <div class="card-body">
                <div class="stat-number">
                    {{ number_format($stats['cases']) }}
                </div>

                <h6>Clinical Cases</h6>
            </div>
        </div>

    </div>


    {{-- FEATURED DISEASES --}}
    <div class="col-12 mt-5">

        <h3 class="section-title">

            Featured Diseases

        </h3>

    </div>

    @foreach($featuredDiseases as $disease)

        <div class="col-xl-4">

            <div class="card disease-card">

                <div class="card-body">

                    <h5>

                        {{ $disease->name }}

                    </h5>

                    <a href="{{ route('diseases.show',$disease->slug) }}"
                       class="btn btn-primary btn-sm mt-2">

                        Explore Disease

                    </a>

                </div>

            </div>

        </div>

    @endforeach


    {{-- RECENT CASES --}}
    <div class="col-12 mt-5">

        <h3 class="section-title">

            Recent Clinical Cases

        </h3>

    </div>

    @foreach($recentCases as $case)

        <div class="col-xl-6">

            <div class="card case-card">

                <div class="card-body">

                    <h5>

                        {{ $case->title }}

                    </h5>

                    <p class="text-muted">

                        {{ optional($case->species)->name }}

                    </p>

                    <a href="{{ route('cases.show',$case->slug) }}"
                       class="btn btn-outline-primary btn-sm">

                        View Case

                    </a>

                </div>

            </div>

        </div>

    @endforeach


    {{-- CTA --}}
    <div class="col-12 mt-5">

        <div class="card cta-box">

            <div class="card-body text-center py-5">

                <h2>

                    Need Help Diagnosing a Poultry Outbreak?

                </h2>

                <p class="mb-4">

                    Start an intelligent diagnostic session now.

                </p>

                <a href="{{ url('/diagnostic') }}"
                   class="btn btn-light btn-lg">

                    Start Diagnosis

                </a>

            </div>

        </div>

    </div>

</div>

@endsection


@section('js')
@endsection