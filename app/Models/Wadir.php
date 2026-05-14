<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wadir extends Model
{
    protected $primaryKey = 'wadir_id';
    public $timestamps = false;

    protected $fillable = ['nama_wadir'];
}
