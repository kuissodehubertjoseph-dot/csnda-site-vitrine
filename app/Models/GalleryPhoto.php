<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GalleryPhoto extends Model
{
    protected $fillable = ['gallery_category_id', 'title', 'image'];

    public function category(): BelongsTo
    {
        return $this->belongsTo(GalleryCategory::class, 'gallery_category_id');
    }
}
