<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! in_array(DB::connection()->getDriverName(), ['mysql', 'mariadb'], true)) {
            return;
        }

        if (! Schema::hasTable('auditform')) {
            return;
        }

        $column = collect(DB::select("SHOW COLUMNS FROM auditform WHERE Field = 'id'"))->first();

        if (! $column) {
            DB::statement('ALTER TABLE auditform ADD COLUMN id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST');
            return;
        }

        $extra = $column->Extra ?? '';

        if (stripos($extra, 'auto_increment') === false) {
            DB::statement('ALTER TABLE auditform MODIFY id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT');
        }
    }

    public function down(): void
    {
        // Tidak mengubah kembali agar tidak merusak data existing.
    }
};
