<?php

namespace Unusualdope\LaravelEcommerce\Livewire\Admin\Product\Product;

use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithFileUploads;
use Unusualdope\LaravelEcommerce\Models\Product\Brand;
use Unusualdope\LaravelEcommerce\Models\Product\Feature;
use Unusualdope\LaravelEcommerce\Models\Product\FeatureGroup;
use Unusualdope\LaravelEcommerce\Models\Product\Product;
use Unusualdope\LaravelEcommerce\Models\Product\ProductCategory;
use Unusualdope\LaravelEcommerce\Models\Product\ProductImage;
use Unusualdope\LaravelEcommerce\Models\Product\ProductImageLanguage;
use Unusualdope\LaravelEcommerce\Models\Product\ProductLanguage;
use Unusualdope\LaravelEcommerce\Models\Product\Season;
use Unusualdope\LaravelEcommerce\Models\Product\SpecificPrice;
use Unusualdope\LaravelEcommerce\Models\Stock\Variant;
use Unusualdope\LaravelEcommerce\Models\Stock\VariantGroup;
use Unusualdope\LaravelEcommerce\Models\Stock\Variation;

class ProductCreateEdit extends Component
{
    use WithFileUploads;

    public ?Product $product = null;

    public bool $isEditing = false;

    public $product_id = 0;

    public $languageModel;

    public $selected_language;

    public int $brand_id;

    public $sku;

    public $ean;

    public $mpn;

    public $upc;

    public $isbn;

    public string $product_type = 'simple';

    public int $low_stock_alert = 0;

    public int $minimal_quantity = 0;

    public int $quantity = 0;

    public float $price = 0;

    public bool $status = false;

    public array $category_ids = [];

    public array $season_ids = [];

    public array $productFeatures = [];

    // translatable fields
    public array $name = [];

    public array $description_long = [];

    public array $description_short = [];

    public array $link_rewrite = [];

    public array $meta_title = [];

    public array $meta_description = [];

    public array $selections = [];

    public array $photos = [];

    public array $variations = [];

    public array $variationImages = []; // Store uploaded images for variations

    public bool $showVariantModal = false;

    public ?int $currentVariationIndex = null;

    public array $selectedVariants = [];

    public array $variantGroups = [];

    public bool $showGroupVariantModal = false;

    public array $selectedGroupVariants = [];

    public array $specificPrices = [];

    public bool $showSpecificPriceModal = false;

    public ?int $editingSpecificPriceIndex = null;

    public array $currentSpecificPrice = [];

    public bool $applyToAllCustomers = true;

    public bool $unlimitedDuration = false;

    public bool $applyDiscount = false;

    public bool $setSpecificPrice = false;

    protected $rules = [
        'photos.*' => 'image|max:2048', // 2MB per file
        'variationImages.*' => 'image|max:2048', // 2MB per file
    ];

    public function mount(?Product $product = null): void
    {
        $languageModel = config('lmt.language_model');
        $this->languageModel = $languageModel;
        $this->product = $product;
        if ($product?->exists) {
            $this->isEditing = true;
            $this->brand_id = $product->brand_id;
            $this->product_type = $product->type;
            $this->price = $product->price;
            $this->sku = $product->sku;
            $this->ean = $product->ean;
            $this->mpn = $product->mpn;
            $this->upc = $product->upc;
            $this->isbn = $product->isbn;
            $this->status = $product->status;
            $this->low_stock_alert = $product->low_stock_alert;
            $this->minimal_quantity = $product->minimal_quantity;
            $this->quantity = $product->quantity;
            $this->category_ids = $product->categories->pluck('id')->toArray();
            $this->season_ids = $product->seasons->pluck('id')->toArray();
            $this->loadProductFeatures();
            foreach ($product->images as $images) {
                // Only load images without variation_id (product images, not variation images)
                if (is_null($images->variation_id)) {
                    $caption = [];
                    $caption[0] = null;
                    foreach ($images->productImageLanguages as $imageLanguage) {
                        $caption[1][$imageLanguage->language_id] = $imageLanguage->caption;
                    }

                    $this->selections[] = [
                        'image_id' => $images->id,
                        'photo' => null,
                        'path' => $images->image,
                        'variation_id' => $images->variation_id,
                        'caption' => $caption,
                    ];
                }
            }

            // Load existing variations
            foreach ($product->variations as $variation) {
                $variantsByGroup = [];
                foreach ($variation->variants as $variant) {
                    $variantsByGroup[$variant->variant_group_id] = $variant->id;
                }

                // Load variation image if exists
                $variationImage = $product->images()->where('variation_id', $variation->id)->first();

                $this->variations[] = [
                    'variation_id' => $variation->id,
                    'sku' => $variation->sku,
                    'ean' => $variation->ean,
                    'mpn' => $variation->mpn,
                    'upc' => $variation->upc,
                    'isbn' => $variation->isbn,
                    'low_stock_alert' => $variation->low_stock_alert,
                    'minimal_quantity' => $variation->minimal_quantity,
                    'available_from' => $variation->available_from,
                    'available_to' => $variation->available_to,
                    'quantity' => $variation->quantity,
                    'price' => $variation->price,
                    'image_id' => $variationImage?->id,
                    'image_path' => $variationImage?->image,
                    'image' => null,
                    'variants_by_group' => $variantsByGroup,
                ];
            }

            $this->loadTranslatableData();
            $this->loadSpecificPrices();
        } else {
            $this->product = new Product;
            $this->initializeTranslatableData();
        }
        $this->selected_language = $this->languageModel::getDefaultLanguage();
    }

