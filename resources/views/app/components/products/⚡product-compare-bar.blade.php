<?php

use App\Actions\CompareAction;
use App\Models\Product;
use Livewire\Component;

new class extends Component
{
    public function getCompareProducts(): array
    {
        $ids = app(CompareAction::class)->all();

        if (empty($ids)) {
            return [];
        }

        return Product::query()
            ->whereIn('id', $ids)
            ->get()
            ->map(fn (Product $p) => ['id' => $p->id, 'name' => $p->trade_name])
            ->values()
            ->toArray();
    }

    public function remove(string $id): void
    {
        app(CompareAction::class)->remove($id);
        $this->dispatch('compare-updated');
    }
};
?>

<div
    x-data="{
        products: @js($this->getCompareProducts()),
        get count() { return this.products.length; },
        max: @js(app(CompareAction::class)->max()),
        dispatchCount() {
            window.dispatchEvent(new CustomEvent('compare-count', {
                detail: { count: this.products.length, max: this.max }
            }));
        },
        removeItem(id) {
            this.products = this.products.filter(p => p.id !== id);
            this.dispatchCount();
            window.dispatchEvent(new CustomEvent('compare-toggle', {
                detail: { id: id, add: false }
            }));
            $wire.remove(id);
        },
        clearAll() {
            this.products = [];
            this.dispatchCount();
            window.dispatchEvent(new CustomEvent('compare-clear'));
        }
    }"
    x-on:compare-clear.window="products = []"
    x-on:compare-toggle.window="
        if ($event.detail.add) {
            if (!products.find(p => p.id === $event.detail.id)) {
                products.push({ id: $event.detail.id, name: $event.detail.name });
            }
        } else {
            products = products.filter(p => p.id !== $event.detail.id);
        }
        dispatchCount();
    ">
    <template x-if="count > 0">
        <div
            x-transition:enter-start="translate-y-full"
            x-transition:enter-end="translate-y-0"
            x-transition:leave-start="translate-y-0"
            x-transition:leave-end="translate-y-full"
            class="fixed bottom-0 inset-x-0 z-50 bg-blue-900 dark:bg-gray-300 border-t border-blue-950/30 dark:border-gray-400 shadow-xl">
            <div class="max-w-7xl mx-auto px-3 sm:px-4 py-2 sm:py-3 flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-4">
                {{-- Product names --}}
                <div class="flex items-center gap-2 sm:gap-3 flex-1 min-w-0 overflow-x-auto">
                    <template x-for="product in products" :key="product.id">
                        <div class="flex items-center gap-1.5 bg-white/15 dark:bg-white/60 rounded-base px-2.5 sm:px-3 py-1.5 sm:py-2 border border-white/20 dark:border-gray-300/50 shrink-0 max-w-[180px] sm:max-w-[220px]">
                            <x-lucide-check-circle class="w-3.5 h-3.5 text-sky-300 dark:text-blue-900 shrink-0" />
                            <span class="text-xs text-sky-200 dark:text-blue-900/80 font-medium hidden sm:inline">{{ __('messages.compare.in_compare') }}</span>
                            <span class="text-xs sm:text-sm font-medium text-white dark:text-gray-800 truncate" x-text="product.name"></span>
                            <button
                                x-on:click="removeItem(product.id)"
                                class="text-white/60 dark:text-gray-500 hover:text-red-300 dark:hover:text-red-500 transition-colors p-0.5 shrink-0"
                                title="{{ __('messages.compare.remove_from_compare') }}">
                                <x-lucide-x class="w-3 h-3 sm:w-3.5 sm:h-3.5" />
                            </button>
                        </div>
                    </template>
                </div>

                {{-- Actions --}}
                <div class="flex items-center gap-2 sm:gap-3 shrink-0 justify-between sm:justify-end">
                    <span class="text-xs text-white/70 dark:text-gray-500 whitespace-nowrap" x-text="count + '/' + max"></span>

                    <a href="{{ route('products.compare') }}" wire:navigate
                        class="inline-flex items-center gap-1.5 sm:gap-2 px-3 sm:px-4 py-1.5 sm:py-2 text-xs sm:text-sm font-medium text-blue-900 bg-white hover:bg-white/90 focus:ring-4 focus:ring-white/30 rounded-base transition-all dark:text-blue-900 dark:bg-gray-200 dark:hover:bg-gray-300">
                        <x-lucide-git-compare class="w-3.5 h-3.5 sm:w-4 sm:h-4" />
                        <span>{{ __('messages.compare.compare') }}</span>
                    </a>

                    <button x-on:click="clearAll()"
                        class="text-xs text-white/70 dark:text-gray-500 hover:text-white dark:hover:text-gray-800 underline transition-colors">
                        {{ __('messages.compare.clear_all') }}
                    </button>
                </div>
            </div>
        </div>
    </template>
</div>
