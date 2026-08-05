@extends('app.layouts.master')

@section('title', __('large-animals.comparison.title'))

@section('content')
    @livewire('large-animals.disease-compare-builder')
@endsection
