<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use PDOException;

class SetupCommand extends Command
{
    protected $signature = 'setup';
    protected $description = 'Setup application environment, database, and run migrations';

    public function handle(): int
    {
        $this->info('🚀 Starting application setup...');

        $project = $this->resolveProjectMeta();

        $this->setupEnvFile();
        $this->setAppDefaults($project);
        $this->configureDatabase($project);
        $this->generateAppKey();
        $this->runMigrations();

        $this->info('✅ Application setup completed successfully.');

        return Command::SUCCESS;
    }

    protected function resolveProjectMeta(): array
    {
        $folder = basename(base_path());

        $isSimpleLowercase = preg_match('/^[a-z]+$/', $folder);

        $appName = $isSimpleLowercase
            ? $folder
            : Str::of($folder)
            ->replace(['_', '-'], ' ')
            ->snake()
            ->replace('_', ' ')
            ->title()
            ->toString();

        $urlSlug = Str::of($folder)
            ->snake()
            ->replace('_', '')
            ->lower()
            ->toString();

        $dbName = $isSimpleLowercase
            ? $folder
            : Str::of($folder)->snake()->toString();

        return [
            'folder' => $folder,
            'app_name' => $appName,
            'app_url' => "https://{$urlSlug}.test",
            'db_name' => $dbName,
        ];
    }


    protected function setupEnvFile(): void
    {
        if (!File::exists(base_path('.env'))) {
            File::copy(
                base_path('.env.example'),
                base_path('.env')
            );

            $this->info('.env created from .env.example');
        }
    }

    protected function setAppDefaults(array $project): void
    {
        $this->info('⚙️ Setting application defaults...');

        $this->updateEnv([
            'APP_NAME' => env('APP_NAME') ?: $project['app_name'],
            'APP_URL' => env('APP_URL') ?: $project['app_url'],
        ]);

        $this->info("APP_NAME → {$project['app_name']}");
        $this->info("APP_URL  → {$project['app_url']}");
    }

    protected function configureDatabase(array $project): void
    {
        $this->info('🔍 Checking database configuration...');

        if (empty(env('DB_DATABASE'))) {
            $this->warn('Database not configured.');

            $useGuess = $this->confirm(
                "Use guessed database name '{$project['db_name']}'?",
                true
            );

            $dbName = $useGuess
                ? $project['db_name']
                : $this->ask('Database name');

            $dbConnection = $this->choice(
                'Database connection',
                ['mysql', 'pgsql', 'sqlite', 'sqlsrv'],
                0
            );

            $dbHost = $this->ask('Database host', '127.0.0.1');
            $dbPort = $this->ask(
                'Database port',
                $dbConnection === 'pgsql' ? '5432' : '3306'
            );
            $dbUser = $this->ask('Database username');
            $dbPass = $this->secret('Database password (optional)');

            $this->updateEnv([
                'DB_CONNECTION' => $dbConnection,
                'DB_HOST' => $dbHost,
                'DB_PORT' => $dbPort,
                'DB_DATABASE' => $dbName,
                'DB_USERNAME' => $dbUser,
                'DB_PASSWORD' => $dbPass,
            ]);
        }

        $this->validateDatabaseConnection();
    }

    protected function validateDatabaseConnection(): void
    {
        try {
            DB::purge();
            DB::connection()->getPdo();
            $this->info('✅ Database connection successful');
        } catch (PDOException $e) {
            $this->error('❌ Database connection failed');
            $this->line($e->getMessage());
            exit(Command::FAILURE);
        }
    }

    protected function generateAppKey(): void
    {
        if (empty(config('app.key'))) {
            $this->callSilent('key:generate');
            $this->info('🔑 APP_KEY generated');
        }
    }

    protected function runMigrations(): void
    {
        if ($this->confirm('Run migrations now?', true)) {
            $this->call('migrate', ['--force' => true]);
        }
    }

    protected function updateEnv(array $values): void
    {
        $envPath = base_path('.env');
        $env = file_get_contents($envPath);

        foreach ($values as $key => $value) {
            $value = str_replace('"', '\"', $value ?? '');

            if (preg_match("/^{$key}=.*$/m", $env)) {
                $env = preg_replace(
                    "/^{$key}=.*$/m",
                    "{$key}=\"{$value}\"",
                    $env
                );
            } else {
                $env .= PHP_EOL . "{$key}=\"{$value}\"";
            }
        }

        file_put_contents($envPath, $env);
    }
}