    protected function initializeTranslatableData(): void
    {
        $languages = $this->languageModel::getLanguagesForMultilangForm();
        foreach ($languages as $language) {
            $this->name[$language['id']] = '';
            $this->description_long[$language['id']] = '';
            $this->description_short[$language['id']] = '';
            $this->link_rewrite[$language['id']] = '';
            $this->meta_title[$language['id']] = '';
            $this->meta_description[$language['id']] = '';
        }
    }

    protected function loadTranslatableData(): void
    {
        $languages = $this->languageModel::getLanguagesForMultilangForm();
        foreach ($languages as $language) {
            $langData = $this->product->getSpecificLanguage($language['id']);
            $this->name[$language['id']] = $langData?->name ?? '';
            $this->description_long[$language['id']] = $langData?->description_long ?? '';
            $this->description_short[$language['id']] = $langData?->description_short ?? '';
            $this->link_rewrite[$language['id']] = $langData?->link_rewrite ?? '';
            $this->meta_title[$language['id']] = $langData?->meta_title ?? '';
            $this->meta_description[$language['id']] = $langData?->meta_description ?? '';
        }
    }

    public function addImageCard()
    {
        $this->selections[] = [
            'photo' => null,
            'caption' => [],
            'variation_id' => null,
        ];
    }

    public function removeImageCard($index)
    {
        if (! isset($this->selections[$index])) {
            return;
        }

        $selection = $this->selections[$index];

        if (! empty($selection['image_id'])) {
            $image = ProductImage::find($selection['image_id']);
            if ($image) {
                Storage::disk('public')->delete($image->image);
                ProductImageLanguage::where('product_image_id', $image->id)->delete();
                $image->delete();
            }
        }

        unset($this->selections[$index]);
        $this->selections = array_values($this->selections);
    }

    public function addVariation()
    {
        $this->variations[] = [
            'sku' => '',
            'ean' => '',
            'mpn' => '',
            'upc' => '',
            'isbn' => '',
            'quantity' => 0,
            'price' => $this->price ?? 0,
            'image' => null,
            'image_path' => null,
            'variants_by_group' => [],
            'low_stock_alert' => 0,
            'minimal_quantity' => 0,
            'available_from' => null,
            'available_to' => null,
        ];
    }

    public function removeVariationImage($index)
    {
        unset($this->variations[$index]['image']);
        unset($this->variations[$index]['image_path']);
    }

    public function updatedVariations($index, $value)
    {
        if ($value instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
            $this->variations[$index]['image'] = $value;
        }
    }

    public function removeVariation($index)
    {
        if (! isset($this->variations[$index])) {
            return;
        }

        $variation = $this->variations[$index];

        // Delete variation and its image
        if (! empty($variation['variation_id'])) {
            $variationModel = Variation::find($variation['variation_id']);
            if ($variationModel) {
                // Delete variation image from product_images table
                $variationImage = ProductImage::where('product_id', $this->product->id)
                    ->where('variation_id', $variationModel->id)
                    ->first();

                if ($variationImage) {
                    Storage::disk('public')->delete($variationImage->image);
                    $variationImage->delete();
                }

                $variationModel->variants()->detach();
                $variationModel->delete();
            }
        }

        unset($this->variations[$index]);
        $this->variations = array_values($this->variations);
    }

