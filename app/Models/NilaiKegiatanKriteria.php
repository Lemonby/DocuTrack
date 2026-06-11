<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NilaiKegiatanKriteria extends Model
{
    protected $table = 'nilai_kegiatan_kriterias';

    protected $fillable = [
        'kegiatan_id',
        'kriteria_id',
        'nilai_mentah',
    ];

    protected $casts = [
        'nilai_mentah' => 'float',
    ];

    /**
     * Relasi ke Kegiatan.
     */
    public function kegiatan()
    {
        return $this->belongsTo(Kegiatan::class, 'kegiatan_id', 'kegiatan_id');
    }

    /**
     * Relasi ke Kriteria.
     */
    public function kriteria()
    {
        return $this->belongsTo(Kriteria::class, 'kriteria_id', 'kriteria_id');
    }
}
