@php
    $isActive = fn (string $pattern): bool => request()->routeIs($pattern);
    $iconClass = 'w-5 h-5 text-body transition duration-75 dark:text-gray-400 group-hover:text-heading dark:group-hover:text-white';
@endphp

<aside id="logo-sidebar" class="fixed top-0 left-0 z-40 w-64 h-screen pt-20 transition-transform -translate-x-full bg-neutral-primary-soft border-r border-default-medium sm:translate-x-0 dark:bg-gray-800 dark:border-gray-700" aria-label="Sidebar">
    <div class="h-full px-3 pb-4 overflow-y-auto bg-neutral-primary-soft dark:bg-gray-800">
        <ul class="space-y-2 font-medium">
            <li class="pt-2">
                <span class="text-xs font-semibold uppercase text-body dark:text-gray-500 px-3">Dashboard</span>
            </li>
            <li>
                <a href="{{ route('home') }}" class="flex items-center p-2 text-heading rounded-base dark:text-white hover:bg-neutral-secondary-soft dark:hover:bg-gray-700 group {{ $isActive('home') ? 'bg-neutral-secondary-soft dark:bg-gray-700' : '' }}">
                    <x-lucide-home class="{{ $iconClass }}" />
                    <span class="ms-3">Home</span>
                </a>
            </li>
            <li>
                <a href="{{ route('search') }}" class="flex items-center p-2 text-heading rounded-base dark:text-white hover:bg-neutral-secondary-soft dark:hover:bg-gray-700 group {{ $isActive('search') ? 'bg-neutral-secondary-soft dark:bg-gray-700' : '' }}">
                    <x-lucide-search class="{{ $iconClass }}" />
                    <span class="ms-3">Global Search</span>
                </a>
            </li>

            <li class="pt-4">
                <span class="text-xs font-semibold uppercase text-body dark:text-gray-500 px-3">Knowledge Base</span>
            </li>
            <li>
                <a href="{{ route('products.index') }}" class="flex items-center p-2 text-heading rounded-base dark:text-white hover:bg-neutral-secondary-soft dark:hover:bg-gray-700 group {{ $isActive('products.*') ? 'bg-neutral-secondary-soft dark:bg-gray-700' : '' }}">
                    <x-lucide-package class="{{ $iconClass }}" />
                    <span class="ms-3">Products</span>
                </a>
            </li>
            <li>
                <a href="{{ route('active-ingredients.index') }}" class="flex items-center p-2 text-heading rounded-base dark:text-white hover:bg-neutral-secondary-soft dark:hover:bg-gray-700 group {{ $isActive('active-ingredients.*') ? 'bg-neutral-secondary-soft dark:bg-gray-700' : '' }}">
                    <x-lucide-flask-conical class="{{ $iconClass }}" />
                    <span class="ms-3">Active Ingredients</span>
                </a>
            </li>
            <li>
                <a href="{{ route('diseases.index') }}" class="flex items-center p-2 text-heading rounded-base dark:text-white hover:bg-neutral-secondary-soft dark:hover:bg-gray-700 group {{ $isActive('diseases.*') ? 'bg-neutral-secondary-soft dark:bg-gray-700' : '' }}">
                    <x-lucide-activity class="{{ $iconClass }}" />
                    <span class="ms-3">Diseases</span>
                </a>
            </li>
            <li>
                <a href="{{ route('companies.index') }}" class="flex items-center p-2 text-heading rounded-base dark:text-white hover:bg-neutral-secondary-soft dark:hover:bg-gray-700 group {{ $isActive('companies.*') ? 'bg-neutral-secondary-soft dark:bg-gray-700' : '' }}">
                    <x-lucide-building-2 class="{{ $iconClass }}" />
                    <span class="ms-3">Companies</span>
                </a>
            </li>

            <li class="pt-4">
                <span class="text-xs font-semibold uppercase text-body dark:text-gray-500 px-3">Platform</span>
            </li>
            <li>
                <a href="{{ route('about') }}" class="flex items-center p-2 text-heading rounded-base dark:text-white hover:bg-neutral-secondary-soft dark:hover:bg-gray-700 group {{ $isActive('about') ? 'bg-neutral-secondary-soft dark:bg-gray-700' : '' }}">
                    <x-lucide-circle-help class="{{ $iconClass }}" />
                    <span class="ms-3">About</span>
                </a>
            </li>
            <li>
                <a href="{{ route('contact') }}" class="flex items-center p-2 text-heading rounded-base dark:text-white hover:bg-neutral-secondary-soft dark:hover:bg-gray-700 group {{ $isActive('contact') ? 'bg-neutral-secondary-soft dark:bg-gray-700' : '' }}">
                    <x-lucide-mail class="{{ $iconClass }}" />
                    <span class="ms-3">Contact</span>
                </a>
            </li>
            <li>
                <a href="{{ route('privacy-policy') }}" class="flex items-center p-2 text-heading rounded-base dark:text-white hover:bg-neutral-secondary-soft dark:hover:bg-gray-700 group {{ $isActive('privacy-policy') ? 'bg-neutral-secondary-soft dark:bg-gray-700' : '' }}">
                    <x-lucide-shield-check class="{{ $iconClass }}" />
                    <span class="ms-3">Privacy Policy</span>
                </a>
            </li>
            <li>
                <a href="{{ route('terms-of-service') }}" class="flex items-center p-2 text-heading rounded-base dark:text-white hover:bg-neutral-secondary-soft dark:hover:bg-gray-700 group {{ $isActive('terms-of-service') ? 'bg-neutral-secondary-soft dark:bg-gray-700' : '' }}">
                    <x-lucide-file-text class="{{ $iconClass }}" />
                    <span class="ms-3">Terms of Service</span>
                </a>
            </li>
        </ul>
    </div>
</aside>
