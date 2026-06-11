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
        Schema::create('nilai_kegiatan_kriterias', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('kegiatan_id');
            $table->unsignedBigInteger('kriteria_id');
            $table->decimal('nilai_mentah', 5, 4);
            $table->timestamps();

            $table->foreign('kegiatan_id')->references('kegiatan_id')->on('kegiatans')->cascadeOnDelete();
            $table->foreign('kriteria_id')->references('kriteria_id')->on('kriterias')->cascadeOnDelete();
            
            $table->unique(['kegiatan_id', 'kriteria_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nilai_kegiatan_kriterias');
    }
};
