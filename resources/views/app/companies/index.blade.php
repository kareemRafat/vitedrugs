@extends('app.layouts.master')

@section('title', __('messages.companies.index_title'))

@section('content')
    @livewire('company.company-browser')
@endsection
