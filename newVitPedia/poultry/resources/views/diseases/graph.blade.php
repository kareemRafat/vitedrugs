@extends('layouts.master')

@section('css')
    <link rel="stylesheet" href="https://unpkg.com/vis-network/styles/vis-network.min.css" />

    <style>
        .graph-hero {

            background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);

            color: #fff;

            border: 0;

            border-radius: 18px;

            overflow: hidden;

            box-shadow: 0 10px 30px rgba(0, 0, 0, .12);
        }

        .graph-hero .card-body {

            padding: 60px 40px;
        }

        .graph-subtitle {

            opacity: .85;

            font-size: 16px;

            margin-bottom: 0;
        }

        #knowledge-graph {

            width: 100%;

            height: 780px;

            border-radius: 18px;

            border: 1px solid #edf2f9;

            background: #ffffff;

            box-shadow: 0 4px 15px rgba(0, 0, 0, .05);
        }

        .graph-card {

            border: 0;

            border-radius: 18px;

            overflow: hidden;

            box-shadow: 0 4px 15px rgba(0, 0, 0, .05);
        }

        .graph-toolbar {

            display: flex;

            gap: 10px;

            flex-wrap: wrap;
        }

        .legend-box {

            background: #fff;

            border-radius: 18px;

            padding: 25px;

            border: 1px solid #edf2f9;

            box-shadow: 0 4px 15px rgba(0, 0, 0, .05);

            position: sticky;

            top: 100px;
        }

        .legend-title {

            font-size: 18px;

            font-weight: 600;
        }

        .legend-item {

            display: flex;

            align-items: center;

            margin-bottom: 18px;

            font-size: 14px;
        }

        .legend-circle {

            width: 18px;

            height: 18px;

            border-radius: 50%;

            display: inline-block;

            margin-right: 12px;

            flex-shrink: 0;
        }

        .graph-stat {

            border-radius: 12px;

            padding: 15px;

            background: #f8fafc;

            text-align: center;

            margin-bottom: 15px;
        }

        .graph-stat h4 {

            margin: 0;

            font-weight: 700;
        }

        .graph-stat span {

            font-size: 13px;

            color: #6c757d;
        }

        @media(max-width:991px) {

            #knowledge-graph {

                height: 600px;
            }

            .legend-box {

                margin-top: 20px;

                position: relative;

                top: 0;
            }
        }
    </style>
@endsection

@section('page-header')
    @include('layouts.breadcrumbs', [
        'items' => [
            [
                'title' => 'Disease Explorer',
                'url' => route('home'),
            ],
            [
                'title' => $disease->name,
                'url' => route('diseases.show', $disease->slug),
            ],
            [
                'title' => 'Knowledge Graph',
            ],
        ],
    ])
@endsection

@section('content')
    <div class="row">

        
        <div class="col-12">

            <div class="card graph-hero">

                <div class="card-body text-center">

                    <h1 class="mb-3">

                        {{ $disease->name }}

                    </h1>

                    <p class="graph-subtitle">

                        Interactive Clinical Knowledge Graph

                    </p>

                </div>

            </div>

        </div>
        

    </div>

    <div class="row mt-4">

        
        <div class="col-xl-9">

            <div class="card graph-card">

                <div class="card-header bg-white">

                    <div class="d-flex justify-content-between align-items-center flex-wrap">

                        <h5 class="mb-2 mb-md-0">

                            Disease Network

                        </h5>

                        <div class="graph-toolbar">

                            <button class="btn btn-sm btn-primary" id="fitGraphBtn">

                                <i class="fa fa-expand"></i>

                                Fit Graph

                            </button>

                            <button class="btn btn-sm btn-light" id="resetGraphBtn">

                                <i class="fa fa-refresh"></i>

                                Reset

                            </button>

                        </div>

                    </div>

                </div>

                <div class="card-body p-3">

                    <div id="knowledge-graph"></div>

                </div>

            </div>

        </div>


        <div class="col-xl-3">

            <div class="legend-box">

                <div class="legend-title mb-4">

                    Graph Overview

                </div>

                <div class="graph-stat">

                    <h4>

                        {{ $disease->findings->count() }}

                    </h4>

                    <span>Clinical Findings</span>

                </div>

                <div class="graph-stat">

                    <h4>

                        {{ $disease->lesions->count() }}

                    </h4>

                    <span>Necropsy Lesions</span>

                </div>

                <div class="graph-stat">

                    <h4>

                        {{ $disease->diagnosticTests->count() }}

                    </h4>

                    <span>Diagnostic Tests</span>

                </div>

                <div class="graph-stat">

                    <h4>

                        {{ $disease->differentialDiagnoses->count() }}

                    </h4>

                    <span>Differential Diseases</span>

                </div>

                <hr>

                <div class="legend-item">

                    <span class="legend-circle" style="background:#dc3545"></span>

                    Disease Node

                </div>

                <div class="legend-item">

                    <span class="legend-circle" style="background:#007bff"></span>

                    Clinical Finding

                </div>

                <div class="legend-item">

                    <span class="legend-circle" style="background:#fd7e14"></span>

                    Necropsy Lesion

                </div>

                <div class="legend-item">

                    <span class="legend-circle" style="background:#20c997"></span>

                    Diagnostic Test

                </div>

                <div class="legend-item">

                    <span class="legend-circle" style="background:#6f42c1"></span>

                    Differential Disease

                </div>

            </div>

        </div>
        

    </div>
