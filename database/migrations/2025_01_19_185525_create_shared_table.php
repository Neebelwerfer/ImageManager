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
        Schema::create('shared_collections', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['album', 'category']);
            $table->integer('resource_id');
            $table->foreignId('shared_by_user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('shared_with_user_id')->constrained('users')->onDelete('cascade');
            $table->enum('level', ['view', 'edit']);
            $table->timestamps();
        });

        Schema::create('shared_images', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('image_uuid')->constrained('images', 'uuid')->cascadeOnDelete();
            $table->foreignId('shared_by_user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('shared_with_user_id')->constrained('users')->onDelete('cascade');
            $table->enum('level', ['view', 'edit']);
            $table->timestamps();
        });

        Schema::create('shared_source', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shared_image')->constrained('shared_images')->cascadeOnDelete();
            $table->foreignId('shared_by_user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('source', ['image', 'category']);
            $table->unique(['shared_image', 'shared_by_user_id', 'source']);
        });

        Schema::table('images', function (Blueprint $table) {
            $table->boolean('is_shared')->default(false);
        });

        Schema::table('albums', function (Blueprint $table) {
            $table->boolean('is_shared')->default(false);
        });

        Schema::table('image_categories', function (Blueprint $table) {
            $table->boolean('is_shared')->default(false);
        });

        Schema::table('image_tags', function (Blueprint $table) {
            $table->foreignId('shared_image')->nullable()->constrained('shared_images')->cascadeOnDelete();
        });

        Schema::table('album_images', function (Blueprint $table) {
            $table->foreignId('shared_image')->nullable()->constrained('shared_images')->cascadeOnDelete();
        });

        Schema::table('image_traits', function (Blueprint $table) {
            $table->foreignId('shared_image')->nullable()->constrained('shared_images')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shared_source');
        Schema::dropIfExists('shared_collections');
        Schema::dropIfExists('shared_images');
        Schema::table('images', function (Blueprint $table) {
            $table->dropColumn('is_shared');
        });
        Schema::table('albums', function (Blueprint $table) {
            $table->dropColumn('is_shared');
        });
        Schema::table('image_categories', function (Blueprint $table) {
            $table->dropColumn('is_shared');
        });
        Schema::table('image_tags', function (Blueprint $table) {
            $table->dropConstrainedForeignId('shared_image');
        });

        Schema::table('album_images', function (Blueprint $table) {
            $table->dropConstrainedForeignId('shared_image');
        });

        Schema::table('image_traits', function (Blueprint $table) {
            $table->dropConstrainedForeignId('shared_image');
        });
    }
};
