<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_owner')->default(false)->after('email_verified_at')->index();
        });

        // The earliest account is the real owner; anything created later is not.
        $ownerId = DB::table('users')->orderBy('id')->value('id');

        if ($ownerId !== null) {
            DB::table('users')->where('id', $ownerId)->update(['is_owner' => true]);
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_owner');
        });
    }
};
