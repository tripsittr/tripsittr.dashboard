<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Temporary File Uploads
    |--------------------------------------------------------------------------
    | Increase beyond default 12MB. The values are in kilobytes.
    | Setting both rules and the Livewire built-in limits ensures consistency.
    */
    'temporary_file_upload' => [
        'disk' => env('LIVEWIRE_TEMP_UPLOAD_DISK', null),
        'directory' => env('LIVEWIRE_TEMP_UPLOAD_DIRECTORY', 'livewire-tmp'),
        'middleware' => null,
        'rules' => ['required', 'file', 'max:262144'], // 256 MB
        'preview_mimes' => [
            'png', 'gif', 'bmp', 'svg', 'wav', 'mp4', 'mov', 'avi', 'mp3', 'webm', 'jpg', 'jpeg', 'm4a', 'flac',
        ],
        'max_upload_time' => 300, // seconds
    ],

    /*
    |--------------------------------------------------------------------------
    | Legacy Model Binding
    |--------------------------------------------------------------------------
    */
    'legacy_model_binding' => false,

    /*
    |--------------------------------------------------------------------------
    | Auto-Discovery
    |--------------------------------------------------------------------------
    */
    'discover_views' => true,
    'discover_components' => true,
    'autoload_components' => true,

    /*
    |--------------------------------------------------------------------------
    | Asset Loading
    |--------------------------------------------------------------------------
    */
    'asset_url' => null,

    /*
    |--------------------------------------------------------------------------
    | Navigating Using Turbo / PJAX
    |--------------------------------------------------------------------------
    */
    'navigate' => [
        'show_progress_bar' => true,
    ],
];
