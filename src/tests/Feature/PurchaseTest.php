<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Item;
use App\Models\Order;
use App\Models\User;
use Mockery;
use Stripe\Webhook;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PurchaseTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function webhook完了で購入が完了する()
    {
        $seller = User::factory()->create();
        $buyer = User::factory()->create();

        $item = Item::factory()->create([
            'seller_id' => $seller->id,
            'item_name' => 'テスト商品',
            'price' => 5000,
            'status' => 'on_sale',
        ]);

        $payloadObject = (object) [
            'type' => 'checkout.session.completed',
            'data' => (object) [
                'object' => (object) [
                    'id' => 'cs_test_123',
                    'metadata' => (object) [
                        'item_id' => (string) $item->id,
                        'buyer_id' => (string) $buyer->id,
                        'payment_method' => 'card',
                        'post_code' => '123-4567',
                        'address' => '大阪府大阪市1-1-1',
                        'building' => 'テストマンション101',
                        'price_at_purchase' => (string) $item->price,
                    ],
                ],
            ],
        ];

        Mockery::mock('alias:' . Webhook::class)
            ->shouldReceive('constructEvent')
            ->once()
            ->andReturn($payloadObject);

        $response = $this->post('/stripe/webhook', [], [
            'Stripe-Signature' => 'test-signature',
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('orders', [
            'item_id' => $item->id,
            'buyer_id' => $buyer->id,
            'stripe_session_id' => 'cs_test_123',
            'payment_method' => 'card',
            'post_code' => '123-4567',
            'address' => '大阪府大阪市1-1-1',
            'building' => 'テストマンション101',
            'price_at_purchase' => 5000,
        ]);

        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'status' => 'sold',
        ]);
    }

    /** @test */
    public function 購入した商品は商品一覧画面でsoldと表示される()
    {
        $seller = User::factory()->create();
        $buyer = User::factory()->create();

        $item = Item::factory()->create([
            'seller_id' => $seller->id,
            'item_name' => '購入済み商品',
            'price' => 5000,
            'status' => 'on_sale',
        ]);

        Order::create([
            'item_id' => $item->id,
            'buyer_id' => $buyer->id,
            'stripe_session_id' => 'cs_test_456',
            'payment_method' => 'card',
            'post_code' => '123-4567',
            'address' => '大阪府大阪市1-1-1',
            'building' => 'テストマンション101',
            'price_at_purchase' => $item->price,
            'purchased_at' => now(),
        ]);

        $item->update(['status' => 'sold']);

        $response = $this->get(route('items.index'));

        $response->assertStatus(200);
        $response->assertSee('購入済み商品');
        $response->assertSee('Sold');
    }

    /** @test */
    public function 購入した商品はプロフィールの購入した商品一覧に追加されている()
    {
        $seller = User::factory()->create();
        $buyer = User::factory()->create();

        $item = Item::factory()->create([
            'seller_id' => $seller->id,
            'item_name' => 'プロフィール購入商品',
            'price' => 7000,
            'status' => 'on_sale',
        ]);

        Order::create([
            'item_id' => $item->id,
            'buyer_id' => $buyer->id,
            'stripe_session_id' => 'cs_test_789',
            'payment_method' => 'konbini',
            'post_code' => '123-4567',
            'address' => '大阪府大阪市1-1-1',
            'building' => 'テストマンション101',
            'price_at_purchase' => $item->price,
            'purchased_at' => now(),
        ]);

        $item->update(['status' => 'sold']);

        $response = $this->actingAs($buyer)->get(route('profile.show', [
            'page' => 'buy',
        ]));

        $response->assertStatus(200);
        $response->assertSee('プロフィール購入商品');
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}