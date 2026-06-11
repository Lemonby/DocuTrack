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
        Schema::create('kriterias', function (Blueprint $table) {
            $table->id('kriteria_id');
            $table->string('kode_kriteria', 10)->unique();
            $table->string('nama_kriteria', 150);
            $table->decimal('bobot', 5, 4);
            $table->enum('tipe', ['benefit', 'cost'])->default('benefit');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kriterias');
    }
};
