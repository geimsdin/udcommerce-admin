<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl">{{ __('ecommerce::products.title') }}</flux:heading>
            <flux:subheading>{{ __('ecommerce::products.subtitle') }}</flux:subheading>
        </div>
        <div class="flex items-center gap-2">
            <flux:button icon="adjustments-horizontal"
                :href="route(config('ud-ecommerce.admin_route_prefix', 'admin').
                    '.products.mass-assign')"
                wire:navigate>
                {{ __('ecommerce::products.mass.title') }}
            </flux:button>
            <flux:button variant="primary" icon="plus"
                :href="route(config('ud-ecommerce.admin_route_prefix', 'admin').
                    '.products.create')"
                wire:navigate>
                {{ __('ecommerce::products.add_product') }}
            </flux:button>
        </div>
    </div>

    @if (session('status'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show"
            x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0">
            <flux:callout variant="success" icon="check-circle">{{ session('status') }}</flux:callout>
        </div>
    @endif

    {{-- Product list --}}
    <flux:card class="space-y-4">
        <div class="flex flex-col gap-4">
            <div class="flex gap-2 justify-between">
                <flux:input wire:model.live.debounce.300ms="search" placeholder="{{ __('common.search_placeholder') }}"
                    icon="magnifying-glass" />
                <flux:button icon="funnel" wire:click="$toggle('show_filters')"
                    variant="{{ $show_filters ? 'filled' : 'outline' }}"
                    class="{{ $filter_brand_id || $filter_category_id || $filter_type ? 'text-primary-600 dark:text-primary-400' : '' }}">
                    @if ($filter_brand_id || $filter_category_id || $filter_type)
                        <flux:badge size="sm" color="zinc"
                            class="ml-1 inset-0 absolute -top-1 -right-1 !w-2 !h-2 rounded-full p-0 border-2 border-white dark:border-zinc-900">
                        </flux:badge>
                    @endif
                    {{ __('ecommerce::products.filters') }}
                </flux:button>
            </div>

            @if ($show_filters)
                <flux:card>
                    <div class="grid grid-cols-3 gap-3 mb-4">
                        <flux:select wire:model="filter_brand_id" variant="listbox" searchable clearable
                            placeholder="{{ __('ecommerce::products.filter.brand') }}">
                            @foreach ($all_brands as $brand)
                                <flux:select.option value="{{ $brand->id }}">{{ $brand->name }}
                                </flux:select.option>
                            @endforeach
                        </flux:select>

                        <flux:select wire:model="filter_category_id" variant="listbox" searchable clearable
                            placeholder="{{ __('ecommerce::products.filter.category') }}">
                            @foreach ($all_categories as $cat)
                                <flux:select.option value="{{ $cat->id }}">{{ $cat->name }}
                                </flux:select.option>
                            @endforeach
                        </flux:select>

                        <flux:select wire:model="filter_type" variant="listbox" clearable
                            placeholder="{{ __('ecommerce::products.filter.type') }}">
                            @foreach ($this->getProductTypes() as $key => $product_type_label)
                                <flux:select.option :value="$key">{{ $product_type_label }}
                                </flux:select.option>
                            @endforeach
                        </flux:select>
                    </div>

                    <div class="flex justify-end gap-2">
                        <flux:button variant="ghost" wire:click="resetFilters" size="sm">
                            {{ __('common.reset') }}
                        </flux:button>
                        <flux:button variant="primary" wire:click="applyFilters" size="sm">
                            {{ __('common.apply') }}
                        </flux:button>
                    </div>
                </flux:card>
            @endif
        </div>
        <flux:table :paginate="$products">
            <flux:table.columns>
                <flux:table.column>{{ __('ecommerce::products.table.name') }}</flux:table.column>
                <flux:table.column>{{ __('ecommerce::products.table.price') }}</flux:table.column>
                <flux:table.column>{{ __('ecommerce::products.table.quantity') }}</flux:table.column>
                <flux:table.column>{{ __('ecommerce::products.table.minimal_quantity') }}</flux:table.column>
                <flux:table.column>{{ __('ecommerce::products.table.low_stock_alert') }}</flux:table.column>
                <flux:table.column>{{ __('ecommerce::products.table.status') }}</flux:table.column>
                <flux:table.column class="w-32">{{ __('common.actions') }}</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse ($products as $product)
                    <flux:table.row>
                        <flux:table.cell>{{ $product->name }}</flux:table.cell>
                        <flux:table.cell>${{ number_format($product->price, 2) }}</flux:table.cell>
                        <flux:table.cell>{{ $product->quantity }}</flux:table.cell>
                        <flux:table.cell>{{ $product->minimal_quantity }}</flux:table.cell>
                        <flux:table.cell>{{ $product->low_stock_alert }}</flux:table.cell>
                        <flux:table.cell>
                            <flux:badge color="{{ $product->status ? 'green' : 'red' }}"
                                icon="{{ $product->status ? 'check-circle' : 'x-circle' }}">
                                {{ $product->status ? __('common.active') : __('common.inactive') }}
                            </flux:badge>
                        </flux:table.cell>
                        <flux:table.cell>
                            <div class="flex items-center gap-1">
                                <flux:button icon="pencil"
                                    :href="route(config('ud-ecommerce.admin_route_prefix', 'admin').
                                        '.products.edit', $product)"
                                    wire:navigate>{{ __('common.edit') }}</flux:button>
                                <flux:button variant="danger" icon="trash"
                                    wire:click="requestDelete({{ $product->id }})">{{ __('common.delete') }}
                                </flux:button>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="11" class="text-center py-8">
                            <flux:text class="text-zinc-500">{{ __('ecommerce::products.no_products_found') }}
                            </flux:text>
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </flux:card>

    {{-- Delete modal --}}
    <flux:modal wire:model="show_delete_modal" class="max-w-md">
        <div class="text-center">
            <div
                class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900/30 mb-4">
                <flux:icon.exclamation-triangle class="h-6 w-6 text-red-600 dark:text-red-400" />
            </div>
            <flux:heading size="lg" class="mb-2">{{ __('ecommerce::products.delete_confirmation_title') }}
            </flux:heading>
            <flux:subheading class="mb-6 text-gray-600 dark:text-zinc-400">
                {{ __('ecommerce::products.delete_confirmation_text') }}</flux:subheading>
        </div>
        <div class="flex gap-3 justify-center">
            <flux:button type="button" wire:click="$set('show_delete_modal', false)">{{ __('common.cancel') }}
            </flux:button>
            <flux:button type="button" variant="danger" wire:click="delete">{{ __('common.delete') }}</flux:button>
        </div>
    </flux:modal>

</div>
