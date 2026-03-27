<?php

use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\PurchaseAddressController;
use App\Http\Controllers\StripeWebhookController;
use App\Http\Controllers\SellController;


Route::get('/email/verify', function () {
    return view('auth.verify-email'); // ★自作ビュー
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect('/mypage/profile'); // 認証後の遷移先
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('message', '認証メールを再送しました');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

Route::get('/', [ItemController::class, 'index'])->name('items.index');

Route::get('/item/{item_id}', [ItemController::class, 'show'])->name('items.show');


// いいね・コメントはログイン必須のはずなので middleware を付けるのがおすすめ
Route::middleware('auth')->group(function () {
    Route::post('/item/{item_id}/like', [ItemController::class, 'toggleLike'])->name('items.like.toggle');
    Route::post('/item/{item_id}/comment', [ItemController::class, 'storeComment'])->name('items.comment.store');
});

// 購入画面
Route::middleware('auth')->group(function () {
    // 購入画面（表示）
    Route::get('/purchase/{item_id}', [PurchaseController::class, 'create'])->name('purchase.create');

    // 購入（確定）
    Route::post('/purchase/{item_id}', [PurchaseController::class, 'store'])->name('purchase.store');

     // Stripeから戻る表示用（確定はWebhook）
    Route::get('/purchase/{item_id}/success', [PurchaseController::class, 'success'])->name('purchase.success');
    Route::get('/purchase/{item_id}/cancel', [PurchaseController::class, 'cancel'])->name('purchase.cancel');

    // 住所変更画面（表示）
    Route::get('/purchase/address/{item_id}', [PurchaseAddressController::class, 'edit'])->name('purchase.address.edit');

    // 住所変更（保存：session）
    Route::post('/purchase/address/{item_id}', [PurchaseAddressController::class, 'update'])->name('purchase.address.update');

    Route::get('/purchase/{item_id}', [PurchaseController::class, 'create'])
  ->name('purchase.create');
});

Route::post('/stripe/webhook', [StripeWebhookController::class, 'handle'])
    ->name('stripe.webhook');
    
Route::middleware('auth','verified')->group(function () {
    // プロフィール表示
    Route::get('/mypage', [ProfileController::class, 'show'])->name('profile.show');

    // プロフィール編集
    Route::get('/mypage/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/mypage/profile', [ProfileController::class, 'update'])->name('profile.update');

    // ✅ 出品画面
    Route::get('/sell', [SellController::class, 'create'])->name('sell.create');
    // ✅ 出品保存
    Route::post('/sell', [SellController::class, 'store'])->name('sell.store');
});