    public function openVariantModal($index)
    {
        $this->currentVariationIndex = $index;
        $this->selectedVariants = $this->variations[$index]['variants_by_group'] ?? [];
        $this->showVariantModal = true;
    }

    public function closeVariantModal()
    {
        $this->showVariantModal = false;
        $this->currentVariationIndex = null;
        $this->selectedVariants = [];
    }

    public function saveVariantSelection()
    {
        if ($this->currentVariationIndex === null || ! isset($this->variations[$this->currentVariationIndex])) {
            session()->flash('error', __('ecommerce::products.variation_not_found'));
            $this->closeVariantModal();

            return;
        }

        $this->selectedVariants = array_filter($this->selectedVariants, function ($value) {
            return ! is_null($value) && $value !== '';
        });

        // Check for duplicate variant combinations
        if (! $this->isVariantCombinationUnique($this->selectedVariants, $this->currentVariationIndex)) {
            session()->flash('error', __('ecommerce::products.duplicate_variant_combination'));

            return;
        }

        $this->variations[$this->currentVariationIndex]['variants_by_group'] = $this->selectedVariants;

        $this->closeVariantModal();

        session()->flash('success', __('ecommerce::products.variants_updated'));
    }

    private function isVariantCombinationUnique($variantsByGroup, $excludeIndex = null)
    {
        // Sort the variants by group for consistent comparison
        ksort($variantsByGroup);
        $combination = json_encode($variantsByGroup);

        foreach ($this->variations as $index => $variation) {
            // Skip the current variation being edited
            if ($index === $excludeIndex) {
                continue;
            }

            // Sort and compare
            $existingVariants = $variation['variants_by_group'] ?? [];
            ksort($existingVariants);
            $existingCombination = json_encode($existingVariants);

            if ($combination === $existingCombination) {
                return false;
            }
        }

        return true;
    }

    public function openGroupVariantModal()
    {
        $this->showGroupVariantModal = true;
    }

    public function closeGroupVariantModal()
    {
        $this->showGroupVariantModal = false;
        $this->selectedGroupVariants = [];
    }

    public function autoGenerateVariations()
    {
        $groupedVariants = [];
        $variants_filled = false;
        foreach ($this->selectedGroupVariants as $group_id => $group) {
            if (empty($group)) {
                continue;
            }
            if (count($group) > 0) {
                $variants_filled = true;
                $groupedVariants[$group_id] = $group;
            }
        }

        if (! $variants_filled) {
            session()->flash('error_group_variants', __('ecommerce::products.no_variants_selected'));

            return;
        }

        // Generate all combinations
        $combinations = $this->generateGroupCombinations($groupedVariants);

        // Check each combination for duplicates
        $duplicatesFound = false;
        foreach ($combinations as $combination) {
            if (! $this->isVariantCombinationUnique($combination)) {
                $duplicatesFound = true;

                continue; // Skip this combination
            }

            $this->variations[] = [
                'sku' => '',
                'ean' => '',
                'mpn' => '',
                'upc' => '',
                'isbn' => '',
                'quantity' => 0,
                'price' => $this->price,
                'image' => null,
                'image_path' => null,
                'variants_by_group' => $combination,
                'low_stock_alert' => 0,
                'minimal_quantity' => 0,
                'available_from' => null,
                'available_to' => null,
            ];
        }

        if ($duplicatesFound) {
            session()->flash('warning', __('ecommerce::products.some_variations_skipped'));
        } else {
            session()->flash('success', __('ecommerce::products.variations_generated'));
        }

        $this->closeGroupVariantModal();
    }

    private function generateGroupCombinations($groupedVariants)
    {
        $groupIds = array_keys($groupedVariants);
        $result = [[]];

        foreach ($groupIds as $groupId) {
            $temp = [];
            foreach ($result as $combination) {
                foreach ($groupedVariants[$groupId] as $variantId) {
                    $newCombination = $combination;
                    $newCombination[$groupId] = $variantId;
                    $temp[] = $newCombination;
                }
            }
            $result = $temp;
        }

        return $result;
    }

