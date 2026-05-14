<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lpj extends Model
{
    protected $primaryKey = 'lpj_id';
    public $timestamps = false;

    protected $fillable = [
        'kegiatan_id', 'grand_total_realisasi', 'submitted_at', 'approved_at',
        'tenggat_lpj', 'status_id', 'komentar_penolakan', 'komentar_revisi',
    ];

    protected function casts(): array
    {
        return [
            'grand_total_realisasi' => 'decimal:2',
            'submitted_at' => 'datetime',
            'approved_at' => 'datetime',
            'tenggat_lpj' => 'date',
        ];
    }

    public function kegiatan()
    {
        return $this->belongsTo(Kegiatan::class, 'kegiatan_id');
    }

    public function status()
    {
        return $this->belongsTo(StatusUtama::class, 'status_id', 'status_id');
    }

    public function items()
    {
        return $this->hasMany(LpjItem::class, 'lpj_id');
    }

    public function getDeadlineStatusAttribute(): string
    {
        if ($this->status_id !== 1) {
            return 'COMPLETED';
        }

        return match (true) {
            $this->tenggat_lpj < now()->toDateString() => 'OVERDUE',
            $this->tenggat_lpj == now()->toDateString() => 'DUE_TODAY',
            default => 'PENDING',
        };
    }
}
