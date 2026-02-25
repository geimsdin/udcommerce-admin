<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl">{{ __('variantgroups.title') }}</flux:heading>
            <flux:subheading>{{ __('variantgroups.subtitle') }}</flux:subheading>
        </div>
        <flux:button variant="primary" icon="plus" :href="route(config('ud-ecommerce.admin_route_prefix', 'admin').'.variantgroups.create')" wire:navigate>
            {{ __('variantgroups.add_variantgroup') }}
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

    {{-- Card --}}
    <flux:card class="space-y-4">
        {{-- Search --}}
        <div class="max-w-sm">
            <flux:input
                wire:model.live.debounce.300ms="search"
                placeholder="{{ __('variantgroups.search_placeholder') }}"
                icon="magnifying-glass"
            />
        </div>

        {{-- Table --}}
        <flux:table>
            <flux:table.columns>
                <flux:table.column class="w-10"></flux:table.column>
                <flux:table.column>{{ __('variantgroups.table.name') }}</flux:table.column>
                <flux:table.column>{{ __('variantgroups.table.type') }}</flux:table.column>
                <flux:table.column class="w-32">{{ __('variantgroups.table.actions') }}</flux:table.column>
            </flux:table.columns>

            <flux:table.rows wire:sort="reorder">
                @forelse ($variantgroups as $variantgroup)
                    <flux:table.row wire:key="variantgroups-{{ $variantgroup->id }}" wire:sort:item="{{ $variantgroup->id }}" class="cursor-move">
                        <flux:table.cell>
                            <div class="cursor-grab active:cursor-grabbing text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"></path>
                                </svg>
                            </div>
                        </flux:table.cell>
                        <flux:table.cell>{{ $variantgroup->name }}</flux:table.cell>
                        <flux:table.cell>{{ $variantgroup->type }}</flux:table.cell> 
                        <flux:table.cell>
                            <div class="flex items-center gap-1">
                                <flux:button
                                    size="sm"
                                    variant="ghost"
                                    icon="pencil"
                                    :href="route(config('ud-ecommerce.admin_route_prefix', 'admin').'.variantgroups.edit', $variantgroup)"
                                    wire:navigate
                                />
                                <flux:button
                                    size="sm"
                                    variant="ghost"
                                    icon="trash"
                                    wire:click="requestDelete({{ $variantgroup->id }})"
                                    class="text-red-500 hover:text-red-700 hover:bg-red-50 dark:hover:bg-red-900/20"
                                />
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="4" class="text-center py-8">
                            <div class="flex flex-col items-center gap-2">
                                <flux:icon.users class="size-12 text-zinc-300 dark:text-zinc-600" />
                                <flux:text class="text-zinc-500">{{ __('variantgroups.no_found') }}</flux:text>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>

        {{-- Pagination --}}
        @if ($variantgroups->hasPages())
            <div class="pt-4">
                {{ $variantgroups->links() }}
            </div>
        @endif
    </flux:card>
    <flux:modal wire:model="show_delete_modal" class="max-w-md">
        <div class="text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                <flux:icon.exclamation-triangle class="h-6 w-6 text-red-600" />
            </div>
            
            <flux:heading size="lg" class="mb-2">
                {{ __('variantgroups.delete_confirmation_title') }}
            </flux:heading>
            
            <flux:subheading class="mb-6 text-gray-600">
                {{ __('variantgroups.delete_confirmation_text') }}
            </flux:subheading>
        </div>

        <div class="flex gap-3 justify-center mt-6">
            <flux:button type="button" wire:click="$set('show_delete_modal', false)">
                {{ __('common.cancel') }}
            </flux:button>

            <flux:button type="button" variant="danger" wire:click="delete">
                {{ __('common.delete') }}
            </flux:button>
        </div>
    </flux:modal>
</div>

