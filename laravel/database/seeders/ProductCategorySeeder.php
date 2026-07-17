<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File as FileFacade;
use Illuminate\Support\Str;
use Gogilo\Products\Services\ProductService;
use Gogilo\Products\Services\ProductCategoryService;

class ProductCategorySeeder extends Seeder
{
    protected ProductService $productService;
    protected ProductCategoryService $productCategoryService;

    public function __construct(
        ProductService $productService,
        ProductCategoryService $productCategoryService
    ) {
        $this->productService = $productService;
        $this->productCategoryService = $productCategoryService;
    }

    public function run(): void
    {
        $categories = collect([
            [
                'name' => 'Head Protection',
                'picture' => 'products/categories/head_protection.png',
                'icon' => 'shield-check',
                'description' => 'Safety helmets, hard hats, and bump caps designed to protect workers from head injuries and falling objects.',
                'products' => require storage_path('data/products/php/head-protection.php'),
            ],
            [
                'name' => 'Eye & Face Protection',
                'picture' => 'products/categories/eye_protection.png',
                'icon' => 'eye',
                'description' => 'Safety glasses, goggles, and face shields designed to protect against flying particles, chemical splashes, and harmful radiation.',
                'products' => require storage_path('data/products/php/eye-protection.php'),
            ],
            [
                'name' => 'Ear Protection',
                'picture' => 'products/categories/ear_protection.png',
                'icon' => 'speaker-wave',
                'description' => 'Earplugs, earmuffs, and hearing protection devices to prevent hearing damage in high-noise work environments.',
                'products' => require storage_path('data/products/php/ear-protection.php'),
            ],
            [
                'name' => 'Respiratory Protection',
                'picture' => 'products/categories/respiratory_protection.png',
                'icon' => 'face-smile',
                'description' => 'Respirators, masks, and breathing apparatus to protect against dust, fumes, vapors, and other airborne contaminants.',
                'products' => require storage_path('data/products/php/respiratory-protection.php'),
            ],
            [
                'name' => 'Body Protection',
                'picture' => 'products/categories/body_protection.png',
                'icon' => 'user',
                'description' => 'High-visibility vests, overalls, coveralls, and protective clothing for safe and comfortable body protection.',
                'products' => [],
            ],
            [
                'name' => 'Hand Protection',
                'picture' => 'products/categories/hand_protection.png',
                'icon' => 'hand-thumb-up',
                'description' => 'Durable safety gloves offering grip, dexterity, and protection against cuts, heat, chemicals, and abrasions.',
                'products' => require storage_path('data/products/php/hand-protection.php'),
            ],
            [
                'name' => 'Foot Protection',
                'picture' => 'products/categories/feet_protection.png',
                'icon' => 'cube',
                'description' => 'Industrial safety boots and shoes with steel toes, anti-slip soles, and waterproof features for all working conditions.',
                'products' => require storage_path('data/products/php/foot-protection.php'),
            ],
            [
                'name' => 'Promo',
                'picture' => 'products/categories/promo.png',
                'icon' => 'tag',
                'description' => 'Special offers, branded merchandise, and promotional products designed to enhance brand identity and workplace safety culture.',
                'products' => [],
            ],
            [
                'name' => 'Staff Uniforms',
                'picture' => 'products/categories/staff_uniforms.png',
                'icon' => 'users',
                'description' => 'Professional staff uniforms tailored for comfort, durability, safety compliance, and a polished corporate image.',
                'products' => [],
            ],
            [
                'name' => 'Other Products',
                'picture' => 'products/categories/other_products.png',
                'icon' => 'archive-box',
                'description' => 'A wide range of complementary safety equipment, workplace essentials, and specialized products to meet unique business needs.',
                'products' => [],
            ],
        ]);

        /* ---------------- RESET STORAGE ---------------- */

        if (Storage::disk('public')->exists('products')) {
            Storage::disk('public')->deleteDirectory('products');
        }

        Storage::disk('public')->makeDirectory('products/categories');

        /* ---------------- SEED ---------------- */

        $categories->each(function ($category) {

            /* ---------- CATEGORY IMAGE ---------- */

            $imagePath = storage_path('data/images/' . $category['picture']);
            $categoryImage = null;

            if (FileFacade::exists($imagePath)) {
                $categoryImage = new UploadedFile(
                    $imagePath,
                    basename($imagePath),
                    mime_content_type($imagePath),
                    null,
                    true // test mode (important for seeders)
                );
                $this->command->info(sprintf("%s category Picture found: %s", $category['name'], $imagePath));
            } else {
                $this->command->error(sprintf("%s category Picture not found: %s", $category['name'], $imagePath));
            }

            /* ---------- CREATE CATEGORY (SERVICE) ---------- */

            $productCategory = $this->productCategoryService->createProductCategory([
                'name'        => $category['name'],
                'slug'        => Str::slug($category['name']),
                'description' => $category['description'],
                'icon'        => $category['icon'],
                'picture'     => $categoryImage,
                'published'   => true,
                'promoted'    => (bool) rand(0, 1),
            ]);

            $this->command->info(
                'Category created: ' . $productCategory->name
            );

            /* ---------- PRODUCTS ---------- */

            collect($category['products'])->each(function ($data) use ($productCategory) {

                $productImagePath = storage_path('data/images/products/products/' . $data['picture']);
                $productImage = null;

                if (FileFacade::exists($productImagePath)) {
                    $productImage = new UploadedFile(
                        $productImagePath,
                        basename($productImagePath),
                        mime_content_type($productImagePath),
                        null,
                        true
                    );
                } else {
                    $this->command->error(
                        'Picture not found: ' . $data['picture']
                    );
                }

                $this->productService->createProduct([
                    'title'      => $data['title'],
                    'slug'       => $data['slug'],
                    'summary'    => $data['summary'],
                    'content'    => $data['content'],
                    'price'      => $data['price'],
                    'features'   => $data['features'],
                    'published'  => $data['published'],
                    'front'      => $data['front'],
                    'category'   => $productCategory->id,
                    'brand_id'   => null,
                    'picture'    => $productImage,
                ]);
            });
        });
    }
}
