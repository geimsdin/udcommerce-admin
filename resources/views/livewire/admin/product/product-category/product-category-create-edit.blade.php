<div>
    {{-- Header --}}
    <div class="mb-5">
        <flux:heading size="xl">
            {{ $isEditing ? __('ecommerce::product-categories.edit_category') : __('ecommerce::product-categories.add_category') }}
        </flux:heading>
        <flux:subheading>
            {{ $isEditing ? __('ecommerce::product-categories.messages.edit_subtitle') : __('ecommerce::product-categories.messages.create_subtitle') }}
        </flux:subheading>
    </div>
    <form wire:submit="save">
        <div class="flex justify-between gap-6">
            <div class="w-2/3">
                <flux:card class="space-y-6">
                    <flux:heading size="lg">
                        {{ __('ecommerce::product-categories.form.category_details') }}
                    </flux:heading>
                    <flux:separator />
                    {{-- Name (Multi-language) --}}
                    <flux:card>
                        <div class="mb-5">
                            <livewire:lmt-LangSelector wire:model.live="selected_language" />
                        </div>
                        <livewire:lmt-TextInput label="{{ __('ecommerce::product-categories.form.name') }}"
                            placeholder="{{ __('ecommerce::product-categories.form.name_placeholder') }}" wire:model="name"
                            :required="true" />
                        <div class="mt-2">
                            <livewire:lmt-Textarea label="{{ __('ecommerce::product-categories.form.description') }}"
                                placeholder="{{ __('ecommerce::product-categories.form.description_placeholder') }}"
                                wire:model="description" :required="false" />
                        </div>
                    </flux:card>
                    <flux:separator />

                    {{-- Category Image --}}
                    <div>
                        <flux:heading size="lg" class="mb-3">
                            {{ __('ecommerce::product-categories.form.category_image') }}
                        </flux:heading>

                        @if ($image || $existingMediaUrl)
                            <div class="mt-2">
                                <div class="relative w-full">
                                    <div
                                        class="w-full h-48 rounded-lg overflow-hidden border-2 border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-900 flex items-center justify-center p-6">
                                        @if ($image)
                                            <img src="{{ $image->temporaryUrl() }}" class="max-h-full max-w-full object-contain"
                                                alt="Category preview" />
                                        @elseif ($existingMediaUrl)
                                            <img src="{{ $existingMediaUrl }}" class="max-h-full max-w-full object-contain"
                                                alt="Category image" />
                                        @endif
                                    </div>

                                    {{-- Show all generated conversions --}}
                                    @if (!empty($existingConversions) && !$image)
                                        <div class="mt-3">
                                            <span
                                                class="text-xs font-medium text-zinc-500 dark:text-zinc-400 mb-2 block">{{ __('ecommerce::product-categories.form.thumbnail_preview') }}:</span>
                                            <div class="flex flex-wrap gap-3">
                                                @foreach ($existingConversions as $conversionName => $conversionUrl)
                                                    <div class="text-center">
                                                        <img src="{{ $conversionUrl }}"
                                                            class="h-16 w-16 object-contain rounded border border-zinc-200 dark:border-zinc-700"
                                                            alt="{{ $conversionName }}" />
                                                        <span
                                                            class="text-xs text-zinc-400 mt-1 block font-mono">{{ $conversionName }}</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif

                                    <button type="button" wire:click="deleteImage"
                                        class="absolute -top-2 -right-2 size-8 rounded-full bg-red-500 hover:bg-red-600 text-white flex items-center justify-center shadow-lg transition-colors">
                                        <flux:icon name="x-mark" class="size-5" />
                                    </button>
                                </div>
                            </div>
                        @else
                            <flux:file-upload wire:model="image" class="mt-2">
                                <div
                                    class="flex flex-col items-center justify-center py-8 px-4 border-2 border-dashed border-zinc-300 dark:border-zinc-700 rounded-lg bg-zinc-50 dark:bg-zinc-900/50 hover:bg-zinc-100 dark:hover:bg-zinc-900 transition-colors cursor-pointer">
                                    <div
                                        class="size-16 rounded-full bg-zinc-200 dark:bg-zinc-800 flex items-center justify-center mb-4">
                                        <flux:icon name="photo" class="size-8 text-zinc-500 dark:text-zinc-400" />
                                    </div>
                                    <flux:heading size="lg" class="mb-1">
                                        {{ __('ecommerce::product-categories.form.upload_image') }}
                                    </flux:heading>
                                    <flux:subheading class="text-center">
                                        {{ __('ecommerce::product-categories.form.upload_image_text') }}
                                    </flux:subheading>
                                </div>
                            </flux:file-upload>
                        @endif

                        @error('image')
                            <flux:error>{{ $message }}</flux:error>
                        @enderror
                    </div>

                    <flux:separator />

                    {{-- Buttons --}}
                    <div class="flex items-center gap-4">
                        <flux:button variant="primary" type="submit">
                            {{ $isEditing ? __('ecommerce::product-categories.update_category') : __('ecommerce::product-categories.create_category') }}
                        </flux:button>
                        <flux:button variant="ghost"
                            :href="route(config('ud-ecommerce.admin_route_prefix', 'admin').'.product-categories.index')"
                            wire:navigate>
                            {{ __('common.cancel') }}
                        </flux:button>
                    </div>
                </flux:card>
            </div>
            <div class="w-1/3">
                <flux:card class="space-y-6">
                    <flux:heading size="lg">
                        {{ __('ecommerce::product-categories.form.settings') }}
                    </flux:heading>
                    <flux:separator />
                    <flux:switch wire:model="status" :label="__('ecommerce::product-categories.form.status')" />
                    <flux:select variant="listbox" searchable :label="__('ecommerce::product-categories.form.parent_id')"
                        placeholder="{{ __('ecommerce::product-categories.form.parent_id_placeholder') }}" wire:model="parent_id"
                        clearable>
                        @foreach ($this->productCategories as $productCategory)
                            <flux:select.option :value="$productCategory->id">{{ $productCategory->name }}
                            </flux:select.option>
                        @endforeach
                    </flux:select>
                </flux:card>
            </div>
        </div>
    </form>
</div>