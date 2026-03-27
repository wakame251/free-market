<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SellTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 商品出品画面にて必要な情報が保存できること
     * （カテゴリ、商品の状態、商品名、ブランド名、商品の説明、販売価格）
     */
    public function test_user_can_store_item_with_required_information(): void
    {
        Storage::fake('public');

        // 1. ユーザー作成＆ログイン
        $user = User::factory()->create();
        $this->actingAs($user);

        // 2. カテゴリ作成
        $category1 = Category::factory()->create([
            'name' => 'ファッション',
        ]);

        $category2 = Category::factory()->create([
            'name' => '家電',
        ]);

        // 3. 出品画像作成
        $image = UploadedFile::fake()->image('item.png');

        // 4. 出品実行
        $response = $this->post(route('sell.store'), [
            'category_ids' => [$category1->id, $category2->id],
            'condition' => '良好',
            'item_name' => 'テスト商品',
            'brand_name' => 'テストブランド',
            'description' => 'これはテスト用の商品説明です',
            'price' => '50,000',
            'image' => $image,
        ]);

        // 5. リダイレクト確認
        $response->assertRedirect(route('items.index'));

        // 6. items テーブルに保存されていることを確認
        $this->assertDatabaseHas('items', [
            'seller_id' => $user->id,
            'item_name' => 'テスト商品',
            'brand_name' => 'テストブランド',
            'description' => 'これはテスト用の商品説明です',
            'price' => 50000, // Controller内でカンマ除去してint化しているため
            'condition' => '良好',
            'status' => 'on_sale',
            'category_id' => $category1->id, // 先頭カテゴリが保存される仕様
        ]);

        // 7. 保存された商品を取得
        $item = Item::where('item_name', 'テスト商品')->first();

        $this->assertNotNull($item);

        // 8. 画像が保存されていることを確認
        Storage::disk('public')->assertExists($item->image_path);

        // 9. pivot テーブルにカテゴリ紐付けが保存されていることを確認
        $this->assertDatabaseHas('category_item', [
            'item_id' => $item->id,
            'category_id' => $category1->id,
        ]);

        $this->assertDatabaseHas('category_item', [
            'item_id' => $item->id,
            'category_id' => $category2->id,
        ]);
    }
}