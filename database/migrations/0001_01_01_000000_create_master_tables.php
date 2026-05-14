<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jurusans', function (Blueprint $table) {
            $table->string('nama_jurusan', 50)->primary();
        });

        Schema::create('prodis', function (Blueprint $table) {
            $table->string('nama_prodi', 50)->primary();
            $table->string('nama_jurusan', 50);
            $table->foreign('nama_jurusan')->references('nama_jurusan')->on('jurusans')->cascadeOnUpdate();
        });

        Schema::create('status_utamas', function (Blueprint $table) {
            $table->id('status_id');
            $table->string('nama_status_usulan', 100)->unique();
        });

        Schema::create('wadirs', function (Blueprint $table) {
            $table->id('wadir_id');
            $table->string('nama_wadir', 20)->nullable();
        });

        Schema::create('kategori_rabs', function (Blueprint $table) {
            $table->id('kategori_rab_id');
            $table->string('nama_kategori', 50)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kategori_rabs');
        Schema::dropIfExists('wadirs');
        Schema::dropIfExists('status_utamas');
        Schema::dropIfExists('prodis');
        Schema::dropIfExists('jurusans');
    }
};
