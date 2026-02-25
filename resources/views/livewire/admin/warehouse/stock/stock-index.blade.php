<div class="space-y-6">
    {{-- Breadcrumb & Header --}}
    <div class="flex flex-col gap-2">

        <div class="flex items-center justify-between">
            <flux:heading size="xl" level="1">{{ __('stocks.title') }}</flux:heading>
        </div>

        {{-- Tabs --}}
        <flux:tab.group>
            <flux:tabs class="border-b border-zinc-200 dark:border-zinc-700 -mb-px">
                <flux:tab name="warehouse" :selected="true">{{ __('stocks.tab_warehouse') }}</flux:tab>
            </flux:tabs>

            <flux:tab.panel name="warehouse" class="pt-6">
                <flux:card class="overflow-visible">
                    <div class="space-y-4">
                        {{-- Search & Filters --}}
                        <div class="flex flex-wrap items-end gap-4">
                            <div class="flex flex-1 min-w-[200px] max-w-md items-center gap-2">
                                <flux:input wire:model.live.debounce.300ms="search"
                                    placeholder="{{ __('stocks.search_placeholder') }}" class="flex-1" />
                                <flux:button variant="primary" icon="magnifying-glass" wire:click="$refresh">
                                    {{ __('stocks.search_button') }}
                                </flux:button>
                            </div>
                        </div>

                        {{-- Bulk quantity: build pageRowKeys from all rows (simple + variation rows) --}}
                        @php
                            $pageRowKeys = [];
                            foreach ($products as $product) {
                                $hasVariations =
                                    $product->relationLoaded('variations') && $product->variations->isNotEmpty();
                                $isVariable = strtolower((string) ($product->type ?? '')) === 'variable';
                                if ($isVariable && $hasVariations) {
                                    foreach ($product->variations as $variation) {
                                        $pageRowKeys[] = 'variation_' . $variation->id;
                                    }
                                } else {
                                    $pageRowKeys[] = 'product_' . $product->id;
                                }
                            }
                            $allPageSelected =
                                count($pageRowKeys) > 0 &&
                                count(array_diff($pageRowKeys, $selectedRowKeys)) === 0 &&
                                count(array_diff($selectedRowKeys, $pageRowKeys)) === 0;
                        @endphp
                        <div
                            class="flex flex-wrap items-center gap-4 py-3 px-4 rounded-lg bg-zinc-50 dark:bg-zinc-800/50">
                            <span
                                class="text-sm font-medium text-zinc-700 dark:text-zinc-300">{{ __('stocks.bulk_quantity_label') }}</span>
                            <flux:checkbox :checked="$allPageSelected"
                                wire:click="toggleSelectPage({{ json_encode($pageRowKeys) }})" />
                            <flux:input type="number" wire:model="bulkQuantity" min="0" class="w-24" />
                            <flux:button variant="outline" icon="pencil" wire:click="applyBulkQuantity">
                                {{ __('stocks.apply_new_quantity') }}
                            </flux:button>
                        </div>
                        @if (session('status'))
                            <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show"
                                x-transition:leave="transition ease-in duration-300"
                                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                                <flux:callout variant="success" icon="check-circle">
                                    {{ session('status') }}
                                </flux:callout>
                            </div>
                        @endif

                        {{-- Table: products with variations = accordion group, products without = single row --}}
                        <flux:table>
                            <flux:table.columns>
                                <flux:table.column class="w-10"></flux:table.column>
                                <flux:table.column sortable>{{ __('stocks.table.product') }}</flux:table.column>
                                <flux:table.column>{{ __('stocks.reference') }}</flux:table.column>
                                <flux:table.column>{{ __('stocks.supplier') }}</flux:table.column>
                                <flux:table.column>{{ __('stocks.status') }}</flux:table.column>
                                <flux:table.column>{{ __('stocks.physical') }}</flux:table.column>
                                <flux:table.column>{{ __('stocks.reserved') }}</flux:table.column>
                                <flux:table.column>{{ __('stocks.available') }}</flux:table.column>
                                <flux:table.column>{{ __('stocks.modify_quantity') }}</flux:table.column>
                            </flux:table.columns>

                            @forelse ($products as $product)
                                @php
                                    $hasVariations =
                                        $product->relationLoaded('variations') && $product->variations->isNotEmpty();
                                    $isVariable = strtolower((string) ($product->type ?? '')) === 'variable';
                                @endphp

                                @if ($isVariable && $hasVariations)
                                    {{-- Product with variations: accordion group (header row + expandable variation rows) --}}
                                    <tbody x-data="{ open: false }" wire:key="product-group-{{ $product->id }}"
                                        class="border-b border-zinc-200 dark:border-zinc-700">
                                        {{-- Accordion header row: product name + expand button --}}
                                        <flux:table.row class="bg-zinc-50 dark:bg-zinc-800/50 cursor-pointer"
                                            x-on:click="open = !open">
                                            <flux:table.cell class="w-10">
                                                <button type="button"
                                                    class="p-1 rounded hover:bg-zinc-200 dark:hover:bg-zinc-700"
                                                    x-on:click.stop="open = !open">
                                                    <flux:icon.chevron-down
                                                        class="size-5 text-zinc-500 transition-transform"
                                                        x-bind:class="{ 'rotate-[-90deg]': !open }" />
                                                </button>
                                            </flux:table.cell>
                                            <flux:table.cell>
                                                <div class="flex items-center gap-3">
                                                    @if ($product->defaultImage && $product->defaultImage->image)
                                                        <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($product->defaultImage->image) }}"
                                                            alt=""
                                                            class="size-10 rounded object-cover shrink-0" />
                                                    @else
                                                        <div
                                                            class="size-10 rounded bg-zinc-200 dark:bg-zinc-700 shrink-0 flex items-center justify-center">
                                                            <flux:icon.photo class="size-5 text-zinc-400" />
                                                        </div>
                                                    @endif
                                                    <span
                                                        class="text-sm font-medium">{{ $product->getNameCurrentLanguage($selected_language) }}</span>
                                                    <span
                                                        class="text-xs text-zinc-500 dark:text-zinc-400">({{ $product->variations->count() }}
                                                        {{ __('stocks.table.variant') }})</span>
                                                </div>
                                            </flux:table.cell>
                                            <flux:table.cell class="text-sm text-zinc-500">—</flux:table.cell>
                                            <flux:table.cell class="text-sm text-zinc-500">N/A</flux:table.cell>
                                            <flux:table.cell>
                                                @if ($product->status)
                                                    <flux:icon.check-circle class="size-5 text-green-500" />
                                                @else
                                                    <flux:icon.x-circle class="size-5 text-red-500" />
                                                @endif
                                            </flux:table.cell>
                                            <flux:table.cell class="font-mono text-sm text-zinc-500">—</flux:table.cell>
                                            <flux:table.cell class="font-mono text-sm text-zinc-500">—</flux:table.cell>
                                            <flux:table.cell class="text-zinc-500">—</flux:table.cell>
                                            <flux:table.cell></flux:table.cell>
                                        </flux:table.row>

                                        {{-- Variation rows (shown when accordion open) --}}
                                        @foreach ($product->variations as $variation)
                                            @php
                                                $labels = $variation->getVariantLabelsForLanguage($selected_language);
                                                $qty = (int) ($variation->quantity ?? 0);
                                                $minQty = (int) ($variation->minimal_quantity ?? 0);
                                                $isLowStock = $qty < $minQty;
                                                $rowKey = 'variation_' . $variation->id;
                                            @endphp
                                            <flux:table.row x-show="open"
                                                x-transition:enter="transition ease-out duration-150"
                                                x-transition:enter-start="opacity-0"
                                                x-transition:enter-end="opacity-100"
                                                wire:key="variation-{{ $variation->id }}"
                                                class="{{ $isLowStock ? 'bg-red-50 dark:bg-red-900/10' : '' }}">
                                                <flux:table.cell class="pl-12">
                                                    <flux:checkbox wire:model.live="selectedRowKeys"
                                                        value="{{ $rowKey }}" />
                                                </flux:table.cell>
                                                <flux:table.cell class="pl-4">
                                                    <div
                                                        class="pl-3 border-l-2 border-zinc-200 dark:border-zinc-600 text-xs text-zinc-600 dark:text-zinc-300">
                                                        @foreach ($labels as $groupName => $variantName)
                                                            <span
                                                                class="font-medium text-zinc-600 dark:text-zinc-300">{{ $groupName }}:</span>
                                                            <span>{{ $variantName }}</span>
                                                            @if (!$loop->last)
                                                                <span class="text-zinc-400 mx-1">·</span>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                </flux:table.cell>
                                                <flux:table.cell class="text-sm text-zinc-600 dark:text-zinc-400">
                                                    {{ $variation->sku ?? '—' }}</flux:table.cell>
                                                <flux:table.cell class="text-sm text-zinc-500">N/A</flux:table.cell>
                                                <flux:table.cell>
                                                    @if ($product->status)
                                                        <flux:icon.check-circle class="size-5 text-green-500" />
                                                    @else
                                                        <flux:icon.x-circle class="size-5 text-red-500" />
                                                    @endif
                                                </flux:table.cell>
                                                <flux:table.cell class="font-mono text-sm">{{ $qty }}
                                                </flux:table.cell>
                                                <flux:table.cell class="font-mono text-sm">0</flux:table.cell>
                                                <flux:table.cell>
                                                    @if ($isLowStock)
                                                        <span
                                                            class="inline-flex items-center justify-center size-6 rounded bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400">
                                                            <flux:icon.exclamation-triangle class="size-4" />
                                                        </span>
                                                    @endif
                                                    <span class="font-mono text-sm">{{ $qty }}</span>
                                                </flux:table.cell>
                                                <flux:table.cell>
                                                    <div class="flex items-center gap-1">
                                                        <flux:input type="number"
                                                            wire:model.lazy="rowQuantities.{{ $rowKey }}"
                                                            min="0" class="w-20 text-sm" />
                                                        <flux:button variant="ghost" size="sm" icon="check"
                                                            class="text-zinc-500"
                                                            wire:click="applyRowQuantityByKey('{{ $rowKey }}')" />
                                                    </div>
                                                </flux:table.cell>
                                            </flux:table.row>
                                        @endforeach
                                    </tbody>
                                @else
                                    {{-- Product without variations: single row (like Product 2) --}}
                                    @php
                                        $qty = $product->getQuantityForStockDisplay();
                                        $minQty = (int) ($product->minimal_quantity ?? 0);
                                        $isLowStock = $qty < $minQty;
                                        $rowKey = 'product_' . $product->id;
                                    @endphp
                                    <tbody wire:key="product-{{ $product->id }}">
                                        <flux:table.row
                                            class="{{ $isLowStock ? 'bg-red-50 dark:bg-red-900/10' : '' }}">
                                            <flux:table.cell>
                                                <flux:checkbox wire:model.live="selectedRowKeys"
                                                    value="{{ $rowKey }}" />
                                            </flux:table.cell>
                                            <flux:table.cell>
                                                <div class="flex items-center gap-3">
                                                    @if ($product->defaultImage && $product->defaultImage->image)
                                                        <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($product->defaultImage->image) }}"
                                                            alt=""
                                                            class="size-12 rounded object-cover shrink-0" />
                                                    @else
                                                        <div
                                                            class="size-12 rounded bg-zinc-200 dark:bg-zinc-700 shrink-0 flex items-center justify-center">
                                                            <flux:icon.photo class="size-6 text-zinc-400" />
                                                        </div>
                                                    @endif
                                                    <span
                                                        class="line-clamp-2 text-sm font-medium">{{ $product->getNameCurrentLanguage($selected_language) }}</span>
                                                </div>
                                            </flux:table.cell>
                                            <flux:table.cell class="text-sm text-zinc-600 dark:text-zinc-400">
                                                {{ $product->sku ?? $product->id }}</flux:table.cell>
                                            <flux:table.cell class="text-sm text-zinc-500">N/A</flux:table.cell>
                                            <flux:table.cell>
                                                @if ($product->status)
                                                    <flux:icon.check-circle class="size-5 text-green-500" />
                                                @else
                                                    <flux:icon.x-circle class="size-5 text-red-500" />
                                                @endif
                                            </flux:table.cell>
                                            <flux:table.cell class="font-mono text-sm">{{ $qty }}
                                            </flux:table.cell>
                                            <flux:table.cell class="font-mono text-sm">0</flux:table.cell>
                                            <flux:table.cell>
                                                @if ($isLowStock)
                                                    <span
                                                        class="inline-flex items-center justify-center size-8 rounded bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400">
                                                        <flux:icon.exclamation-triangle class="size-5" />
                                                    </span>
                                                @endif
                                                <span class="font-mono text-sm">{{ $qty }}</span>
                                            </flux:table.cell>
                                            <flux:table.cell>
                                                <div class="flex items-center gap-1">
                                                    <flux:input type="number"
                                                        wire:model.lazy="rowQuantities.{{ $rowKey }}"
                                                        min="0" class="w-20 text-sm" />
                                                    <flux:button variant="ghost" size="sm" icon="check"
                                                        class="text-zinc-500"
                                                        wire:click="applyRowQuantityByKey('{{ $rowKey }}')" />
                                                </div>
                                            </flux:table.cell>
                                        </flux:table.row>
                                    </tbody>
                                @endif
                            @empty
                                <tbody>
                                    <flux:table.row>
                                        <flux:table.cell colspan="9" class="text-center py-12">
                                            <div class="flex flex-col items-center gap-2">
                                                <flux:icon.cube class="size-12 text-zinc-300 dark:text-zinc-600" />
                                                <flux:text class="text-zinc-500">{{ __('stocks.no_found') }}
                                                </flux:text>
                                            </div>
                                        </flux:table.cell>
                                    </flux:table.row>
                                </tbody>
                            @endforelse
                        </flux:table>

                        @if ($products->hasPages())
                            <div class="pt-4 border-t border-zinc-200 dark:border-zinc-700">
                                {{ $products->links() }}
                            </div>
                        @endif
                    </div>
                </flux:card>
            </flux:tab.panel>
        </flux:tab.group>
    </div>

</div>
