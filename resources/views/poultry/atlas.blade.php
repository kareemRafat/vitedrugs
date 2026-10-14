@extends('app.layouts.master')

@section('title', __($part.'.atlas.title'))

@section('meta_description', __($part.'.atlas.subtitle'))

@section('content')
    {{-- The filters, the letter rail, the sections and the per-lesion disease
         lists are the component's. This page only names the part, which is also
         the translation namespace, so a typo would change the URL and its copy
         together. --}}
    @livewire('knowledge.lesion-atlas', ['part' => $part])
@endsection