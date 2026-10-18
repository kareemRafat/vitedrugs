@props([
    'id' => 'toast',
    'type' => 'success',
    'title' => '',
    'message' => '',
    // `top` clears nothing but sits at the viewport's top edge. `bottom` right-aligns
    // itself on sm+ so it clears bottom-left widgets such as the cookie consent.
    'position' => 'top',
    /**
     * Flip the surface against the page theme: dark toast on a light page, light
     * toast on a dark one.
     *
     * Off by default, because the default — a toast that matches the page — is
     * what a toast should be everywhere. It is on for the homepage, where the
     * toast is a fixed overlay sitting over a page that mixes a permanently dark
     * contact band with a light one above it. Matching the *theme* there meant
     * matching neither, and a light toast in light mode vanished against the
     * dark band it was anchored to.
     *
     * The colours are literal rather than semantic on purpose: this element is
     * deliberately built from stock Tailwind values, and the site's `.dark`
     * remap of --color-slate-50…800 would otherwise turn the light-mode surface
     * navy. slate-900/950 are outside that remap, and white is not remapped at
     * all, so both states land where they are asked to.
     */
    'invert' => false,
    /**
     * Palette for the icon tile.
     *
     * 'success' is the semantic default — green on green-soft, used everywhere
     * a toast reports that something worked.
     *
     * 'brand' is the landing page's indigo. It exists because the homepage toast
     * is a brand-coloured notification on a brand-coloured page, and a green
     * tick sitting in the middle of an indigo design reads as a stock component
     * that was dropped in. `error` deliberately stays red in both palettes — an
     * error that recoloured itself to match the theme would be a lie about how
     * serious it is.
     *
     * The value is mirrored onto `data-icon-tone` because resources/js/toast.js
     * rewrites the icon tile's classes on every show; the tone has to survive
     * that or this setting would only apply until the first message.
     */
    'iconTone' => 'success',
])

@php
    $iconColors = [
        'success' => 'text-fg-success bg-success-soft',
        'error' => 'text-fg-danger bg-danger-soft',
        'warning' => 'text-body bg-amber-50',
        'info' => 'text-body bg-blue-50',
    ];

    /*
     * Indigo, tuned per surface rather than picked once. On the dark toast
     * (light page) it is a light indigo on a translucent indigo wash; on the
     * white toast (dark page) it drops to indigo-600 on indigo-50, because
     * indigo-300 on white has nowhere near enough contrast.
     */
    $brandColors = [
        'success' => 'text-indigo-300 bg-indigo-500/15 dark:text-indigo-600 dark:bg-indigo-50',
        'error' => 'text-fg-danger bg-danger-soft',
        'warning' => 'text-indigo-300 bg-indigo-500/15 dark:text-indigo-600 dark:bg-indigo-50',
        'info' => 'text-indigo-300 bg-indigo-500/15 dark:text-indigo-600 dark:bg-indigo-50',
    ];

    $iconMap = $iconTone === 'brand' ? $brandColors : $iconColors;
    $iconBox = $iconMap[$type] ?? $iconMap['success'];

    $positionClasses = [
        'top' => 'inset-x-4 top-4',
        'bottom' => 'inset-x-4 bottom-4 sm:start-auto sm:end-4',
    ];
    $positionClass = $positionClasses[$position] ?? $positionClasses['top'];

    $surface = $invert
        ? 'bg-slate-900 text-slate-100 border-slate-700 shadow-2xl shadow-black/40 dark:bg-white dark:text-slate-900 dark:border-slate-200 dark:shadow-black/10'
        : 'text-body bg-neutral-primary-soft border-default shadow-xs';

    /*
     * The dismiss button cannot keep `text-body hover:text-heading` when the
     * surface is flipped — those are dark-on-dark in one theme. `text-current`
     * inherits whatever the flipped surface resolved to, and the hover wash
     * follows it.
     */
    $dismiss = $invert
        ? 'text-current hover:bg-white/10 dark:hover:bg-slate-900/10'
        : 'text-body hover:text-heading hover:bg-neutral-secondary-medium focus:ring-neutral-tertiary';
@endphp

<div id="{{ $id }}" role="alert" data-icon-tone="{{ $iconTone }}" class="fixed {{ $positionClass }} z-[100] hidden flex items-center max-w-sm p-4 rounded-base border transition-all duration-300 {{ $surface }}">
    <div class="inline-flex items-center justify-center shrink-0 w-7 h-7 {{ $iconBox }} rounded">
        @switch($type)
            @case('success')
                <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 11.917 9.724 16.5 19 7.5"/></svg>
                @break
            @case('error')
                <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 17.94 6M18 18 6.06 6"/></svg>
                @break
            @default
                <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
        @endswitch
        <span class="sr-only">{{ $title }}</span>
    </div>
    <div class="toast-message ms-3 text-sm font-semibold rtl:font-medium">{{ $message }}</div>
    <button type="button" data-dismiss-target="#{{ $id }}" aria-label="{{ __('messages.common.close') }}" class="ms-auto flex items-center justify-center bg-transparent box-border border border-transparent font-medium leading-5 rounded text-sm h-8 w-8 focus:outline-none focus:ring-4 {{ $dismiss }}">
        <span class="sr-only">{{ __('messages.common.close') }}</span>
        <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 17.94 6M18 18 6.06 6"/></svg>
    </button>
</div>