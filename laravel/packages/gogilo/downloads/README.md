# Laravel Downloads Package

A comprehensive Laravel package for managing file downloads with secure signed URLs, S3 support, and frontend integration.

## Features

- **Secure Downloads**: Signed URLs with expiration and signature verification
- **Storage Abstraction**: Support for local and S3 storage
- **Frontend Integration**: Blade components and Vue/React compatible API
- **Rate Limiting**: Built-in download throttling
- **Permission Control**: Custom permission middleware
- **File Metadata**: Track downloads, file sizes, MIME types
- **File Preview**: Generate preview URLs for files
- **Responsive UI**: Pre-built Blade components

## Installation

```bash
composer require gogilo/downloads
```

## Configuration

Publish the configuration file:

```bash
php artisan vendor:publish --provider="Gogilo\Downloads\DownloadsServiceProvider" --tag="downloads-config"
```

Configure your settings in `config/downloads.php`:

```php
return [
    'default_disk' => env('DOWNLOADS_DEFAULT_DISK', 'public'),
    'preview_disk' => env('DOWNLOADS_PREVIEW_DISK', 'public'),
    'signed_url_expiry_minutes' => env('DOWNLOADS_SIGNED_URL_EXPIRY_MINUTES', 15),
    'verify_permissions' => env('DOWNLOADS_VERIFY_PERMISSIONS', true),
    'allowed_extensions' => env('DOWNLOADS_ALLOWED_EXTENSIONS', ['pdf', 'doc', 'docx']),
    'max_file_size' => env('DOWNLOADS_MAX_FILE_SIZE', 10485760),
    'model_class' => \App\Models\Download::class,
    'rate_limit' => [
        'max_attempts' => env('DOWNLOADS_RATE_LIMIT_MAX_ATTEMPTS', 60),
        'decay_minutes' => env('DOWNLOADS_RATE_LIMIT_DECAY_MINUTES', 1),
    ],
    'security' => [
        'require_signature' => env('DOWNLOADS_REQUIRE_SIGNATURE', true),
        'signature_expiry' => env('DOWNLOADS_SIGNATURE_EXPIRY', 900),
        'ip_logging' => env('DOWNLOADS_IP_LOGGING', true),
    ],
];
```

## Usage

### Basic Download

```php
use Gogilo\Downloads\Facades\Downloads;

// Store a file
$download = Downloads::store($request->file('document'));

// Generate signed URL
$url = Downloads::signedUrl($download->id);

// Download file
return Downloads::download($download->id, $request);
```

### Using DownloadManager

```php
use Gogilo\Downloads\DownloadManager;

$downloadManager = app('downloads');

// Store file
$download = $downloadManager->store($file);

// Generate signed URL
$url = $downloadManager->signedUrl($fileId);
```

### API Endpoints

The package provides the following API endpoints:

- `GET /downloads-api/{file}` - Download file via API
- `GET /downloads-api/{file}/metadata` - Get file metadata
- `GET /downloads-api/{file}/preview` - Get preview URL
- `GET /downloads/secure/{file}` - Secure download with signature verification
- `GET /downloads/preview/{file}` - Preview file in browser

### Blade Components

```blade
{{-- Download button --}}
@download-button($fileId)

{{-- With custom text --}}
@download-button($fileId, 'Get File', class='btn-primary')

{{-- File metadata card --}}
@download-card($file)
```

## Database Migrations

The package includes migrations for the downloads table:

```bash
php artisan migrate
```

## Testing

Run the test suite:

```bash
composer test
```

## Security

- All downloads require signed URLs by default
- IP logging enabled by default
- Rate limiting to prevent abuse
- File extension validation
- Maximum file size limits

## License

MIT