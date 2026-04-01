<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use App\Models\Item;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Requests\ProfileRequest;

class ProfileController extends Controller
{
    /**
     * マイページ（プロフィール表示 + 出品/購入タブ）
     */
    public function show(Request $request)
    {
        $user = $request->user();
        $profile = $user->profile;

        // page=sell / page=buy（デフォルト sell）
        $page = $request->query('page', 'sell');

        if ($page === 'buy') {
            // 購入商品（Order経由）
            $items = Order::with('item')
                ->where('buyer_id', $user->id)
                ->latest('purchased_at')
                ->get()
                ->pluck('item')
                ->filter(); // itemがnullの対策
        } else {
            // 出品商品
            $items = Item::query()
                ->where('seller_id', $user->id)
                ->latest('id')
                ->get();
        }

        return view('mypage.profile_show', compact('user', 'profile', 'items', 'page'));
    }

    /**
     * プロフィール編集画面
     */
    public function edit(Request $request)
    {
        $user = $request->user();
        $profile = $user->profile;

        return view('mypage.profile_edit', compact('user', 'profile'));
    }

    /**
     * プロフィール更新
     */
    public function update(ProfileRequest $request)
    {
        $user = $request->user();

        // users テーブル側の user_name を更新
        $user->update([
            'user_name' => $request->user_name,
        ]);

        // profiles テーブル側の情報を更新
        $data = [
            'post_code' => $request->post_code,
            'address'   => $request->address,
            'building'  => $request->building,
        ];

        // 画像アップロード
        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $data['avatar_path'] = $path;
        }

        Profile::updateOrCreate(
            ['user_id' => $user->id],
            $data
        );

        return redirect()
            ->route('profile.show')
            ->with('message', 'プロフィールを更新しました');
    }
}