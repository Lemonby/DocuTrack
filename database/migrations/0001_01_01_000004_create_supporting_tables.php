<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('log_statuses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('tipe_log', 50);
            $table->unsignedBigInteger('id_referensi')->nullable();
            $table->string('status', 20)->default('BELUM_DIBACA');
            $table->json('konten_json')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('user_id')->references('user_id')->on('users')->cascadeOnDelete();
            $table->index(['user_id', 'status']);
            $table->index('tipe_log');
        });

        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id('log_id');
            $table->unsignedBigInteger('user_id');
            $table->string('action', 50);
            $table->enum('category', ['authentication', 'workflow', 'document', 'financial', 'user_management', 'security'])->default('workflow');
            $table->string('entity_type', 50)->nullable();
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->text('description')->nullable();
            $table->json('old_value')->nullable();
            $table->json('new_value')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 500)->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['user_id', 'action']);
            $table->index('category');
            $table->index(['entity_type', 'entity_id']);
        });

        Schema::create('ikus', function (Blueprint $table) {
            $table->id();
            $table->string('kode_iku', 50)->nullable();
            $table->string('indikator_kinerja', 255);
            $table->text('deskripsi')->nullable();
            $table->string('target', 100)->nullable();
            $table->string('realisasi', 100)->nullable();
            $table->year('tahun')->nullable();
            $table->timestamps();
        });

        Schema::create('ai_log_summaries', function (Blueprint $table) {
            $table->id();
            $table->text('summary_text');
            $table->integer('error_count');
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('ai_security_alerts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('ip_address', 255);
            $table->text('input_payload');
            $table->enum('severity', ['low', 'medium', 'high']);
            $table->string('detection_type', 255);
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_security_alerts');
        Schema::dropIfExists('ai_log_summaries');
        Schema::dropIfExists('ikus');
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('log_statuses');
    }
};
