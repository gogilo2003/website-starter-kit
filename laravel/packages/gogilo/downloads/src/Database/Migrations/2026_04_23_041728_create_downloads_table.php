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
        if (!Schema::hasTable('downloads')) {
            Schema::create('downloads', function (Blueprint $table) {
                $table->id();
                $table->string('original_filename');
                $table->string('storage_path');
                $table->bigInteger('file_size');
                $table->string('mime_type');
                $table->string('disk');
                $table->json('metadata')->nullable();
                $table->unsignedBigInteger('download_count')->default(0);
                $table->timestamps();
                $table->softDeletes();
                
                // Indexes for performance
                $table->index('disk');
                $table->index('mime_type');
                $table->index('created_at');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('downloads');
    }
};