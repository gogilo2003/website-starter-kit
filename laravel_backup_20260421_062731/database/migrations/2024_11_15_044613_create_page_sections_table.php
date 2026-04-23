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
        Schema::create('page_sections', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('title')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('element_page_section', function (Blueprint $table) {
            $table->foreignId('page_section_id');
            $table->foreignId('element_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('element_page_section');
        Schema::dropIfExists('page_sections');
    }
};
