<x-filament-panels::page>

    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">

        <x-filament::section>
            <div class="text-sm text-gray-500">
                Products
            </div>

            <div class="text-3xl font-bold">
                {{ $this->stats['products'] }}
            </div>
        </x-filament::section>

        <x-filament::section>
            <div class="text-sm text-gray-500">
                Companies
            </div>

            <div class="text-3xl font-bold">
                {{ $this->stats['companies'] }}
            </div>
        </x-filament::section>

        <x-filament::section>
            <div class="text-sm text-gray-500">
                Diseases
            </div>

            <div class="text-3xl font-bold">
                {{ $this->stats['diseases'] }}
            </div>
        </x-filament::section>

        <x-filament::section>
            <div class="text-sm text-gray-500">
                Active Ingredients
            </div>

            <div class="text-3xl font-bold">
                {{ $this->stats['ingredients'] }}
            </div>
        </x-filament::section>

    </div>

    <div class="grid gap-4 mt-6 lg:grid-cols-2">

        <x-filament::section>
            <x-slot name="heading">
                Top Companies
            </x-slot>

            <ul class="space-y-2">

                @foreach ($this->topCompanies as $company)
                    <li class="flex items-center justify-between border-b py-2">

                        <span class="font-medium">
                            <a href="{{ route('companies.show', $company['slug']) }}"
                                class="font-medium text-primary-600">
                                {{ $company['name'] }}
                            </a>
                        </span>

                        <span class="px-2 py-1 text-xs rounded bg-gray-100">
                            {{ $company['products_count'] }}
                        </span>

                    </li>
                @endforeach

            </ul>

        </x-filament::section>

        <x-filament::section>
            <x-slot name="heading">
                Top Diseases
            </x-slot>

            <ul class="space-y-2">

                @foreach ($this->topDiseases as $disease)
                    <li class="flex items-center justify-between border-b py-2">

                        <span class="font-medium">
                            <a href="{{ route('diseases.show', $disease['slug']) }}">
                                {{ $disease['name'] }}
                            </a>
                        </span>

                        <span class="px-2 py-1 text-xs rounded bg-gray-100">
                            {{ $disease['products_count'] }}
                        </span>

                    </li>
                @endforeach

            </ul>

        </x-filament::section>

        <x-filament::section>
            <x-slot name="heading">
                Top Active Ingredients
            </x-slot>

            <ul class="space-y-2">

                @foreach ($this->topIngredients as $ingredient)
                    <li class="flex items-center justify-between border-b py-2">

                        <span class="font-medium">
                            <a href="{{ route('active-ingredients.show', $ingredient['slug']) }}">
                                {{ $ingredient['name'] }}
                            </a>
                        </span>

                        <span class="px-2 py-1 text-xs rounded bg-gray-100">
                            {{ $ingredient['products_count'] }}
                        </span>

                    </li>
                @endforeach

            </ul>

        </x-filament::section>

        <x-filament::section>
            <x-slot name="heading">
                Top Dosage Forms
            </x-slot>

            <ul class="space-y-2">

                @foreach ($this->topDosageForms as $form)
                    <li class="flex items-center justify-between border-b py-2">

                        <span class="font-medium">
                            {{ $form['name'] }}
                        </span>

                        <span class="px-2 py-1 text-xs rounded bg-gray-100">
                            {{ $form['products_count'] }}
                        </span>

                    </li>
                @endforeach

            </ul>

        </x-filament::section>

    </div>

</x-filament-panels::page>
