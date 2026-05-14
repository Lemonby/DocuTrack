<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogStatus extends Model
{
    public $timestamps = false;

    protected $fillable = ['user_id', 'tipe_log', 'id_referensi', 'status', 'konten_json'];

    protected function casts(): array
    {
        return [
            'konten_json' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopeUnread($query)
    {
        return $query->where('status', 'BELUM_DIBACA');
    }
}
