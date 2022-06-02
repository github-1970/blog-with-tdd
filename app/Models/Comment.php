<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'body',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function commentable()
    {
        return $this->morphTo();
    }

    public function postUser()
    {
        return $this->hasManyThrough(
            User::class,
            Post::class,
            'user_id', // Foreign key on the posts table...
            'id', // Foreign key on the users table...
            'commentable_id', // Local key on the comments table...
            'id' // Local key on the posts table...
        );
    }
}
