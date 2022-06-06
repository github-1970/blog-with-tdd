<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use HasFactory, SoftDeletes;

    // protected $touches = ['user'];

    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'description',
        'body',
    ];

    public function scopePublished($query)
    {
        return $query->where('published_at', '<=', now());
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function tags()
    {
        return $this->morphedByMany(Tag::class, 'taggable', 'taggables', 'taggable_id', 'tag_id');
    }
}
