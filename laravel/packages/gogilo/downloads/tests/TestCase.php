<?php

namespace Gogilo\Downloads\Tests;

use Gogilo\Downloads\DownloadsServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

class TestCase extends OrchestraTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Setup database connections if needed
        $this->artisan('migrate', ['--database' => 'testing'])->run();
    }

    protected function getPackageProviders($app)
    {
        return [
            DownloadsServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app)
    {
        // Use in-memory SQLite database for testing
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
        
        // Configure package specific requirements
        $app['config']->set('downloads', [
            'default_disk' => 'public',
            'preview_disk' => 'public',
            'signed_url_expiry_minutes' => 15,
            'verify_permissions' => true,
            'allowed_extensions' => ['pdf', 'doc', 'docx'],
            'max_file_size' => 10485760,
            'model_class' => \Gogilo\Downloads\Models\Download::class,
            'rate_limit' => [
                'max_attempts' => 60,
                'decay_minutes' => 1,
            ],
            'security' => [
                'require_signature' => true,
                'signature_expiry' => 900,
                'ip_logging' => true,
            ],
        ]);
    }

    protected function defineDatabaseMigrations()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../src/Database/Migrations');
    }
}
