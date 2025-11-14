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

        if (Schema::hasTable('findloss_recoveries')) {
            return;
        }

        Schema::create('findloss_recoveries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('findlossdetail_id');
            $table->decimal('amount', 18, 2);
            $table->date('recorded_at')->nullable();
            $table->string('notes')->nullable();
            $table->timestamps();

            $table->foreign('findlossdetail_id')
                ->references('id')
                ->on('findlossdetail')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('findloss_recoveries');
    }
};
