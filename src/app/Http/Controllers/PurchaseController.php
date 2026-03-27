<?php

namespace App\Http\Controllers;

use App\Http\Requests\PurchaseRequest;
use App\Models\Item;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session as CheckoutSession;

class PurchaseController extends Controller
{
    public function create($item_id, Request $request)
    {
        $user = $request->user();
        $item = Item::with('order')->findOrFail($item_id);

        if ($item->is_sold) {
            return redirect()->route('items.show', ['item_id' => $item->id])
                ->with('message', 'この商品は売り切れです。');
        }

        // 配送先：session優先 → profile
        $sessionKey = "purchase_address.{$item->id}";
        $addr = session($sessionKey);
        $profile = $user->profile;

        $post_code = $addr['post_code'] ?? ($profile->post_code ?? '');
        $address   = $addr['address']   ?? ($profile->address ?? '');
        $building  = $addr['building']  ?? ($profile->building ?? '');

        return view('purchases.create', compact('item','post_code','address','building'));
    }

    public function store($item_id, PurchaseRequest $request)
    {
        $user = $request->user();
        $item = Item::with('order')->findOrFail($item_id);

        if ($item->is_sold) {
            return redirect()->route('items.show', ['item_id' => $item->id])
                ->with('message', 'この商品は売り切れです。');
        }

        Stripe::setApiKey(config('services.stripe.secret'));

        $paymentMethodTypes = $request->payment_method === 'konbini'
            ? ['konbini']   // コンビニ決済 :contentReference[oaicite:2]{index=2}
            : ['card'];

        $appUrl = rtrim(config('app.url'), '/');

        // Checkout Session作成
        $session = CheckoutSession::create([
            'mode' => 'payment',
            'payment_method_types' => $paymentMethodTypes,
            'line_items' => [[
                'quantity' => 1,
                'price_data' => [
                    'currency' => 'jpy',
                    'unit_amount' => $item->price, // 円（整数）
                    'product_data' => [
                        'name' => $item->item_name,
                    ],
                ],
            ]],
            // ここは「表示用」：確定はWebhookで :contentReference[oaicite:3]{index=3}
            'success_url' => $appUrl . route('purchase.success', ['item_id' => $item->id], false) . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url'  => $appUrl . route('purchase.cancel',  ['item_id' => $item->id], false),

            // Webhookで注文確定するために必要情報を入れておく
            'client_reference_id' => (string)$user->id,
            'metadata' => [
                'item_id' => (string)$item->id,
                'buyer_id' => (string)$user->id,
                'payment_method' => $request->payment_method,
                'post_code' => $request->post_code,
                'address' => $request->address,
                'building' => $request->building ?? '',
                'price_at_purchase' => (string)$item->price,
            ],
        ]);

        // Stripe決済画面へ
        return redirect()->away($session->url);
    }

    public function success($item_id, Request $request)
    {
        // ここでは「購入完了しました」と表示するだけ（確定はWebhook）
        return redirect()->route('profile.show', ['page' => 'buy'])
            ->with('message', '決済処理が完了しました（反映まで少し時間がかかる場合があります）。');
    }

    public function cancel($item_id)
    {
        return redirect()->route('purchase.create', ['item_id' => $item_id])
            ->with('message', '決済をキャンセルしました。');
    }
}