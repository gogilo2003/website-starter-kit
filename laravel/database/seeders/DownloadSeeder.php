<?php

namespace Database\Seeders;

use Gogilo\Downloads\Models\Download;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Gogilo\Downloads\Models\DownloadCategory;
use Illuminate\Http\UploadedFile;
use App\Services\FileUploadService;
use Illuminate\Support\Facades\Storage;

class DownloadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = collect([
            [
                "name" => 'General',
                "slug" => 'general',
                "description" => 'General downloads including brochures, catalogs, and other resources.',
                "icon" => 'arrow-down-tray',
                "is_active" => true,
            ],
            [
                'name' => 'Safety Manuals',
                'slug' => 'safety-manuals',
                'description' => 'Comprehensive safety manuals for various industries and workplaces.',
                'icon' => 'fa fa-book',
                'is_active' => true,
            ],
        ]);

        // Instantiate upload service
        /** @var FileUploadService $fileUploadService */
        $fileUploadService = app(FileUploadService::class);

        // Reusable local sample files (must exist in storage/data/files/)
        $files = ['upload.pdf', 'upload.doc', 'upload.ppt', 'upload.xls', 'upload.zip'];

        if (Storage::exists('uploads')) {
            Storage::deleteDirectory('uploads');
        }

        Storage::makeDirectory('uploads');

        $categories->each(function ($categoryData) use ($fileUploadService, $files) {
            // Create category
            $downloadCategory = new DownloadCategory();
            $downloadCategory->name = $categoryData['name'];
            $downloadCategory->slug = $categoryData['slug'];
            $downloadCategory->description = $categoryData['description'];
            $downloadCategory->icon = $categoryData['icon'];
            $downloadCategory->is_active = $categoryData['is_active'];
            $downloadCategory->save();

            // Seed random downloads under this category
            $start = rand(0, 4);
            $end = rand($start, 4);

            foreach (range($start, $end) as $index) {
                $download = new Download();

                $download->title = "{$downloadCategory->name} Document {$index}";
                $download->slug = Str::slug($download->title);
                $download->description = "This is a description for {$download->title}.";

                // Randomly select one of the sample files
                $fileName = $files[$index];
                $filePath = storage_path("data/downloads/{$fileName}");

                $this->command->info("File stored: {$filePath}");

                if (!file_exists($filePath)) {
                    $this->command->warn("⚠️ Skipped missing file: {$filePath}");
                    continue;
                }

                // Wrap it as UploadedFile for the FileUploadService
                $uploadedFile = new UploadedFile(
                    $filePath,
                    basename($filePath),
                    mime_content_type($filePath) ?: 'application/octet-stream',
                    null,
                    false // test mode, don't move file physically
                );

                // Upload file using your upload service
                $uploadedFilePath = $fileUploadService->handle($uploadedFile);

                // Fill download attributes
                $download->file_path = $uploadedFilePath['name'];
                $download->file_name = $uploadedFilePath['name'];
                $download->file_type = $uploadedFilePath['type'];
                $download->file_size = $uploadedFilePath['size'];
                $download->download_category_id = $downloadCategory->id;
                $download->is_featured = (bool) rand(0, 1);
                $download->is_active = true;

                $download->save();
            }
        });
    }
}
