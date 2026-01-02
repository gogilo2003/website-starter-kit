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
        Schema::create('download_categories', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->string('name'); // Name of the category
            $table->string('slug')->unique(); // URL-friendly version of the name
            $table->text('description')->nullable(); // Optional description of the category
            $table->string('icon')->nullable(); // Optional icon for the category
            $table->boolean('is_active')->default(true); // To enable/disable the category
            $table->timestamps(); // created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('download_categories');
    }
};
