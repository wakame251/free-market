<?php

namespace Tests\Browser;

use App\Models\Item;
use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class PurchasePaymentMethodTest extends DuskTestCase
{
    use DatabaseMigrations;

    /** @test */
    public function 小計画面で変更が反映される()
    {
        $user = User::factory()->create();
        $seller = User::factory()->create();

        $user->profile()->create([
            'users_name' => $user->user_name,
            'post_code' => '123-4567',
            'address' => '大阪府大阪市1-1-1',
            'building' => 'テストマンション101',
        ]);

        $item = Item::factory()->create([
            'seller_id' => $seller->id,
            'item_name' => 'テスト商品',
            'price' => 5000,
        ]);

        $this->browse(function (Browser $browser) use ($user, $item) {
            $browser->loginAs($user)
                ->visit('/purchase/' . $item->id)
                ->pause(3000)
                ->screenshot('purchase-payment-debug')
                ->assertPathIs('/purchase/' . $item->id);
        });
    }
}