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

        // Configure database and ensure connection works before proceeding
        if (!$this->ensureDatabaseConnection($project)) {
            $this->newLine();
            $this->error('❌ Setup aborted: Database connection could not be established.');
            $this->warn('Please fix the database configuration in .env and run the setup again.');
            return Command::FAILURE;
        }

        $this->generateAppKey();
        $this->runMigrations();

        $this->newLine();
        $this->info('✅ Application setup completed successfully!');
        $this->line('   You can now start the server with: <comment>php artisan serve</comment> or your preferred method.');

        return Command::SUCCESS;
    }

    protected function resolveProjectMeta(): array
    {
        $projectRoot = dirname(base_path());
        $folder = basename($projectRoot); // e.g. "website-starter-kit"

        // 1. DB_DATABASE → snake_case (underscores), lowercase
        $dbName = Str::of($folder)
            ->replace('-', '_')     // kebab → snake (critical fix!)
            ->snake()               // now handles camelCase too: mySite → my_site
            ->lower()
            ->toString();

        // 2. APP_URL → kebab-case (hyphens), lowercase
        $urlSlug = Str::of($folder)
            ->kebab()           // handles camelCase, snake_case, spaces → kebab
            ->lower()
            ->toString();       // "website-starter-kit" → "website-starter-kit"
        // "mySite" → "my-site"
        // "my_site" → "my-site"

        // 3. APP_NAME → human-readable title
        $appName = Str::of($folder)
            ->replace(['-', '_'], ' ')  // turn - and _ into spaces
            ->title()                   // "Website Starter Kit"
            ->toString();

        // Special case: if folder is all lowercase letters only (no hyphens/underscores), keep original
        if (preg_match('/^[a-z]+$/', $folder)) {
            $appName = $folder; // e.g. "mysite" → "mysite" (not "Mysite")
        }

        return [
            'folder'    => $folder,
            'app_name'  => $appName,
            'app_url'   => "https://{$urlSlug}.test",
            'db_name'   => $dbName,
        ];
    }

    protected function setupEnvFile(): void
    {
        if (!File::exists(base_path('.env'))) {
            File::copy(base_path('.env.example'), base_path('.env'));
            $this->info('.env created from .env.example');
        }
    }

    protected function setAppDefaults(array $project): void
    {
        $this->info('⚙️ Setting application defaults...');

        $this->updateEnv([
            'APP_NAME' => $project['app_name'],
            'APP_URL'  => $project['app_url'],
        ]);

        $this->info("APP_NAME → {$project['app_name']}");
        $this->info("APP_URL  → {$project['app_url']}");
    }

    /**
     * Ensure database is configured and connected.
     * Loops on failure until user fixes it or aborts.
     */
    protected function ensureDatabaseConnection(array $project): bool
    {
        $this->info('🔍 Configuring database...');

        while (true) {
            $this->configureDatabaseOnce($project);

            // Clear config to apply latest .env changes
            $this->callSilent('config:clear');

            $this->newLine();
            if ($this->testDatabaseConnection()) {
                $this->info('✅ Database connection successful! Proceeding with setup...');
                return true;
            }

            // Connection failed
            $this->error('❌ Database connection failed.');

            if (!$this->confirm('Do you want to update the database credentials again?', true)) {
                return false; // User doesn't want to retry → abort setup
            }

            $this->newLine();
            $this->warn('Let\'s update the database configuration...');
        }
    }

    /**
     * Configure database (prompt if needed) – runs once per loop
     */
    protected function configureDatabaseOnce(array $project): void
    {
        $dbName = trim($this->envValue('DB_DATABASE') ?? '', '"');
        $dbUser = trim($this->envValue('DB_USERNAME') ?? '', '"');

        if (empty($dbName) || empty($dbUser)) {
            $this->warn('Database not configured in .env yet.');
        } else {
            $this->info('Current database config loaded from .env');
        }

        $useGuess = empty($dbName) && $this->confirm(
            "Use guessed database name '{$project['db_name']}'?",
            true
        );

        $database = $useGuess
            ? $project['db_name']
            : $this->ask('Database name', $dbName ?: $project['db_name']);

        $connection = $this->choice(
            'Database connection type',
            ['mysql', 'mariadb', 'pgsql', 'sqlite', 'sqlsrv'],
            $this->envValue('DB_CONNECTION') ?? 'mysql'
        );

        $defaultHost = $this->envValue('DB_HOST') ?? '127.0.0.1';
        $host = $this->ask('Database host', trim($defaultHost, '"'));

        $defaultPort = $connection === 'pgsql' ? '5432' : ($connection === 'mysql' ? '3306' : '3307');
        $port = $this->ask('Database port', trim($this->envValue('DB_PORT') ?? $defaultPort, '"'));

        $username = $this->ask('Database username', trim($this->envValue('DB_USERNAME') ?? 'root', '"'));
        $password = $this->secret('Database password (leave blank if none)');

        $this->updateEnv([
            'DB_CONNECTION' => $connection,
            'DB_HOST'       => $host,
            'DB_PORT'       => $port,
            'DB_DATABASE'   => $database,
            'DB_USERNAME'   => $username,
            'DB_PASSWORD'   => $password ?? '',
        ]);

        $this->info('Database configuration updated in .env');
    }

    /**
     * Test database connection
     * Returns true if:
     *   - Connected successfully, OR
     *   - Failed with "Unknown database" (1049) → safe to proceed, migrate will create it
     * Returns false on real connection issues (wrong host, user, password, port)
     */
    protected function testDatabaseConnection(): bool
    {
        $connection = trim($this->envValue('DB_CONNECTION') ?? 'mysql', '"');
        $database   = trim($this->envValue('DB_DATABASE') ?? '', '"');
        $host       = trim($this->envValue('DB_HOST') ?? '127.0.0.1', '"');
        $port       = trim($this->envValue('DB_PORT') ?? '3306', '"');
        $username   = trim($this->envValue('DB_USERNAME') ?? '', '"');
        $password   = trim($this->envValue('DB_PASSWORD') ?? '', '"');

        config()->set('database.default', $connection);
        config()->set("database.connections.$connection", [
            'driver'    => $connection,
            'host'      => $host,
            'port'      => $port,
            'database'  => $database,
            'username'  => $username,
            'password'  => $password,
            'charset'   => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix'    => '',
            'strict'    => true,
            'engine'    => null,
        ]);

        DB::purge($connection);

        // SQLite: always succeed (handled separately)
        if ($connection === 'sqlite') {
            $path = Str::startsWith($database, '/')
                ? $database
                : database_path($database ?: 'database.sqlite');

            if (!File::exists($path)) {
                File::ensureDirectoryExists(dirname($path));
                touch($path);
            }
            $this->info('✅ SQLite ready (database file will be created if needed)');
            return true;
        }

        try {
            DB::connection()->getPdo();
            $this->info('✅ Database connection successful!');
            return true;
        } catch (PDOException $e) {
            $message = $e->getMessage();
            $sqlState = $e->getCode(); // Actually PDO error code, but Laravel wraps SQLSTATE here

            // Extract SQLSTATE if available
            if (preg_match('/SQLSTATE\[(\d+)\]/', $message, $matches)) {
                $sqlState = $matches[1];
            }

            // 1049 = Unknown database → OK! We can proceed, migrate will create it
            if ($sqlState === '1049') {
                $this->warn("⚠️  Database '{$database}' does not exist yet.");
                $this->info('   This is fine — migrations will create it automatically.');
                return true; // Allow setup to continue
            }

            // Real connection problems: wrong host, port, credentials, etc.
            $this->error('❌ Database connection failed');
            $this->line('<fg=red>' . $e->getMessage() . '</>');

            // Common helpful hints
            if (str_contains($message, 'Access denied')) {
                $this->line('   → Check username and password');
            } elseif (str_contains($message, 'No such host') || str_contains($message, 'Connection refused')) {
                $this->line('   → Check DB_HOST and DB_PORT');
            } elseif (str_contains($message, '1045')) {
                $this->line('   → Access denied — invalid credentials');
            }

            return false;
        }
    }
    protected function generateAppKey(): void
    {
        if (empty(config('app.key'))) {
            $this->callSilent('key:generate');
            $this->info('🔑 Application key generated');
        }
    }

    protected function runMigrations(): void
    {
        if ($this->confirm('Run database migrations now?', true)) {
            $this->call('migrate', ['--force' => true]);
            $this->info('Migrations completed');
        }
    }

    protected function updateEnv(array $values): void
    {
        $envPath = base_path('.env');
        if (!File::exists($envPath)) return;

        $env = file_get_contents($envPath);

        foreach ($values as $key => $value) {
            $value = $value === null ? '' : str_replace('"', '\"', $value);
            $quotedValue = '"' . $value . '"';

            if (preg_match("/^{$key}=/m", $env)) {
                $env = preg_replace("/^{$key}=.*$/m", "{$key}={$quotedValue}", $env);
            } else {
                $env .= PHP_EOL . "{$key}={$quotedValue}";
            }
        }

        file_put_contents($envPath, $env);
    }

    protected function envValue(string $key): ?string
    {
        $path = base_path('.env');
        if (!File::exists($path)) return null;

        $content = File::get($path);
        if (preg_match("/^{$key}=(.*)$/m", $content, $matches)) {
            return $matches[1] ?? null;
        }

        return null;
    }
}
