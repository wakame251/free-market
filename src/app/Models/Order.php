<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'stripe_session_id',
        'buyer_id',
        'payment_method',
        'post_code',
        'address',
        'building',
        'price_at_purchase',
        'purchased_at',
    ];

    protected $casts = [
        'purchased_at' => 'datetime',
    ];

    // 購入した商品（N:1）
    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    // 購入者（N:1）
    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }
}