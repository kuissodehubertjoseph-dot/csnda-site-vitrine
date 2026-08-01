<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    protected $fillable = ['title', 'slug', 'image', 'excerpt', 'content', 'published_at'];

    protected $casts = [
        'published_at' => 'date',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
