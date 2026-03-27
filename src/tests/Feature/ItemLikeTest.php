<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Item;
use App\Models\Like;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ItemLikeTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function いいねアイコンを押下することによっていいねした商品として登録することができる()
    {
        $user = User::factory()->create();
        $seller = User::factory()->create();

        $item = Item::factory()->create([
            'seller_id' => $seller->id,
            'item_name' => 'テスト商品',
        ]);

        $response = $this->actingAs($user)->post(route('items.like.toggle', [
            'item_id' => $item->id,
        ]));

        $response->assertRedirect();

        $this->assertDatabaseHas('likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $this->assertEquals(1, Like::where('item_id', $item->id)->count());

        $showResponse = $this->actingAs($user)->get(route('items.show', [
            'item_id' => $item->id,
        ]));

        $showResponse->assertStatus(200);
        $showResponse->assertSee('1');
    }

    /** @test */
    public function 追加済みのアイコンは色が変化する()
    {
        $user = User::factory()->create();
        $seller = User::factory()->create();

        $item = Item::factory()->create([
            'seller_id' => $seller->id,
            'item_name' => 'テスト商品',
        ]);

        Like::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $response = $this->actingAs($user)->get(route('items.show', [
            'item_id' => $item->id,
        ]));

        $response->assertStatus(200);
        $response->assertSee('like-button is-liked', false);
    }

    /** @test */
    public function 再度いいねアイコンを押下することによっていいねを解除することができる()
    {
        $user = User::factory()->create();
        $seller = User::factory()->create();

        $item = Item::factory()->create([
            'seller_id' => $seller->id,
            'item_name' => 'テスト商品',
        ]);

        Like::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $this->assertDatabaseHas('likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $response = $this->actingAs($user)->post(route('items.like.toggle', [
            'item_id' => $item->id,
        ]));

        $response->assertRedirect();

        $this->assertDatabaseMissing('likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $this->assertEquals(0, Like::where('item_id', $item->id)->count());

        $showResponse = $this->actingAs($user)->get(route('items.show', [
            'item_id' => $item->id,
        ]));

        $showResponse->assertStatus(200);
        $showResponse->assertDontSee('like-button is-liked', false);
    }
}