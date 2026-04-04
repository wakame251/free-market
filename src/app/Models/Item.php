<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Item extends Model
{
    use HasFactory;

    // category_id は代表カテゴリ保持用。
    // 実際の複数カテゴリ紐付けは category_item テーブルで管理する。
    // テスト要件上、先頭カテゴリを items.category_id にも保存している。
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

    // カテゴリ（N:N）
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_item')->withTimestamps();
    }

    // Sold判定（ordersの有無に寄せる）
    public function getIsSoldAttribute(): bool
    {
        if ($this->relationLoaded('order')) {
            return $this->order !== null;
        }

        return $this->order()->exists();
    }

    // 商品画像URL
    // Seeder画像: images/sample/xxx.jpg → asset(...)
    // アップロード画像: items/xxx.jpg → asset('storage/' . ...)
    public function getImageUrlAttribute(): ?string
    {
        if (empty($this->image_path)) {
            return null;
        }

        if (str_starts_with($this->image_path, 'images/')) {
            return asset($this->image_path);
        }

        return asset('storage/' . $this->image_path);
    }
}