<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Item;
use App\Models\User;
use App\Models\Comment;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ItemCommentTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function ログイン済みのユーザーはコメントを送信できる()
    {
        $user = User::factory()->create();
        $seller = User::factory()->create();

        $item = Item::factory()->create([
            'seller_id' => $seller->id,
            'item_name' => 'テスト商品',
        ]);

        $response = $this->actingAs($user)->post(route('items.comment.store', [
            'item_id' => $item->id,
        ]), [
            'body' => 'これはテストコメントです',
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('comments', [
            'item_id' => $item->id,
            'user_id' => $user->id,
            'body' => 'これはテストコメントです',
        ]);

        $this->assertEquals(1, Comment::where('item_id', $item->id)->count());

        $showResponse = $this->actingAs($user)->get(route('items.show', [
            'item_id' => $item->id,
        ]));

        $showResponse->assertStatus(200);
        $showResponse->assertSee('コメント（1）');
        $showResponse->assertSee('これはテストコメントです');
    }

    /** @test */
    public function ログイン前のユーザーはコメントを送信できない()
    {
        $seller = User::factory()->create();

        $item = Item::factory()->create([
            'seller_id' => $seller->id,
            'item_name' => 'テスト商品',
        ]);

        $response = $this->post(route('items.comment.store', [
            'item_id' => $item->id,
        ]), [
            'body' => '未ログインコメント',
        ]);

        // auth ミドルウェア前提
        $response->assertRedirect('/login');

        $this->assertDatabaseMissing('comments', [
            'item_id' => $item->id,
            'body' => '未ログインコメント',
        ]);
    }

    /** @test */
    public function コメントが入力されていない場合バリデーションメッセージが表示される()
    {
        $user = User::factory()->create();
        $seller = User::factory()->create();

        $item = Item::factory()->create([
            'seller_id' => $seller->id,
            'item_name' => 'テスト商品',
        ]);

        $response = $this->from(route('items.show', ['item_id' => $item->id]))
            ->actingAs($user)
            ->post(route('items.comment.store', [
                'item_id' => $item->id,
            ]), [
                'body' => '',
            ]);

        $response->assertRedirect(route('items.show', ['item_id' => $item->id]));
        $response->assertSessionHasErrors(['body']);

        $followResponse = $this->actingAs($user)->get(route('items.show', [
            'item_id' => $item->id,
        ]));

        $followResponse->assertSee('コメントを入力してください。');
    }

    /** @test */
    public function コメントが255字以上の場合バリデーションメッセージが表示される()
    {
        $user = User::factory()->create();
        $seller = User::factory()->create();

        $item = Item::factory()->create([
            'seller_id' => $seller->id,
            'item_name' => 'テスト商品',
        ]);

        $longComment = str_repeat('あ', 256);

        $response = $this->from(route('items.show', ['item_id' => $item->id]))
            ->actingAs($user)
            ->post(route('items.comment.store', [
                'item_id' => $item->id,
            ]), [
                'body' => $longComment,
            ]);

        $response->assertRedirect(route('items.show', ['item_id' => $item->id]));
        $response->assertSessionHasErrors(['body']);

        $followResponse = $this->actingAs($user)->get(route('items.show', [
            'item_id' => $item->id,
        ]));

        $followResponse->assertSee('コメントは255文字以内で入力してください。');
    }
}