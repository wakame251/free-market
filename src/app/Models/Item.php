<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'seller_id',
        'item_name',
        'description',
        'price',
        'brand_name',
        'image_path',
        'condition',
        'status',
        'category_id',
    ];

    // 出品者（N:1）
    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    // 注文（1:1）※ item_id unique 前提
    public function order()
    {
        return $this->hasOne(Order::class);
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

    // いいねしたユーザー（N:N相当：likes経由）
    public function likedUsers()
    {
        return $this->belongsToMany(User::class, 'likes', 'item_id', 'user_id')
            ->withTimestamps();
    }

    // Sold判定（ordersの有無に寄せる）
    public function getIsSoldAttribute(): bool
    {
        // 既に eager load されている場合はそれを使う
        if ($this->relationLoaded('order')) {
            return $this->order !== null;
        }
        return $this->order()->exists();
    }

    public function categories()
    {
    return $this->belongsToMany(Category::class,'category_item')->withTimestamps();
    }
}