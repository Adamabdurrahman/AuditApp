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
        if (! Schema::hasTable('findlossdetail')) {
            return;
        }

        Schema::table('findlossdetail', function (Blueprint $table) {
            if (! Schema::hasColumn('findlossdetail', 'paid_amount')) {
                $table->decimal('paid_amount', 18, 2)->default(0)->after('nilai');
            }

            if (! Schema::hasColumn('findlossdetail', 'recorded_at')) {
                $table->timestamp('recorded_at')->nullable()->after('paid_amount');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('findlossdetail')) {
            return;
        }

        Schema::table('findlossdetail', function (Blueprint $table) {
            if (Schema::hasColumn('findlossdetail', 'paid_amount')) {
                $table->dropColumn('paid_amount');
            }

            if (Schema::hasColumn('findlossdetail', 'recorded_at')) {
                $table->dropColumn('recorded_at');
            }
        });
    }
};
