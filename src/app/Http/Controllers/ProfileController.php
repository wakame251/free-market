<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use App\Models\Item;
use Illuminate\Http\Request;
use App\Models\Order;

class ProfileController extends Controller
{
    /**
     * マイページ（プロフィール表示 + 出品/購入タブ）
     */
    public function show(Request $request)
    {
        $user = $request->user();
        $profile = $user->profile; // nullあり

        // page=sell / page=buy（デフォルトは sell）
        $page = $request->query('page', 'sell');

        if ($page === 'buy') {
            $orders = Order::with('item')
                ->where('buyer_id', $user->id)
                ->latest('purchased_at')
                ->get();

            $items = $orders->pluck('item')->filter(); // itemがnull対策
        } else {
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
        $profile = $user->profile; // nullあり

        return view('mypage.profile_edit', compact('user', 'profile'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'users_name' => ['required', 'string', 'max:20'],
            'post_code' => ['nullable', 'string'],
            'address' => ['nullable', 'string'],
            'building' => ['nullable', 'string'],
            'avatar' => ['nullable', 'image', 'max:2048'],
        ], [
            'users_name.required' => 'お名前を入力してください',
            'users_name.max' => 'お名前は20文字以内で入力してください',
            'avatar.image' => '画像ファイルを選択してください',
            'avatar.max' => '画像サイズは2MB以下にしてください',
        ]);

        $user = $request->user();

        $data = [
            'users_name' => $request->users_name,
            'post_code' => $request->post_code,
            'address' => $request->address,
            'building' => $request->building,
        ];

        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $data['avatar_path'] = $path;
        }

        Profile::updateOrCreate(
            ['user_id' => $user->id],
            $data
        );

        return back()->with('message', '更新しました');
    }
}