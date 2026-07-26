<?php

namespace Database\Seeders;

use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PartnerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $partners = [
            [
                'title' => '3M',
                'logo' => 'partners/3m.png',
                'website' => 'https://www.3m.com/',
                'description' => 'A global leader in safety, health, and industrial solutions.',
                'published' => true,
                'front' => true,
            ],
            [
                'title' => 'Honeywell',
                'logo' => 'partners/honeywell.png',
                'website' => 'https://www.honeywell.com/',
                'description' => 'Provider of innovative safety and productivity solutions.',
                'published' => true,
                'front' => true,
            ],
            [
                'title' => 'Portwest',
                'logo' => 'partners/port-west.png',
                'website' => 'https://www.portwest.com/',
                'description' => 'Manufacturer of high-quality workwear, safety wear, and PPE.',
                'published' => true,
                'front' => true,
            ],
            [
                'title' => 'Safety Jogger',
                'logo' => 'partners/safety-jogger.png',
                'website' => 'https://www.safetyjogger.com/',
                'description' => 'Global safety footwear and personal protective equipment brand.',
                'published' => true,
                'front' => true,
            ],
            [
                'title' => 'Valuetex',
                'logo' => 'partners/valuetex.png',
                'website' => 'https://www.valuetex.com/',
                'description' => 'Supplier of durable and affordable industrial safety products.',
                'published' => true,
                'front' => true,
            ],
        ];

        if (Storage::disk('public')->exists('partners')) {
            Storage::disk('public')->deleteDirectory('partners');
        }

        Storage::disk('public')->makeDirectory('partners');

        foreach ($partners as $partner) {
            $path = $partner['logo'] ? storage_path('data/images/' . $partner['logo']) : null;

            $filename = file_exists($path) ? \Illuminate\Support\Facades\Storage::disk('public')->putFile(
                'partners',
                $path,
            ) : null;

            $title = $partner['title'];
            $slug = Str::slug($partner['title']);
            $website = $partner['website'];
            $description = $partner['description'];
            $published = $partner['published'];
            $front = $partner['front'];

            $partnerModel = new \MeaCms\Partners\Models\Partner();
            $partnerModel->title = $title;
            $partnerModel->slug = $slug;
            $partnerModel->logo = $filename;
            $partnerModel->website = $website;
            $partnerModel->description = $description;
            $partnerModel->published = $published;
            $partnerModel->front = $front;
            $partnerModel->save();

            $this->command->info('Stored picture: ' . $filename . ' for partner: ' . $title);
        }
    }
}
