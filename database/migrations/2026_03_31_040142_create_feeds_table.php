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
        Schema::create('feeds', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->text('body');
            $table->json('images')->nullable();
            $table->string('location')->nullable();
            $table->string('mood')->nullable();
            $table->string('activity_type')->nullable();
            $table->json('tags')->nullable();
            $table->string('slug')->unique();
            $table->enum('visibility', ['public', 'private'])->default('public');
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->integer('likes_count')->default(0);
            $table->integer('views')->default(0);
            $table->boolean('is_pinned')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'visibility', 'published_at']);
            $table->index('activity_type');
            $table->index('is_pinned');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feeds');
    }
};
