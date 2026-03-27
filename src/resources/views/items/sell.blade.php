@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/sell.css') }}">
@endsection

@section('content')
<div class="sell">
  <h1 class="sell__title">商品の出品</h1>

  {{-- エラー表示 --}}
  @if ($errors->any())
    <div class="sell__errors">
      <ul>
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form class="sell__form" action="{{ route('sell.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
  {{-- 商品画像 --}}
    <div class="sell__section">
      <div class="sell__label">商品画像</div>

      <label class="sell__drop" for="sell-image-input">
        <input
          id="sell-image-input"
          class="sell__file"
          type="file"
          name="image"
          accept="image/jpeg,image/png"
        >

        {{-- 初期表示（未選択） --}}
        <span id="sell-image-placeholder" class="sell__drop-btn">画像を選択する</span>

        {{-- プレビュー表示（選択後に表示） --}}
        <img
          id="sell-image-preview"
          class="sell__preview"
          src=""
          alt="preview"
          style="display:none;"
        >
      </label>
    </div>

  {{-- 商品の詳細 --}}
    <div class="sell__section">
      <div class="sell__section-title">商品の詳細</div>
      <div class="sell__divider"></div>

      <div class="sell__label">カテゴリー</div>
      <div class="sell__chips">

      @foreach ($categories as $category)

      <label class="chip">

      <input
      type="checkbox"
      name="category_ids[]"
      value="{{ $category->id }}"
      {{ in_array($category->id, old('category_ids', [])) ? 'checked' : '' }}
      >

      <span class="chip__body">
      {{ $category->name }}
      </span>

      </label>

      @endforeach

      </div>

      <div class="sell__label">商品の状態</div>
      <div class="sell__select-wrap">
        <select class="sell__select" name="condition">
          <option value="">選択してください</option>
          @foreach ($conditions as $c)
            <option value="{{ $c }}" {{ old('condition') === $c ? 'selected' : '' }}>{{ $c }}</option>
          @endforeach
        </select>
      </div>
    </div>

  {{-- 商品名と説明 --}}
    <div class="sell__section">
      <div class="sell__section-title">商品名と説明</div>
      <div class="sell__divider"></div>

      <label class="sell__label">商品名</label>
      <input class="sell__input" type="text" name="item_name" value="{{ old('item_name') }}">

      <label class="sell__label">ブランド名</label>
      <input class="sell__input" type="text" name="brand_name" value="{{ old('brand_name') }}">

      <label class="sell__label">商品の説明</label>
      <textarea class="sell__textarea" name="description">{{ old('description') }}</textarea>

      <label class="sell__label">販売価格</label>
      <div class="sell__price">
        <span class="sell__yen">¥</span>
        <input class="sell__input sell__input--price" type="text" name="price" value="{{ old('price') }}">
      </div>
    </div>

    <button class="sell__submit" type="submit">出品する</button>
  </form>
</div>

<script>
  (function () {
    const input = document.getElementById('sell-image-input');
    const preview = document.getElementById('sell-image-preview');
    const placeholder = document.getElementById('sell-image-placeholder');

    if (!input || !preview || !placeholder) return;

    input.addEventListener('change', function (e) {
      const file = e.target.files && e.target.files[0];

      // 選択解除された場合
      if (!file) {
        preview.src = '';
        preview.style.display = 'none';
        placeholder.style.display = 'inline-block';
        return;
      }

      // jpeg/png 以外は念のため弾く（バリデーションはサーバ側でもされる）
      const okTypes = ['image/jpeg', 'image/png'];
      if (!okTypes.includes(file.type)) {
        alert('画像は JPEG または PNG を選択してください。');
        input.value = '';
        preview.src = '';
        preview.style.display = 'none';
        placeholder.style.display = 'inline-block';
        return;
      }

      // 既存のURLがあれば開放
      if (preview.dataset.objectUrl) {
        URL.revokeObjectURL(preview.dataset.objectUrl);
      }

      const objectUrl = URL.createObjectURL(file);
      preview.dataset.objectUrl = objectUrl;
      preview.src = objectUrl;

      placeholder.style.display = 'none';
      preview.style.display = 'block';
    });

    // ページ離脱時に開放（メモリリーク防止）
    window.addEventListener('beforeunload', function () {
      if (preview.dataset.objectUrl) {
        URL.revokeObjectURL(preview.dataset.objectUrl);
      }
    });
  })();
</script>

@endsection