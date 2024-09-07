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

        Schema::create('image_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('is_public')->default(false);
            $table->foreignId('owner_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('images', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('path');
            $table->string('thumbnail_path');
            $table->tinyInteger('rating')->default(5)->unsigned();
            $table->foreignId('category_id')->nullable()->constrained('image_categories');
            $table->boolean('is_public')->default(false);
            $table->foreignId('owner_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('image_tags' , function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('is_public')->default(false);
            $table->foreignId('owner_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });


        Schema::create('image_image_tag', function (Blueprint $table) {
            $table->foreignId('image_id')->constrained('images')->onDelete('cascade');
            $table->foreignId('image_tag_id')->constrained('image_tags')->onDelete('cascade');
            $table->primary(['image_id', 'image_tag_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('images');
        Schema::dropIfExists('image_categories');
        Schema::dropIfExists('image_tags');
        Schema::dropIfExists('image_image_category');
        Schema::dropIfExists('image_image_tag');
    }
};
