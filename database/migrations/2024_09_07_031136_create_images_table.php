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
            $table->smallInteger('width', false, true);
            $table->smallInteger('height', false, true);
            $table->foreignId('category_id')->nullable()->constrained('image_categories')->nullOnDelete();
            $table->foreignId('owner_id')->constrained('users')->onDelete('cascade');
            $table->text('image_hash');
            $table->tinyText('format');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('traits', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('min')->nullable();
            $table->integer('max')->nullable();
            $table->string('default');
            $table->boolean('global')->default(false);
            $table->foreignId('owner_id')->constrained('users')->onDelete('cascade');
            $table->enum('type', ['integer', 'float', 'text', 'boolean', 'date']);
        });

        Schema::create('image_traits', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('image_uuid')->constrained('images', 'uuid')->onDelete('cascade');
            $table->foreignId('trait_id')->constrained('traits')->onDelete('cascade');
            $table->foreignId('owner_id')->constrained('users')->onDelete('cascade');
            $table->unique(['image_uuid', 'trait_id', 'owner_id']);
            $table->string('value');
            $table->timestamps();
        });

        Schema::create('tags' , function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        Schema::create('album_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('album_id')->constrained('albums')->onDelete('cascade');
            $table->foreignUuid('image_uuid')->constrained('images', 'uuid')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('image_tags', function (Blueprint $table) {
            $table->foreignUuid('image_uuid')->constrained('images', 'uuid')->onDelete('cascade');
            $table->foreignId('tags_id')->constrained('tags')->onDelete('cascade');
            $table->foreignId('added_by')->constrained('users')->cascadeOnDelete();
            $table->boolean('personal')->default(true);
            $table->primary(['image_uuid', 'tags_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('image_tags');
        Schema::dropIfExists('album_images');
        Schema::dropIfExists('tags');
        Schema::dropIfExists('images');
        Schema::dropIfExists('image_traits');
        Schema::dropIfExists('traits');
        Schema::dropIfExists('image_categories');
        Schema::dropIfExists('albums');
    }
};
