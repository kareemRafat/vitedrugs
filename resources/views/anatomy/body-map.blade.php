@extends('app.layouts.master')

@section('title', __($part.'.body_map.title'))

@section('meta_description', __($part.'.body_map.subtitle'))

@section('content')
    {{-- The diagram, the panel and both views are the component's; this page
         only names the part it is rendering, which is also the translation
         namespace, so a typo would change the URL and its copy together. --}}
    @livewire('anatomy.body-map', ['part' => $part])
@endsection