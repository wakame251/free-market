<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Item;
use App\Models\Like;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ItemIndexTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 全商品を取得できる()
    {
        $seller1 = User::factory()->create();
        $seller2 = User::factory()->create();

        Item::factory()->create([
            'seller_id' => $seller1->id,
            'item_name' => '商品A',
        ]);

        Item::factory()->create([
            'seller_id' => $seller2->id,
            'item_name' => '商品B',
        ]);

        $response = $this->get(route('items.index'));

        $response->assertStatus(200);
        $response->assertSee('商品A');
        $response->assertSee('商品B');
    }

    /** @test */
    public function 購入済み商品は_sold_と表示される()
    {
        $seller = User::factory()->create();
        $buyer = User::factory()->create();

        $item = Item::factory()->create([
            'seller_id' => $seller->id,
            'item_name' => '売り切れ商品',
            'price' => 5000,
        ]);

        Order::create([
            'item_id' => $item->id,
            'buyer_id' => $buyer->id,
            'payment_method' => 'コンビニ払い',
            'post_code' => '123-4567',
            'address' => '大阪府大阪市1-1-1',
            'building' => 'テストマンション101',
            'price_at_purchase' => $item->price,
            'purchased_at' => now(),
        ]);

        $response = $this->get(route('items.index'));

        $response->assertStatus(200);
        $response->assertSee('売り切れ商品');
        $response->assertSee('Sold');
    }

    /** @test */
    public function 自分が出品した商品は表示されない()
    {
        $loginUser = User::factory()->create();

        Item::factory()->create([
            'seller_id' => $loginUser->id,
            'item_name' => '自分の商品',
        ]);

        Item::factory()->create([
            'seller_id' => User::factory()->create()->id,
            'item_name' => '他人の商品',
        ]);

        $response = $this->actingAs($loginUser)->get(route('items.index'));

        $response->assertStatus(200);
        $response->assertDontSee('自分の商品');
        $response->assertSee('他人の商品');
    }

    /** @test */
    public function いいねした商品だけが表示される()
    {
        $user = User::factory()->create();

        $likedItem = Item::factory()->create([
            'seller_id' => User::factory()->create()->id,
            'item_name' => 'いいねした商品',
        ]);

        $notLikedItem = Item::factory()->create([
            'seller_id' => User::factory()->create()->id,
            'item_name' => 'いいねしていない商品',
        ]);

        Like::create([
            'user_id' => $user->id,
            'item_id' => $likedItem->id,
        ]);

        $response = $this->actingAs($user)->get(route('items.index', [
            'tab' => 'mylist',
        ]));

        $response->assertStatus(200);
        $response->assertSee('いいねした商品');
        $response->assertDontSee('いいねしていない商品');
    }

    /** @test */
    public function マイリストの購入済み商品は_sold_と表示される()
    {
        $user = User::factory()->create();
        $seller = User::factory()->create();
        $buyer = User::factory()->create();

        $likedSoldItem = Item::factory()->create([
            'seller_id' => $seller->id,
            'item_name' => 'いいねした売り切れ商品',
            'price' => 5000,
        ]);

        Like::create([
            'user_id' => $user->id,
            'item_id' => $likedSoldItem->id,
        ]);

        Order::create([
            'item_id' => $likedSoldItem->id,
            'buyer_id' => $buyer->id,
            'payment_method' => 'コンビニ払い',
            'post_code' => '123-4567',
            'address' => '大阪府大阪市1-1-1',
            'building' => 'テストマンション101',
            'price_at_purchase' => $likedSoldItem->price,
            'purchased_at' => now(),
        ]);

        $response = $this->actingAs($user)->get(route('items.index', [
            'tab' => 'mylist',
        ]));

        $response->assertStatus(200);
        $response->assertSee('いいねした売り切れ商品');
        $response->assertSee('Sold');
    }

    /** @test */
    public function 未認証の場合マイリストには何も表示されない()
    {
        $item = Item::factory()->create([
            'seller_id' => User::factory()->create()->id,
            'item_name' => 'いいね商品',
        ]);

        $user = User::factory()->create();

        Like::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $response = $this->get(route('items.index', [
            'tab' => 'mylist',
        ]));

        $response->assertStatus(200);
        $response->assertDontSee('いいね商品');
    }

    /** @test */
    public function 商品名で部分一致検索ができる()
    {
        Item::factory()->create([
            'seller_id' => User::factory()->create()->id,
            'item_name' => '赤いバッグ',
        ]);

        Item::factory()->create([
            'seller_id' => User::factory()->create()->id,
            'item_name' => '青いバッグ',
        ]);

        Item::factory()->create([
            'seller_id' => User::factory()->create()->id,
            'item_name' => '腕時計',
        ]);

        $response = $this->get(route('items.index', [
            'keyword' => 'バッグ',
        ]));

        $response->assertStatus(200);
        $response->assertSee('赤いバッグ');
        $response->assertSee('青いバッグ');
        $response->assertDontSee('腕時計');
    }

    /** @test */
    public function 検索状態がマイリストでも保持されている()
    {
        $user = User::factory()->create();

        $likedItem = Item::factory()->create([
            'seller_id' => User::factory()->create()->id,
            'item_name' => '赤いバッグ',
        ]);

        $notMatchedLikedItem = Item::factory()->create([
            'seller_id' => User::factory()->create()->id,
            'item_name' => '青い靴',
        ]);

        Like::create([
            'user_id' => $user->id,
            'item_id' => $likedItem->id,
        ]);

        Like::create([
            'user_id' => $user->id,
            'item_id' => $notMatchedLikedItem->id,
        ]);

        $response = $this->actingAs($user)->get(route('items.index', [
            'tab' => 'mylist',
            'keyword' => 'バッグ',
        ]));

        $response->assertStatus(200);

        // 検索キーワードが入力欄に保持されていること
        $response->assertSee('value="バッグ"', false);

        // keyword=バッグ で絞り込まれていること
        $response->assertSee('赤いバッグ');
        $response->assertDontSee('青い靴');

        // マイリストタブのリンクにも keyword が保持されていること
        $expectedUrl = e(route('items.index', [
            'tab' => 'mylist',
            'keyword' => 'バッグ',
        ]));

        $response->assertSee($expectedUrl, false);
    }
}