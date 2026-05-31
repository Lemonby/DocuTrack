<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Iku extends Model
{
    protected $primaryKey = 'id';
    protected $table = 'ikus';
    protected $fillable = ['kode_iku', 'indikator_kinerja', 'deskripsi', 'target', 'realisasi', 'tahun'];

    protected function casts(): array
    {
        return ['tahun' => 'integer'];
    }
}
