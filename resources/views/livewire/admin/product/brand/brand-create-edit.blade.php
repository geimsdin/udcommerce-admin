<div>
    {{-- Header --}}
    <div class="flex items-center justify-between mb-4">
        <div>
            <flux:heading size="xl">{{ $isEditing ? __('ecommerce::brands.edit_brand') : __('ecommerce::brands.create_brand') }}</flux:heading>
            <flux:subheading>{{ $isEditing ? __('ecommerce::brands.messages.edit_subtitle') : __('ecommerce::brands.messages.create_subtitle') }}</flux:subheading>
        </div>
    </div>

    {{-- Card --}}
    <flux:card>
        <form wire:submit="save" class="space-y-6">
            {{-- Basic Information --}}
            <flux:input 
                wire:model="name" 
                :label="__('ecommerce::brands.form.name')" 
                placeholder="{{ __('ecommerce::brands.form.name_placeholder') }}" 
                :badge="__('common.required')"
            />
            
            <flux:textarea 
                wire:model="description" 
                :label="__('ecommerce::brands.form.description')" 
                placeholder="{{ __('ecommerce::brands.form.description_placeholder') }}" 
                rows="3" 
            />

            <div>
                <div class="mb-2">
                    <flux:label>{{ __('ecommerce::brands.form.brand_logo') }}</flux:label>
                </div>
                
                @if ($image || $existingImage)
                    <div class="mt-2">
                        <div class="relative w-full">
                            <div class="w-full h-48 rounded-lg overflow-hidden border-2 border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-900 flex items-center justify-center p-6">
                                @if ($image)
                                    <img src="{{ $image->temporaryUrl() }}" class="max-h-full max-w-full object-contain" alt="Brand preview" />
                                @elseif ($existingImage)
                                    <img src="{{ asset('storage/' . $existingImage) }}" class="max-h-full max-w-full object-contain" alt="Brand logo" />
                                @endif
                            </div>
                            
                            <button 
                                type="button"
                                wire:click="deleteImage"
                                class="absolute -top-2 -right-2 size-8 rounded-full bg-red-500 hover:bg-red-600 text-white flex items-center justify-center shadow-lg transition-colors"
                            >
                                <flux:icon name="x-mark" class="size-5" />
                            </button>
                        </div>
                        
                    </div>
                @else
                    <flux:file-upload wire:model="image" class="mt-2">
                        <div class="flex flex-col items-center justify-center py-8 px-4 border-2 border-dashed border-zinc-300 dark:border-zinc-700 rounded-lg bg-zinc-50 dark:bg-zinc-900/50 hover:bg-zinc-100 dark:hover:bg-zinc-900 transition-colors cursor-pointer">
                            <div class="size-16 rounded-full bg-zinc-200 dark:bg-zinc-800 flex items-center justify-center mb-4">
                                <flux:icon name="photo" class="size-8 text-zinc-500 dark:text-zinc-400" />
                            </div>
                            
                            <flux:heading size="lg" class="mb-1">
                                {{ __('ecommerce::brands.form.upload_image') }}
                            </flux:heading>
                            
                            <flux:subheading class="text-center">
                                {{ __('ecommerce::brands.form.upload_image_text') }}
                            </flux:subheading>
                        </div>
                    </flux:file-upload>
                @endif
            </div>

            {{-- Company Information --}}
            <flux:separator />
            
            <flux:heading size="lg">{{ __('ecommerce::brands.form.company_information') }}</flux:heading>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <flux:input 
                    wire:model="company_name" 
                    :label="__('ecommerce::brands.form.company_name')" 
                    placeholder="{{ __('ecommerce::brands.form.company_name_placeholder') }}"
                />
                
                <flux:input 
                    wire:model="email" 
                    type="email"
                    :label="__('ecommerce::brands.form.email')" 
                    placeholder="{{ __('ecommerce::brands.form.email_placeholder') }}"
                />
                
                <flux:input 
                    wire:model="tel" 
                    type="tel"
                    :label="__('ecommerce::brands.form.tel')" 
                    placeholder="{{ __('ecommerce::brands.form.tel_placeholder') }}"
                />
                
                <flux:input 
                    wire:model="address" 
                    :label="__('ecommerce::brands.form.address')" 
                    placeholder="{{ __('ecommerce::brands.form.address_placeholder') }}"
                />
                
                <flux:input 
                    wire:model="city" 
                    :label="__('ecommerce::brands.form.city')" 
                    placeholder="{{ __('ecommerce::brands.form.city_placeholder') }}"
                />
                
                <flux:input 
                    wire:model="state" 
                    :label="__('ecommerce::brands.form.state')" 
                    placeholder="{{ __('ecommerce::brands.form.state_placeholder') }}"
                />
                
                <flux:input 
                    wire:model="country" 
                    :label="__('ecommerce::brands.form.country')" 
                    placeholder="{{ __('ecommerce::brands.form.country_placeholder') }}"
                />
            </div>

            {{-- Actions --}}
            <flux:separator />
            
            <div class="flex items-center gap-4 justify-end">
                <flux:button variant="ghost" :href="route(config('ud-ecommerce.admin_route_prefix', 'admin').'.brands.index')" wire:navigate>
                    {{ __('common.cancel') }}
                </flux:button>
                
                <flux:button variant="primary" type="submit">
                    {{ $isEditing ? __('ecommerce::brands.update_brand') : __('ecommerce::brands.create_brand') }}
                </flux:button>
            </div>
        </form>
    </flux:card>
</div>