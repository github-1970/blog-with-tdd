<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tag extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['title', 'slug'];

    public function posts()
    {
        // return $this->morphedByMany(Post::class, 'tagable');
        return $this->belongsToMany(Post::class, 'post_tag');
    }
}
