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
        if (Schema::hasTable('findlossdetail')) {
            Schema::table('findlossdetail', function (Blueprint $table) {
                if (! Schema::hasColumn('findlossdetail', 'nilai_usd')) {
                    $table->decimal('nilai_usd', 18, 2)->default(0)->after('nilai');
                }

                if (! Schema::hasColumn('findlossdetail', 'paid_amount_usd')) {
                    $table->decimal('paid_amount_usd', 18, 2)->default(0)->after('paid_amount');
                }
            });
        }

        if (Schema::hasTable('findloss_recoveries')) {
            Schema::table('findloss_recoveries', function (Blueprint $table) {
                if (! Schema::hasColumn('findloss_recoveries', 'amount_usd')) {
                    $table->decimal('amount_usd', 18, 2)->default(0)->after('amount');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('findlossdetail')) {
            Schema::table('findlossdetail', function (Blueprint $table) {
                $columns = [];

                if (Schema::hasColumn('findlossdetail', 'nilai_usd')) {
                    $columns[] = 'nilai_usd';
                }

                if (Schema::hasColumn('findlossdetail', 'paid_amount_usd')) {
                    $columns[] = 'paid_amount_usd';
                }

                if (! empty($columns)) {
                    $table->dropColumn($columns);
                }
            });
        }

        if (Schema::hasTable('findloss_recoveries') && Schema::hasColumn('findloss_recoveries', 'amount_usd')) {
            Schema::table('findloss_recoveries', function (Blueprint $table) {
                $table->dropColumn('amount_usd');
            });
        }
    }
};
