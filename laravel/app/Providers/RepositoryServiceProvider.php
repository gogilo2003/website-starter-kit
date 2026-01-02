<?php

namespace App\Providers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Path to interface and repository directories
        $interfacePath = app_path('Interfaces/Repositories');
        $repositoryPath = app_path('Repositories');

        // Ensure both directories exist
        if (File::exists($interfacePath) && File::exists($repositoryPath)) {
            // Loop through each interface file
            foreach (File::files($interfacePath) as $interfaceFile) {
                $interfaceName = $interfaceFile->getFilenameWithoutExtension();

                // Build the fully qualified class names
                $interfaceClass = "App\\Interfaces\\Repositories\\{$interfaceName}";
                $repositoryClass = "App\\Repositories\\" . str_replace('Interface', '', $interfaceName);

                // If the repository class exists, bind it to the interface
                if (class_exists($repositoryClass)) {
                    $this->app->bind($interfaceClass, $repositoryClass);
                }
            }
        }
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
