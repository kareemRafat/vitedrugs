<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>
        @yield('title', 'VetPedia')
    </title>
    <meta name="description" content="@yield('meta_description', 'Veterinary pharmaceutical and disease knowledge platform.')" />
    <meta name="keywords" content="@yield('meta_keywords', 'veterinary drugs, veterinary diseases, active ingredients')" />
    <meta property="og:type" content="website">
    <meta property="og:title" content="@yield('og_title', View::yieldContent('title', 'VetPedia'))">
    <meta property="og:description" content="@yield('og_description', View::yieldContent('meta_description'))">
    <meta property="og:url" content="{{ url()->current() }}">

    @include('layouts.head')
</head>

<body>
    @include('layouts.main-sidebar')
    <div class="main-content">
        @include('layouts.main-header')
        <div class="container-fluid">
            @yield('page-header')
            @yield('content')
            @include('layouts.sidebar')
            @include('layouts.models')
            @include('layouts.footer')
            @include('layouts.footer-scripts')
</body>

</html>
