<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Kegiatan extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'judul','slug','lokasi','deskripsi','waktu_mulai','waktu_selesai','poster_path','is_published','published_at','created_by'
    ];

    protected $casts = [
        'waktu_mulai' => 'datetime',
        'waktu_selesai' => 'datetime',
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function($m){
            if (empty($m->slug)) { $m->slug = static::uniqueSlug($m->judul); }
        });
        static::updating(function($m){
            if ($m->isDirty('judul')) { $m->slug = static::uniqueSlug($m->judul, $m->id); }
        });
    }

    public static function uniqueSlug(string $judul, ?int $ignoreId=null): string
    {
        $base = Str::slug($judul);
        $slug = $base; $i=1;
        while (static::where('slug',$slug)->when($ignoreId, fn($q)=>$q->where('id','!=',$ignoreId))->exists()) {
            $slug = $base.'-'.$i++;
        }
        return $slug;
    }

    public function creator(){ return $this->belongsTo(User::class, 'created_by'); }
    public function participants(){
        return $this->belongsToMany(User::class, 'kegiatan_user')
            ->withPivot(['status','kode','registered_at','checked_in_at'])
            ->withTimestamps();
    }

    // Scopes
    public function scopePublished($q){ return $q->where('is_published', true); }
    public function scopeUpcoming($q){ return $q->where('waktu_mulai', '>=', now()); }
    public function scopePast($q){ return $q->where('waktu_mulai', '<', now()); }
}
