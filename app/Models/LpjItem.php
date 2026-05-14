<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LpjItem extends Model
{
    protected $primaryKey = 'lpj_item_id';

    protected $fillable = [
        'lpj_id', 'kategori_id', 'jenis_belanja', 'uraian', 'rincian',
        'total_harga', 'realisasi', 'sub_total', 'file_bukti', 'komentar',
        'sat1', 'sat2', 'vol1', 'vol2', 'harga',
    ];

    protected function casts(): array
    {
        return [
            'total_harga' => 'decimal:2', 'realisasi' => 'decimal:2',
            'harga' => 'decimal:2', 'vol1' => 'decimal:2', 'vol2' => 'decimal:2',
        ];
    }

    public function lpj()
    {
        return $this->belongsTo(Lpj::class, 'lpj_id');
    }

    public function kategori()
    {
        return $this->belongsTo(KategoriRab::class, 'kategori_id', 'kategori_rab_id');
    }
}
