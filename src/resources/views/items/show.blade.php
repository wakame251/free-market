@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/show.css') }}">
@endsection


@section('content')
<div class="item-show">
  <div class="item-show__main">
    {{-- 左：商品画像 --}}
    <div class="item-show__image">
      @if ($item->image_path)
        <img class="item-show__image-img" src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->item_name }}">
      @else
        <div class="item-show__image-placeholder">商品画像</div>
      @endif
    </div>

    {{-- 右：商品情報 --}}
    <div class="item-show__info">
      <h1 class="item-show__name">{{ $item->item_name }}</h1>

      {{-- ブランド名（無い場合は空表示） --}}
      <div class="item-show__brand">{{ $item->brand_name ?? '' }}</div>

      <div class="item-show__price">¥{{ number_format($item->price) }} <span class="item-show__tax">(税込)</span></div>

    <div class="item-show__counts">
      <div class="item-show__count">
        @auth
          <form method="POST" action="{{ route('items.like.toggle', ['item_id' => $item->id]) }}">
            @csrf
            <button type="submit" class="like-button {{ $isLiked ? 'is-liked' : '' }}" aria-label="いいね">
              <img
                src="{{ asset($isLiked ? 'images/icon-like-active.png' : 'images/icon-like-default.png') }}"
                alt="いいね"
                class="like-button__icon-img"
              >
              <span class="item-show__num">{{ $item->likes_count }}</span>
            </button>
          </form>
        @else
          <div class="like-button is-disabled" title="ログインするといいねできます">
            <img
              src="{{ asset('images/icon-like-default.png') }}"
              alt="いいね"
              class="like-button__icon-img"
            >
            <span class="item-show__num">{{ $item->likes_count }}</span>
          </div>
        @endauth
      </div>

  <div class="item-show__count">
    <img
      src="{{ asset('images/icon-comment.png') }}"
      alt="コメント"
      class="item-show__icon-img"
    >
    <span class="item-show__num">{{ $item->comments_count }}</span>
  </div>
</div>

      {{-- 購入ボタン（売り切れは無効っぽく） --}}
      <div class="item-show__purchase">
        @if ($item->is_sold)
          <button class="item-show__purchase-btn is-disabled" disabled>売り切れ</button>
        @else
          <a class="item-show__purchase-btn" href="/purchase/{{ $item->id }}">購入手続きへ</a>
        @endif
      </div>

      <div class="item-show__section">
        <h2 class="item-show__section-title">商品説明</h2>
        <div class="item-show__desc">
          <div class="item-show__desc-row">カラー：{{ $item->color ?? '—' }}</div>
          <div class="item-show__desc-row">{{ $item->description ?? '（説明文が未設定です）' }}</div>
        </div>
      </div>

      <div class="item-show__section">
        <h2 class="item-show__section-title">商品の情報</h2>
        <div class="item-show__meta">
          <div class="item-show__meta-row">
            <div class="item-show__meta-label">カテゴリー</div>
            <div class="item-show__meta-value">
              @forelse ($item->categories as $category)
                <span class="item-show__category-tag">{{ $category->name }}</span>
              @empty
    —
              @endforelse
            </div>
          </div>

          <div class="item-show__meta-row">
            <div class="item-show__meta-label">商品の状態</div>
            <div class="item-show__meta-value">
              {{ $item->condition_label ?? ($item->condition ?? '—') }}
            </div>
          </div>
        </div>
      </div>

      {{-- コメント一覧 --}}
      <div class="item-show__section">
        <h2 class="item-show__section-title">コメント（{{ $item->comments_count }}）</h2>

        <div class="item-show__comments">
          @forelse ($item->comments as $comment)
            <div class="comment">
              <div class="comment__user">
                <div class="comment__avatar"></div>
                <div class="comment__name">{{ $comment->user->user_name ?? '名無し' }}</div>
              </div>
              <div class="comment__body">{{ $comment->body }}</div>
            </div>
          @empty
            <div class="item-show__no-comments">まだコメントはありません</div>
          @endforelse
        </div>
      </div>

      {{-- コメント投稿フォーム --}}
      <div class="item-show__section">
        <h2 class="item-show__section-title">商品へのコメント</h2>

        @auth
          @if ($errors->any())
            <div class="form-error">
              <ul>
                @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          @endif

          <form class="item-show__comment-form" method="POST" action="{{ route('items.comment.store', ['item_id' => $item->id]) }}">
            @csrf
            <textarea class="item-show__comment-textarea" name="body" rows="5">{{ old('body') }}</textarea>
            <button class="item-show__comment-submit" type="submit">コメントを送信する</button>
          </form>
        @else
          <div class="item-show__login-note">
            コメントするにはログインが必要です。
          </div>
        @endauth
      </div>
    </div>
  </div>
</div>
@endsection