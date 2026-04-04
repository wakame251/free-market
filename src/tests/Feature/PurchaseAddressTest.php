<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Item;
use App\Models\User;
use Mockery;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PurchaseAddressTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 送付先住所変更画面で登録した住所が商品購入画面に反映される()
    {
        $user = User::factory()->create();
        $seller = User::factory()->create();

        $user->profile()->create([
            'post_code' => '111-1111',
            'address' => '大阪府大阪市旧住所1-1-1',
            'building' => '旧ビル',
        ]);

        $item = Item::factory()->create([
            'seller_id' => $seller->id,
            'item_name' => 'テスト商品',
            'price' => 5000,
        ]);

        $response = $this->actingAs($user)->post(route('purchase.address.update', [
            'item_id' => $item->id,
        ]), [
            'post_code' => '123-4567',
            'address' => '大阪府大阪市新住所2-2-2',
            'building' => '新ビル101',
        ]);

        $response->assertRedirect(route('purchase.create', ['item_id' => $item->id]));

        $purchaseResponse = $this->actingAs($user)->get(route('purchase.create', [
            'item_id' => $item->id,
        ]));

        $purchaseResponse->assertStatus(200);
        $purchaseResponse->assertSee('123-4567');
        $purchaseResponse->assertSee('大阪府大阪市新住所2-2-2');
        $purchaseResponse->assertSee('新ビル101');
        $purchaseResponse->assertDontSee('大阪府大阪市旧住所1-1-1');
        $purchaseResponse->assertDontSee('旧ビル');
    }

    /** @test */
    public function 購入した商品に送付先住所が紐づいて登録される()
    {
        $seller = User::factory()->create();
        $buyer = User::factory()->create();

        $item = Item::factory()->create([
            'seller_id' => $seller->id,
            'item_name' => '配送先付き商品',
            'price' => 8000,
            'status' => 'on_sale',
        ]);

        $payloadObject = (object) [
            'type' => 'checkout.session.completed',
            'data' => (object) [
                'object' => (object) [
                    'id' => 'cs_test_address_001',
                    'metadata' => (object) [
                        'item_id' => (string) $item->id,
                        'buyer_id' => (string) $buyer->id,
                        'payment_method' => 'card',
                        'post_code' => '987-6543',
                        'address' => '東京都新宿区3-3-3',
                        'building' => '配送先マンション505',
                        'price_at_purchase' => (string) $item->price,
                    ],
                ],
            ],
        ];

        Mockery::mock('alias:\Stripe\Webhook')
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
            'post_code' => '987-6543',
            'address' => '東京都新宿区3-3-3',
            'building' => '配送先マンション505',
        ]);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}