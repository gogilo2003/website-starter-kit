<?php

namespace Gogilo\Products\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class InstallProductsCommand extends Command
{
    protected $signature = 'products:install {--force : Overwrite any existing files}';
    protected $description = 'Install and publish the Products package scaffolding';

    public function handle(): int
    {
        $this->info('Publishing Products package files...');

        $this->call('vendor:publish', [
            '--tag' => 'products-package',
            '--force' => (bool) $this->option('force'),
        ]);

        $this->ensureProductsRoutesIncluded();

        $this->newLine();
        $this->info('Products package installed successfully.');
        $this->line('Next steps:');
        $this->line('1) php artisan migrate');
        $this->line('2) npm run build (or npm run dev)');
        $this->line('3) composer dump-autoload');

        return self::SUCCESS;
    }

    protected function ensureProductsRoutesIncluded(): void
    {
        $bootstrapPath = base_path('bootstrap/app.php');

        if (!File::exists($bootstrapPath)) {
            return;
        }

        $content = File::get($bootstrapPath);
        $needle = "routes/web.php";
        $productsRoute = "routes/products.php";

        if (str_contains($content, $productsRoute)) {
            return;
        }

        $updated = str_replace(
            $needle,
            $needle . "',\n        web: base_path('" . $productsRoute,
            $content
        );

        if ($updated !== $content) {
            File::put($bootstrapPath, $updated);
            $this->info('Added routes/products.php to bootstrap/app.php');
        } else {
            $this->warn('Could not auto-register routes/products.php. Please register it manually.');
        }
    }
}
