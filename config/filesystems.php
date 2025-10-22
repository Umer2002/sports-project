<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application for file storage.
    |
    */

    'default' => env('FILESYSTEM_DISK', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Below you may configure as many filesystem disks as necessary, and you
    | may even configure multiple disks for the same driver. Examples for
    | most supported storage drivers are configured here for reference.
    |
    | Supported drivers: "local", "ftp", "sftp", "s3"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app/private'),
            'serve' => true,
            'throw' => false,
            'report' => false,
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
            'throw' => false,
            'report' => false,
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'throw' => false,
            'report' => false,
        ],

        // Google Cloud Storage via S3-compatible HMAC keys
        // Set VIDEO_DISK=gcs and configure env: GCS_BUCKET, GCS_KEY, GCS_SECRET
        'gcs' => [
            'driver' => 's3',
            'key' => env('GCS_KEY', env('AWS_ACCESS_KEY_ID')),
            'secret' => env('GCS_SECRET', env('AWS_SECRET_ACCESS_KEY')),
            'region' => env('GCS_REGION', 'auto'),
            'bucket' => env('GCS_BUCKET', env('AWS_BUCKET')),
            'url' => env('GCS_URL', env('AWS_URL')),
            'endpoint' => env('GCS_ENDPOINT', env('AWS_ENDPOINT', 'https://storage.googleapis.com')),
            'use_path_style_endpoint' => env('GCS_PATH_STYLE', false),
            'throw' => false,
            'report' => false,
        ],

        // Cloudflare R2 (S3-compatible)
        // Set VIDEO_DISK=r2 and configure: R2_ACCOUNT_ID, R2_ACCESS_KEY_ID, R2_SECRET_ACCESS_KEY, R2_BUCKET, R2_PUBLIC_URL
        'r2' => [
            'driver' => 's3',
            'key' => env('R2_ACCESS_KEY_ID'),
            'secret' => env('R2_SECRET_ACCESS_KEY'),
            'region' => env('R2_REGION', 'auto'),
            'bucket' => env('R2_BUCKET'),
            // Public base URL (e.g., https://cdn.example.com or https://pub-xxxxxxxxxxxxxxxx.r2.dev/bucket)
            'url' => env('R2_PUBLIC_URL'),
            // S3 endpoint for R2: https://<account_id>.r2.cloudflarestorage.com
            'endpoint' => env('R2_ENDPOINT', env('R2_ACCOUNT_ID') ? ('https://' . env('R2_ACCOUNT_ID') . '.r2.cloudflarestorage.com') : null),
            // R2 typically needs path-style addressing
            'use_path_style_endpoint' => env('R2_PATH_STYLE', true),
            'throw' => false,
            'report' => false,
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
