<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Item;
use App\Models\Like;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoTransactionSeeder extends Seeder
{
    public function run(): void
    {
        $user1 = User::where('email', 'test@userone.com')->firstOrFail();
        $user2 = User::where('email', 'test@usertwo.com')->firstOrFail();

        $pikachu = Item::where('seller_id', $user1->id)
            ->where('item_name', 'ピカチュウぬいぐるみ')
            ->firstOrFail();

        $horse = Item::where('seller_id', $user2->id)
            ->where('item_name', '木馬のおもちゃ')
            ->firstOrFail();

        // 一般ユーザー① が 木馬のおもちゃ を購入
        Order::updateOrCreate(
            ['item_id' => $horse->id],
            [
                'stripe_session_id' => 'seed_order_horse_001',
                'buyer_id'          => $user1->id,
                'payment_method'    => 'card',
                'post_code'         => '000-0000',
                'address'           => '東京都渋谷区1-1-1',
                'building'          => null,
                'price_at_purchase' => $horse->price,
                'purchased_at'      => now(),
            ]
        );

        // 一般ユーザー② が ピカチュウぬいぐるみ を購入
        Order::updateOrCreate(
            ['item_id' => $pikachu->id],
            [
                'stripe_session_id' => 'seed_order_pikachu_001',
                'buyer_id'          => $user2->id,
                'payment_method'    => 'card',
                'post_code'         => '111-1111',
                'address'           => '大阪府大阪市1-1-1',
                'building'          => null,
                'price_at_purchase' => $pikachu->price,
                'purchased_at'      => now(),
            ]
        );

        // 商品ステータスも揃える
        $horse->update(['status' => 'sold']);
        $pikachu->update(['status' => 'sold']);

        // いいね
        Like::updateOrCreate(
            [
                'user_id' => $user1->id,
                'item_id' => $horse->id,
            ],
            []
        );

        Like::updateOrCreate(
            [
                'user_id' => $user2->id,
                'item_id' => $pikachu->id,
            ],
            []
        );

        // コメント
        Comment::updateOrCreate(
            [
                'user_id' => $user1->id,
                'item_id' => $horse->id,
            ],
            [
                'body' => '子どもの誕生日プレゼントに買ってあげたところ、とても喜んで遊んでいました。状態もとても綺麗に保たれていました。購入してよかったです！',
            ]
        );

        Comment::updateOrCreate(
            [
                'user_id' => $user2->id,
                'item_id' => $pikachu->id,
            ],
            [
                'body' => 'どこの店舗やショッピングサイトを探しても売り切れだったので、ここで購入できてとても嬉しいです！',
            ]
        );
    }
}