    #[Computed]
    public function getVariants()
    {
        return Variant::all();
    }

    #[Computed]
    public function getVariantGroups()
    {
        return VariantGroup::with('variants')->get();
    }

    #[Computed]
    public function getProductTab(): array
    {
        if ($this->product_type == 'variable') {
            return [
                'general' => __('ecommerce::products.form.general'),
                'variations' => __('ecommerce::products.form.variations'),
                'images' => __('ecommerce::products.form.images'),
                'prices' => __('ecommerce::products.form.prices&fees'),
                'seo' => __('ecommerce::products.form.seo'),
            ];
        } else {
            return [
                'general' => __('ecommerce::products.form.general'),
                'images' => __('ecommerce::products.form.images'),
                'stocks' => __('ecommerce::products.form.stocks'),
                'prices' => __('ecommerce::products.form.prices&fees'),
                'seo' => __('ecommerce::products.form.seo'),
            ];
        }
    }

    #[Computed]
    public function getProductTypes(): array
    {
        return [
            'simple' => __('ecommerce::products.form.product_type_simple'),
            'virtual' => __('ecommerce::products.form.product_type_virtual'),
            'variable' => __('ecommerce::products.form.product_type_variable'),
        ];
    }

    #[Computed]
    public function getBrands()
    {
        return Brand::all();
    }

    #[Computed]
    public function getCategories()
    {
        return ProductCategory::with('currentLanguage')->orderBy('id')->get();
    }

    #[Computed]
    public function getSeasons()
    {
        return Season::all();
    }

    #[Computed]
    public function getFeatureGroups()
    {
        return FeatureGroup::with(['features.currentLanguage'])->orderBy('id')->get();
    }

    protected function loadProductFeatures(): void
    {
        if ($this->product && $this->product->exists) {
            // Group features by feature group
            $featuresByGroup = $this->product->features->groupBy('feature_group_id');
            
            foreach ($featuresByGroup as $groupId => $features) {
                $this->productFeatures[] = [
                    'id' => null,
                    'feature_group_id' => $groupId,
                    'feature_ids' => $features->pluck('id')->toArray(),
                ];
            }
        }
    }

    public function addProductFeature(): void
    {
        $this->productFeatures[] = [
            'id' => null,
            'feature_group_id' => null,
            'feature_ids' => [],
        ];
    }

    public function removeProductFeature($index): void
    {
        if (!isset($this->productFeatures[$index])) {
            return;
        }

        unset($this->productFeatures[$index]);
        $this->productFeatures = array_values($this->productFeatures);
    }

    public function updatedProductFeatures($value, $key): void
    {
        // Handle nested property updates
        $keys = explode('.', $key);
        
        if (count($keys) >= 2 && $keys[1] === 'feature_group_id') {
            $index = (int) $keys[0];
            // Reset feature_ids when feature group changes
            if (isset($this->productFeatures[$index])) {
                $this->productFeatures[$index]['feature_ids'] = [];
            }
        }
    }

    protected function saveProductFeatures(): void
    {
        if (!$this->product || !$this->product->exists) {
            return;
        }

        $allFeatureIds = [];
        
        foreach ($this->productFeatures as $featureRow) {
            if (!empty($featureRow['feature_group_id']) && !empty($featureRow['feature_ids'])) {
                // Add all selected feature IDs
                $allFeatureIds = array_merge($allFeatureIds, $featureRow['feature_ids']);
            }
        }

        // Sync all features
        $this->product->features()->sync(array_unique($allFeatureIds));
    }

    public function getFeaturesByGroup($groupId)
    {
        if (empty($groupId)) {
            return collect();
        }
        
        return Feature::where('feature_group_id', $groupId)
            ->with('currentLanguage')
            ->orderBy('id')
            ->get();
    }

