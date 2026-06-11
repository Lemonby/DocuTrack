<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RevisiComment extends Model
{
    protected $primaryKey = 'revisi_comment_id';

    public $timestamps = false;

    protected $fillable = ['progress_history_id', 'komentar_revisi', 'target_tabel', 'target_kolom'];

    public function progressHistory()
    {
        return $this->belongsTo(ProgressHistory::class, 'progress_history_id');
    }
}
