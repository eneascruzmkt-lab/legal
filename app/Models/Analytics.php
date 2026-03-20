<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Analytics extends Model
{
    protected $fillable = [
        'restaurant_id', 'menu_item_id', 'event_type', 'ip', 'user_agent',
    ];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function menuItem()
    {
        return $this->belongsTo(MenuItem::class);
    }
}
