<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ForumTopic extends Model
{
    use HasFactory;

    protected $fillable = [
        'judul','slug','kategori','banner_url','body','is_open','is_pinned','is_solved','solved_post_id','posts_count','last_post_at','author_id'
    ];

    protected $casts = [
        'is_open' => 'boolean',
        'is_pinned' => 'boolean',
        'is_solved' => 'boolean',
        'last_post_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function($m){ if (empty($m->slug)) $m->slug = static::uniqueSlug($m->judul); });
        static::updating(function($m){ if ($m->isDirty('judul')) $m->slug = static::uniqueSlug($m->judul, $m->id); });
    }

    public static function uniqueSlug(string $judul, ?int $ignoreId=null): string
    {
        $base = Str::slug($judul); $slug = $base; $i=1;
        while (static::where('slug',$slug)->when($ignoreId, fn($q)=>$q->where('id','!=',$ignoreId))->exists()) $slug = $base.'-'.$i++;
        return $slug;
    }

    public function author(){ return $this->belongsTo(User::class, 'author_id'); }
    public function posts(){ return $this->hasMany(ForumPost::class, 'topic_id'); }
}
