<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Item;
use App\Models\Like;
use App\Models\User;
use App\Models\Comment;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ItemShowTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 必要な情報が表示される()
    {
        $seller = User::factory()->create();
        $commentUser = User::factory()->create([
            'user_name' => 'コメント太郎',
        ]);

        $item = Item::factory()->create([
            'seller_id' => $seller->id,
            'item_name' => 'レザーバッグ',
            'brand_name' => 'COACH',
            'price' => 15000,
            'description' => 'とても使いやすいバッグです',
            'condition' => '良好',
            'image_path' => 'items/test-bag.jpg',
        ]);

        $category1 = Category::factory()->create([
            'name' => 'ファッション',
        ]);

        $category2 = Category::factory()->create([
            'name' => 'バッグ',
        ]);

        $item->categories()->attach([$category1->id, $category2->id]);

        Like::factory()->count(2)->create([
            'item_id' => $item->id,
        ]);

        Comment::factory()->create([
            'item_id' => $item->id,
            'user_id' => $commentUser->id,
            'body' => 'とても気になります',
        ]);

        $response = $this->get(route('items.show', ['item_id' => $item->id]));

        $response->assertStatus(200);

        // 商品情報
        $response->assertSee('レザーバッグ');
        $response->assertSee('COACH');
        $response->assertSee('15,000', false); // number_formatしていない場合
        $response->assertSee('とても使いやすいバッグです');
        $response->assertSee('良好');

        // 画像パス
        $response->assertSee('storage/items/test-bag.jpg', false);

        // いいね数・コメント数
        $response->assertSee('2');
        $response->assertSee('1');

        // カテゴリ
        $response->assertSee('ファッション');
        $response->assertSee('バッグ');

        // コメントユーザー情報・コメント内容
        $response->assertSee('コメント太郎');
        $response->assertSee('とても気になります');
    }

    /** @test */
    public function 複数選択されたカテゴリが表示される()
    {
        $seller = User::factory()->create();

        $item = Item::factory()->create([
            'seller_id' => $seller->id,
            'item_name' => 'スニーカー',
        ]);

        $category1 = Category::factory()->create([
            'name' => 'メンズ',
        ]);

        $category2 = Category::factory()->create([
            'name' => '靴',
        ]);

        $category3 = Category::factory()->create([
            'name' => 'スポーツ',
        ]);

        $item->categories()->attach([
            $category1->id,
            $category2->id,
            $category3->id,
        ]);

        $response = $this->get(route('items.show', ['item_id' => $item->id]));

        $response->assertStatus(200);
        $response->assertSee('メンズ');
        $response->assertSee('靴');
        $response->assertSee('スポーツ');
    }
}