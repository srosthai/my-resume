<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('about_me', function (Blueprint $table) {
            $table->renameColumn('fucus_on', 'focus_on');
        });
    }

    public function down(): void
    {
        Schema::table('about_me', function (Blueprint $table) {
            $table->renameColumn('focus_on', 'fucus_on');
        });
    }
};
