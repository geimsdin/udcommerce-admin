<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl">{{ __('Payment Gateways') }}</flux:heading>
            <flux:subheading>{{ __('Manage active payment methods for your store') }}</flux:subheading>
        </div>
    </div>

    @if (session('status'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show"
            x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100"
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
        <flux:table :paginate="$gateways">
            <flux:table.columns>
                <flux:table.column>{{ __('Name') }}</flux:table.column>
                <flux:table.column>{{ __('Driver') }}</flux:table.column>
                <flux:table.column>{{ __('Status') }}</flux:table.column>
                <flux:table.column class="w-32">{{ __('Action') }}</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse ($gateways as $gateway)
                    <flux:table.row>
                        <flux:table.cell>{{ collect(explode('\\', $gateway->name))->last() }}</flux:table.cell>
                        <flux:table.cell>{{ $gateway->driver }}</flux:table.cell>
                        <flux:table.cell>
                            <flux:badge color="{{ $gateway->active ? 'green' : 'red' }}"
                                icon="{{ $gateway->active ? 'check-circle' : 'x-circle' }}">
                                {{ $gateway->active ? __('Active') : __('Inactive') }}
                            </flux:badge>
                        </flux:table.cell>
                        <flux:table.cell>
                            <div class="flex items-center gap-2">
                                <flux:button size="sm" variant="ghost" icon="pencil-square"
                                    wire:click="edit({{ $gateway->id }})">
                                    {{ __('Edit') }}
                                </flux:button>
                                <flux:button size="sm" variant="{{ $gateway->active ? 'danger' : 'primary' }}"
                                    wire:click="toggleActive({{ $gateway->id }})">
                                    {{ $gateway->active ? __('Deactivate') : __('Activate') }}
                                </flux:button>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="4" class="text-center py-8">
                            <div class="flex flex-col items-center gap-2">
                                <flux:text class="text-zinc-500">{{ __('No payment gateways found.') }}</flux:text>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </flux:card>

    {{-- Edit Modal --}}
    <flux:modal wire:model="editing" class="md:w-[32rem]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('Configure') }} {{ $editingName }}</flux:heading>
                <flux:subheading>{{ __('Update API keys and other settings for this gateway') }}</flux:subheading>
            </div>

            <div class="grid gap-6">
                @foreach ($editingConfig as $key => $value)
                    {{-- @dd($key) --}}
                    @if (is_bool($value) || $key === 'testMode')
                        <flux:checkbox wire:model="editingConfig.{{ $key }}" :label="str($key)->headline()" />
                    @else
                        <flux:input wire:key="input.{{ $key }}" wire:model="editingConfig.{{ $key }}"
                            :label="str($key)->headline()" />
                    @endif
                @endforeach
            </div>

            <div class="flex">
                <flux:spacer />
                <div class="flex gap-2">
                    <flux:button variant="ghost" wire:click="cancel">{{ __('Cancel') }}</flux:button>
                    <flux:button variant="primary" wire:click="save">{{ __('Save Changes') }}</flux:button>
                </div>
            </div>
        </div>
    </flux:modal>
</div>