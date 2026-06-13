<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, HasRoles, Notifiable, SoftDeletes;

    protected $primaryKey = 'user_id';

    protected $guard_name = 'sanctum';

    protected $fillable = [
        'nama', 'email', 'password', 'nama_jurusan', 'status', 'foto_profil',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    // --- Relationships ---

    public function jurusan()
    {
        return $this->belongsTo(Jurusan::class, 'nama_jurusan', 'nama_jurusan');
    }

    public function kegiatans()
    {
        return $this->hasMany(Kegiatan::class, 'user_id');
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class, 'user_id');
    }

    public function logStatuses()
    {
        return $this->hasMany(LogStatus::class, 'user_id');
    }

    // --- Scopes ---

    public function scopeActive($query)
    {
        return $query->where('status', 'Aktif');
    }

    public function scopeByJurusan($query, string $jurusan)
    {
        return $query->where('nama_jurusan', $jurusan);
    }
}
