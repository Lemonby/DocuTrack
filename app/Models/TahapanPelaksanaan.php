<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TahapanPelaksanaan extends Model
{
    protected $primaryKey = 'tahapan_id';
    public $timestamps = false;

    protected $fillable = ['kak_id', 'nama_tahapan'];

    public function kak()
    {
        return $this->belongsTo(Kak::class, 'kak_id');
    }
}
