<?php

namespace Gogilo\Downloads\Tests\Feature;

use Gogilo\Downloads\Models\Download;
use Gogilo\Downloads\Models\DownloadCategory;
use Gogilo\Downloads\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DownloadTest extends TestCase
{
    use RefreshDatabase;

    public function test_download_can_be_created_with_factory(): void
    {
        $download = Download::factory()->create([
            'title' => 'Product Brochure',
            'slug' => 'product-brochure',
        ]);

        $this->assertDatabaseHas('downloads', [
            'title' => 'Product Brochure',
            'slug' => 'product-brochure',
        ]);
        $this->assertEquals('product-brochure', $download->slug);
    }

    public function test_download_belongs_to_category(): void
    {
        $category = DownloadCategory::create(['name' => 'Manuals', 'slug' => 'manuals']);
        $download = Download::factory()->create(['download_category_id' => $category->id]);

        $this->assertTrue($download->category->is($category));
        $this->assertTrue($category->downloads->contains($download));
    }

    public function test_download_active_flag_toggles(): void
    {
        $download = Download::factory()->active()->create();

        $this->assertTrue($download->is_active);
    }
}
