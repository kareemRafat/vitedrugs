@extends('app.layouts.master')

@section('title', $company->name . ' | VetPedia')

@section('meta_description')
    {{ \Illuminate\Support\Str::limit(strip_tags(app()->getLocale() === 'ar' && $company->description_ar ? $company->description_ar : $company->description ?? ''), 160) }}
@endsection

@section('meta_keywords')
    {{ $company->name }}, veterinary company
@endsection

@section('og_title', $company->name)

@section('og_description')
    {{ \Illuminate\Support\Str::limit(app()->getLocale() === 'ar' && $company->description_ar ? $company->description_ar : $company->description, 160) }}
@endsection

@section('content')
    @livewire('company.company-show', ['company' => $company])
@endsection
