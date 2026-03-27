<?php
// app/Http/Controllers/SellController.php
namespace App\Http\Controllers;

use App\Http\Requests\ExhibitionRequest;
use App\Models\Category;
use App\Models\Item;

class SellController extends Controller
{
    public function create()
    {
        $categories = Category::query()->orderBy('id')->get();

        // ✅「テーブルのconditionから選択項目」＝ items.condition の既存値を選択肢にする
        // まだデータが少ない場合は、fallbackとして固定配列も混ぜる
        $conditions = Item::query()
            ->select('condition')
            ->whereNotNull('condition')
            ->distinct()
            ->pluck('condition')
            ->filter()
            ->values();

        if ($conditions->isEmpty()) {
            $conditions = collect([
                '良好',
                '目立った傷や汚れなし',
                'やや傷や汚れあり',
                '状態が悪い',
            ]);
        }

        return view('items.sell', compact('categories', 'conditions'));
    }

    public function store(ExhibitionRequest $request)
    {
        $user = $request->user();

        // 画像保存
        $path = $request->file('image')->store('items', 'public');

        // まず items テーブルに保存（既存仕様に合わせて category_id は先頭を入れておく）
        $categoryIds = $request->input('category_ids', []);
        $primaryCategoryId = $categoryIds[0] ?? null;

        $rawPrice = $request->input('price');
        $price = (int) str_replace(',', '', $rawPrice);

        $item = Item::create([
            'seller_id' => $user->id,
            'item_name' => $request->item_name,
            'brand_name' => $request->brand_name,
            'description' => $request->description,
            'price' => $price,
            'image_path' => $path,
            'condition' => $request->condition,
            'status' => 'on_sale',
            'category_id' => $primaryCategoryId, // 互換用
        ]);

        // 複数カテゴリは pivot に同期
        $item->categories()->sync($categoryIds);

        return redirect()->route('items.index')->with('message', '出品しました');
    }
}