@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/address_edit.css') }}">
@endsection

@section('header')
<x-headers.authed />
@endsection

@section('content')
<div class="address-edit">
  <h1 class="address-edit__title">住所の変更</h1>

  <form class="address-edit__form" method="POST" action="{{ route('purchase.address.update', ['item_id' => $item->id]) }}">
    @csrf

    <div class="address-edit__field">
      <label class="address-edit__label">郵便番号</label>
      <input class="address-edit__input" type="text" name="post_code" value="{{ $post_code }}" placeholder="123-4567">
    </div>

    <div class="address-edit__field">
      <label class="address-edit__label">住所</label>
      <input class="address-edit__input" type="text" name="address" value="{{ $address }}">
    </div>

    <div class="address-edit__field">
      <label class="address-edit__label">建物名</label>
      <input class="address-edit__input" type="text" name="building" value="{{ $building }}">
    </div>

    <button class="address-edit__btn" type="submit">更新する</button>
  </form>
</div>
@endsection