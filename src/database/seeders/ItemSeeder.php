<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\User;
use App\Models\Category;
use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder
{
    public function run(): void
    {
        // ✅ 重複しないように firstOrCreate を使う
        $seller1 = User::firstOrCreate(
            ['email' => 'a@example.com'],
            [
                'user_name' => '鈴木 舞',
                'email_verified_at' => now(),
                'password' => bcrypt('password'),
            ]
        );

        $seller2 = User::firstOrCreate(
            ['email' => 'b@example.com'],
            [
                'user_name' => '田辺 裕美子',
                'email_verified_at' => now(),
                'password' => bcrypt('password'),
            ]
        );

        $catFashion =  Category::firstOrCreate(['name' => 'ファッション']);
        $catElec =  Category::firstOrCreate(['name' => '家電']);
        $catInterior =  Category::firstOrCreate(['name' => 'インテリア']);
        $catLadies =  Category::firstOrCreate(['name' => 'レディース']);
        $catMens =  Category::firstOrCreate(['name' => 'メンズ']);
        $catCosme =  Category::firstOrCreate(['name' => 'コスメ']);
        $catBook =  Category::firstOrCreate(['name' => '本']);
        $catGame =  Category::firstOrCreate(['name' => 'ゲーム']);
        $catSports =  Category::firstOrCreate(['name' => 'スポーツ']);
        $catKitchen =  Category::firstOrCreate(['name' => 'キッチン']);
        $catHandmade =  Category::firstOrCreate(['name' => 'ハンドメイド']);
        $catAccessories =  Category::firstOrCreate(['name' => 'アクセサリー']);
        $catToys =  Category::firstOrCreate(['name' => 'おもちゃ']);
        $catKids =  Category::firstOrCreate(['name' => 'ベビー・キッズ']);

        $items = [
            // 腕時計
            [
                'item_name'     => '腕時計',
                'image_path'    => 'items/watch.jpg',
                'price'         => 15000,
                'category_ids'  => [$catFashion->id, $catMens->id, $catAccessories->id],
                'brand_name'    => 'Rolax',
                'description'   => 'スタイリッシュなデザインのメンズ腕時計',
                'condition'     => '良好',
            ],
            // HDD
            [
                'item_name'   => 'HDD',
                'image_path'  => 'items/HDD.jpg',
                'price'       => 5000,
                'category_ids' => [$catElec->id],
                'brand_name'       => '西芝' ,
                'description'  => '高速で信頼性の高いハードディスク',
                'condition'    => '目立った傷や汚れなし',
            ],
            // 玉ねぎ3束
            [
                'item_name'   => '玉ねぎ3束',
                'image_path'  => 'items/onion.jpg',
                'price'       => 300,
                'category_ids' => [$catKitchen->id],
                'brand_name'       => 'なし' ,
                'description'  => '新鮮な玉ねぎ3束のセット',
                'condition'    => 'やや傷や汚れあり',
            ],
            // 革靴
            [
                'item_name'   => '革靴',
                'image_path'  => 'items/leather_shoes.jpg',
                'price'       => 4000,
                'category_ids'  => [$catFashion->id, $catMens->id],
                'description'  => 'クラシックなデザインの革靴',
                'condition'    => '状態が悪い',
            ],
            // ノートPC
            [
                'item_name'   => 'ノートPC',
                'image_path'  => 'items/Notebook_PC.jpg',
                'price'       => 45000,
                'category_ids' => [$catElec->id],
                'description'  => '高性能なノートパソコン',
                'condition'    => '良好',
            ],
            // マイク
            [
                'item_name'   => 'マイク',
                'image_path'  => 'items/microphone.jpg',
                'price'       => 8000,
                'category_ids' => [$catElec->id],
                'brand_name'       => 'なし' ,
                'description'  => '高音質のレコーディング用マイク',
                'condition'    => '目立った傷や汚れなし',
            ],
            // ショルダーバッグ
            [
                'item_name'   => 'ショルダーバッグ',
                'image_path'  => 'items/shoulder_bag.jpg',
                'price'       => 3500,
                'category_ids'  => [$catFashion->id, $catMens->id],
                'description'  => 'おしゃれなショルダーバッグ',
                'condition'    => 'やや傷や汚れあり',
            ],
            // タンブラー
            [
                'item_name'   => 'タンブラー',
                'image_path'  => 'items/tumbler.jpg',
                'price'       => 500,
                'category_ids'  => [$catKitchen->id, $catInterior->id],
                'brand_name'       => 'なし' ,
                'description'  => '使いやすいタンブラー',
                'condition'    => '状態が悪い',
            ],
            // コーヒーミル
            [
                'item_name'   => 'コーヒーミル',
                'image_path'  => 'items/coffee_mill.jpg',
                'price'       => 4000,
                'category_ids'  => [$catKitchen->id, $catInterior->id],
                'brand_name'       => 'Starbacks' ,
                'description'  => '手動のコーヒーミル',
                'condition'    => '良好',
            ],
            // メイクセット
            [
                'item_name'   => 'メイクセット',
                'image_path'  => 'items/makeup_set.jpg',
                'price'       => 2500,
                'category_ids'  => [ $catCosme->id,$catLadies->id, $catFashion->id],
                'description'  => '便利なメイクアップセット',
                'condition'    => '目立った傷や汚れなし',
            ],
        ];

        // ✅ 同じ商品を「on_sale」「sold」で2件ずつ作る（元の挙動を踏襲）
        foreach ($items as $data) {
            $item1 = Item::create([
                'seller_id'    => $seller1->id,
                'item_name'    => $data['item_name'],
                'image_path'   => $data['image_path'],
                'price'        => $data['price'],
                'brand_name'   => $data['brand_name'] ?? null,
                'status'       => 'on_sale',
                'description'  => $data['description'],
                'condition'    => $data['condition'],
            ]);

            $item1->categories()->sync($data['category_ids']);

            $item2 = Item::create([
                'seller_id'    => $seller2->id,
                'item_name'    => $data['item_name'],
                'image_path'   => $data['image_path'],
                'price'        => $data['price'],
                'brand_name'   => $data['brand_name'] ?? null,
                'status'       => 'sold',
                'description'  => $data['description'],
                'condition'    => $data['condition'],
            ]);

            $item2->categories()->sync($data['category_ids']);
        }
    }
}