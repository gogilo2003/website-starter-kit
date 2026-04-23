<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@youngolive.co.ke',
        ]);
        $this->call([
            ProductCategorySeeder::class,
            ElementSeeder::class,
            SlideSeeder::class,
            DownloadSeeder::class,
            PartnerSeeder::class,
            BrandSeeder::class,
        ]);
    }
}
