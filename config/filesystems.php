<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application. Just store away!
    |
    */

    'default' => env('FILESYSTEM_DISK', 's3'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many filesystem "disks" as you wish, and you
    | may even configure multiple disks of the same driver. Defaults have
    | been set up for each driver as an example of the required values.
    |
    | Supported Drivers: "local", "ftp", "sftp", "s3"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
            'throw' => false,
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
            'throw' => false,
        ],


        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_FILE_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'throw' => false,
            'visibility' => 'public',
        ],

        
        's3Photos' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_PHOTO_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'throw' => false,
            'visibility' => 'public',
        ],

        'cloud' => [
            'driver' => 's3',
            'key' => env('LARAVEL_CLOUD_DISK_CONFIG_ACCESS_KEY_ID', '4a8cb0fe9a1321cd9d5c14ca6131d891'),
            'secret' => env('LARAVEL_CLOUD_DISK_CONFIG_ACCESS_KEY_SECRET', 'c7affc828051db3997daffcdf024bc9550cef1b90eeda93bfa3a3d73c11cdde7'),
            'region' => env('LARAVEL_CLOUD_DISK_CONFIG_DEFAULT_REGION', 'auto'),
            'bucket' => env('LARAVEL_CLOUD_DISK_CONFIG_BUCKET', 'fls-9e68b869-a100-420c-bb3d-e0845955481d'),
            'url' => env('LARAVEL_CLOUD_DISK_CONFIG_URL', 'https://fls-9e68b869-a100-420c-bb3d-e0845955481d.laravel.cloud'),
            'endpoint' => env('LARAVEL_CLOUD_DISK_CONFIG_ENDPOINT', 'https://367be3a2035528943240074d0096e0cd.r2.cloudflarestorage.com'),
            'use_path_style_endpoint' => env('LARAVEL_CLOUD_DISK_CONFIG_USE_PATH_STYLE_ENDPOINT', false),
            'throw' => false,
            'visibility' => 'public',
        ],


        

    ],

    /*
    |--------------------------------------------------------------------------
    | Symbolic Links
    |--------------------------------------------------------------------------
    |
    | Here you may configure the symbolic links that will be created when the
    | `storage:link` Artisan command is executed. The array keys should be
    | the locations of the links and the values should be their targets.
    |
    */

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];
