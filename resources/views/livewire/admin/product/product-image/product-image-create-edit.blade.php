<div>
    {{-- Header --}}
    <div class="flex items-center justify-between mb-4">
        <div>
            <flux:heading size="xl">{{ $isEditing ? __('product_images.edit_image') : __('product_images.create_image') }}</flux:heading>
            <flux:subheading>{{ $isEditing ? __('product_images.messages.edit_subtitle') : __('product_images.messages.create_subtitle') }}</flux:subheading>
        </div>
    </div>

    {{-- Card --}}
    <flux:card>
        <form wire:submit="save" class="space-y-6">
            <flux:select
                variant="listbox"
                searchable
                :label="__('product_images.form.product')"
                placeholder="{{ __('product_images.form.product_placeholder') }}"
                wire:model.live="product_id"
                :required="true"
            >
                @foreach ($products as $product)
                    <flux:select.option :value="$product->id">{{ $product->name ?? 'Product #' . $product->id }}</flux:select.option>
                @endforeach
            </flux:select>

            <flux:select
                variant="listbox"
                :label="__('product_images.form.variation')"
                placeholder="{{ __('product_images.form.variation_placeholder') }}"
                wire:model="variation_id"
                clearable
            >
                @foreach ($variations as $variation)
                    <flux:select.option :value="$variation->id">{{ __('product_images.form.variation_option', ['id' => $variation->id]) }}</flux:select.option>
                @endforeach
            </flux:select>

            <flux:input
                wire:model="position"
                type="number"
                min="0"
                :label="__('product_images.form.position')"
                placeholder="{{ __('product_images.form.position_placeholder') }}"
            />

            {{-- Caption per language (like variant create edit) --}}
            <flux:card>
                <div class="mb-5">
                    <livewire:lmt-LangSelector wire:model.live="selected_language" />
                </div>
                <flux:input
                    wire:model="caption.{{ $selected_language }}"
                    :label="__('product_images.form.caption')"
                    placeholder="{{ __('product_images.form.caption_placeholder') }}"
                />
            </flux:card>

            <div>
                <div class="mb-2">
                    <flux:label>{{ __('product_images.form.image') }}</flux:label>
                    @if (!$isEditing)
                        <span class="text-red-500 text-sm">*</span>
                    @endif
                </div>

                @if ($image || $existingImage)
                    <div class="mt-2">
                        <div class="relative w-full">
                            <div class="w-full h-48 rounded-lg overflow-hidden border-2 border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-900 flex items-center justify-center p-6">
                                @if ($image)
                                    <img src="{{ $image->temporaryUrl() }}" class="max-h-full max-w-full object-contain" alt="Preview" />
                                @elseif ($existingImage)
                                    <img src="{{ asset('storage/' . $existingImage) }}" class="max-h-full max-w-full object-contain" alt="Current" />
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
                                {{ __('product_images.form.upload_image') }}
                            </flux:heading>
                            <flux:subheading class="text-center">
                                {{ __('product_images.form.upload_image_text') }}
                            </flux:subheading>
                        </div>
                    </flux:file-upload>
                @endif

                @error('image')
                    <flux:error>{{ $message }}</flux:error>
                @enderror
            </div>

            {{-- Actions --}}
            <flux:separator />

            <div class="flex items-center gap-4 justify-end">
                <flux:button variant="ghost" :href="route(config('ud-ecommerce.admin_route_prefix', 'admin').'.productimages.index')" wire:navigate>
                    {{ __('common.cancel') }}
                </flux:button>
                <flux:button variant="primary" type="submit">
                    {{ $isEditing ? __('product_images.update_image') : __('product_images.create_image') }}
                </flux:button>
            </div>
        </form>
    </flux:card>
</div>
