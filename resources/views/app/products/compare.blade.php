@extends('app.layouts.master')

@section('title', __('messages.compare.page_title'))

@section('content')
    @livewire('products.product-compare-builder')
@endsection
