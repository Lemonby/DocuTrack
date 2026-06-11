<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kriteria extends Model
{
    protected $table = 'kriterias';
    protected $primaryKey = 'kriteria_id';

    protected $fillable = [
        'kode_kriteria',
        'nama_kriteria',
        'bobot',
        'tipe',
    ];

    protected $casts = [
        'bobot' => 'float',
    ];

    /**
     * Relasi ke nilai kegiatan per kriteria.
     */
    public function nilaiKegiatans()
    {
        return $this->hasMany(NilaiKegiatanKriteria::class, 'kriteria_id', 'kriteria_id');
    }
}
