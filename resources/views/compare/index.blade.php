@extends('app.layouts.master')

@section('title', __($part.'.comparison.title'))

@section('content')
    {{-- Shared by both parts. `$part` is the route-name prefix and the
         translation namespace at once, which is deliberate (§12.1): one typo
         would change the URL and its copy together. --}}
    @livewire('knowledge.disease-compare-builder', ['part' => $part])
@endsection