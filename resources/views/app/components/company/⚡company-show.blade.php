<?php

use App\Models\Company;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component
{
    use WithPagination;

    public Company $company;

    public function mount(Company $company): void
    {
        $this->company = $company->load([
            'parentCompany',
            'subsidiaries',
        ]);
    }

    #[Computed]
    public function products(): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return $this->company
            ->products()
            ->with('dosageForm')
            ->paginate(10);
    }
};

?>

<div class="space-y-4">
    {{-- Breadcrumb --}}
    <nav class="flex mb-4 pt-4" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 rtl:space-x-reverse text-base text-body dark:text-slate-400">
            <li class="inline-flex items-center">
                <a href="{{ route('home') }}" wire:navigate class="hover:text-fg-brand dark:hover:text-white">{{ __('messages.companies.home') }}</a>
            </li>
            <li>
                <div class="flex items-center gap-1">
                    <x-lucide-chevron-right class="w-4 h-4 rtl:rotate-180" />
                    <a href="{{ route('companies.index') }}" wire:navigate class="hover:text-fg-brand dark:hover:text-white">{{ __('messages.companies.companies') }}</a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center gap-1">
                    <x-lucide-chevron-right class="w-4 h-4 rtl:rotate-180" />
                    <span class="text-heading dark:text-white font-medium">{{ $this->company->name }}</span>
                </div>
            </li>
        </ol>
    </nav>

    {{-- Company Hero --}}
    <div class="bg-neutral-primary-soft rounded-base shadow-sm p-4 sm:p-6 mb-4 dark:bg-slate-800">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <div class="lg:col-span-7 xl:col-span-8">
                <h1 class="text-2xl sm:text-3xl font-bold text-heading dark:text-white mb-1">{{ $this->company->name }}</h1>
                @if ($this->company->name_ar && app()->getLocale() === 'ar')
                    <p class="text-body dark:text-slate-400 text-base mb-2">{{ $this->company->name_ar }}</p>
                @endif
                <div class="flex flex-wrap gap-2 mb-3">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-base text-sm font-medium bg-brand-soft text-fg-brand dark:bg-brand/20 dark:text-brand capitalize">
                        {{ __('messages.companies.types.' . $this->company->company_type) }}
                    </span>
                    @if ($this->company->country)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-base text-sm font-medium bg-neutral-secondary-soft text-heading border border-default-medium dark:bg-slate-700 dark:text-white dark:border-slate-600">
                            <x-lucide-map-pin class="w-3.5 h-3.5 me-1" />
                            {{ $this->company->country }}
                        </span>
                    @endif
                </div>
                @php
                    $locale = app()->getLocale();
                    $displayDescription = $locale === 'ar' && $this->company->description_ar
                        ? $this->company->description_ar
                        : $this->company->description;
                @endphp
                @if ($displayDescription)
                    <p class="text-base text-body dark:text-slate-400">{{ $displayDescription }}</p>
                @endif
            </div>

            <div class="lg:col-span-5 xl:col-span-4">
                <div class="grid grid-cols-2 gap-3">
                    <div class="bg-neutral-secondary-soft rounded-base p-3 border border-default-medium shadow-sm dark:bg-slate-700 dark:border-slate-600">
                        <span class="block text-base uppercase text-body dark:text-slate-400 mb-1">{{ __('messages.companies.type') }}</span>
                        <span class="font-semibold text-heading dark:text-white text-base capitalize">{{ __('messages.companies.types.' . $this->company->company_type) }}</span>
                    </div>
                    <div class="bg-neutral-secondary-soft rounded-base p-3 border border-default-medium shadow-sm dark:bg-slate-700 dark:border-slate-600">
                        <span class="block text-base uppercase text-body dark:text-slate-400 mb-1">{{ __('messages.companies.country') }}</span>
                        <span class="font-semibold text-heading dark:text-white text-base">{{ $this->company->country ?? __('messages.companies.na') }}</span>
                    </div>
                    <div class="bg-neutral-secondary-soft rounded-base p-3 border border-default-medium shadow-sm dark:bg-slate-700 dark:border-slate-600">
                        <span class="block text-base uppercase text-body dark:text-slate-400 mb-1">{{ __('messages.companies.products_count') }}</span>
                        <span class="font-semibold text-heading dark:text-white text-base">{{ $this->products->total() }}</span>
                    </div>
                    <div class="bg-neutral-secondary-soft rounded-base p-3 border border-default-medium shadow-sm dark:bg-slate-700 dark:border-slate-600">
                        <span class="block text-base uppercase text-body dark:text-slate-400 mb-1">{{ __('messages.companies.registration') }}</span>
                        <span class="font-semibold text-heading dark:text-white text-base">{{ $this->company->registration_number ?? __('messages.companies.na') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">

        {{-- Main Column --}}
        <div class="lg:col-span-7 xl:col-span-8 space-y-4">

            {{-- Products --}}
            <div class="bg-neutral-primary-soft rounded-base shadow-sm dark:bg-slate-800 overflow-hidden">
                <div class="px-5 py-4 border-b border-default-medium flex items-center gap-2">
                    <x-lucide-package class="w-4 h-4 text-body" />
                    <h2 class="text-base font-semibold text-heading dark:text-white">{{ __('messages.companies.products_title') }}</h2>
                </div>
                <div class="overflow-x-auto">
                    @if ($this->products->isEmpty())
                        <p class="text-base text-body dark:text-slate-400 text-center py-8">{{ __('messages.companies.no_products') }}</p>
                    @else
                        <table class="w-full text-base text-left rtl:text-right text-heading dark:text-white">
                            <thead class="text-base uppercase text-body bg-neutral-secondary-soft dark:bg-slate-700 dark:text-slate-400">
                                <tr>
                                    <th scope="col" class="px-5 py-3">{{ __('messages.companies.trade_name') }}</th>
                                    <th scope="col" class="px-5 py-3 hidden md:table-cell">{{ __('messages.companies.dosage_form') }}</th>
                                    <th scope="col" class="px-5 py-3 w-20">{{ __('messages.companies.details') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($this->products as $product)
                                    <tr class="border-b border-default-medium dark:border-slate-700">
                                        <td class="px-5 py-4">
                                            <a href="{{ route('products.show', $product) }}" wire:navigate class="font-medium text-fg-brand hover:underline">
                                                {{ $product->trade_name }}
                                            </a>
                                        </td>
                                        <td class="px-5 py-4 hidden md:table-cell text-body dark:text-slate-400 text-base">
                                            {{ $product->dosageForm?->name ?? __('messages.companies.na') }}
                                        </td>
                                        <td class="px-5 py-4">
                                            <a href="{{ route('products.show', $product) }}" wire:navigate
                                                class="inline-flex items-center justify-center w-8 h-8 text-white bg-brand hover:bg-brand-strong focus:ring-4 focus:ring-brand-medium rounded-base text-sm">
                                                <x-lucide-arrow-right class="w-4 h-4 rtl:rotate-180" />
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>

                {{-- Pagination --}}
                @if ($this->products->hasPages())
                    @php
                        $currentPage = $this->products->currentPage();
                        $lastPage = $this->products->lastPage();
                        $window = 2;
                        $startPage = max(1, $currentPage - $window);
                        $endPage = min($lastPage, $currentPage + $window);
                        $showStartEllipsis = $startPage > 2;
                        $showEndEllipsis = $endPage < $lastPage - 1;
                    @endphp
                    <div class="border-t border-default-medium dark:border-slate-700 px-5 py-4">
                        <div class="flex flex-col sm:flex-row items-center justify-between gap-3">
                            <div class="hidden sm:block text-sm text-body dark:text-slate-400">
                                {{ __('messages.companies.showing') }}
                                {{ $this->products->firstItem() }}–{{ $this->products->lastItem() }}
                                {{ __('messages.companies.of') }}
                                {{ $this->products->total() }}
                            </div>
                            <div class="flex items-center gap-3">
                                <button type="button" wire:click="setPage({{ $currentPage - 1 }})" @if($this->products->onFirstPage()) disabled @endif
                                    class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium text-heading bg-neutral-primary-soft border border-default-medium rounded-base hover:bg-neutral-secondary-soft disabled:opacity-40 disabled:cursor-not-allowed dark:bg-slate-700 dark:text-white dark:border-slate-600 dark:hover:bg-slate-600 transition-colors">
                                    <x-lucide-chevron-left class="w-4 h-4 rtl:rotate-180" />
                                    <span>{{ __('messages.companies.previous_page') }}</span>
                                </button>

                                <div class="hidden sm:flex items-center gap-1">
                                    {{-- First page --}}
                                    @if ($startPage > 1)
                                        <button type="button" wire:click="setPage(1)"
                                            class="inline-flex items-center justify-center w-9 h-9 text-sm font-medium text-heading bg-neutral-primary-soft border border-default-medium rounded-base hover:bg-neutral-secondary-soft dark:bg-slate-700 dark:text-white dark:border-slate-600 dark:hover:bg-slate-600 transition-colors">
                                            1
                                        </button>
                                    @endif

                                    {{-- Start ellipsis --}}
                                    @if ($showStartEllipsis)
                                        <span class="inline-flex items-center justify-center w-9 h-9 text-sm text-body dark:text-slate-400">...</span>
                                    @endif

                                    {{-- Page window --}}
                                    @for ($i = $startPage; $i <= $endPage; $i++)
                                        <button type="button" wire:click="setPage({{ $i }})"
                                            class="inline-flex items-center justify-center w-9 h-9 text-sm font-medium rounded-base transition-colors
                                            @if ($i === $currentPage)
                                                text-white bg-brand shadow-xs
                                            @else
                                                text-heading bg-neutral-primary-soft border border-default-medium hover:bg-neutral-secondary-soft dark:bg-slate-700 dark:text-white dark:border-slate-600 dark:hover:bg-slate-600
                                            @endif">
                                            {{ $i }}
                                        </button>
                                    @endfor

                                    {{-- End ellipsis --}}
                                    @if ($showEndEllipsis)
                                        <span class="inline-flex items-center justify-center w-9 h-9 text-sm text-body dark:text-slate-400">...</span>
                                    @endif

                                    {{-- Last page --}}
                                    @if ($endPage < $lastPage)
                                        <button type="button" wire:click="setPage({{ $lastPage }})"
                                            class="inline-flex items-center justify-center w-9 h-9 text-sm font-medium text-heading bg-neutral-primary-soft border border-default-medium rounded-base hover:bg-neutral-secondary-soft dark:bg-slate-700 dark:text-white dark:border-slate-600 dark:hover:bg-slate-600 transition-colors">
                                            {{ $lastPage }}
                                        </button>
                                    @endif
                                </div>

                                <button type="button" wire:click="setPage({{ $currentPage + 1 }})" @if(!$this->products->hasMorePages()) disabled @endif
                                    class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium text-heading bg-neutral-primary-soft border border-default-medium rounded-base hover:bg-neutral-secondary-soft disabled:opacity-40 disabled:cursor-not-allowed dark:bg-slate-700 dark:text-white dark:border-slate-600 dark:hover:bg-slate-600 transition-colors">
                                    <span>{{ __('messages.companies.next_page') }}</span>
                                    <x-lucide-chevron-right class="w-4 h-4 rtl:rotate-180" />
                                </button>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Parent Company --}}
            @if ($this->company->parentCompany)
                <div class="bg-neutral-primary-soft rounded-base shadow-sm dark:bg-slate-800 overflow-hidden">
                    <div class="px-5 py-4 border-b border-default-medium flex items-center gap-2">
                        <x-lucide-building class="w-4 h-4 text-body" />
                        <h2 class="text-base font-semibold text-heading dark:text-white">{{ __('messages.companies.parent_company') }}</h2>
                    </div>
                    <div class="p-5">
                        <a href="{{ route('companies.show', $this->company->parentCompany) }}" wire:navigate class="text-base font-medium text-fg-brand hover:underline">
                            {{ $this->company->parentCompany->name }}
                        </a>
                    </div>
                </div>
            @endif

            {{-- Subsidiaries --}}
            @if ($this->company->subsidiaries->isNotEmpty())
                <div class="bg-neutral-primary-soft rounded-base shadow-sm dark:bg-slate-800 overflow-hidden">
                    <div class="px-5 py-4 border-b border-default-medium flex items-center gap-2">
                        <x-lucide-building class="w-4 h-4 text-body" />
                        <h2 class="text-base font-semibold text-heading dark:text-white">{{ __('messages.companies.subsidiaries') }}</h2>
                    </div>
                    <div class="p-5">
                        <ul class="space-y-2">
                            @foreach ($this->company->subsidiaries as $subsidiary)
                                <li>
                                    <a href="{{ route('companies.show', $subsidiary) }}" wire:navigate class="text-base font-medium text-fg-brand hover:underline">
                                        {{ $subsidiary->name }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            {{-- Map Section --}}
            @if ($this->company->embed_map_url)
                <div class="bg-neutral-primary-soft rounded-base shadow-sm dark:bg-slate-800 overflow-hidden">
                    <div class="px-5 py-4 border-b border-default-medium flex items-center gap-2">
                        <x-lucide-map-pin class="w-4 h-4 text-body" />
                        <h2 class="text-base font-semibold text-heading dark:text-white">{{ __('messages.companies.location_map') }}</h2>
                    </div>
                    <div class="p-5">
                        <div class="aspect-video w-full rounded-base overflow-hidden">
                            <iframe
                                src="{{ $this->company->embed_map_url }}"
                                class="w-full h-full border-0"
                                allowfullscreen
                                loading="lazy"
                                referrerpolicy="no-referrer-when-downgrade"
                            ></iframe>
                        </div>
                    </div>
                </div>
            @endif

        </div>

        {{-- Sidebar --}}
        <div class="lg:col-span-5 xl:col-span-4 space-y-4">

            {{-- Contact Information --}}
            <div class="bg-neutral-primary-soft rounded-base shadow-sm dark:bg-slate-800">
                <div class="px-5 py-4 border-b border-default-medium flex items-center gap-2">
                    <x-lucide-phone class="w-4 h-4 text-body" />
                    <h2 class="text-base font-semibold text-heading dark:text-white">{{ __('messages.companies.contact_info') }}</h2>
                </div>
                <div class="p-5 space-y-3">
                    @php
                        $contactFields = [
                            'phone' => ['icon' => 'phone', 'label' => __('messages.companies.phone')],
                            'mobile' => ['icon' => 'smartphone', 'label' => __('messages.companies.mobile')],
                            'whatsapp' => ['icon' => 'message-circle', 'label' => __('messages.companies.whatsapp')],
                            'email' => ['icon' => 'mail', 'label' => __('messages.companies.email')],
                            'website' => ['icon' => 'globe', 'label' => __('messages.companies.website')],
                            'contact_person' => ['icon' => 'user', 'label' => __('messages.companies.contact_person')],
                            'facebook' => ['icon' => 'globe', 'label' => __('messages.companies.facebook')],
                            'linkedin' => ['icon' => 'briefcase', 'label' => __('messages.companies.linkedin')],
                            'telegram' => ['icon' => 'send', 'label' => __('messages.companies.telegram')],
                            'youtube' => ['icon' => 'video', 'label' => __('messages.companies.youtube')],
                            'instagram' => ['icon' => 'camera', 'label' => __('messages.companies.instagram')],
                            'governorate' => ['icon' => 'map-pin', 'label' => __('messages.companies.governorate')],
                            'coverage_area' => ['icon' => 'compass', 'label' => __('messages.companies.coverage_area')],
                        ];
                    @endphp
                    @foreach ($contactFields as $field => $meta)
                        @if ($this->company->$field)
                            <div class="flex items-start gap-3">
                                <x-dynamic-component :component="'lucide-' . $meta['icon']" class="w-4 h-4 text-body mt-0.5 shrink-0" />
                                <div class="min-w-0">
                                    <span class="block text-sm uppercase text-body dark:text-slate-400">{{ $meta['label'] }}</span>
                                    @if (in_array($field, ['email', 'website']))
                                        <a href="{{ $field === 'email' ? 'mailto:' . $this->company->$field : $this->company->$field }}"
                                           target="_blank" class="text-base font-medium text-fg-brand hover:underline break-all">
                                            {{ $this->company->$field }}
                                        </a>
                                    @elseif (in_array($field, ['facebook', 'linkedin', 'telegram', 'youtube', 'instagram']))
                                        <a href="{{ $this->company->$field }}"
                                           target="_blank" class="text-base font-medium text-fg-brand hover:underline break-all">
                                            {{ $this->company->$field }}
                                        </a>
                                    @else
                                        <span dir="ltr" class="text-base font-medium text-heading dark:text-white">{{ str_replace(' ', '', $this->company->$field) }}</span>
                                    @endif
                                </div>
                            </div>
                        @endif
                    @endforeach
                    @php
                        $displayAddress = $locale === 'ar' && $this->company->address_ar
                            ? $this->company->address_ar
                            : $this->company->address;
                    @endphp
                    @if ($displayAddress)
                        <div class="flex items-start gap-3 pt-3 border-t border-default-medium">
                            <x-lucide-map-pin class="w-4 h-4 text-body mt-0.5 shrink-0" />
                            <div class="min-w-0">
                                <span class="block text-sm uppercase text-body dark:text-slate-400">{{ __('messages.companies.address') }}</span>
                                <span class="text-base font-medium text-heading dark:text-white">{{ $displayAddress }}</span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

        </div>

    </div>
</div>
