<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Item;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProfileShowTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 必要な情報が取得できる()
    {
        $user = User::factory()->create([
            'user_name' => 'テストユーザー',
        ]);

        $user->profile()->create([
            'avatar_path' => 'avatars/test.png',
            'post_code' => '123-4567',
            'address' => '大阪府大阪市1-1-1',
            'building' => 'テストマンション101',
        ]);

        $otherSeller = User::factory()->create();

        // 出品した商品
        $sellItem = Item::factory()->create([
            'seller_id' => $user->id,
            'item_name' => '出品商品A',
        ]);

        // 購入した商品
        $buyItem = Item::factory()->create([
            'seller_id' => $otherSeller->id,
            'item_name' => '購入商品B',
            'price' => 5000,
        ]);

        Order::create([
            'item_id' => $buyItem->id,
            'buyer_id' => $user->id,
            'stripe_session_id' => 'cs_test_profile_001',
            'payment_method' => 'card',
            'post_code' => '123-4567',
            'address' => '大阪府大阪市1-1-1',
            'building' => 'テストマンション101',
            'price_at_purchase' => $buyItem->price,
            'purchased_at' => now(),
        ]);

        // 出品ページ確認
        $sellResponse = $this->actingAs($user)->get(route('profile.show', [
            'page' => 'sell',
        ]));

        $sellResponse->assertStatus(200);

        // プロフィール画像
        $sellResponse->assertSee('storage/avatars/test.png', false);

        // ユーザー名
        $sellResponse->assertSee('テストユーザー');

        // 出品した商品一覧
        $sellResponse->assertSee('出品商品A');

        // sellページでは購入商品は出ない想定
        $sellResponse->assertDontSee('購入商品B');

        // 購入ページ確認
        $buyResponse = $this->actingAs($user)->get(route('profile.show', [
            'page' => 'buy',
        ]));

        $buyResponse->assertStatus(200);

        // プロフィール画像
        $buyResponse->assertSee('storage/avatars/test.png', false);

        // ユーザー名
        $buyResponse->assertSee('テストユーザー');

        // 購入した商品一覧
        $buyResponse->assertSee('購入商品B');

        // buyページでは出品商品は出ない想定
        $buyResponse->assertDontSee('出品商品A');
    }
}