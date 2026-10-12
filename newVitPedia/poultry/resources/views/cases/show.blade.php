@extends('layouts.master')

@section('content')
    <div class="card">

        <div class="card-body">

            <h1>

                {{ $case->title }}

            </h1>

            <hr>

            <h4>History</h4>

            {!! nl2br(e($case->history)) !!}

            <hr>

            <h4>Clinical Findings</h4>

            {!! nl2br(e($case->clinical_findings)) !!}

            <hr>

            <h4>Necropsy Findings</h4>

            {!! nl2br(e($case->necropsy_findings)) !!}

            <hr>

            <h4>Diagnosis</h4>

            {!! nl2br(e($case->diagnosis)) !!}

            <hr>

            <h4>Treatment</h4>

            {!! nl2br(e($case->treatment)) !!}

            <hr>

            <h4>Outcome</h4>

            {!! nl2br(e($case->outcome)) !!}

        </div>
        <hr>

        <h3>

            Similar Cases

        </h3>

        <div class="row">

            @foreach ($relatedCases as $related)
                <div class="col-md-6">

                    <div class="card mb-3">

                        <div class="card-body">

                            <h5>

                                <a
                                    href="{{ route('cases.show', $related['case']->slug) }}">

                                    {{ $related['case']->title }}

                                </a>

                            </h5>

                            <span class="badge badge-success">

                                Similarity

                                {{ $related['similarity'] }}%

                            </span>

                            <hr>

                            <strong>

                                Matched Terms

                            </strong>

                            <br>

                            {{ implode(', ', $related['matched_terms']) }}

                        </div>

                    </div>

                </div>
            @endforeach

        </div>

    </div>
@endsection
@section('js')

<script>

document.addEventListener(

    'DOMContentLoaded',

    function(){

        saveRecentlyViewed(

            'case',

            @json($case->title),

            window.location.href
        );
    }
);

</script>

@endsection