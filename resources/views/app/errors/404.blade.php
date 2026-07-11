@extends('app.layouts.master')

@section('content')

<div class="text-center py-5">

    <h1>404</h1>

    <h3>Page Not Found</h3>

    <p>
        The requested page does not exist.
    </p>

    <a href="{{ route('home') }}"
       class="btn btn-primary">
        Back to Home
    </a>

</div>

@endsection
