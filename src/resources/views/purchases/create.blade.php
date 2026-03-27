@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/purchase.css') }}">
@endsection

@section('header')
<x-headers.authed />
@endsection

@section('content')
<form method="POST" action="{{ route('purchase.store', ['item_id' => $item->id]) }}">
  @csrf

  {{-- 配送先（必須） --}}
  <input type="hidden" name="post_code" value="{{ $post_code }}">
  <input type="hidden" name="address" value="{{ $address }}">
  <input type="hidden" name="building" value="{{ $building }}">

  <div class="purchase">
    <div class="purchase__left">
      <div class="purchase__item">
        <div class="purchase__image">
          @if ($item->image_path)
            <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->item_name }}">
          @else
            <div class="purchase__image-placeholder">商品画像</div>
          @endif
        </div>

        <div class="purchase__info">
          <div class="purchase__name">{{ $item->item_name }}</div>
          <div class="purchase__price">¥{{ number_format($item->price) }}</div>
        </div>
      </div>

      <hr class="purchase__hr">

      <div class="purchase__section">
        <div class="purchase__label">支払い方法</div>

        @error('payment_method')
          <div class="form-error">{{ $message }}</div>
        @enderror

          <select class="purchase__select" name="payment_method" id="payment_method" dusk="payment-method-select">
            <option value="" selected>選択してください</option>
            <option value="konbini">コンビニ払い</option>
            <option value="card">カード払い</option>
          </select>
      </div>

      <hr class="purchase__hr">

      <div class="purchase__section purchase__address">
        <div class="purchase__label">配送先</div>
        <a class="purchase__change" href="{{ route('purchase.address.edit', ['item_id' => $item->id]) }}">変更する</a>

        @error('post_code')
          <div class="form-error">{{ $message }}</div>
        @enderror
        @error('address')
          <div class="form-error">{{ $message }}</div>
        @enderror

        <div class="purchase__address-text">
          〒 {{ $post_code ?: '（未設定）' }}<br>
          {{ $address ?: '（未設定）' }}<br>
          {{ $building }}
        </div>
      </div>
    </div>

    <div class="purchase__right">
      <div class="purchase__summary">
        <div class="purchase__row">
          <div class="purchase__row-label">商品代金</div>
          <div class="purchase__row-value">¥{{ number_format($item->price) }}</div>
        </div>
        <div class="purchase__row">
          <div class="purchase__row-label">支払い方法</div>
          <div class="purchase__row-value" id="summary_payment" dusk="summary-payment">未選択</div>
        </div>
      </div>

      <button class="purchase__btn" type="submit">購入する</button>
    </div>
  </div>
</form>

<script>
  const select = document.getElementById('payment_method');
  const summary = document.getElementById('summary_payment');

  const label = (v) => {
    if (v === 'konbini') return 'コンビニ払い';
    if (v === 'card') return 'カード払い';
    return '未選択';
  };

  // 初期反映
  summary.textContent = label(select.value);

  // 即時反映
  select.addEventListener('change', () => {
    summary.textContent = label(select.value);
  });
</script>
@endsection