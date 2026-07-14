<?php

namespace Gogilo\Downloads\Database\Factories;

use Gogilo\Downloads\Models\Download;
use Illuminate\Database\Eloquent\Factories\Factory;

class DownloadFactory extends Factory
{
    protected $model = Download::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(3),
            'slug' => $this->faker->slug(),
            'description' => $this->faker->paragraph(),
            'file_path' => 'downloads',
            'file_name' => $this->faker->word().'.pdf',
            'file_type' => 'application/pdf',
            'file_size' => $this->faker->numberBetween(1024, 10485760),
            'download_category_id' => null,
            'download_count' => $this->faker->numberBetween(0, 1000),
            'is_featured' => $this->faker->boolean(),
            'is_active' => $this->faker->boolean(),
        ];
    }

    public function featured(): static
    {
        return $this->state(fn (array $attributes) => ['is_featured' => true]);
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => ['is_active' => true]);
    }
}
