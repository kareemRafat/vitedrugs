@extends('app.layouts.master')

@section('title', __($part.'.catalogue.title'))

@section('content')
    {{-- Shared by both parts. `$part` is the route-name prefix and the
         translation namespace at once, which is deliberate (§12.1): one typo
         would change the URL and its copy together. --}}
    @livewire('products.part-catalogue', ['part' => $part])
@endsection