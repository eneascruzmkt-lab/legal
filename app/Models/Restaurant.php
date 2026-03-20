<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Restaurant extends Model
{
    protected $fillable = [
        'user_id', 'name', 'slug', 'description', 'logo',
        'primary_color', 'secondary_color', 'address', 'phone',
        'instagram', 'whatsapp', 'working_hours', 'active',
    ];

    protected $casts = [
        'working_hours' => 'array',
        'active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function ($restaurant) {
            if (empty($restaurant->slug)) {
                $restaurant->slug = Str::slug($restaurant->name) . '-' . Str::random(5);
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function categories()
    {
        return $this->hasMany(Category::class)->orderBy('order');
    }

    public function menuItems()
    {
        return $this->hasMany(MenuItem::class);
    }

    public function qrCodes()
    {
        return $this->hasMany(QrCode::class);
    }

    public function analytics()
    {
        return $this->hasMany(Analytics::class);
    }
}
