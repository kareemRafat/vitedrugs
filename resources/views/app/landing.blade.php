{{--
    Redesigned landing page.

    Section order follows the reference: hero → pillars → chapters → stats →
    contact → blog → CTA. The chatty blocks that used to live here (features,
    how-it-works, browse-by-category, why-us) are gone — they duplicated what
    the pillars and chapters sections now say, in less detail.

    The parts under views/app/landing/sections/ are plain includes, deliberately
    NOT under views/app/components/, which is a registered Livewire finder
    location — a Blade file dropped there would be resolved as a component name.
--}}
@extends('app.layouts.landing')

@section('title', __('messages.landing.meta.title'))
@section('meta_description', __('messages.landing.meta.description'))
@section('meta_keywords', __('messages.landing.meta.keywords'))
@section('og_title', __('messages.landing.meta.title'))
@section('og_description', __('messages.landing.meta.description'))

@section('content')

    @include('app.landing.sections.hero')
    @include('app.landing.sections.pillars')
    @include('app.landing.sections.chapters')
    @include('app.landing.sections.stats')
    @include('app.landing.sections.contact')
    @include('app.landing.sections.blog')
    @include('app.landing.sections.cta')

@endsection