    protected function loadSpecificPrices(): void
    {
        if ($this->product && $this->product->exists) {
            foreach ($this->product->specificPrices as $specificPrice) {
                $this->specificPrices[] = [
                    'id' => $specificPrice->id,
                    'id_currency' => $specificPrice->id_currency,
                    'id_client_type' => $specificPrice->id_client_type,
                    'id_customer' => $specificPrice->id_customer,
                    'price' => $specificPrice->price,
                    'from_quantity' => $specificPrice->from_quantity,
                    'reduction' => $specificPrice->reduction,
                    'reduction_tax' => $specificPrice->reduction_tax,
                    'reduction_type' => $specificPrice->reduction_type,
                    'from' => $specificPrice->from ? $specificPrice->from->format('Y-m-d\TH:i') : null,
                    'to' => $specificPrice->to ? $specificPrice->to->format('Y-m-d\TH:i') : null,
                ];
            }
        }
    }

    public function openSpecificPriceModal(?int $index = null): void
    {
        if ($index !== null && isset($this->specificPrices[$index])) {
            $this->editingSpecificPriceIndex = $index;
            $this->currentSpecificPrice = $this->specificPrices[$index];
            $this->applyToAllCustomers = empty($this->currentSpecificPrice['id_customer']);
            $this->unlimitedDuration = empty($this->currentSpecificPrice['from']) && empty($this->currentSpecificPrice['to']);
            $this->applyDiscount = !empty($this->currentSpecificPrice['reduction']) && (float)$this->currentSpecificPrice['reduction'] > 0;
            $this->setSpecificPrice = !empty($this->currentSpecificPrice['price']) && (float)$this->currentSpecificPrice['price'] > 0;
        } else {
            $this->editingSpecificPriceIndex = null;
            $this->currentSpecificPrice = [
                'id' => null,
                'id_currency' => null,
                'id_client_type' => null,
                'id_customer' => null,
                'price' => 0,
                'from_quantity' => 1,
                'reduction' => 0,
                'reduction_tax' => false,
                'reduction_type' => 'amount',
                'from' => null,
                'to' => null,
            ];
            $this->applyToAllCustomers = true;
            $this->unlimitedDuration = true;
            $this->applyDiscount = false;
            $this->setSpecificPrice = false;
        }
        $this->showSpecificPriceModal = true;
    }

    public function closeSpecificPriceModal(): void
    {
        $this->showSpecificPriceModal = false;
        $this->editingSpecificPriceIndex = null;
        $this->currentSpecificPrice = [];
        $this->applyToAllCustomers = true;
        $this->unlimitedDuration = false;
        $this->applyDiscount = false;
        $this->setSpecificPrice = false;
    }

    public function updatedApplyToAllCustomers(): void
    {
        if ($this->applyToAllCustomers) {
            $this->currentSpecificPrice['id_customer'] = null;
        }
    }

    public function updatedUnlimitedDuration(): void
    {
        if ($this->unlimitedDuration) {
            $this->currentSpecificPrice['from'] = null;
            $this->currentSpecificPrice['to'] = null;
        }
    }

    public function updatedApplyDiscount(): void
    {
        if (!$this->applyDiscount) {
            $this->currentSpecificPrice['reduction'] = 0;
        }
    }

    public function updatedSetSpecificPrice(): void
    {
        if (!$this->setSpecificPrice) {
            $this->currentSpecificPrice['price'] = 0;
        }
    }

    public function saveSpecificPrice(): void
    {
        if (!$this->applyDiscount && !$this->setSpecificPrice) {
            session()->flash('error', __('ecommerce::products.form.apply_discount_or_specific_price'));
            return;
        }

        if ($this->applyToAllCustomers) {
            $this->currentSpecificPrice['id_customer'] = null;
        }

        if ($this->unlimitedDuration) {
            $this->currentSpecificPrice['from'] = null;
            $this->currentSpecificPrice['to'] = null;
        }

        $rules = [
            'currentSpecificPrice.from_quantity' => ['required', 'integer', 'min:1'],
        ];

        if ($this->applyDiscount) {
            $rules['currentSpecificPrice.reduction'] = ['required', 'numeric', 'min:0'];
            $rules['currentSpecificPrice.reduction_type'] = ['required', 'in:amount,percentage'];
        }

        if ($this->setSpecificPrice) {
            $rules['currentSpecificPrice.price'] = ['required', 'numeric', 'min:0'];
        }

        if (!$this->applyToAllCustomers) {
            $rules['currentSpecificPrice.id_customer'] = ['required', 'exists:clients,id'];
        }

        if (!$this->unlimitedDuration) {
            $rules['currentSpecificPrice.from'] = ['nullable', 'date'];
            $rules['currentSpecificPrice.to'] = ['nullable', 'date', 'after_or_equal:currentSpecificPrice.from'];
        }

        $this->validate($rules);

        if ($this->editingSpecificPriceIndex !== null) {
            $this->specificPrices[$this->editingSpecificPriceIndex] = $this->currentSpecificPrice;
        } else {
            $this->specificPrices[] = $this->currentSpecificPrice;
        }

        $this->closeSpecificPriceModal();
        session()->flash('success', __('ecommerce::products.form.specific_price_saved'));
    }

