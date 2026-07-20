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

    public function clear(): void
    {
        app(CompareAction::class)->clear();
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
            $wire.clear();
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
            x-transition:enter="transform transition ease-out duration-300"
            x-transition:enter-start="translate-y-full"
            x-transition:enter-end="translate-y-0"
            x-transition:leave="transform transition ease-in duration-200"
            x-transition:leave-start="translate-y-0"
            x-transition:leave-end="translate-y-full"
            class="fixed bottom-0 inset-x-0 z-50 bg-neutral-primary-soft dark:bg-slate-800 border-t border-default-medium dark:border-slate-700 shadow-lg">
            <div class="max-w-7xl mx-auto px-3 sm:px-4 py-2 sm:py-3 flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-4">
                {{-- Product names --}}
                <div class="flex items-center gap-2 sm:gap-3 flex-1 min-w-0 overflow-x-auto">
                    <template x-for="product in products" :key="product.id">
                        <div class="flex items-center gap-1.5 bg-neutral-secondary-soft dark:bg-slate-700 rounded-base px-2.5 sm:px-3 py-1.5 sm:py-2 border border-default-medium dark:border-slate-600 shrink-0 max-w-[180px] sm:max-w-[220px]">
                            <x-lucide-check-circle class="w-3.5 h-3.5 text-brand shrink-0" />
                            <span class="text-xs text-brand font-medium hidden sm:inline">{{ __('messages.compare.in_compare') }}</span>
                            <span class="text-xs sm:text-sm font-medium text-heading dark:text-white truncate" x-text="product.name"></span>
                            <button
                                x-on:click="removeItem(product.id)"
                                class="text-body hover:text-red-500 dark:text-slate-400 dark:hover:text-red-400 transition-colors p-0.5 shrink-0"
                                title="{{ __('messages.compare.remove_from_compare') }}">
                                <x-lucide-x class="w-3 h-3 sm:w-3.5 sm:h-3.5" />
                            </button>
                        </div>
                    </template>
                </div>

                {{-- Actions --}}
                <div class="flex items-center gap-2 sm:gap-3 shrink-0 justify-between sm:justify-end">
                    <span class="text-xs text-body dark:text-slate-400 whitespace-nowrap" x-text="count + '/' + max"></span>

                    <a href="{{ route('products.compare') }}" wire:navigate
                        class="inline-flex items-center gap-1.5 sm:gap-2 px-3 sm:px-4 py-1.5 sm:py-2 text-xs sm:text-sm font-medium text-white bg-brand hover:bg-brand-strong focus:ring-4 focus:ring-brand-medium rounded-base transition-all">
                        <x-lucide-git-compare class="w-3.5 h-3.5 sm:w-4 sm:h-4" />
                        <span>{{ __('messages.compare.compare') }}</span>
                    </a>

                    <button x-on:click="clearAll()"
                        class="text-xs text-body hover:text-heading dark:text-slate-400 dark:hover:text-white underline transition-colors">
                        {{ __('messages.compare.clear_all') }}
                    </button>
                </div>
            </div>
        </div>
    </template>
</div>
