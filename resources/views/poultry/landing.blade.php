@extends('app.layouts.master')

@section('title', 'VetPedia Poultry')

@section('meta_description', 'VetPedia poultry health and veterinary resources.')

@section('content')
    <section class="relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-brand/15 via-transparent to-brand/15 dark:from-brand/20 dark:to-brand/10 pointer-events-none"></div>
        <div class="relative max-w-7xl mx-auto px-4 py-16 sm:py-28 text-center">
            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-brand/10 dark:bg-brand/20 text-fg-brand-strong dark:text-brand text-xs font-semibold uppercase tracking-widest border border-brand-subtle">
                <span class="w-1.5 h-1.5 rounded-full bg-brand"></span>
                {{ $part ?? 'poultry' }}
            </span>
            <h1 class="mt-5 text-4xl sm:text-6xl font-extrabold text-heading dark:text-white tracking-tight">VetPedia Poultry</h1>
            <p class="mt-5 mx-auto max-w-2xl text-lg text-body dark:text-slate-400 leading-relaxed">
                Poultry health knowledge and veterinary resources — coming to life part by part.
            </p>
            <div class="mt-9 flex flex-col sm:flex-row items-center justify-center gap-3">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2 px-6 py-3 text-sm font-semibold text-white bg-brand hover:bg-brand-strong rounded-base shadow-lg shadow-brand/25 transition-all active:scale-95">
                    Back to hub
                </a>
                <a href="{{ route('drugs.home') }}" class="inline-flex items-center gap-2 px-6 py-3 text-sm font-semibold text-fg-brand border border-brand rounded-base hover:bg-brand-soft transition-colors">
                    Explore Drugs
                </a>
            </div>
        </div>
    </section>

    <section class="px-4 py-12 sm:py-20">
        <div class="max-w-7xl mx-auto">
            <div class="rounded-2xl sm:rounded-3xl border border-default-medium dark:border-slate-700 bg-white dark:bg-slate-800/50 p-6 sm:p-10 text-center">
                <div class="mx-auto w-16 h-16 rounded-2xl bg-brand/10 dark:bg-brand/20 text-brand flex items-center justify-center mb-5">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 20h9m-9 0a9 9 0 119-9m-9 3a6 6 0 106-6m0-3a6 6 0 11-6-6"/></svg>
                </div>
                <h3 class="text-lg sm:text-xl font-bold text-heading dark:text-white mb-2">Poultry section is under construction</h3>
                <p class="max-w-md mx-auto text-sm text-body dark:text-slate-400 leading-relaxed">Dedicated poultry health features will be added here soon. In the meantime, explore the shared veterinary knowledge.</p>
            </div>
        </div>
    </section>

    <section class="px-4 pb-16 sm:pb-24">
        <div class="max-w-7xl mx-auto">
            <div class="rounded-2xl sm:rounded-3xl bg-gradient-to-br from-brand via-brand-strong to-brand-strong p-6 sm:p-10 text-center">
                <h2 class="text-lg sm:text-2xl font-bold text-white mb-3 tracking-tight">Start with the veterinary drug & disease knowledge.</h2>
                <a href="{{ route('drugs.home') }}" class="inline-flex items-center gap-2 px-6 py-3 text-sm font-semibold bg-white text-brand-strong rounded-full hover:bg-white/90 transition-all">
                    Go to Drugs
                </a>
            </div>
        </div>
    </section>
@endsection