    public function removeSpecificPrice($index): void
    {
        if (!isset($this->specificPrices[$index])) {
            return;
        }

        $specificPrice = $this->specificPrices[$index];

        // Delete from database if it exists
        if (!empty($specificPrice['id'])) {
            SpecificPrice::find($specificPrice['id'])?->delete();
        }

        unset($this->specificPrices[$index]);
        $this->specificPrices = array_values($this->specificPrices);
        session()->flash('success', __('ecommerce::products.form.specific_price_deleted'));
    }

    #[Computed]
    public function getCurrencies()
    {
        return \Unusualdope\LaravelEcommerce\Models\Administration\Currency::all();
    }

    #[Computed]
    public function getClientGroups()
    {
        return \Unusualdope\LaravelEcommerce\Models\Customer\ClientGroup::all();
    }

    #[Computed]
    public function getClients()
    {
        return \Unusualdope\LaravelEcommerce\Models\Customer\Client::with('user')->get();
    }

    public function updatedPhotos()
    {
        $this->dispatch('photos-updated');
    }

    public function removePhoto($index)
    {
        if (isset($this->photos[$index])) {
            unset($this->photos[$index]);
            $this->photos = array_values($this->photos);
        }
    }

    public function save(): void
    {
        $languages = $this->languageModel::getLanguagesForMultilangForm();
        $fields = [
            'brand_id' => $this->brand_id,
            'type' => $this->product_type,
            'status' => $this->status,
            'sku' => ($this->sku != '') ? $this->sku : null,
            'ean' => ($this->ean != '') ? $this->ean : null,
            'mpn' => ($this->mpn != '') ? $this->mpn : null,
            'upc' => ($this->upc != '') ? $this->upc : null,
            'isbn' => ($this->isbn != '') ? $this->isbn : null,
            'low_stock_alert' => $this->low_stock_alert,
            'minimal_quantity' => $this->minimal_quantity,
            'quantity' => $this->quantity,
            'price' => $this->price,
        ];
        if (! $this->product->exists) {
            $this->product = Product::create($fields);
        } else {
            $this->product->update($fields);
        }

        $this->product->categories()->sync($this->category_ids);
        $this->product->seasons()->sync($this->season_ids);
        $this->saveProductFeatures();
        foreach ($languages as $language) {
            ProductLanguage::updateOrCreate(
                [
                    'product_id' => $this->product->id,
                    'language_id' => $language['id'],
                ],
                [
                    'name' => $this->name[$language['id']] ?? '',
                    'description_long' => $this->description_long[$language['id']] ?? '',
                    'description_short' => $this->description_short[$language['id']] ?? '',
                    'link_rewrite' => $this->link_rewrite[$language['id']] ?? '',
                    'meta_title' => $this->meta_title[$language['id']] ?? '',
                    'meta_description' => $this->meta_description[$language['id']] ?? '',
                ]
            );
        }

        // Save variations for variable products
        if ($this->product_type == 'variable') {
            foreach ($this->variations as $variationData) {
                $variation = Variation::updateOrCreate(
                    [
                        'id' => $variationData['variation_id'] ?? null,
                    ],
                    [
                        'product_id' => $this->product->id,
                        'sku' => $variationData['sku'] ?? null,
                        'ean' => $variationData['ean'] ?? null,
                        'mpn' => $variationData['mpn'] ?? null,
                        'upc' => $variationData['upc'] ?? null,
                        'isbn' => $variationData['isbn'] ?? null,
                        'quantity' => $variationData['quantity'] ?? 0,
                        'price' => $variationData['price'] ?? 0,
                        'low_stock_alert' => $variationData['low_stock_alert'] ?? 0,
                        'minimal_quantity' => $variationData['minimal_quantity'] ?? 0,
                        'available_from' => $variationData['available_from'] ?? null,
                        'available_to' => $variationData['available_to'] ?? null,
                    ]
                );

                // Handle variation image
                if (! empty($variationData['image'])) {
                    // Upload new image
                    $path = $variationData['image']->store('images/products/variations', 'public');

                    // Delete old image if exists
                    if (! empty($variationData['image_id'])) {
                        $oldImage = ProductImage::find($variationData['image_id']);
                        if ($oldImage) {
                            Storage::disk('public')->delete($oldImage->image);
                            $oldImage->delete();
                        }
                    }

                    // Create new image record in product_images table with variation_id
                    ProductImage::create([
                        'product_id' => $this->product->id,
                        'variation_id' => $variation->id,
                        'image' => $path,
                        'position' => 0,
                    ]);
                }

                if (! empty($variationData['variants_by_group'])) {
                    // Extract variant IDs from the group structure
                    $variantIds = array_values($variationData['variants_by_group']);
                    $variation->variants()->sync($variantIds);
                }
            }
        }
        foreach ($this->selections as $selection) {
            if (! empty($selection['photo'])) {
                // upload image
                $path = $selection['photo']->store('images/products', 'public');

                // create image
                $productImage = ProductImage::create([
                    'product_id' => $this->product->id,
                    'variation_id' => null,
                    'image' => $path,
                ]);
                foreach ($selection['caption'] as $key) {
                    if (! is_null($key)) {
                        foreach ($key as $index => $value) {
                            ProductImageLanguage::updateOrCreate(
                                [
                                    'product_image_id' => $productImage->id,
                                    'language_id' => $index,
                                ],
                                [
                                    'caption' => $value,
                                ]
                            );
                        }
                    }
                }
            } else {
                // update image
                $productImage = ProductImage::find($selection['image_id']);
                if ($productImage) {
                    foreach ($selection['caption'] as $key) {
                        if (! is_null($key)) {
                            foreach ($key as $index => $value) {
                                ProductImageLanguage::updateOrCreate(
                                    [
                                        'product_image_id' => $productImage->id,
                                        'language_id' => $index,
                                    ],
                                    [
                                        'caption' => $value,
                                    ]
                                );
                            }
                        }
                    }
                }
            }
        }

        $this->saveSpecificPrices();

        if ($this->isEditing) {
            session()->flash('status', __('ecommerce::products.product_updated'));
        } else {
            session()->flash('status', __('ecommerce::products.product_created'));
        }
        $this->redirect(route(config('ud-ecommerce.admin_route_prefix', 'admin').'.products.index'), navigate: true);
    }

