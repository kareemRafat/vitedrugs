@extends('app.layouts.master')

@section('title', __($part.'.diseases_list.title'))

@section('meta_description', __($part.'.diseases_list.subtitle'))

@section('content')
    {{-- Shared by both parts. `$part` is the route-name prefix and the
         translation namespace at once, which is deliberate (§12.1): one typo
         would change the URL and its copy together. The list, the letter rail,
         the filters and the counts are the component's; this page only names
         the part it is rendering. --}}
    @livewire('knowledge.disease-list', ['part' => $part])
@endsection