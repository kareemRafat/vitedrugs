@extends('app.layouts.master')

@section('title', __('drugs.comparison.title'))

@section('content')
    @livewire('drugs.disease-compare-builder')
@endsection