    protected function saveSpecificPrices(): void
    {
        if (!$this->product || !$this->product->exists) {
            return;
        }

        // Get all existing specific price IDs
        $existingIds = collect($this->specificPrices)->pluck('id')->filter()->toArray();

        // Delete specific prices that are no longer in the array
        SpecificPrice::where('id_product', $this->product->id)
            ->whereNotIn('id', $existingIds)
            ->delete();

        // Update or create specific prices
        foreach ($this->specificPrices as $specificPriceData) {
            SpecificPrice::updateOrCreate(
                [
                    'id' => $specificPriceData['id'] ?? null,
                ],
                [
                    'id_product' => $this->product->id,
                    'id_currency' => $specificPriceData['id_currency'] ?? 0,
                    'id_client_type' => $specificPriceData['id_client_type'] ?? 0,
                    'id_customer' => $specificPriceData['id_customer'] ?? 0,
                    'price' => $specificPriceData['price'] ?? 0,
                    'from_quantity' => $specificPriceData['from_quantity'] ?? 1,
                    'reduction' => $specificPriceData['reduction'] ?? 0,
                    'reduction_tax' => $specificPriceData['reduction_tax'] ?? false,
                    'reduction_type' => $specificPriceData['reduction_type'] ?? 'amount',
                    'from' => !empty($specificPriceData['from']) ? $specificPriceData['from'] : null,
                    'to' => !empty($specificPriceData['to']) ? $specificPriceData['to'] : null,
                ]
            );
        }
    }

    public function render()
    {
        return view('ecommerce::livewire.admin.product.product.product-create-edit');
    }
}
