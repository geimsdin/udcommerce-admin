<?php

/**
 * Typesense configuration for Laravel Scout.
 * Merged into config('scout.typesense') by UdLaravelEcommerceServiceProvider.
 *
 * @see https://typesense.org/docs/guide/reference-implementations/laravel-scout-integration.html
 */

return [
    'client-settings' => [
        'api_key' => env('TYPESENSE_API_KEY', 'xyz'),
        'nodes' => [
            [
                'host' => env('TYPESENSE_HOST', 'localhost'),
                'port' => env('TYPESENSE_PORT', '8108'),
                'path' => env('TYPESENSE_PATH', ''),
                'protocol' => env('TYPESENSE_PROTOCOL', 'http'),
            ],
        ],
        'nearest_node' => [
            'host' => env('TYPESENSE_HOST', 'localhost'),
            'port' => env('TYPESENSE_PORT', '8108'),
            'path' => env('TYPESENSE_PATH', ''),
            'protocol' => env('TYPESENSE_PROTOCOL', 'http'),
        ],
        'connection_timeout_seconds' => env('TYPESENSE_CONNECTION_TIMEOUT_SECONDS', 2),
        'healthcheck_interval_seconds' => env('TYPESENSE_HEALTHCHECK_INTERVAL_SECONDS', 30),
        'num_retries' => env('TYPESENSE_NUM_RETRIES', 3),
        'retry_interval_seconds' => env('TYPESENSE_RETRY_INTERVAL_SECONDS', 1),
    ],
    'model-settings' => [
        \Unusualdope\LaravelEcommerce\Models\Product\Product::class => [
            'collection-schema' => [
                'fields' => [
                    ['name' => 'id', 'type' => 'string'],
                    ['name' => 'name', 'type' => 'string'],
                    ['name' => 'language_name', 'type' => 'string'],
                    ['name' => 'sku', 'type' => 'string'],
                    ['name' => 'link_rewrite', 'type' => 'string'],
                    ['name' => 'status', 'type' => 'int64'],
                    ['name' => 'brand_id', 'type' => 'int64'],
                    ['name' => 'category_ids', 'type' => 'int64[]'],
                    ['name' => 'created_at', 'type' => 'int64'],
                    ['name' => 'price', 'type' => 'float'],
                    ['name' => '.*', 'type' => 'auto'],
                ],
            ],
            'search-parameters' => [
                'query_by' => 'name,sku,link_rewrite',
            ],
        ],
        \Unusualdope\LaravelEcommerce\Models\Product\ProductCategory::class => [
            'collection-schema' => [
                'fields' => [
                    ['name' => 'id', 'type' => 'string'],
                    ['name' => 'title', 'type' => 'string'],
                    ['name' => 'link_rewrite', 'type' => 'string'],
                    ['name' => 'status', 'type' => 'int32'],
                    ['name' => 'products_count', 'type' => 'int32'],
                    ['name' => '.*', 'type' => 'auto'],
                ],
            ],
            'search-parameters' => [
                'query_by' => 'name,link_rewrite',
            ],
        ],
        \Unusualdope\LaravelEcommerce\Models\Product\Brand::class => [
            'collection-schema' => [
                'fields' => [
                    ['name' => 'id', 'type' => 'string'],
                    ['name' => 'name', 'type' => 'string'],
                    ['name' => 'slug', 'type' => 'string'],
                    ['name' => 'status', 'type' => 'int32'],
                    ['name' => 'products_count', 'type' => 'int32'],
                    ['name' => '.*', 'type' => 'auto'],
                ],
            ],
            'search-parameters' => [
                'query_by' => 'name,slug',
            ],
        ],
    ],
    'import_action' => env('TYPESENSE_IMPORT_ACTION', 'upsert'),
];
