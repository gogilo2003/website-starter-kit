<?php

namespace Gogilo\Downloads\Tests\Feature;

use Gogilo\Downloads\Models\Download;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Gogilo\Downloads\Tests\TestCase;

class DownloadTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that a file can be downloaded.
     */
    public function test_file_can_be_downloaded(): void
    {
        Storage::fake('public');
        
        $file = UploadedFile::fake()->create('document.pdf', 100);
        $download = Download::factory()->create([
            'storage_path' => 'downloads/document.pdf',
            'disk' => 'public',
            'file_size' => 100,
            'mime_type' => 'application/pdf',
            'original_filename' => 'document.pdf',
        ]);
        
        Storage::disk('public')->put('downloads/document.pdf', 'content');
        
        $response = $this->getJson("/api/downloads/{$download->id}/metadata");
        $response->assertOk()->assertJson([
            'id' => $download->id,
            'original_filename' => 'document.pdf',
        ]);
    }

    /**
     * Test that signed URLs work.
     */
    public function test_signed_url_generation(): void
    {
        $download = Download::factory()->create();
        
        $url = $download->file_url;
        $this->assertStringContainsString('/downloads/' . $download->id, $url);
    }

    /**
     * Test file size formatting.
     */
    public function test_file_size_formatting(): void
    {
        $download = Download::factory()->create(['file_size' => 1500]);
        $this->assertEquals('1.46 KB', $download->formatted_size);
        
        $download = Download::factory()->create(['file_size' => 5000000]);
        $this->assertEquals('4.77 MB', $download->formatted_size);
    }

    /**
     * Test download count formatting.
     */
    public function test_download_count_formatting(): void
    {
        $download = Download::factory()->create(['download_count' => 1500]);
        $this->assertEquals('1.5K', $download->formatted_download_count);
        
        $download = Download::factory()->create(['download_count' => 2500000]);
        $this->assertEquals('2.5M', $download->formatted_download_count);
    }
}