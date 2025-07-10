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
        Schema::create('popular_songs', function (Blueprint $table) {
            $table->id();
            $table->string('title')->comment('Title of the song');
            $table->string('artist')->comment('Artist of the song');
            $table->longText('url')->comment('URL to the song');
            $table->integer('duration')->comment('Duration of the song in seconds');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('popular_songs');
    }
};
