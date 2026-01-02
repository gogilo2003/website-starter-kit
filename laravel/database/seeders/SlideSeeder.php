<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SlideSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $slides = collect([
            [
                "picture" => 'slide_1.jpg',
                "title" => 'Hazardous Protection Gear',
                "caption" => 'High-quality gear to keep you safe in hazardous environments.',
                "medi_type" => 'image',
                "published" => true,
            ],
            [
                "picture" => 'slide_2.jpg',
                "title" => 'Workplace Safety Solutions',
                "caption" => 'Comprehensive solutions to ensure safety in your workplace.',
                "medi_type" => 'image',
                "published" => true,
            ],
            [
                "picture" => 'slide_3.jpg',
                "title" => 'Professional Apparel',
                "caption" => 'Durable and comfortable apparel for all your professional needs.',
                "medi_type" => 'image',
                "published" => true,
            ],

            [
                "picture" => 'slide_4.jpg',
                "title" => 'Customized Safety Equipment',
                "caption" => 'Tailored safety equipment to meet your specific requirements.',
                "medi_type" => 'image',
                "published" => true,
            ]
        ]);

        if (Storage::disk('public')->exists('slides')) {
            Storage::disk('public')->deleteDirectory('slides');
        }

        Storage::disk('public')->makeDirectory('slides');

        $slides->each(function ($slide) {
            $path = storage_path('data/images/slides/' . $slide['picture']);
            $filename = Storage::disk('public')->putFile(
                'slides',
                $path
            );
            $this->command->info('Stored picture: ' . $filename . ' for slide: ' . $slide['title']);
            $title = $slide['title'];
            $caption = $slide['caption'];
            $media_type = $slide['medi_type'];
            $published = $slide['published'];
            $slide = new \App\Models\Slide();
            $slide->title = $title;
            $slide->caption = $caption;
            $slide->picture = $filename;
            $slide->media_type = $media_type;
            $slide->published = $published;
            $slide->save();
        });
    }
}
