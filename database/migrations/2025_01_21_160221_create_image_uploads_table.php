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
        Schema::create('image_uploads', function (Blueprint $table) {
            $table->uuid()->primary();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('extension');
            $table->enum('state', ['waiting', 'scanning', 'foundDuplicates', 'processing', 'error', 'done'])->default('waiting');
            $table->text('hash');
            $table->json('duplicates')->nullable();
            $table->json('data')->nullable();
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
    }
};
