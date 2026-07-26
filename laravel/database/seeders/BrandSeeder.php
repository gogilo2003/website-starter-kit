<?php

namespace Database\Seeders;

use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $brands = [
            [
                'name' => '3M',
                'logo' => 'partners/3m.png',
            ],
            [
                'name' => 'Honeywell',
                'logo' => 'partners/honeywell.png',
            ],
            [
                'name' => 'Portwest',
                'logo' => 'partners/port-west.png',
            ],
            [
                'name' => 'Safety Jogger',
                'logo' => 'partners/safety-jogger.png',
            ],
            [
                'name' => 'Valuetex',
                'logo' => 'partners/valuetex.png',
            ],
        ];

        if (Storage::disk('public')->exists('brands')) {
            Storage::disk('public')->deleteDirectory('brands');
        }

        Storage::disk('public')->makeDirectory('brands');
        Schema::disableForeignKeyConstraints();
        \MeaCms\Products\Models\Brand::truncate();
        Schema::enableForeignKeyConstraints();
        foreach ($brands as $brand) {
            $path = $brand['logo'] ? storage_path('data/images/' . $brand['logo']) : null;

            $filename = file_exists($path) ? Storage::disk('public')->putFile(
                'brands',
                $path,
            ) : null;

            $name = $brand['name'];

            $brandModel = new \MeaCms\Products\Models\Brand();
            $brandModel->name = $name;
            $brandModel->logo = $filename;
            $brandModel->save();

            $this->command->info('Stored picture: ' . $filename . ' for partner: ' . $name);
        }
    }
}
