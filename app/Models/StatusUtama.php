<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StatusUtama extends Model
{
    protected $primaryKey = 'status_id';

    public $timestamps = false;

    protected $fillable = ['nama_status_usulan'];

    public function kegiatans()
    {
        return $this->hasMany(Kegiatan::class, 'status_utama_id', 'status_id');
    }
}
