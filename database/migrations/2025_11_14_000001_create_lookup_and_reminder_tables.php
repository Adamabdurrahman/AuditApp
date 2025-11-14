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
        if (! Schema::hasTable('kategori')) {
            Schema::create('kategori', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('name');
            });
        }

        if (! Schema::hasTable('priority')) {
            Schema::create('priority', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('name');
            });
        }

        if (! Schema::hasTable('status')) {
            Schema::create('status', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('status');
            });
        }

        if (! Schema::hasTable('subkategori')) {
            Schema::create('subkategori', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('name');
            });
        }

        if (! Schema::hasTable('reminder')) {
            Schema::create('reminder', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('pt')->nullable();
                $table->string('nama')->nullable();
                $table->string('email')->nullable();
            });
        }

        if (! Schema::hasTable('audit_report_templates')) {
            Schema::create('audit_report_templates', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('title');
                $table->year('year');
                $table->text('description')->nullable();
                $table->string('status')->default('draft');
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('notificationstype')) {
            Schema::create('notificationstype', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('name');
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('audit_finding_plans')) {
            Schema::create('audit_finding_plans', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('audit_report_template_id')->nullable();
                $table->string('title');
                $table->unsignedBigInteger('kategori_id')->nullable();
                $table->unsignedBigInteger('priority_id')->nullable();
                $table->string('sub_category')->nullable();
                $table->date('start_date')->nullable();
                $table->date('due_date')->nullable();
                $table->text('notes')->nullable();
                $table->string('status')->default('planned');
                $table->timestamps();

                $table->foreign('audit_report_template_id')
                    ->references('id')
                    ->on('audit_report_templates')
                    ->nullOnDelete();

                $table->foreign('kategori_id')
                    ->references('id')
                    ->on('kategori')
                    ->nullOnDelete();

                $table->foreign('priority_id')
                    ->references('id')
                    ->on('priority')
                    ->nullOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_finding_plans');
        Schema::dropIfExists('notificationstype');
        Schema::dropIfExists('audit_report_templates');
        Schema::dropIfExists('reminder');
        Schema::dropIfExists('subkategori');
        Schema::dropIfExists('status');
        Schema::dropIfExists('priority');
        Schema::dropIfExists('kategori');
    }
};