@endsection

@section('js')
    <script src="https://unpkg.com/vis-network/standalone/umd/vis-network.min.js"></script>

    <script>
        const nodes = [];
        const edges = [];

        nodes.push({

            id: 'disease',

            label: @json($disease->name),

            color: '#dc3545',

            shape: 'dot',

            size: 42,

            font: {
                color: '#fff',
                size: 18,
                face: 'Arial'
            }
        });


        @foreach ($disease->findings as $finding)

            nodes.push({

                id: 'finding_{{ $finding->id }}',

                label: @json($finding->display_name),

                color: '#007bff',

                shape: 'dot',

                size: 20
            });

            edges.push({

                from: 'disease',

                to: 'finding_{{ $finding->id }}',

                label: 'finding'
            });
        @endforeach


        @foreach ($disease->lesions as $lesion)

            nodes.push({

                id: 'lesion_{{ $lesion->id }}',

                label: @json($lesion->display_name),

                color: '#fd7e14',

                shape: 'dot',

                size: 22
            });

            edges.push({

                from: 'disease',

                to: 'lesion_{{ $lesion->id }}',

                label: 'lesion'
            });
        @endforeach


        @foreach ($disease->diagnosticTests as $test)

            nodes.push({

                id: 'test_{{ $test->id }}',

                label: @json($test->name),

                color: '#20c997',

                shape: 'dot',

                size: 18
            });

            edges.push({

                from: 'disease',

                to: 'test_{{ $test->id }}',

                label: 'test'
            });
        @endforeach


        @foreach ($disease->differentialDiagnoses as $diff)

            nodes.push({

                id: 'diff_{{ $diff->id }}',

                label: @json($diff->name),

                color: '#6f42c1',

                shape: 'dot',

                size: 24
            });

            edges.push({

                from: 'disease',

                to: 'diff_{{ $diff->id }}',

                label: 'differential'
            });
        @endforeach


        const container =
            document.getElementById(
                'knowledge-graph'
            );

        const data = {

            nodes: new vis.DataSet(nodes),

            edges: new vis.DataSet(edges)
        };

        const options = {

            autoResize: true,

            physics: {

                enabled: true,

                stabilization: true,

                barnesHut: {

                    gravitationalConstant: -8000,

                    springLength: 180
                }
            },

            interaction: {

                hover: true,

                tooltipDelay: 100,

                navigationButtons: true,

                keyboard: true,

                zoomView: true
            },

            nodes: {

                borderWidth: 2
            },

            edges: {

                smooth: true,

                width: 2,

                color: '#c5cdd8',

                font: {
                    size: 10
                }
            }
        };

        const network =
            new vis.Network(
                container,
                data,
                options
            );


        document
            .getElementById(
                'fitGraphBtn'
            )
            .addEventListener(
                'click',
                () => network.fit()
            );


        document
            .getElementById(
                'resetGraphBtn'
            )
            .addEventListener(
                'click',
                () => {

                    network.moveTo({

                        position: {
                            x: 0,
                            y: 0
                        },

                        scale: 1
                    });

                    network.fit();
                }
            );


        network.on(

            'doubleClick',

            function(params) {

                if (!params.nodes.length)
                    return;

                const nodeId =
                    params.nodes[0];

                if (nodeId.startsWith('diff_')) {

                    const id =
                        nodeId.replace(
                            'diff_',
                            ''
                        );

                    window.location =
                        '/diseases/' + id;
                }
            }
        );
    </script>
@endsection
