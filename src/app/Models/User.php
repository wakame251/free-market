<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'user_name',
        'email',
        'password',
        'postcode',
        'address',
        'building',
        'profile_image_path',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function profile()
    {
    return $this->hasOne(Profile::class);
    }

    // 出品した商品（1:N）
    public function sellingItems()
    {
        return $this->hasMany(Item::class, 'seller_id');
    }

    // 購入した注文（1:N）
    public function orders()
    {
        return $this->hasMany(Order::class, 'buyer_id');
    }

    // コメント（1:N）
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    // いいね（1:N）
    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    // いいねした商品（N:N相当：likes経由）
    public function likedItems()
    {
        return $this->belongsToMany(Item::class, 'likes', 'user_id', 'item_id')
            ->withTimestamps();
    }
}