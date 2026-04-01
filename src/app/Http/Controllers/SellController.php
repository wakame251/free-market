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

        // category_ids を 0 始まりの配列に詰め直す
        $categoryIds = $request->input('category_ids', []);
        $categoryIds = is_array($categoryIds) ? array_values($categoryIds) : [];
        $primaryCategoryId = $categoryIds[0] ?? null;

        $rawPrice = $request->input('price');
        $price = (int) str_replace(',', '', $rawPrice);

        $item = Item::create([
            'seller_id'   => $user->id,
            'item_name'   => $request->item_name,
            'brand_name'  => $request->brand_name,
            'description' => $request->description,
            'price'       => $price,
            'image_path'  => $path,
            'condition'   => $request->condition,
            'status'      => 'on_sale',
            'category_id' => $request->category_ids[0] ?? null, // 先頭カテゴリを代表カテゴリとして保存
        ]);

        $item->categories()->sync($categoryIds);

        return redirect()->route('items.index')->with('message', '出品しました');
    }
}