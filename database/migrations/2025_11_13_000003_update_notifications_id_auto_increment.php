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

        if (! Schema::hasTable('notifications')) {
            return;
        }
        if (! Schema::hasTable('notificationstype')) {
            return;
        }

        $column = collect(DB::select("SHOW COLUMNS FROM notifications WHERE Field = 'id'"))->first();
        $column2 = collect(DB::select("SHOW COLUMNS FROM notificationstype WHERE Field = 'id'"))->first();

        if (! $column) {
            DB::statement('ALTER TABLE notifications ADD COLUMN id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST');
            return;
        }

        if (! $column2) {
            DB::statement('ALTER TABLE notificationstype ADD COLUMN id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST');
            return;
        }

        $extra = $column->Extra ?? '';
        $extra2 = $column2->Extra ?? '';
        $key = $column->Key ?? '';
        $key2 = $column2->Key ?? '';

        if ($key !== 'PRI') {
            DB::statement('ALTER TABLE notifications ADD PRIMARY KEY (id)');
        }
        if ($key2 !== 'PRI') {
            DB::statement('ALTER TABLE notificationstype ADD PRIMARY KEY (id)');
        }

        if (stripos($extra, 'auto_increment') === false) {
            DB::statement('ALTER TABLE notifications MODIFY id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT');
        }
        if (stripos($extra2, 'auto_increment') === false) {
            DB::statement('ALTER TABLE notificationstype MODIFY id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT');
        }
    }

    public function down(): void
    {
        // Dibiarkan kosong untuk menjaga konsistensi data.
    }
};
