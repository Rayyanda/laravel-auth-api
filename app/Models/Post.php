<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    //
    protected $fillable = [
        'title',
        'slug',
        'image',
        'content',
        'published_at',
        'author'
    ];

    public function author()
    {
        return $this->belongsTo(User::class,'author','id');
    }
}
