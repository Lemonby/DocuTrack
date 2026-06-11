<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TahapanPencairan extends Model
{
    protected $primaryKey = 'tahapan_id';

    public $timestamps = false;

    const UPDATED_AT = null;

    const CREATED_AT = 'created_at';

    protected $fillable = [
        'kegiatan_id', 'tgl_pencairan', 'termin', 'nominal', 'catatan', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'tgl_pencairan' => 'date',
            'nominal' => 'decimal:2',
        ];
    }

    public function kegiatan()
    {
        return $this->belongsTo(Kegiatan::class, 'kegiatan_id');
    }
}
