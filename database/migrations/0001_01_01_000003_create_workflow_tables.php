<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('progress_histories', function (Blueprint $table) {
            $table->id('progress_history_id');
            $table->unsignedBigInteger('kegiatan_id');
            $table->unsignedBigInteger('status_id');
            $table->unsignedBigInteger('changed_by_user_id')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('kegiatan_id')->references('kegiatan_id')->on('kegiatans')->cascadeOnDelete();
            $table->foreign('status_id')->references('status_id')->on('status_utamas')->cascadeOnUpdate();
            $table->foreign('changed_by_user_id')->references('user_id')->on('users')->cascadeOnUpdate();
            $table->index(['kegiatan_id', 'created_at']);
        });

        Schema::create('revisi_comments', function (Blueprint $table) {
            $table->id('revisi_comment_id');
            $table->unsignedBigInteger('progress_history_id');
            $table->text('komentar_revisi')->nullable();
            $table->string('target_tabel', 100)->nullable();
            $table->string('target_kolom', 100)->nullable();

            $table->foreign('progress_history_id')->references('progress_history_id')->on('progress_histories')->cascadeOnDelete();
        });

        Schema::create('lpjs', function (Blueprint $table) {
            $table->id('lpj_id');
            $table->unsignedBigInteger('kegiatan_id')->unique();
            $table->decimal('grand_total_realisasi', 15, 2)->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->date('tenggat_lpj')->nullable();
            $table->unsignedBigInteger('status_id')->default(1);
            $table->text('komentar_penolakan')->nullable();
            $table->text('komentar_revisi')->nullable();

            $table->foreign('kegiatan_id')->references('kegiatan_id')->on('kegiatans')->cascadeOnDelete();
            $table->foreign('status_id')->references('status_id')->on('status_utamas')->cascadeOnUpdate();
            $table->index(['status_id', 'tenggat_lpj']);
        });

        Schema::create('lpj_items', function (Blueprint $table) {
            $table->id('lpj_item_id');
            $table->unsignedBigInteger('lpj_id');
            $table->unsignedBigInteger('kategori_id')->nullable();
            $table->string('jenis_belanja', 100)->nullable();
            $table->text('uraian')->nullable();
            $table->text('rincian')->nullable();
            $table->decimal('total_harga', 15, 2)->nullable();
            $table->decimal('realisasi', 15, 2)->nullable();
            $table->decimal('sub_total', 15, 2)->nullable();
            $table->string('file_bukti', 255)->nullable();
            $table->text('komentar')->nullable();
            $table->string('sat1', 50)->nullable();
            $table->string('sat2', 50)->nullable();
            $table->decimal('vol1', 10, 2)->nullable();
            $table->decimal('vol2', 10, 2)->nullable();
            $table->decimal('harga', 15, 2)->nullable();
            $table->timestamps();

            $table->foreign('lpj_id')->references('lpj_id')->on('lpjs')->cascadeOnDelete();
            $table->foreign('kategori_id')->references('kategori_rab_id')->on('kategori_rabs')->nullOnDelete()->cascadeOnUpdate();
        });

        Schema::create('tahapan_pencairans', function (Blueprint $table) {
            $table->id('tahapan_id');
            $table->unsignedBigInteger('kegiatan_id');
            $table->date('tgl_pencairan');
            $table->string('termin', 100);
            $table->decimal('nominal', 15, 2);
            $table->text('catatan')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('kegiatan_id')->references('kegiatan_id')->on('kegiatans')->cascadeOnDelete()->cascadeOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tahapan_pencairans');
        Schema::dropIfExists('lpj_items');
        Schema::dropIfExists('lpjs');
        Schema::dropIfExists('revisi_comments');
        Schema::dropIfExists('progress_histories');
    }
};
