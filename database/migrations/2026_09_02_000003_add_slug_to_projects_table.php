<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->string('slug')->nullable()->unique()->after('title');
        });

        // Backfill existing rows with a unique slug derived from the title.
        $taken = [];
        foreach (DB::table('projects')->orderBy('id')->get(['id', 'title']) as $row) {
            $base = Str::slug((string) $row->title) ?: 'project-'.$row->id;
            $slug = $base;
            $i = 1;
            while (in_array($slug, $taken, true)) {
                $slug = $base.'-'.$i++;
            }
            $taken[] = $slug;
            DB::table('projects')->where('id', $row->id)->update(['slug' => $slug]);
        }
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->dropColumn('slug');
        });
    }
};
