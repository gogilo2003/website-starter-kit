<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('product_categories', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('icon')->nullable();
            $table->string('picture')->nullable();
            $table->boolean('published')->default(false);
            $table->boolean('promoted')->default(false);
            $table->timestamps();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('product_category_id')
                ->after('content')
                ->nullable()
                ->constrained('product_categories')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['product_category_id']);
            $table->dropColumn('product_category_id');
        });

        Schema::dropIfExists('product_categories');
    }
};
