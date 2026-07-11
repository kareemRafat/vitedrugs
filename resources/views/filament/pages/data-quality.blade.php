<x-filament-panels::page>

    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">

        <x-filament::section>

            <div class="text-sm text-gray-500">
                Products without Diseases
            </div>

            <div class="text-3xl font-bold">
                {{ $this->stats['products_without_diseases'] }}
            </div>

        </x-filament::section>

        <x-filament::section>

            <div class="text-sm text-gray-500">
                Products without Ingredients
            </div>

            <div class="text-3xl font-bold">
                {{ $this->stats['products_without_ingredients'] }}
            </div>

        </x-filament::section>

        <x-filament::section>

            <div class="text-sm text-gray-500">
                Companies without Products
            </div>

            <div class="text-3xl font-bold">
                {{ $this->stats['companies_without_products'] }}
            </div>

        </x-filament::section>

        <x-filament::section>

            <div class="text-sm text-gray-500">
                Diseases without Products
            </div>

            <div class="text-3xl font-bold">
                {{ $this->stats['diseases_without_products'] }}
            </div>

        </x-filament::section>

        <x-filament::section>

            <div class="text-sm text-gray-500">
                Ingredients without Products
            </div>

            <div class="text-3xl font-bold">
                {{ $this->stats['ingredients_without_products'] }}
            </div>

        </x-filament::section>

    </div>

</x-filament-panels::page>