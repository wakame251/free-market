<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;

class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $secret = config('services.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $secret);
        } catch (\UnexpectedValueException $e) {
            return response('Invalid payload', 400);
        } catch (SignatureVerificationException $e) {
            return response('Invalid signature', 400);
        }

        // 決済完了
        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;

            // 二重処理防止（stripe_session_id unique）
            DB::transaction(function () use ($session) {
                $itemId = (int)($session->metadata->item_id ?? 0);
                $buyerId = (int)($session->metadata->buyer_id ?? 0);

                if (!$itemId || !$buyerId) return;

                $item = Item::lockForUpdate()->find($itemId);
                if (!$item) return;

                // すでに売れてたら何もしない
                if ($item->order()->exists()) return;

                // order作成
                Order::create([
                    'item_id'           => $item->id,
                    'stripe_session_id' => $session->id,
                    'buyer_id'          => $buyerId,
                    'payment_method'    => $session->metadata->payment_method ?? 'card',
                    'post_code'         => $session->metadata->post_code ?? '',
                    'address'           => $session->metadata->address ?? '',
                    'building'          => $session->metadata->building ?? null,
                    'price_at_purchase' => (int)($session->metadata->price_at_purchase ?? $item->price),
                    'purchased_at'      => now(),
                ]);

                // items側のstatusも更新（一覧のSold表示用）
                $item->update(['status' => 'sold']);
            });
        }

        return response('ok', 200);
    }
}
