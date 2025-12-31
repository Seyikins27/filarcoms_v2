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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('author');
            $table->string('title')->unique();
            $table->string('slug')->unique();
            $table->longText('content');
            $table->tinyText('image');
            $table->string('location')->nullable();
            $table->date('start_date')->index();
            $table->time('start_time');
            $table->text('seo_tags')->nullable();
            $table->text('meta_description')->nullable();
            $table->date('end_date')->index();
            $table->time('end_time');
            $table->boolean('active')->default(true);
            $table->boolean('archived')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
