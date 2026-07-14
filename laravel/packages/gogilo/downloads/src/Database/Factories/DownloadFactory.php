<?php

namespace Gogilo\Downloads\Database\Factories;

use Gogilo\Downloads\Models\Download;
use Illuminate\Database\Eloquent\Factories\Factory;

class DownloadFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = Download::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $filenames = ['document.pdf', 'image.jpg', 'archive.zip', 'presentation.pptx', 'spreadsheet.xlsx'];
        $mimeTypes = ['application/pdf', 'image/jpeg', 'application/zip', 'application/vnd.ms-powerpoint', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];
        $disks = ['local', 's3'];
        
        return [
            'original_filename' => $this->faker->word() . '_' . $this->faker->randomElement($filenames),
            'storage_path' => 'downloads/' . $this->faker->uuid() . '_' . $this->faker->word(),
            'file_size' => $this->faker->numberBetween(1024, 10485760),
            'mime_type' => $this->faker->randomElement($mimeTypes),
            'disk' => $this->faker->randomElement($disks),
            'metadata' => [
                'uploaded_by' => $this->faker->name(),
                'category' => $this->faker->randomElement(['documents', 'images', 'archives', 'presentations']),
            ],
            'download_count' => $this->faker->numberBetween(0, 1000),
        ];
    }

    /**
     * Indicate that the download is recent.
     */
    public function recent(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'created_at' => now()->subDays(random_int(1, 30)),
            ];
        });
    }

    /**
     * Indicate that the download has high download count.
     */
    public function popular(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'download_count' => random_int(100, 10000),
            ];
        });
    }
}