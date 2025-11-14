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

        if (! Schema::hasTable('reminder')) {
            return;
        }

        $column = collect(DB::select("SHOW COLUMNS FROM reminder WHERE Field = 'id'"))->first();

        if (! $column) {
            DB::statement('ALTER TABLE reminder ADD COLUMN id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST');
            return;
        }

        $extra = $column->Extra ?? '';
        $key = $column->Key ?? '';

        if (stripos($extra, 'auto_increment') === false || strtoupper($key) !== 'PRI') {
            DB::statement('ALTER TABLE reminder MODIFY id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY');
        }
    }

    public function down(): void
    {
        // Dibiarkan kosong karena mengembalikan ke kondisi sebelumnya berpotensi
        // merusak relasi yang sudah terbentuk dengan tabel lain.
    }
};
