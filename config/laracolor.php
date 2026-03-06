<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Color Palette
    |--------------------------------------------------------------------------
    |
    | Available palettes: pastel, vibrant, dark, light, muted, warm, cold, random
    |
    */
    'default_palette' => env('LARACOLOR_PALETTE', 'pastel'),

    /*
    |--------------------------------------------------------------------------
    | Custom Ranges
    |--------------------------------------------------------------------------
    |
    | You can override default palette ranges with custom values
    |
    */
    'custom_ranges' => [
        'saturation' => [
            'min' => env('LARACOLOR_SATURATION_MIN', 35),
            'max' => env('LARACOLOR_SATURATION_MAX', 56),
        ],
        'lightness' => [
            'min' => env('LARACOLOR_LIGHTNESS_MIN', 78),
            'max' => env('LARACOLOR_LIGHTNESS_MAX', 89),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Settings
    |--------------------------------------------------------------------------
    |
    | Enable caching for generated colors to improve performance
    |
    */
    'cache' => [
        'enabled' => env('LARACOLOR_CACHE_ENABLED', false),
        'ttl' => env('LARACOLOR_CACHE_TTL', 3600),
    ],
];