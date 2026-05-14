<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prodi extends Model
{
    protected $primaryKey = 'nama_prodi';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = ['nama_prodi', 'nama_jurusan'];

    public function jurusan()
    {
        return $this->belongsTo(Jurusan::class, 'nama_jurusan', 'nama_jurusan');
    }
}
