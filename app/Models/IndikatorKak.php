<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IndikatorKak extends Model
{
    protected $primaryKey = 'indikator_id';

    public $timestamps = false;

    protected $fillable = ['kak_id', 'bulan', 'indikator_keberhasilan', 'target_persen'];

    public function kak()
    {
        return $this->belongsTo(Kak::class, 'kak_id');
    }
}
