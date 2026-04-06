<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Item;
use App\Models\User;
use Illuminate\Database\Seeder;

class  DemoItemSeeder extends Seeder
{
    public function run(): void
    {
        $user1 = User::where('email', 'test@userone.com')->firstOrFail();
        $user2 = User::where('email', 'test@usertwo.com')->firstOrFail();

        $catToy = Category::firstOrCreate(['name' => 'おもちゃ']);
        $catKids = Category::firstOrCreate(['name' => 'ベビー・キッズ']);
        $catElec = Category::firstOrCreate(['name' => '家電']);

        $items = [
            [
                'seller_id'    => $user1->id,
                'item_name'    => 'ピカチュウぬいぐるみ',
                'image_path'   => 'images/sample/items/pikachu.jpg',
                'price'        => 13000,
                'brand_name'   => 'ポケモン',
                'description'  => '限定生産のレア品です。',
                'condition'    => '良好',
                'status'       => 'sold',
                'category_id'  => $catToy->id,
                'category_ids' => [$catToy->id, $catKids->id],
            ],
            [
                'seller_id'    => $user1->id,
                'item_name'    => 'iPad',
                'image_path'   => 'images/sample/items/ipad.jpg',
                'price'        => 20000,
                'brand_name'   => 'Apple',
                'description'  => '背面に少し傷がありますが、使用には問題ありません。',
                'condition'    => 'やや傷や汚れあり',
                'status'       => 'on_sale',
                'category_id'  => $catElec->id,
                'category_ids' => [$catElec->id],
            ],
            [
                'seller_id'    => $user2->id,
                'item_name'    => '木馬のおもちゃ',
                'image_path'   => 'images/sample/items/rocking_horse.jpg',
                'price'        => 5000,
                'brand_name'   => null,
                'description'  => '2歳児～5歳児向けの、乗るとゆらゆら揺れる木馬のおもちゃです。',
                'condition'    => '良好',
                'status'       => 'sold',
                'category_id'  => $catToy->id,
                'category_ids' => [$catToy->id, $catKids->id],
            ],
        ];

        foreach ($items as $data) {
            $categoryIds = $data['category_ids'];
            unset($data['category_ids']);

            $item = Item::updateOrCreate(
                [
                    'seller_id' => $data['seller_id'],
                    'item_name' => $data['item_name'],
                ],
                $data
            );

            $item->categories()->sync($categoryIds);
        }
    }
}