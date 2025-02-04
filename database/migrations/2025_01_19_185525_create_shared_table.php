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
        Schema::create('shared_resources', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['image', 'album', 'category']);
            $table->integer('resource_id')->nullable();
            $table->uuid('resource_uuid')->nullable();
            $table->foreignId('shared_by_user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('shared_with_user_id')->constrained('users')->onDelete('cascade');
            $table->enum('level', ['view', 'edit']);
            $table->timestamps();
        });

        Schema::create('shared_audit_log', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('resource_id')->constrained('shared_resources')->onDelete('cascade');
            $table->string('action');
            $table->timestamps();
        });

        // Schema::table('images', function (Blueprint $table) {
        //     $table->boolean('is_shared')->default(false);
        // });

        // Schema::table('albums', function (Blueprint $table) {
        //     $table->boolean('is_shared')->default(false);
        // });

        // Schema::table('image_categories', function (Blueprint $table) {
        //     $table->boolean('is_shared')->default(false);
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shared_audit_log');
        Schema::dropIfExists('shared_resources');
        Schema::table('images', function (Blueprint $table) {
            $table->dropColumn('is_shared');
        });
        Schema::table('albums', function (Blueprint $table) {
            $table->dropColumn('is_shared');
        });
        Schema::table('image_categories', function (Blueprint $table) {
            $table->dropColumn('is_shared');
        });
    }
};
