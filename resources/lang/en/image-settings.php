<?php

return [
    'title' => 'Image Settings',
    'subtitle' => 'Define thumbnail dimensions for products, categories, and brands. Each image type generates a resized version when images are uploaded.',

    'add_image_type' => 'Add Image Type',
    'edit_image_type' => 'Edit Image Type',
    'create_image_type' => 'Create Image Type',
    'update_image_type' => 'Update Image Type',

    'no_image_types' => 'No image types defined',
    'no_image_types_text' => 'Add image types to define thumbnail dimensions, or load the defaults.',

    'seed_defaults' => 'Load Defaults',
    'seed_defaults_confirm' => 'This will add default image types (cart, small, medium, large, home, category). Existing types will not be overwritten. Continue?',
    'defaults_seeded' => 'Default image types loaded successfully.',

    'image_type_created' => 'Image type created successfully.',
    'image_type_updated' => 'Image type updated successfully.',
    'image_type_deleted' => 'Image type deleted successfully.',

    'delete_confirmation_text' => 'Are you sure you want to delete this image type? Existing thumbnails of this type will no longer be accessible.',

    'regenerate_info' => 'After adding or changing image settings, click "Regenerate Thumbnails" to update existing images with the new sizes.',

    'regenerate_thumbnails' => 'Regenerate Thumbnails',
    'regenerate_confirm' => 'This will regenerate all thumbnails for all existing images. This may take a while if you have many images. Continue?',
    'thumbnails_regenerated' => 'All thumbnails have been regenerated successfully.',

    'config_saved' => 'Image settings saved successfully.',
    'config_reset' => 'Image settings have been reset to defaults.',

    'table' => [
        'name' => 'Name',
        'dimensions' => 'Dimensions',
        'products' => 'Products',
        'categories' => 'Categories',
        'brands' => 'Brands',
    ],

    'form' => [
        'name' => 'Name',
        'name_placeholder' => 'e.g. cart_default, product_list, email_share',
        'name_description' => 'Use lowercase letters, numbers and underscores only. This is used as the conversion identifier.',
        'name_regex_error' => 'Name must contain only lowercase letters, numbers and underscores.',
        'dimensions' => 'Dimensions',
        'width' => 'Width',
        'height' => 'Height',
        'apply_to' => 'Apply To',
        'apply_to_description' => 'Select which entities should generate thumbnails at this size.',
        'products' => 'Products',
        'products_description' => 'Generate for product images',
        'categories' => 'Categories',
        'categories_description' => 'Generate for category images',
        'brands' => 'Brands',
        'brands_description' => 'Generate for brand images',
    ],

    'messages' => [
        'create_subtitle' => 'Define a new thumbnail size for your images',
        'edit_subtitle' => 'Update the thumbnail size configuration',
    ],
];
