<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // The admin form validates description up to 1000 and address up to 500
        // characters, but both columns were VARCHAR(255).
        Schema::table('users', function (Blueprint $table) {
            $table->text('description')->nullable()->change();
            $table->text('address')->nullable()->change();
        });

        // Orphans left behind by deleted project types would violate the new FK.
        DB::table('projects')
            ->whereNotNull('project_type_id')
            ->whereNotIn('project_type_id', DB::table('project_types')->select('id'))
            ->update(['project_type_id' => null]);

        Schema::table('projects', function (Blueprint $table) {
            $table->foreign('project_type_id')->references('id')->on('project_types')->nullOnDelete();
            $table->index('created_date');
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropForeign(['project_type_id']);
            $table->dropIndex(['created_date']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('description')->nullable()->change();
            $table->string('address')->nullable()->change();
        });
    }
};
