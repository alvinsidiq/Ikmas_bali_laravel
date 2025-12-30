<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IuranTagihan extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode','user_id','judul','periode','nominal','denda','diskon','terbayar_verified','jatuh_tempo','status','paid_at','catatan'
    ];

    protected $casts = [ 'jatuh_tempo' => 'date', 'paid_at' => 'datetime' ];

    public function user(){ return $this->belongsTo(User::class); }
    public function payments(){ return $this->hasMany(IuranPembayaran::class, 'tagihan_id'); }

    public function getTotalTagihanAttribute(): int { return max(0, (int)($this->nominal + $this->denda) - (int)$this->diskon); }
    public function getSisaBayarAttribute(): int { return max(0, (int)$this->total_tagihan - (int)$this->terbayar_verified); }
}

