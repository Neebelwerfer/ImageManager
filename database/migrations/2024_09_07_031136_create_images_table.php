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
            $table->uuid()->primary();
            $table->string('name');
            $table->string('path');
            $table->boolean('is_public')->default(false);
            $table->smallInteger('width', false, true);
            $table->smallInteger('height', false, true);
            $table->string('thumbnail_path');
            $table->tinyInteger('rating')->default(5)->unsigned();
            $table->foreignId('category_id')->nullable()->constrained('image_categories');
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
            $table->foreignUuid('image_uuid')->constrained('images', 'uuid')->onDelete('cascade');
            $table->foreignId('image_tag_id')->constrained('image_tags')->onDelete('cascade');
            $table->primary(['image_uuid', 'image_tag_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('image_image_tag');
        Schema::dropIfExists('image_tags');
        Schema::dropIfExists('images');
        Schema::dropIfExists('image_categories');
    }
};
