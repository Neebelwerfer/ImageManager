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
        Schema::create('albums', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('owner_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('image_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('owner_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('images', function (Blueprint $table) {
            $table->uuid()->primary();
            $table->string('name')->unique();
            $table->string('path');
            $table->string('thumbnail_path');
            $table->smallInteger('width', false, true);
            $table->smallInteger('height', false, true);
            $table->tinyInteger('rating')->default(5)->unsigned();
            $table->foreignId('category_id')->nullable()->constrained('image_categories')->nullOnDelete();
            $table->foreignId('owner_id')->constrained('users')->onDelete('cascade');
            $table->tinyText('image_hash');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('image_tags' , function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('owner_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });


        Schema::create('album_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('album_id')->constrained('albums')->onDelete('cascade');
            $table->foreignUuid('image_uuid')->constrained('images', 'uuid')->onDelete('cascade');
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
        Schema::dropIfExists('album_images');
        Schema::dropIfExists('image_tags');
        Schema::dropIfExists('images');
        Schema::dropIfExists('image_categories');
        Schema::dropIfExists('albums');
    }
};
