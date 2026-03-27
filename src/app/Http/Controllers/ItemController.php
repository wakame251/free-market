<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Like;
use App\Models\Comment;
use Illuminate\Http\Request;
use App\Http\Requests\CommentRequest;


class ItemController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'recommend'); // recommend / mylist
        $keyword = $request->query('keyword');      // 検索語

        $query = Item::query()->latest();

        // 2. 検索：商品名（部分一致）
        if (!empty($keyword)) {
            $query->where('item_name', 'like', '%' . $keyword . '%');
        }

        // 4. ログイン済みなら「自分の出品商品は除外」
        if ($request->user()) {
            $query->where('seller_id', '!=', $request->user()->id);
        }

        // マイリストタブ
        if ($tab === 'mylist') {
            // 4. 未認証の場合は何も表示されない
            if (!$request->user()) {
                $items = collect(); // 空コレクション
            } else {
                // 1. いいねした商品だけ
                $userId = $request->user()->id;

            // Itemモデルに likes リレーションがある前提（Item hasMany Like）
            $items = $query->whereHas('likes', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })->get();
        }
    } else {
        // おすすめタブ（未認証でも表示OK）
        $items = $query->get();
    }

    return view('items.index', compact('items', 'tab', 'keyword'));
    }
    public function show($item_id, Request $request)
    {
        $item = Item::with([
                'categories',
                'comments.user',
            ])
            ->withCount([
                'likes',
                'comments',
            ])
            ->findOrFail($item_id);

        // ログイン中ユーザーが「いいね済みか」
        $isLiked = false;
        if ($request->user()) {
            $isLiked = Like::where('item_id', $item->id)
                ->where('user_id', $request->user()->id)
                ->exists();
        }

        return view('items.show', compact('item', 'isLiked'));
    }

    // いいね：押下でトグル（登録/解除）
    public function toggleLike($item_id, Request $request)
    {
        $user = $request->user();
        $item = Item::findOrFail($item_id);

        $like = Like::where('item_id', $item->id)
            ->where('user_id', $user->id)
            ->first();

        if ($like) {
            $like->delete(); // 解除
        } else {
            Like::create([
                'item_id' => $item->id,
                'user_id' => $user->id,
            ]);
        }

        return back();
    }

    // コメント送信
    public function storeComment($item_id, CommentRequest $request)
    {
        $user = $request->user();
        $item = Item::findOrFail($item_id);

        Comment::create([
            'item_id' => $item->id,
            'user_id' => $user->id,
            'body'    => $request->body,
        ]);

        return back();
    }

    // 購入画面（viewのみ）
    public function purchase($item_id)
    {
        $item = Item::with('category')->findOrFail($item_id);

        return view('purchases.create', compact('item'));
    }
}