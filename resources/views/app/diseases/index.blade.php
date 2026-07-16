@extends('app.layouts.master')

@section('title', __('messages.diseases.index_title'))

@section('content')
    @livewire('disease.disease-browser')
@endsection
