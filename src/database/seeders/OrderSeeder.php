<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        // 購入者用ユーザー（出品者とは別に用意）
        $buyer1 = User::firstOrCreate(
            ['email' => 'buyer1@example.com'],
            [
                'user_name' => '購入者 太郎',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
            ]
        );

        $buyer2 = User::firstOrCreate(
            ['email' => 'buyer2@example.com'],
            [
                'user_name' => '購入者 花子',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
            ]
        );

        // sold の商品だけ取得
        $soldItems = Item::where('status', 'sold')->get();

        foreach ($soldItems as $index => $item) {
            // 購入者を交互に割り当て
            $buyer = $index % 2 === 0 ? $buyer1 : $buyer2;

            // 念のため、出品者本人が購入者にならないようにする
            if ($buyer->id === $item->seller_id) {
                $buyer = $buyer1->id === $item->seller_id ? $buyer2 : $buyer1;
            }

            Order::firstOrCreate(
                ['item_id' => $item->id],
                [
                    'stripe_session_id' => 'seed_session_' . $item->id,
                    'buyer_id' => $buyer->id,
                    'payment_method' => $index % 2 === 0 ? 'card' : 'konbini',
                    'post_code' => '123-4567',
                    'address' => '東京都渋谷区1-2-3',
                    'building' => 'テストマンション101',
                    'price_at_purchase' => $item->price,
                    'purchased_at' => now()->subDays($index + 1),
                ]
            );
        }
    }
}
