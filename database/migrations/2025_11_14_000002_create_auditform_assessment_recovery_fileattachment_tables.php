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
        if (! Schema::hasTable('auditform')) {
            Schema::create('auditform', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('judul_temuan');
                $table->string('pic')->nullable();
                $table->unsignedBigInteger('auditor')->nullable();
                $table->text('temuan_audit')->nullable();
                $table->unsignedBigInteger('kategori_id')->nullable();
                $table->unsignedBigInteger('priority_id')->nullable();
                $table->unsignedBigInteger('subkategori_id')->nullable();
                $table->date('tanggal_temuan')->nullable();
                $table->date('start_date')->nullable();
                $table->date('due_date')->nullable();
                $table->unsignedBigInteger('reminder_id')->nullable();
                $table->text('rekomendasi_author')->nullable();
                $table->text('catatan_tambahan')->nullable();
                $table->unsignedBigInteger('status_id')->nullable();
                $table->unsignedBigInteger('audit_plan_id')->nullable();

                $table->foreign('auditor')
                    ->references('id')
                    ->on('users')
                    ->nullOnDelete();

                $table->foreign('kategori_id')
                    ->references('id')
                    ->on('kategori')
                    ->nullOnDelete();

                $table->foreign('priority_id')
                    ->references('id')
                    ->on('priority')
                    ->nullOnDelete();

                $table->foreign('subkategori_id')
                    ->references('id')
                    ->on('subkategori')
                    ->nullOnDelete();

                $table->foreign('reminder_id')
                    ->references('id')
                    ->on('reminder')
                    ->nullOnDelete();

                $table->foreign('status_id')
                    ->references('id')
                    ->on('status')
                    ->nullOnDelete();

                $table->foreign('audit_plan_id')
                    ->references('id')
                    ->on('audit_plans')
                    ->nullOnDelete();
            });
        }

        if (! Schema::hasTable('assessment')) {
            Schema::create('assessment', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('audit_form_id');
                $table->string('type')->nullable();
                $table->string('title')->nullable();
                $table->text('description')->nullable();
                $table->date('test_date')->nullable();
                $table->text('testing_performed')->nullable();
                $table->timestamps();

                $table->foreign('audit_form_id')
                    ->references('id')
                    ->on('auditform')
                    ->cascadeOnDelete();
            });
        }

        if (! Schema::hasTable('recovery')) {
            Schema::create('recovery', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('item');
                $table->decimal('nilai', 18, 2)->default(0);
                $table->unsignedBigInteger('assessment_id');

                $table->foreign('assessment_id')
                    ->references('id')
                    ->on('assessment')
                    ->cascadeOnDelete();
            });
        }

        if (! Schema::hasTable('fileattachment')) {
            Schema::create('fileattachment', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('auditform_id')->nullable();
                $table->string('file_path');
                $table->string('file_name');
                $table->string('file_type')->nullable();
                $table->integer('file_size')->nullable();

                $table->foreign('auditform_id')
                    ->references('id')
                    ->on('auditform')
                    ->cascadeOnDelete();
            });
        }

        if (! Schema::hasTable('findlossdetail')) {
            Schema::create('findlossdetail', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('item');
                $table->decimal('nilai', 18, 2)->default(0);
                $table->decimal('nilai_usd', 18, 2)->default(0);
                $table->decimal('paid_amount', 18, 2)->default(0);
                $table->decimal('paid_amount_usd', 18, 2)->default(0);
                $table->timestamp('recorded_at')->nullable();
                $table->unsignedBigInteger('audit_form_id');

                $table->foreign('audit_form_id')
                    ->references('id')
                    ->on('auditform')
                    ->cascadeOnDelete();
            });
        }

        if (! Schema::hasTable('findloss_recoveries')) {
            Schema::create('findloss_recoveries', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('findlossdetail_id');
                $table->decimal('amount', 18, 2);
                $table->decimal('amount_usd', 18, 2)->default(0);
                $table->date('recorded_at')->nullable();
                $table->string('notes')->nullable();
                $table->timestamps();

                $table->foreign('findlossdetail_id')
                    ->references('id')
                    ->on('findlossdetail')
                    ->cascadeOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('findlossdetail');
        Schema::dropIfExists('fileattachment');
        Schema::dropIfExists('recovery');
        Schema::dropIfExists('assessment');
        Schema::dropIfExists('auditform');
    }
};
