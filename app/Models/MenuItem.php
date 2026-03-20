<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    protected $fillable = [
        'restaurant_id', 'category_id', 'name', 'description', 'price',
        'image', 'video_url', 'video_thumbnail', 'featured', 'available',
        'order', 'views_count', 'video_plays_count',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'featured' => 'boolean',
        'available' => 'boolean',
    ];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
