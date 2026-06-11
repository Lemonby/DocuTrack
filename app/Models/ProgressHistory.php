<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgressHistory extends Model
{
    protected $primaryKey = 'progress_history_id';

    public $timestamps = false;

    protected $fillable = ['kegiatan_id', 'status_id', 'changed_by_user_id', 'created_at'];

    protected function casts(): array
    {
        return ['created_at' => 'datetime'];
    }

    public function kegiatan()
    {
        return $this->belongsTo(Kegiatan::class, 'kegiatan_id');
    }

    public function status()
    {
        return $this->belongsTo(StatusUtama::class, 'status_id', 'status_id');
    }

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by_user_id', 'user_id');
    }

    public function revisiComments()
    {
        return $this->hasMany(RevisiComment::class, 'progress_history_id');
    }
}
