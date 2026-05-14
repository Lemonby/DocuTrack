<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rab extends Model
{
    protected $primaryKey = 'rab_item_id';
    public $timestamps = false;

    protected $fillable = [
        'kak_id', 'kategori_id', 'uraian', 'rincian',
        'sat1', 'sat2', 'vol1', 'vol2', 'harga', 'total_harga', 'subtotal',
    ];

    protected function casts(): array
    {
        return [
            'vol1' => 'decimal:2', 'vol2' => 'decimal:2',
            'harga' => 'decimal:2', 'total_harga' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        // Auto-calculate total_harga on creating/updating
        static::saving(function (Rab $rab) {
            $rab->total_harga = $rab->vol1 * $rab->vol2 * $rab->harga;
        });
    }

    public function kak()
    {
        return $this->belongsTo(Kak::class, 'kak_id');
    }

    public function kategori()
    {
        return $this->belongsTo(KategoriRab::class, 'kategori_id', 'kategori_rab_id');
    }
}
