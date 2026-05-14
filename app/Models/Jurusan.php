<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jurusan extends Model
{
    protected $primaryKey = 'nama_jurusan';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = ['nama_jurusan'];

    public function prodis()
    {
        return $this->hasMany(Prodi::class, 'nama_jurusan', 'nama_jurusan');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'nama_jurusan', 'nama_jurusan');
    }
}
