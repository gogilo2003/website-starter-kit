<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('downloads', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->string('title'); // Title of the downloadable resource
            $table->string('slug')->unique(); // URL-friendly version of the title
            $table->text('description')->nullable(); // Optional description of the resource
            $table->string('file_path'); // Path to the downloadable file
            $table->string('file_name'); // Original name of the file
            $table->string('file_type')->nullable(); // MIME type of the file (e.g., application/pdf)
            $table->integer('file_size')->nullable(); // Size of the file in bytes
            $table->unsignedBigInteger('download_category_id')->nullable(); // Foreign key to the category
            $table->integer('download_count')->default(0); // Track how many times the file has been downloaded
            $table->boolean('is_featured')->default(false); // Mark as featured download
            $table->boolean('is_active')->default(true); // To enable/disable the download
            $table->timestamps(); // created_at and updated_at

            // Foreign key for category
            $table->foreign('download_category_id')->references('id')->on('download_categories')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('downloads');
    }
};
