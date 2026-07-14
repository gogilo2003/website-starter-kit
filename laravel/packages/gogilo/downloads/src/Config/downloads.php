<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Storage Disk
    |--------------------------------------------------------------------------
    |
    | The default disk to use for storing files. This should be one of the
    | disks configured in your filesystems.php configuration file.
    |
    */
    'default_disk' => env('DOWNLOADS_DEFAULT_DISK', 'public'),

    /*
    |--------------------------------------------------------------------------
    | Preview Disk
    |--------------------------------------------------------------------------
    |
    | The disk to use for file previews.
    |
    */
    'preview_disk' => env('DOWNLOADS_PREVIEW_DISK', 'public'),

    /*
    |--------------------------------------------------------------------------
    | Signed URL Expiry
    |--------------------------------------------------------------------------
    |
    | Number of minutes signed URLs should remain valid.
    |
    */
    'signed_url_expiry_minutes' => env('DOWNLOADS_SIGNED_URL_EXPIRY_MINUTES', 15),

    /*
    |--------------------------------------------------------------------------
    | Verify Permissions
    |--------------------------------------------------------------------------
    |
    | Whether to verify custom permissions before allowing downloads.
    |
    */
    'verify_permissions' => env('DOWNLOADS_VERIFY_PERMISSIONS', true),

    /*
    |--------------------------------------------------------------------------
    | Allowed File Extensions
    |--------------------------------------------------------------------------
    |
    | Array of allowed file extensions. Empty array means all extensions allowed.
    |
    */
    'allowed_extensions' => env('DOWNLOADS_ALLOWED_EXTENSIONS', ['pdf', 'doc', 'docx', 'jpg', 'png', 'zip', 'rar']),

    /*
    |--------------------------------------------------------------------------
    | Maximum File Size
    |--------------------------------------------------------------------------
    |
    | Maximum file size in bytes.
    |
    */
    'max_file_size' => env('DOWNLOADS_MAX_FILE_SIZE', 10485760), // 10MB

    /*
    |--------------------------------------------------------------------------
    | Model Class
    |--------------------------------------------------------------------------
    |
    | The fully qualified class name of the Download model.
    |
    */
    'model_class' => Gogilo\Downloads\Models\Download::class,

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    |
    | Rate limiting configuration for download attempts.
    |
    */
    'rate_limit' => [
        'max_attempts' => env('DOWNLOADS_RATE_LIMIT_MAX_ATTEMPTS', 60),
        'decay_minutes' => env('DOWNLOADS_RATE_LIMIT_DECAY_MINUTES', 1),
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Settings
    |--------------------------------------------------------------------------
    |
    | Security related configurations.
    |
    */
    'security' => [
        'require_signature' => env('DOWNLOADS_REQUIRE_SIGNATURE', true),
        'signature_expiry' => env('DOWNLOADS_SIGNATURE_EXPIRY', 900), // 15 minutes
        'ip_logging' => env('DOWNLOADS_IP_LOGGING', true),
    ],
];