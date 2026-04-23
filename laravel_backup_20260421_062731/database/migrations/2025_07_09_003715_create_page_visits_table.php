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
        Schema::create('page_visits', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('url', 191);
            $table->string('route_name', 191)->nullable();
            $table->string('visitor_id', 191);
            $table->string('ip_address', 45)->nullable(); // IPv6 needs max 45 chars
            $table->text('user_agent')->nullable(); // Changed to text for longer UAs
            $table->string('referrer', 191)->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();

            // Explicitly match production engine
            $table->engine = 'MyISAM';

            // Add production-compatible index
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('page_visits');
    }
};
