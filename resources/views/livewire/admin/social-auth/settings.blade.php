<div class="space-y-6">
    <div>
        <flux:heading size="xl">
            {{ __('ecommerce::social-auth.title') }}
        </flux:heading>
        <flux:subheading>
            {{ __('ecommerce::social-auth.subtitle') }}
        </flux:subheading>
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

    <form wire:submit="save">
        <div class="space-y-6">
            @foreach ($providerMeta as $key => $meta)
                <flux:card>
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-3">
                            {{-- Provider Icon --}}
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center overflow-hidden
                                @if(empty($providers[$key]['icon_path']))
                                    @switch($key)
                                        @case('facebook') bg-blue-100 text-blue-600 @break
                                        @case('google') bg-red-100 text-red-500 @break
                                        @case('twitter') bg-sky-100 text-sky-500 @break
                                        @case('instagram') bg-pink-100 text-pink-500 @break
                                    @endswitch
                                @endif
                            ">
                                @if (!empty($providers[$key]['icon_path']))
                                    <img src="{{ Storage::url($providers[$key]['icon_path']) }}" class="w-full h-full object-cover" alt="{{ $meta['name'] }}">
                                @else
                                    @switch($key)
                                        @case('facebook')
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                                        @break
                                        @case('google')
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 0 1-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
                                        @break
                                        @case('twitter')
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                                        @break
                                        @case('instagram')
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 1 0 0 12.324 6.162 6.162 0 0 0 0-12.324zM12 16a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm6.406-11.845a1.44 1.44 0 1 0 0 2.881 1.44 1.44 0 0 0 0-2.881z"/></svg>
                                        @break
                                    @endswitch
                                @endif
                            </div>

                            <div>
                                <h3 class="text-lg font-semibold">{{ $meta['name'] }}</h3>
                                <p class="text-sm text-zinc-500">
                                    @if ($providers[$key]['is_active'])
                                        <span class="text-green-600">{{ __('ecommerce::social-auth.active') }}</span>
                                    @else
                                        <span class="text-zinc-400">{{ __('ecommerce::social-auth.inactive') }}</span>
                                    @endif
                                </p>
                            </div>
                        </div>

                        <flux:switch
                            wire:model.live="providers.{{ $key }}.is_active"
                            :label="__('ecommerce::social-auth.enable')"
                        />
                    </div>

                    {{-- Provider Settings (shown when active) --}}
                    @if ($providers[$key]['is_active'])
                        <div class="border-t border-zinc-200 dark:border-zinc-700 pt-4 space-y-4">
                            <div class="border-b border-zinc-200 dark:border-zinc-700 pb-6 mb-6">
                                <flux:label>{{ __('ecommerce::social-auth.icon') }}</flux:label>
                                
                                <div class="flex items-start gap-4 mt-3">
                                    {{-- Preview Area --}}
                                    <div class="flex-shrink-0 w-24 h-24 rounded-lg bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 flex items-center justify-center overflow-hidden relative group">
                                        @if (isset($iconUploads[$key]) && $iconUploads[$key])
                                             @if ($iconUploads[$key]->isPreviewable())
                                                <img src="{{ $iconUploads[$key]->temporaryUrl() }}" class="w-full h-full object-cover">
                                             @else
                                                <flux:icon.document class="w-10 h-10 text-zinc-400" />
                                             @endif
                                        @elseif (!empty($providers[$key]['icon_path']))
                                             {{-- Stored Preview --}}
                                            <img src="{{ Storage::url($providers[$key]['icon_path']) }}" class="w-full h-full object-cover">
                                        @else
                                             {{-- Default Placeholder --}}
                                             <svg class="w-6 h-6 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                        @endif
                                    </div>

                                    {{-- Input Area --}}
                                    <div class="flex-1 space-y-2">
                                        <flux:input type="file" wire:model="iconUploads.{{ $key }}" accept="image/*" />
                                        <flux:error name="iconUploads.{{ $key }}" />
                                        
                                        <div class="flex-1 flex-col">
                                            <p class="text-xs text-zinc-500">{{ __('ecommerce::social-auth.icon_help') }}</p>
                                            
                                            @if (!empty($providers[$key]['icon_path']) && empty($iconUploads[$key]))
                                                <!-- <button type="button" 
                                                    wire:click="deleteIcon('{{ $key }}')"
                                                    wire:confirm="{{ __('Are you sure?') }}"
                                                    class="text-sm bg-red-300 text-red-600 hover:text-red-700 font-medium flex items-center gap-1">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                                    {{ __('ecommerce::social-auth.remove_icon') }}
                                                </button> -->
                                                <flux:button class="cursor-pointer mt-3" size="sm" variant="danger" color="red" icon="trash" 
                                                wire:click="deleteIcon('{{ $key }}')">
                                        {{ __('Delete') }}
                                    </flux:button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <flux:field>
                                <flux:label>{{ __('ecommerce::social-auth.client_id') }}</flux:label>
                                <flux:input
                                    wire:model="providers.{{ $key }}.client_id"
                                    placeholder="{{ __('ecommerce::social-auth.client_id_placeholder') }}"
                                />
                                <flux:error name="providers.{{ $key }}.client_id" />
                            </flux:field>

                            <flux:field>
                                <flux:label>{{ __('ecommerce::social-auth.client_secret') }}</flux:label>
                                <flux:input
                                    type="password"
                                    wire:model="providers.{{ $key }}.client_secret"
                                    placeholder="{{ __('ecommerce::social-auth.client_secret_placeholder') }}"
                                />
                                <flux:error name="providers.{{ $key }}.client_secret" />
                            </flux:field>

                            <flux:field>
                                <flux:label>{{ __('ecommerce::social-auth.redirect_url') }}</flux:label>
                                <flux:input
                                    wire:model="providers.{{ $key }}.redirect_url"
                                    placeholder="{{ url('/auth/' . $key . '/callback') }}"
                                />
                                <flux:description>
                                    {{ __('ecommerce::social-auth.redirect_url_hint') }}: <code class="text-xs bg-zinc-100 dark:bg-zinc-800 px-1 py-0.5 rounded">{{ url('/auth/' . $key . '/callback') }}</code>
                                </flux:description>
                                <flux:error name="providers.{{ $key }}.redirect_url" />
                            </flux:field>
                        </div>
                    @endif
                </flux:card>
            @endforeach
        </div>

        <div class="flex items-center gap-4 justify-end mt-6">
            <flux:button variant="primary" type="submit">
                {{ __('common.save') }}
            </flux:button>
        </div>
    </form>
</div>
