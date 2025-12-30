<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Pengumuman extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pengumumen'; // sesuaikan jika tabel berbeda

    protected $fillable = [
        'judul','slug','kategori','isi','cover_path','is_published','published_at','is_pinned','author_id'
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'is_pinned' => 'boolean',
        'published_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function($m){ if (empty($m->slug)) $m->slug = static::uniqueSlug($m->judul); });
        static::updating(function($m){ if ($m->isDirty('judul')) $m->slug = static::uniqueSlug($m->judul, $m->id); });
    }

    public static function uniqueSlug(string $judul, ?int $ignoreId=null): string
    {
        $base = Str::slug($judul); $slug = $base; $i=1;
        while (static::where('slug',$slug)->when($ignoreId, fn($q)=>$q->where('id','!=',$ignoreId))->exists()) {
            $slug = $base.'-'.$i++;
        }
        return $slug;
    }

    public function author(){ return $this->belongsTo(User::class, 'author_id'); }
    public function readers(){
        return $this->belongsToMany(User::class, 'pengumuman_user')
            ->withPivot(['read_at'])
            ->withTimestamps();
    }

    // Scopes
    public function scopePublished($q){ return $q->where('is_published', true); }
    public function scopePinned($q){ return $q->where('is_pinned', true); }
}
