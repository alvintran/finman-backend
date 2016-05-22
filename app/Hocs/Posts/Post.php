<?php namespace Nht\Hocs\Posts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    // use SoftDeletes;
    public $fillable = ['title', 'body'];
    public $hidden = ['created_at', 'updated_at'];

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}
