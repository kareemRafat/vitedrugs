@extends('layouts.master')

@section('css')
    <style>
        .hero-compare {

            background: linear-gradient(135deg, #1e3c72, #2a5298);

            color: #fff;

            border-radius: 20px;

            overflow: hidden;
        }

        .compare-card {

            border: 0;

            border-radius: 15px;

            box-shadow: 0 6px 20px rgba(0, 0, 0, .06);
        }

        .compare-select {

            height: 55px;

            border-radius: 12px;
        }

        .section-title {

            background: #f8f9fa;

            padding: 12px 18px;

            border-radius: 10px;

            font-weight: 600;

            font-size: 16px;

            margin: 30px 0 20px;
        }

        .feature-row {

            padding: 15px 10px;

            border-bottom: 1px solid #f1f1f1;
        }

        .feature-row:hover {

            background: #fafafa;
        }

        .compare-icon {

            font-size: 18px;

            font-weight: bold;
        }

        .compare-yes {

            color: #28a745;
        }

        .compare-no {

            color: #dc3545;
        }

        .compare-column {

            background: #fff;

            border-radius: 12px;

            padding: 20px;

            box-shadow: 0 2px 10px rgba(0, 0, 0, .05);

            height: 100%;
        }

        .compare-column h5 {

            border-bottom: 1px solid #eee;

            padding-bottom: 10px;

            margin-bottom: 15px;
        }

        .loading-box {

            display: none;

            text-align: center;

            padding: 50px;
        }

        .result-wrapper {

            animation: fadeIn .4s ease;
        }

        @keyframes fadeIn {

            from {

                opacity: 0;

                transform: translateY(15px);
            }

            to {

                opacity: 1;

                transform: translateY(0);
            }
        }
    </style>
@endsection


@section('page-header')
    @include('layouts.breadcrumbs', [
        'items' => [
            [
                'title' => 'Tools',
            ],
    
            [
                'title' => 'Disease Comparison',
            ],
        ],
    ])
@endsection


@section('content')
    <div class="row">

        <div class="col-12">

            <div class="card hero-compare">

                <div class="card-body text-center py-5">

                    <h1 class="mb-3">

                        Disease Comparison Engine

                    </h1>

                    <p class="mb-0">

                        Compare diseases side-by-side and explore their similarities and differences.

                    </p>

                </div>

            </div>

        </div>

    </div>


    <div class="card compare-card mt-4">

        <div class="card-body">

            <div class="row">

                <div class="col-lg-5">

                    <select id="diseaseA" class="form-control compare-select">

                        <option value="">

                            Select First Disease

                        </option>

                        @foreach ($diseases as $disease)
                            <option value="{{ $disease->id }}">

                                {{ $disease->name }}

                            </option>
                        @endforeach

                    </select>

                </div>

                <div class="col-lg-5">

                    <select id="diseaseB" class="form-control compare-select">

                        <option value="">

                            Select Second Disease

                        </option>

                        @foreach ($diseases as $disease)
                            <option value="{{ $disease->id }}">

                                {{ $disease->name }}

                            </option>
                        @endforeach

                    </select>

                </div>

                <div class="col-lg-2">

                    <button class="btn btn-primary btn-block h-100" onclick="compareDiseases()">

                        <i class="fa fa-balance-scale"></i>

                        Compare

                    </button>

                </div>

            </div>

        </div>

    </div>


    <div class="loading-box" id="loadingBox">

        <div class="spinner-border text-primary">

        </div>

        <p class="mt-3">

            Comparing diseases...

        </p>

    </div>


    <div id="comparisonResult" class="mt-4">

    </div>
@endsection



@section('js')
    <script>
        async function compareDiseases() {
            const diseaseA =
                document.getElementById(
                    'diseaseA'
                ).value;

            const diseaseB =
                document.getElementById(
                    'diseaseB'
                ).value;

            if (!diseaseA || !diseaseB) {

                Swal.fire({
                    icon: 'warning',
                    title: 'Selection Required',
                    text: 'Please select two diseases.'
                });

                return;
            }

            if (diseaseA === diseaseB) {

                Swal.fire({
                    icon: 'warning',
                    title: 'Invalid Comparison',
                    text: 'Please select two different diseases.'
                });

                return;
            }

            document.getElementById(
                'loadingBox'
            ).style.display = 'block';

            try {

                const response = await fetch(
                    '/api/v1/diseases/compare', {

                        method: 'POST',

                        headers: {

                            'Content-Type': 'application/json',

                            'Accept': 'application/json',

                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },

                        body: JSON.stringify({

                            disease_a: diseaseA,

                            disease_b: diseaseB
                        })
                    }
                );

                if (!response.ok) {

                    throw new Error(
                        'Comparison failed'
                    );
                }

                const data =
                    await response.json();

                renderComparison(data);

            } catch (error) {

                console.error(error);

                Swal.fire({

                    icon: 'error',

                    title: 'Error',

                    text: 'Unable to compare diseases.'
                });

            } finally {

                document.getElementById(
                    'loadingBox'
                ).style.display = 'none';
            }
        }


        function renderComparison(data) {
            let html = `

    <div class="result-wrapper">

        <div class="card compare-card">

            <div class="card-body">

                <h2 class="text-center mb-5">

                    ${data.disease_a.name}

                    <span class="text-muted">

                        VS

                    </span>

                    ${data.disease_b.name}

                </h2>

                <div class="row mb-3">

                    <div class="col-md-4">

                        <strong>

                            Feature

                        </strong>

                    </div>

                    <div class="col-md-4 text-center">

                        <strong>

                            ${data.disease_a.name}

                        </strong>

                    </div>

                    <div class="col-md-4 text-center">

                        <strong>

                            ${data.disease_b.name}

                        </strong>

                    </div>

                </div>
    `;


            data.comparison.forEach(row => {

                html += `

        <div class="row feature-row">

            <div class="col-md-4">

                ${row.feature}

            </div>

            <div class="col-md-4 text-center">

                <i class="fa
                    ${
                        row.disease_a
                            ? 'fa-check compare-yes'
                            : 'fa-times compare-no'
                    }
                    compare-icon">

                </i>

            </div>

            <div class="col-md-4 text-center">

                <i class="fa
                    ${
                        row.disease_b
                            ? 'fa-check compare-yes'
                            : 'fa-times compare-no'
                    }
                    compare-icon">

                </i>

            </div>

        </div>
        `;
            });


            html += buildSection(
                'Species',
                data.species_comparison
            );

            html += buildSection(
                'Age Groups',
                data.age_comparison
            );

            html += buildSection(
                'Transmission Routes',
                data.transmission_comparison
            );

            html += buildSection(
                'Diagnostic Tests',
                data.diagnostic_tests_comparison
            );

            html += buildSection(
                'Vaccination Importance',
                data.vaccination_comparison ?
                {
                    disease_a: [
                        data.vaccination_comparison
                        .disease_a
                    ],

                    disease_b: [
                        data.vaccination_comparison
                        .disease_b
                    ]
                } :
                null
            );

            html += `

            </div>

        </div>

    </div>
    `;

            document.getElementById(
                'comparisonResult'
            ).innerHTML = html;
        }


        function buildSection(
            title,
            section
        ) {
            if (!section) {

                return '';
            }

            const diseaseAItems =
                section.disease_a || [];

            const diseaseBItems =
                section.disease_b || [];

            return `

    <div class="section-title">

        ${title}

    </div>

    <div class="row">

        <div class="col-md-6">

            <div class="compare-column">

                ${
                    diseaseAItems.length

                    ?

                    diseaseAItems
                        .map(item =>

                            `<span class="badge badge-primary m-1">

                                    ${formatItem(item)}

                                </span>`
                        )
                        .join('')

                    :

                    '<span class="text-muted">No data available</span>'
                }

            </div>

        </div>

        <div class="col-md-6">

            <div class="compare-column">

                ${
                    diseaseBItems.length

                    ?

                    diseaseBItems
                        .map(item =>

                            `<span class="badge badge-info m-1">

                                    ${formatItem(item)}

                                </span>`
                        )
                        .join('')

                    :

                    '<span class="text-muted">No data available</span>'
                }

            </div>

        </div>

    </div>
    `;
        }


        function formatItem(item) {
            if (
                item === null ||
                item === undefined
            ) {

                return 'Unknown';
            }

            if (
                typeof item === 'object'
            ) {

                if (
                    item.min_days !== undefined
                ) {

                    return `
                ${item.min_days ?? 0}
                -
                ${item.max_days ?? '∞'}
                days
            `;
                }

                return Object.values(item)
                    .filter(Boolean)
                    .join(' - ');
            }

            return item;
        }
    </script>
@endsection
