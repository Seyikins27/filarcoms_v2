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
        Schema::create('blogs', function (Blueprint $table) {
            $table->id();
            $table->string('title')->unique();
            $table->tinyText('slug')->unique();
            $table->tinyText('image')->nullable();
            $table->mediumText('seo_tags');
            $table->tinyText('meta_description');
            $table->text('content');
            $table->string('author');
            $table->mediumText('tags')->nullable();
            $table->boolean('archived')->default(true);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blogs');
    }
};
