<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddressRequest;
use App\Models\Item;
use Illuminate\Http\Request;

class PurchaseAddressController extends Controller
{
    public function edit($item_id, Request $request)
    {
        $user = $request->user();
        $item = Item::findOrFail($item_id);

        $sessionKey = "purchase_address.{$item->id}";
        $addr = session($sessionKey);

        $profile = $user->profile; // nullあり
        $post_code = old('post_code', $addr['post_code'] ?? ($profile->post_code ?? ''));
        $address   = old('address',   $addr['address']   ?? ($profile->address ?? ''));
        $building  = old('building',  $addr['building']  ?? ($profile->building ?? ''));

        return view('purchases.address_edit', compact('item', 'post_code', 'address', 'building'));
    }

    public function update($item_id, AddressRequest $request)
    {
        $item = Item::findOrFail($item_id);

        session()->put("purchase_address.{$item->id}", [
            'post_code' => $request->post_code,
            'address'   => $request->address,
            'building'  => $request->building,
        ]);

        return redirect()->route('purchase.create', ['item_id' => $item->id])
            ->with('message', '配送先を更新しました。');
    }
}