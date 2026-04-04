@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection

@section('content')
<div class="item-index">
  <div class="item-index__tabs">
    <a class="item-index__tab {{ ($tab ?? request('tab','recommend')) !== 'mylist' ? 'is-active' : '' }}"
      href="{{ route('items.index', ['tab' => 'recommend', 'keyword' => $keyword ?? request('keyword')]) }}">
      おすすめ
    </a>

    <a class="item-index__tab {{ ($tab ?? request('tab','recommend')) === 'mylist' ? 'is-active' : '' }}"
      href="{{ route('items.index', ['tab' => 'mylist', 'keyword' => $keyword ?? request('keyword')]) }}">
      マイリスト
    </a>
  </div>

  <div class="item-grid">
    @foreach ($items as $item)
      <a class="item-card" href="{{ route('items.show', ['item_id' => $item->id]) }}">
        <div class="item-card__image-wrap">
          @if ($item->is_sold)
            <div class="item-card__sold">Sold</div>
          @endif

          @if ($item->image_url)
            <img class="item-card__image" src="{{ $item->image_url }}" alt="{{ $item->item_name }}">
          @else
            <div class="item-card__image-placeholder">商品画像</div>
          @endif
        </div>

        <div class="item-card__name">{{ $item->item_name }}</div>
      </a>
    @endforeach
  </div>
</div>
@endsection