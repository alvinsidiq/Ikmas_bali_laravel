<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function profile()
    {
        return $this->hasOne(\App\Models\AnggotaProfile::class);
    }

    public function getStatusLabelAttribute()
    {
        return $this->profile && $this->profile->is_active ? 'Aktif' : 'Nonaktif';
    }

    public function kegiatanDiikuti(){
        return $this->belongsToMany(\App\Models\Kegiatan::class, 'kegiatan_user')
            ->withPivot(['status','kode','registered_at','checked_in_at'])
            ->withTimestamps();
    }

    public function pengumumanTerbaca(){
        return $this->belongsToMany(\App\Models\Pengumuman::class, 'pengumuman_user')
            ->withPivot(['read_at'])
            ->withTimestamps();
    }
}
