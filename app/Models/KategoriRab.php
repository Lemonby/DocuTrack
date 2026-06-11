<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KategoriRab extends Model
{
    protected $primaryKey = 'kategori_rab_id';

    public $timestamps = false;

    protected $fillable = ['nama_kategori'];

    public function rabs()
    {
        return $this->hasMany(Rab::class, 'kategori_id');
    }
}
