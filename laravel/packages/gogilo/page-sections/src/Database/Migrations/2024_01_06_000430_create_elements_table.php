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
        Schema::create('elements', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('title')->unique();
            $table->longText('content')->nullable();
            $table->char('content_hash', 64)->nullable()->default(null)->unique();
            $table->enum('type', ['text', 'multiline', 'richtext'])->nullable()->default(null);
            $table->string('photo')->nullable()->default(null);
            $table->string('icon')->nullable()->default(null);
            $table->boolean('published')->nullable()->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('elements');
    }
};
