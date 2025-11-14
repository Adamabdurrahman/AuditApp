<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LegacyCsvSeeder extends Seeder
{
    public function run(): void
{
    $basePath = base_path('databases');

    // Master & lookup dulu
    $this->seedFromCsv($basePath . '/roles.csv', 'roles');
    $this->seedFromCsv($basePath . '/users.csv', 'users');
    $this->seedFromCsv($basePath . '/kategori.csv', 'kategori');
    $this->seedFromCsv($basePath . '/priority.csv', 'priority');
    $this->seedFromCsv($basePath . '/status.csv', 'status');
    $this->seedFromCsv($basePath . '/subkategori.csv', 'subkategori');
    $this->seedFromCsv($basePath . '/reminder.csv', 'reminder');

    // Audit_plans dulu, lalu ambil daftar ID valid
    $this->seedFromCsv($basePath . '/audit_plans.csv', 'audit_plans');
    $validPlanIds = DB::table('audit_plans')->pluck('id')->all();

    $this->seedFromCsv($basePath . '/audit_report_templates.csv', 'audit_report_templates');
    $this->seedFromCsv($basePath . '/audit_finding_plans.csv', 'audit_finding_plans');

    // AUDITFORM: normalisasi semua FK
    $this->seedFromCsv($basePath . '/auditform.csv', 'auditform', function (array $row) use ($validPlanIds) {
        // audit_plan_id → null kalau tidak ada parent-nya
        if (! empty($row['audit_plan_id'])) {
            $planId = (int) $row['audit_plan_id'];

            if (! in_array($planId, $validPlanIds, true)) {
                $row['audit_plan_id'] = null;
            } else {
                $row['audit_plan_id'] = $planId;
            }
        } else {
            $row['audit_plan_id'] = null;
        }

        // Bersihkan FK lain yang berisi '?', teks, dll.
        foreach (['kategori_id', 'priority_id', 'subkategori_id', 'reminder_id', 'status_id', 'auditor'] as $col) {
            if (! array_key_exists($col, $row)) {
                continue;
            }

            $val = $row[$col];

            if ($val === null || $val === '') {
                $row[$col] = null;
                continue;
            }

            if (! is_numeric($val)) {
                $row[$col] = null;
                continue;
            }

            $row[$col] = (int) $val;
        }

        return $row;
    });

    // Tabel-tabel lain
    $this->seedFromCsv($basePath . '/assessment.csv', 'assessment');
    $this->seedFromCsv($basePath . '/recovery.csv', 'recovery');
    $this->seedFromCsv($basePath . '/findlossdetail.csv', 'findlossdetail');
    $this->seedFromCsv($basePath . '/findloss_recoveries.csv', 'findloss_recoveries');
    $this->seedFromCsv($basePath . '/fileattachment.csv', 'fileattachment');
    $this->seedFromCsv($basePath . '/notificationstype.csv', 'notificationstype');
    $this->seedFromCsv($basePath . '/notifications.csv', 'notifications');
    }

    protected function seedFromCsv(string $path, string $table, ?callable $rowTransformer = null): void
    {
        if (! file_exists($path)) {
            return;
        }

        if (($handle = fopen($path, 'r')) === false) {
            return;
        }

        $header = fgetcsv($handle, 0, ',');
        if (! $header) {
            fclose($handle);
            return;
        }

        $header = array_map(function ($value) {
            return trim($value, " \t\n\r\0\x0B\"'");
        }, $header);

        $rows = [];

        while (($row = fgetcsv($handle, 0, ',')) !== false) {
            // Skip empty lines
            if (count(array_filter($row, fn ($v) => $v !== null && $v !== '')) === 0) {
                continue;
            }

            $assoc = [];
            foreach ($header as $index => $column) {
                $value = $row[$index] ?? null;

                if ($value === '') {
                    $value = null;
                }

                if (is_string($value) && strtoupper($value) === 'NULL') {
                    $value = null;
                }

                $assoc[$column] = $value;
            }

            if ($rowTransformer) {
                $assoc = $rowTransformer($assoc);

                if ($assoc === null) {
                    continue;
                }
            }

            $rows[] = $assoc;
        }

        fclose($handle);

        if (! empty($rows)) {
            DB::table($table)->insert($rows);
        }
    }
}
