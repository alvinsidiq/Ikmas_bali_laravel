<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ForumPost extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [ 'topic_id','user_id','content','is_solution' ];

    public function topic(){ return $this->belongsTo(ForumTopic::class, 'topic_id'); }
    public function user(){ return $this->belongsTo(User::class, 'user_id'); }
}
