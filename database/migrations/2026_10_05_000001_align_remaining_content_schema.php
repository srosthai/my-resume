<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->normalizeYears('work_experiences');
        $this->normalizeYears('educations');
        $this->toYearColumn('work_experiences', 'from');
        $this->toYearColumn('work_experiences', 'to');
        $this->toYearColumn('educations', 'from');
        $this->toYearColumn('educations', 'to');

        $tooLong = DB::table('popular_songs')->whereRaw('length(url) > 1000')->count();
        if ($tooLong > 0) {
            throw new RuntimeException('popular_songs.url has values longer than 1000 characters.');
        }

        Schema::table('popular_songs', function (Blueprint $table) {
            $table->string('url', 1000)->change();
        });

        $this->dropEnumCheck('projects', 'status');
        $this->dropEnumCheck('notes', 'status');
        $this->dropEnumCheck('feeds', 'status');
        $this->dropEnumCheck('feeds', 'visibility');

        Schema::table('projects', function (Blueprint $table) {
            $table->string('status', 32)->default('processing')->change();
        });

        Schema::table('notes', function (Blueprint $table) {
            $table->string('status', 32)->default('draft')->change();
        });

        Schema::table('feeds', function (Blueprint $table) {
            $table->string('status', 32)->default('draft')->change();
            $table->string('visibility', 32)->default('public')->change();
        });

        foreach (['projects', 'notes', 'feeds'] as $table) {
            Schema::table($table, function (Blueprint $blueprint) {
                $blueprint->softDeletes();
            });
        }
    }

    public function down(): void
    {
        foreach (['projects', 'notes', 'feeds'] as $table) {
            Schema::table($table, function (Blueprint $blueprint) {
                $blueprint->dropSoftDeletes();
            });
        }

        Schema::table('projects', function (Blueprint $table) {
            $table->enum('status', ['processing', 'completed'])->default('processing')->change();
        });

        Schema::table('notes', function (Blueprint $table) {
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft')->change();
        });

        Schema::table('feeds', function (Blueprint $table) {
            $table->enum('visibility', ['public', 'private'])->default('public')->change();
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft')->change();
        });

        Schema::table('popular_songs', function (Blueprint $table) {
            $table->longText('url')->change();
        });

        foreach (['work_experiences', 'educations'] as $table) {
            $this->toStringColumn($table, 'from');
            $this->toStringColumn($table, 'to');
        }
    }

    private function normalizeYears(string $table): void
    {
        foreach (DB::table($table)->select('id', 'from', 'to')->get() as $row) {
            DB::table($table)->where('id', $row->id)->update([
                'from' => $this->year($row->from),
                'to' => $this->year($row->to),
            ]);
        }
    }

    private function year(mixed $value): ?int
    {
        if ($value === null) {
            return null;
        }

        $text = trim((string) $value);

        if ($text === '' || strcasecmp($text, 'Present') === 0) {
            return null;
        }

        if (preg_match('/\b((?:19|20)\d{2})\b/', $text, $matches) === 1) {
            return (int) $matches[1];
        }

        return null;
    }

    private function toYearColumn(string $table, string $column): void
    {
        if (Schema::getConnection()->getDriverName() === 'pgsql') {
            DB::statement(sprintf(
                'alter table "%s" alter column "%s" type smallint using (case when "%s" ~ \'^[0-9]{4}$\' then "%s"::smallint else null end)',
                $table,
                $column,
                $column,
                $column,
            ));
            DB::statement(sprintf('alter table "%s" alter column "%s" drop not null', $table, $column));

            return;
        }

        Schema::table($table, function (Blueprint $blueprint) use ($column) {
            $blueprint->unsignedSmallInteger($column)->nullable()->change();
        });
    }

    private function toStringColumn(string $table, string $column): void
    {
        if (Schema::getConnection()->getDriverName() === 'pgsql') {
            DB::statement(sprintf(
                'alter table "%s" alter column "%s" type varchar(255) using "%s"::varchar',
                $table,
                $column,
                $column,
            ));

            return;
        }

        Schema::table($table, function (Blueprint $blueprint) use ($column) {
            $blueprint->string($column)->nullable()->change();
        });
    }

    /**
     * Laravel stores enum() as varchar plus a check constraint. Drop the
     * check so a new PHP enum case does not need another migration.
     */
    private function dropEnumCheck(string $table, string $column): void
    {
        if (Schema::getConnection()->getDriverName() !== 'pgsql') {
            return;
        }

        DB::statement(sprintf('alter table "%s" drop constraint if exists "%s_%s_check"', $table, $table, $column));

        $rows = DB::select(
            <<<'SQL'
            select con.conname as name
            from pg_constraint con
            inner join pg_class rel on rel.oid = con.conrelid
            inner join pg_namespace nsp on nsp.oid = rel.relnamespace
            where con.contype = 'c'
              and nsp.nspname = current_schema()
              and rel.relname = ?
              and pg_get_constraintdef(con.oid) ilike ?
            SQL,
            [$table, '%"'.$column.'"%'],
        );

        foreach ($rows as $row) {
            DB::statement(sprintf('alter table "%s" drop constraint if exists "%s"', $table, $row->name));
        }
    }
};
