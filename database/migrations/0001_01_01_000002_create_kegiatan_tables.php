<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kegiatans', function (Blueprint $table) {
            $table->id('kegiatan_id');
            $table->string('nama_kegiatan', 255);
            $table->string('prodi_penyelenggara', 50)->nullable();
            $table->string('pemilik_kegiatan', 150)->nullable();
            $table->string('nim_pelaksana', 20)->nullable();
            $table->string('nip', 30)->nullable();
            $table->string('nama_pj', 100)->nullable();
            $table->string('bukti_mak', 255)->nullable();
            $table->unsignedBigInteger('user_id');
            $table->string('jurusan_penyelenggara', 50)->nullable();
            $table->unsignedBigInteger('status_utama_id')->default(1);
            $table->unsignedBigInteger('wadir_tujuan');
            $table->string('surat_pengantar', 255)->nullable();
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();
            $table->unsignedTinyInteger('posisi_id')->default(1)->comment('1=Admin,2=Verifikator,3=Wadir,4=PPK,5=Bendahara');
            $table->dateTime('tanggal_pencairan')->nullable();
            $table->decimal('jumlah_dicairkan', 15, 2)->nullable();
            $table->decimal('dana_di_setujui', 15, 2)->nullable();
            $table->string('metode_pencairan', 50)->nullable();
            $table->text('catatan_bendahara')->nullable();
            $table->text('pencairan_tahap_json')->nullable();
            $table->text('umpan_balik_verifikator')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('user_id')->on('users')->cascadeOnUpdate();
            $table->foreign('status_utama_id')->references('status_id')->on('status_utamas')->cascadeOnUpdate();
            $table->foreign('jurusan_penyelenggara')->references('nama_jurusan')->on('jurusans')->cascadeOnUpdate();
            $table->foreign('wadir_tujuan')->references('wadir_id')->on('wadirs')->cascadeOnUpdate();

            $table->index(['posisi_id', 'status_utama_id', 'created_at'], 'idx_workflow_position');
            $table->index(['user_id', 'status_utama_id'], 'idx_user_status');
            $table->index(['jurusan_penyelenggara', 'status_utama_id'], 'idx_jurusan_status');
        });

        Schema::create('kaks', function (Blueprint $table) {
            $table->id('kak_id');
            $table->unsignedBigInteger('kegiatan_id');
            $table->text('penerima_manfaat')->nullable();
            $table->text('gambaran_umum')->nullable();
            $table->text('metode_pelaksanaan')->nullable();
            $table->date('tgl_pembuatan')->nullable();

            $table->foreign('kegiatan_id')->references('kegiatan_id')->on('kegiatans')->cascadeOnDelete();
        });

        Schema::create('indikator_kaks', function (Blueprint $table) {
            $table->id('indikator_id');
            $table->unsignedBigInteger('kak_id');
            $table->unsignedTinyInteger('bulan')->nullable()->comment('1-12');
            $table->string('indikator_keberhasilan', 250)->nullable();
            $table->unsignedTinyInteger('target_persen')->nullable();

            $table->foreign('kak_id')->references('kak_id')->on('kaks')->cascadeOnDelete();
        });

        Schema::create('tahapan_pelaksanaans', function (Blueprint $table) {
            $table->id('tahapan_id');
            $table->unsignedBigInteger('kak_id');
            $table->string('nama_tahapan', 255)->nullable();

            $table->foreign('kak_id')->references('kak_id')->on('kaks')->cascadeOnDelete();
        });

        Schema::create('rabs', function (Blueprint $table) {
            $table->id('rab_item_id');
            $table->unsignedBigInteger('kak_id');
            $table->unsignedBigInteger('kategori_id');
            $table->text('uraian')->nullable();
            $table->text('rincian')->nullable();
            $table->string('sat1', 50)->nullable();
            $table->string('sat2', 50)->nullable();
            $table->decimal('vol1', 10, 2);
            $table->decimal('vol2', 10, 2);
            $table->decimal('harga', 10, 2);
            $table->decimal('total_harga', 10, 2)->nullable();
            $table->decimal('subtotal', 10, 2)->nullable();

            $table->foreign('kak_id')->references('kak_id')->on('kaks')->cascadeOnDelete();
            $table->foreign('kategori_id')->references('kategori_rab_id')->on('kategori_rabs')->cascadeOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rabs');
        Schema::dropIfExists('tahapan_pelaksanaans');
        Schema::dropIfExists('indikator_kaks');
        Schema::dropIfExists('kaks');
        Schema::dropIfExists('kegiatans');
    }
};
