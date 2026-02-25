<?php

namespace Unusualdope\LaravelEcommerce\Models\Product;

use App\Models\Language;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\GdDriver;
use Intervention\Image\ImageManager;
use Unusualdope\LaravelEcommerce\Models\Stock\Variant;
use Unusualdope\LaravelModelTranslatable\Traits\HasTranslation;

class ProductImage extends Model
{
    use Cachable, HasTranslation;

    public bool $not_filament = false;

    public function setTranslatableFields(): array
    {
        $this->translatable_fields = [
            'caption' => 'string',
        ];

        return $this->translatable_fields;
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(Variant::class);
    }

    public function productImageLanguages(): HasMany
    {
        return $this->hasMany(ProductImageLanguage::class);
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class, 'language_id');
    }

    public function save(array $options = [])
    {
        // Track back who is calling the save and if not Filament, then just save
        $trace = debug_backtrace();

        if (
            (isset($trace[1]['class']) && strpos($trace[1]['class'], 'Filament') !== false) ||
            (isset($trace[2]['class']) && strpos($trace[2]['class'], 'Filament') !== false) ||
            (isset($trace[3]['class']) && strpos($trace[3]['class'], 'Filament') !== false)

        ) {
            $this->alterLangDataBeforeDb();

            $result = parent::save($options);

            $result_lang = $this->saveLanguageData();

            return $result && $result_lang;
        }

        return parent::save($options);

    }

    public function update(array $attributes = [], array $options = [])
    {
        if ($this->not_filament) {
            return parent::update($attributes, $options);
        }
        $this->alterLangDataBeforeDb();

        $result = parent::update($attributes, $options);

        $result_lang = $this->saveLanguageData();

        return $result && $result_lang;
    }

    public function alterLangDataBeforeDb(): void
    {

        foreach ($this->attributes as $key => $value) {
            if (strpos($key, '_fmtLang_')) {
                $this->translated_data[$key] = $value;
                unset($this->attributes[$key]);
            }
        }
    }

    public function saveLanguageData(): bool
    {
        $languages = Language::getLanguages();
        $result = true;

        foreach ($languages as $lang_id => $lang_iso) {
            $update = true;
            if (
                !$pil = ProductImageLanguage::where('language_id', $lang_id)
                    ->where('product_image_id', $this->id)->first()
            ) {
                $update = false;
                $pil = new ProductImageLanguage;
            }

            $c = 0;
            foreach ($this->translatable_fields as $field_name => $default) {
                $pil->$field_name = $this->translated_data[$field_name . '_fmtLang_' . $lang_id] ?? $default;
                $c++;
            }

            if ($c > 0) {
                $pil->language_id = $lang_id;
                $pil->product_image_id = $this->id;
                if ($update) {
                    $result = $result && $pil->update();
                } else {
                    $result = $result && $pil->save();
                }
                unset($pil);
            }
        }

        return $result;
    }

    public static function getProductImagesByProductIds(int|array $product_ids, $language_id = null)
    {
        // Get the current language if none is provided
        if (empty($language_id)) {
            $language_id = Language::getDefaultLanguage();
        }

        if (is_int($product_ids)) {
            $product_ids = [$product_ids];
        }

        // Fetch images and captions, sorted by position
        return DB::table('product_images')
            ->leftJoin('product_image_languages', function (JoinClause $join) use ($language_id) {
                $join->on('product_images.id', '=', 'product_image_languages.product_image_id')
                    ->where('product_image_languages.language_id', '=', $language_id); // Fetch image captions
            })
            ->whereIn('product_images.product_id', $product_ids) // Get images for the fetched products
            ->orderBy('product_images.position', 'asc') // Sort images by position
            ->select([
                'product_images.product_id',
                'product_images.id as image_id',
                'product_images.image as image_url',
                'product_images.position',
                'product_image_languages.caption as image_caption',
            ])
            ->get();

    }

    public static function importImagesFromFolder()
    {
        $startTime = microtime(true);
        $folderPath = storage_path(config('b2b.product_images.import_dir')); // Full path to the folder
        $days_old = 7; // Number of days before files should be deleted

        // Check if the folder exists
        if (is_dir($folderPath)) {
            $files = File::files($folderPath);
            $now = now()->timestamp;

            //            foreach ($files as $file) {
            //                if ($file->getMTime() < ($now - ($days_old * 86400))) {
            //                    File::delete($file);
            //                }
            //            }

            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'];
            $imageNames = collect(scandir($folderPath))
                ->filter(function ($file) use ($folderPath, $now, $allowedExtensions) {
                    // Ignore '.' and '..' directories
                    if ($file === '.' || $file === '..') {
                        return false;
                    }
                    $filePath = $folderPath . DIRECTORY_SEPARATOR . $file;
                    // Ensure it's a file, not a directory
                    if (!is_file($filePath)) {
                        return false;
                    }
                    // Get the file extension and convert to lowercase for case-insensitive comparison
                    $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                    // Check if the extension is in our allowed list
                    $isImage = in_array($extension, $allowedExtensions);
                    // Check if the file is older than 3 minutes (as per your original logic)
                    $isOlderThan3Minutes = filemtime($filePath) < $now - (3 * 60);

                    return $isImage && $isOlderThan3Minutes;
                })
                ->values()
                ->toArray();

            $arr_img = [];
            // Loop through image filenames and process them
            foreach ($imageNames as $imageName) {
                $imagePath = $folderPath . DIRECTORY_SEPARATOR . $imageName;

                // Perform the operation with $imagePath (e.g., storing it or processing it)
                $temp_arr_img = explode('_', $imageName);

                if (count($temp_arr_img) < 2) {
                    $temp_sku = explode('.', $temp_arr_img[0]);
                    $arr_img[$temp_sku[0]][] = $imagePath;
                } else {
                    $arr_img[$temp_arr_img[0]][] = $imagePath;
                }

            }

            $processedProductCount = 0;
            $maxProductsToProcess = config('b2b.product_images.max_products_to_process');
            $processedImagesCount = 0;

            foreach ($arr_img as $sku => $images) {
                // Check if the product processing limit has been reached
                if ($processedProductCount >= $maxProductsToProcess) {
                    if (!app()->isProduction()) {
                        Log::info("Reached {$maxProductsToProcess} products limit. Stopping image import.");
                    }
                    break; // Exit the loop for processing products
                }

                $product = Product::where('sku', $sku)->first();
                if (!$product) {
                    foreach ($images as $image_path_unlink) {
                        unlink($image_path_unlink);
                    }

                    continue;
                }

                // Delete existing images for the product
                foreach ($product->images as $productImage) {
                    Storage::delete($productImage->image);
                    ProductImageLanguage::where('product_image_id', $productImage->id)->delete();
                    $productImage->delete();
                }

                $product_name = $product->currentLanguage->name;

                // Process new images for the product
                foreach ($images as $position => $image_path) {
                    // No try-catch as per your instruction, assuming files exist
                    // However, a basic file_exists check is still good practice for robustness
                    if (!file_exists($image_path)) {
                        Log::warning("Original image file not found, skipping processing: {$image_path}");

                        continue;
                    }

                    $image_name = basename($image_path); // Keep the original file name

                    $manager = new ImageManager(
                        new GdDriver
                    );

                    try {
                        $img = $manager->read($image_path);
                    } catch (\Exception $e) {
                        Log::warning("Error processing image: {$image_path}");
                        unlink($image_path);

                        continue;
                    }

                    // Resize if width is greater than 900px, maintaining aspect ratio
                    if ($img->width() > config('b2b.product_images.max_width')) {
                        $img->scale(width: config('b2b.product_images.max_width'));
                    }

                    // Save the (potentially resized) image to a temporary file
                    $tempPath = tempnam(sys_get_temp_dir(), 'resized_img_');
                    $img->toWebp(config('b2b.product_images.quality'));
                    $img->save($tempPath);

                    // Delete the original source image file immediately after processing
                    unlink($image_path);
                    $storagePath = config('b2b.product_images.save_dir') . '/' . $product->id;
                    $targetDirectory = storage_path('app/' . $storagePath);

                    if (!File::isDirectory($targetDirectory)) {
                        // Ensure the directory exists with 0755 permissions
                        // The 'true' argument ensures recursive creation
                        File::makeDirectory($targetDirectory, 0755, true, true);
                    }

                    // Store the processed image from the temporary path to public storage
                    Storage::putFileAs($storagePath, new \Illuminate\Http\File($tempPath), $image_name);

                    $relativePath = config('b2b.product_images.save_relative_dir') . '/' .
                        $product->id . '/' . $image_name;

                    // Create database records for the new image
                    $productImage = ProductImage::updateOrCreate([
                        'product_id' => $product->id,
                        'variant_id' => 0,
                        'image' => $relativePath,
                        'position' => $position + 1,
                    ]);
                    ProductImageLanguage::Create([
                        'product_image_id' => $productImage->id,
                        'language_id' => 1,
                        'caption' => $product_name . ' - Image ' . $position + 1,
                    ]);

                    // Clean up the temporary file after it has been moved to storage
                    if (file_exists($tempPath)) {
                        unlink($tempPath);
                    }
                    $processedImagesCount++;
                }
                // Increment the processed product count after all images for the current product are handled
                $processedProductCount++;
            }
            $success = true;
        } else {
            $success = false;
        }

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;
        $executionTimeString = 'Image import execution time: ' . $executionTime . " seconds\n";

        return [
            'success' => $success,
            'products' => $processedProductCount,
            'images' => $processedImagesCount,
            'execution_time' => $executionTimeString,
        ];
    }
}
