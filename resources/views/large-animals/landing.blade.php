@extends('app.layouts.master')

@section('title', 'VetPedia Drugs')

@section('meta_description', 'VetPedia veterinary drug and disease knowledge base.')

@section('content')
    <section class="relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-brand/15 via-transparent to-brand/15 dark:from-brand/20 dark:to-brand/10 pointer-events-none"></div>
        <div class="relative max-w-7xl mx-auto px-4 py-16 sm:py-28 text-center">
            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-brand/10 dark:bg-brand/20 text-fg-brand-strong dark:text-brand text-xs font-semibold uppercase tracking-widest border border-brand-subtle">
                <span class="w-1.5 h-1.5 rounded-full bg-brand"></span>
                {{ $part ?? 'drugs' }}
            </span>
            <h1 class="mt-5 text-4xl sm:text-6xl font-extrabold text-heading dark:text-white tracking-tight">VetPedia Drugs</h1>
            <p class="mt-5 mx-auto max-w-2xl text-lg text-body dark:text-slate-400 leading-relaxed">
                A clinical knowledge graph of veterinary drugs: clinical signs, findings, anatomical structures, body systems and disease ontologies.
            </p>
            <div class="mt-9 flex flex-col sm:flex-row items-center justify-center gap-3">
                <a href="{{ route('large-animals.home') }}" class="inline-flex items-center gap-2 px-6 py-3 text-sm font-semibold text-white bg-brand hover:bg-brand-strong rounded-base shadow-lg shadow-brand/25 transition-all active:scale-95">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    Knowledge Base
                </a>
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2 px-6 py-3 text-sm font-semibold text-fg-brand border border-brand rounded-base hover:bg-brand-soft transition-colors">
                    Back to hub
                </a>
            </div>
        </div>
    </section>

    <section class="px-4 py-12 sm:py-20">
        <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-6">
            <div class="rounded-2xl sm:rounded-3xl border border-default-medium dark:border-slate-700 bg-white dark:bg-slate-800/50 p-6 sm:p-8">
                <div class="w-12 h-12 rounded-2xl bg-brand/10 dark:bg-brand/20 text-brand flex items-center justify-center mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </div>
                <h3 class="text-lg font-bold text-heading dark:text-white mb-2">Clinical signs & findings</h3>
                <p class="text-sm text-body dark:text-slate-400 leading-relaxed">Structured clinical signs, findings and modifiers mapped to anatomical structures.</p>
            </div>

            <div class="rounded-2xl sm:rounded-3xl border border-default-medium dark:border-slate-700 bg-brand-soft dark:bg-slate-800 p-6 sm:p-8">
                <div class="w-12 h-12 rounded-2xl bg-white dark:bg-slate-700 text-brand flex items-center justify-center mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.05a2 2 0 00-1.1.105m8.381 8.442l2.646-1.058a2 2 0 001.005-3.488l-1.582-1.414a3.5 3.5 0 00-4.769 0L8.156 17.5a3.5 3.5 0 00-1.242 2.578l.047.522a2 2 0 001.378 1.758l2.088.73a2 2 0 001.55 0z"/></svg>
                </div>
                <h3 class="text-lg font-bold text-heading dark:text-white mb-2">Body systems & microstructure</h3>
                <p class="text-sm text-body dark:text-slate-400 leading-relaxed">Anatomical structures and body systems that anchor the clinical vocabulary.</p>
            </div>

            <div class="rounded-2xl sm:rounded-3xl border border-default-medium dark:border-slate-700 bg-white dark:bg-slate-800/50 p-6 sm:p-8">
                <div class="w-12 h-12 rounded-2xl bg-brand/10 text-brand flex items-center justify-center mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.14 2.286 6.857L13 18l-4.571 3.999L9 14.143l-4.571-2L12 5z"/></svg>
                </div>
                <h3 class="text-lg font-bold text-heading dark:text-white mb-2">Microorganisms</h3>
                <p class="text-sm text-body dark:text-slate-400 leading-relaxed">Taxonomic records of disease-causing microorganisms across the knowledge base.</p>
            </div>
        </div>
    </section>

    <section class="px-4 pb-16 sm:pb-24">
        <div class="max-w-7xl mx-auto">
            <div class="rounded-2xl sm:rounded-3xl bg-gradient-to-br from-brand via-brand-strong to-brand-strong p-6 sm:p-10 text-center">
                <h2 class="text-lg sm:text-2xl font-bold text-white mb-3 tracking-tight">Explore the veterinary drug & disease knowledge.</h2>
                <a href="{{ route('drugs.home') }}" class="inline-flex items-center gap-2 px-6 py-3 text-sm font-semibold bg-white text-brand-strong rounded-full hover:bg-white/90 transition-all">
                    Go to Drugs
                </a>
            </div>
        </div>
    </section>
@endsection