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
        Schema::create('uploads', function (Blueprint $table) {
            $table->ulid()->primary();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('state', ['uploading', 'waiting', 'processing', 'done'])->default('uploading');
            $table->string('active_upload_uuid')->nullable();
            $table->timestamps();
        });

        Schema::create('image_uploads', function (Blueprint $table) {
            $table->uuid()->primary();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignUlid('upload_ulid')->constrained('uploads', 'ulid')->cascadeOnDelete();
            $table->string('extension');
            $table->text('hash');
            $table->jsonb('duplicates')->nullable();
            $table->jsonb('data')->nullable();
            $table->timestamps();
        });

        Schema::create('upload_errors', function (Blueprint $table){
            $table->id();
            $table->foreignUuid('image_upload_uuid')->constrained('image_uploads', 'uuid')->cascadeOnDelete();
            $table->text('message')->default('');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('image_uploads');
        Schema::dropIfExists('upload_errors');
        Schema::dropIfExists('uploads');
    }
};
