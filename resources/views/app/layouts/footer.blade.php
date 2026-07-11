<footer class="py-4 mt-6 border-t border-default-medium dark:border-gray-700">
    <div class="flex flex-col items-center justify-between gap-2 sm:flex-row">
        <span class="text-sm text-body dark:text-gray-400">
            &copy; {{ date('Y') }}
            <strong class="text-heading dark:text-gray-300">VetPedia</strong>.
            Veterinary Drugs, Active Ingredients & Clinical Decision Support Platform. All rights reserved.
        </span>
        <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-sm text-body dark:text-gray-400">
            <a href="{{ route('about') }}" class="hover:text-heading dark:hover:text-gray-300">About</a>
            <span class="text-body dark:text-gray-600">|</span>
            <a href="{{ route('contact') }}" class="hover:text-heading dark:hover:text-gray-300">Contact</a>
            <span class="text-body dark:text-gray-600">|</span>
            <a href="{{ route('privacy-policy') }}" class="hover:text-heading dark:hover:text-gray-300">Privacy Policy</a>
            <span class="text-body dark:text-gray-600">|</span>
            <a href="{{ route('terms-of-service') }}" class="hover:text-heading dark:hover:text-gray-300">Terms of Service</a>
            <span class="text-body dark:text-gray-600">|</span>
            <a href="{{ route('sitemap') }}" class="hover:text-heading dark:hover:text-gray-300">Sitemap</a>
        </div>
    </div>
</footer>
