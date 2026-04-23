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
        Schema::create('page_visit_stats', function (Blueprint $table) {
            $table->id();
            $table->string('url')->nullable();
            $table->string('route_name')->nullable();
            $table->date('date');
            $table->integer('unique_visits')->default(0);
            $table->integer('total_visits')->default(0);
            $table->timestamps();

            $table->unique(['url', 'date']);
            $table->unique(['route_name', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('page_visit_stats');
    }
};
