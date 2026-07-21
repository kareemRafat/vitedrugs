@props([
    'id' => 'toast',
    'type' => 'success',
    'title' => '',
    'message' => '',
])

@php
    $iconColors = [
        'success' => 'text-fg-success bg-success-soft',
        'error' => 'text-fg-danger bg-danger-soft',
        'warning' => 'text-body bg-amber-50',
        'info' => 'text-body bg-blue-50',
    ];
    $iconBox = $iconColors[$type] ?? $iconColors['success'];
@endphp

<div id="{{ $id }}" role="alert" class="fixed top-4 end-4 z-[100] hidden flex items-center w-full max-w-sm p-4 text-body bg-neutral-primary-soft rounded-base shadow-xs border border-default transition-all duration-300">
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
    <button type="button" data-dismiss-target="#{{ $id }}" aria-label="Close" class="ms-auto flex items-center justify-center text-body hover:text-heading bg-transparent box-border border border-transparent hover:bg-neutral-secondary-medium focus:ring-4 focus:ring-neutral-tertiary font-medium leading-5 rounded text-sm h-8 w-8 focus:outline-none">
        <span class="sr-only">Close</span>
        <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 17.94 6M18 18 6.06 6"/></svg>
    </button>
</div>
