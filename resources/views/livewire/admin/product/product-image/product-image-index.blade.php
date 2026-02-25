<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl">{{ __('product_images.title') }}</flux:heading>
            <flux:subheading>{{ __('product_images.subtitle') }}</flux:subheading>
        </div>
        <flux:button variant="primary" icon="plus" :href="route(config('ud-ecommerce.admin_route_prefix', 'admin').'.productimages.create')" wire:navigate>
            {{ __('product_images.add_image') }}
        </flux:button>
    </div>

    @if (session('status'))
        <div x-data="{ show: true }"
             x-init="setTimeout(() => show = false, 3000)"
             x-show="show"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
            <flux:callout variant="success" icon="check-circle">
                {{ session('status') }}
            </flux:callout>
        </div>
    @endif

    <flux:card class="space-y-4">
        {{-- Search --}}
        <div class="max-w-sm">
            <flux:input wire:model.live.debounce.300ms="search" placeholder="{{ __('common.search_placeholder') }}"
                icon="magnifying-glass" />
        </div>
        <flux:table :paginate="$productImages">
            <flux:table.columns>
                <flux:table.column>{{ __('product_images.table.image') }}</flux:table.column>
                <flux:table.column>{{ __('product_images.table.product') }}</flux:table.column>
                <flux:table.column>{{ __('product_images.table.position') }}</flux:table.column>
                <flux:table.column>{{ __('product_images.table.caption') }}</flux:table.column>
                <flux:table.column class="w-32">{{ __('common.actions') }}</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse ($productImages as $productImage)
                    <flux:table.row>
                        <flux:table.cell>
                            @if ($productImage->image)
                                <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($productImage->image) }}" alt=""
                                    class="h-12 w-20 object-cover rounded" onerror="this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' fill=\'%23ddd\' viewBox=\'0 0 24 24\'%3E%3Cpath d=\'M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z\'/%3E%3C/svg%3E';" />
                            @else
                                <span class="text-zinc-400 text-sm">—</span>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>{{ $productImage->product?->name ?? '—' }}</flux:table.cell>
                        <flux:table.cell>{{ $productImage->position }}</flux:table.cell>
                        <flux:table.cell>
                            {{ $productImage->productImageLanguages->first()?->caption ?? '—' }}
                        </flux:table.cell>
                        <flux:table.cell>
                            <div class="flex items-center gap-1">
                                <flux:button icon="pencil" :href="route(config('ud-ecommerce.admin_route_prefix', 'admin').'.productimages.edit', $productImage)" wire:navigate>
                                    {{ __('common.edit') }}
                                </flux:button>
                                <flux:button class="cursor-pointer" variant="danger" icon="trash" wire:click="requestDelete({{ $productImage->id }})">
                                    {{ __('common.delete') }}
                                </flux:button>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="5" class="text-center py-8">
                            <div class="flex flex-col items-center gap-2">
                                <flux:text class="text-zinc-500">{{ __('product_images.no_images_found') }}</flux:text>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </flux:card>

    <flux:modal wire:model="show_delete_modal" class="max-w-md">
        <div class="text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                <flux:icon.exclamation-triangle class="h-6 w-6 text-red-600" />
            </div>

            <flux:heading size="lg" class="mb-2">
                {{ __('product_images.delete_confirmation_title') }}
            </flux:heading>

            <flux:subheading class="mb-6 text-gray-600">
                {{ __('product_images.delete_confirmation_text') }}
            </flux:subheading>
        </div>

        <div class="flex gap-3 justify-center mt-6">
            <flux:button type="button" wire:click="$set('show_delete_modal', false)">
                {{ __('common.cancel') }}
            </flux:button>

            <flux:button class="cursor-pointer" type="button" variant="danger" wire:click="delete">
                {{ __('common.delete') }}
            </flux:button>
        </div>
    </flux:modal>
</div>
