@extends('app.layouts.master')

@section('title', __('messages.active_ingredients.index_title'))

@section('content')
    @livewire('active-ingredient.active-ingredient-browser')
@endsection
