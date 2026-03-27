@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/profile_show.css') }}">
@endsection

@section('content')
<div class="mypage">
  <div class="mypage__top">
    <div class="mypage__profile">
      <div class="mypage__avatar">
        @if (!empty($profile?->avatar_path))
          <img src="{{ asset('storage/' . $profile->avatar_path) }}" alt="avatar">
        @else
          <div class="mypage__avatar-placeholder"></div>
        @endif
      </div>

      <div class="mypage__name">
        {{ $profile->users_name ?? $user->user_name ?? 'ユーザー名' }}
      </div>

      <div class="mypage__edit">
        <a class="mypage__edit-button" href="{{ route('profile.edit') }}">プロフィールを編集</a>
      </div>
    </div>
  </div>

  <div class="mypage__tabs">
    <a
      class="mypage__tab {{ ($page ?? request('page','sell')) === 'sell' ? 'is-active' : '' }}"
      href="{{ route('profile.show', ['page' => 'sell']) }}"
    >出品した商品</a>

    <a
      class="mypage__tab {{ ($page ?? request('page','sell')) === 'buy' ? 'is-active' : '' }}"
      href="{{ route('profile.show', ['page' => 'buy']) }}"
    >購入した商品</a>
  </div>

  <div class="mypage__divider"></div>

  <div class="mypage__grid">
    @forelse ($items as $item)
      <a class="mypage-card" href="{{ route('items.show', ['item_id' => $item->id]) }}">
        <div class="mypage-card__image-wrap">
          @if ($item->image_path)
            <img class="mypage-card__image" src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->item_name }}">
          @else
            <div class="mypage-card__image-placeholder">商品画像</div>
          @endif
        </div>
        <div class="mypage-card__name">{{ $item->item_name }}</div>
      </a>
    @empty
      <p class="mypage__empty">該当する商品がありません</p>
    @endforelse
  </div>
</div>
@endsection