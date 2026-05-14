<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kegiatan extends Model
{
    protected $primaryKey = 'kegiatan_id';

    protected $fillable = [
        'nama_kegiatan', 'prodi_penyelenggara', 'pemilik_kegiatan', 'nim_pelaksana',
        'nip', 'nama_pj', 'bukti_mak', 'user_id', 'jurusan_penyelenggara',
        'status_utama_id', 'wadir_tujuan', 'surat_pengantar', 'tanggal_mulai',
        'tanggal_selesai', 'posisi_id', 'tanggal_pencairan', 'jumlah_dicairkan',
        'dana_di_setujui', 'metode_pencairan', 'catatan_bendahara',
        'pencairan_tahap_json', 'umpan_balik_verifikator',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_mulai' => 'date',
            'tanggal_selesai' => 'date',
            'tanggal_pencairan' => 'datetime',
            'jumlah_dicairkan' => 'decimal:2',
            'dana_di_setujui' => 'decimal:2',
        ];
    }

    // --- Relationships ---

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function statusUtama()
    {
        return $this->belongsTo(StatusUtama::class, 'status_utama_id', 'status_id');
    }

    public function wadir()
    {
        return $this->belongsTo(Wadir::class, 'wadir_tujuan', 'wadir_id');
    }

    public function kak()
    {
        return $this->hasOne(Kak::class, 'kegiatan_id');
    }

    public function lpj()
    {
        return $this->hasOne(Lpj::class, 'kegiatan_id');
    }

    public function progressHistories()
    {
        return $this->hasMany(ProgressHistory::class, 'kegiatan_id');
    }

    public function tahapanPencairans()
    {
        return $this->hasMany(TahapanPencairan::class, 'kegiatan_id');
    }

    // --- Scopes ---

    public function scopeAtPosition($query, int $posisiId)
    {
        return $query->where('posisi_id', $posisiId);
    }

    public function scopeWithStatus($query, int $statusId)
    {
        return $query->where('status_utama_id', $statusId);
    }

    public function scopeByJurusan($query, string $jurusan)
    {
        return $query->where('jurusan_penyelenggara', $jurusan);
    }

    // --- Accessors ---

    public function getWorkflowProgressAttribute(): int
    {
        if (in_array($this->status_utama_id, [2, 4])) {
            return 0;
        }

        return match ((int) $this->posisi_id) {
            1 => 20, 2 => 40, 4 => 60, 3 => 80, 5 => 100,
            default => 0,
        };
    }
}
