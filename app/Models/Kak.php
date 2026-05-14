<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kak extends Model
{
    protected $primaryKey = 'kak_id';
    public $timestamps = false;

    protected $fillable = [
        'kegiatan_id', 'iku', 'penerima_manfaat', 'gambaran_umum',
        'metode_pelaksanaan', 'tgl_pembuatan',
    ];

    protected function casts(): array
    {
        return ['tgl_pembuatan' => 'date'];
    }

    public function kegiatan()
    {
        return $this->belongsTo(Kegiatan::class, 'kegiatan_id');
    }

    public function indikators()
    {
        return $this->hasMany(IndikatorKak::class, 'kak_id');
    }

    public function tahapans()
    {
        return $this->hasMany(TahapanPelaksanaan::class, 'kak_id');
    }

    public function rabs()
    {
        return $this->hasMany(Rab::class, 'kak_id');
    }

    public function getTotalRabAttribute(): float
    {
        return (float) $this->rabs()->sum('total_harga');
    }
}
