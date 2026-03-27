@extends('layouts.app')

@section('title', 'プロフィール設定')

@section('css')
<link rel="stylesheet" href="{{ asset('css/profile_edit.css') }}">
@endsection

@section('content')
<div class="profile">
  <h1 class="profile__title">プロフィール設定</h1>

  @if (session('message'))
    <p class="profile__message">{{ session('message') }}</p>
  @endif

  @if ($errors->any())
    <ul class="profile__errors">
      @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
  @endif

  <form class="profile__form" action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="profile__image-area">
      <div class="profile__avatar" id="avatarPreview">
        @if (!empty($profile?->avatar_path))
          <img
            id="avatarPreviewImage"
            class="profile__icon-img"
            src="{{ asset('storage/' . $profile->avatar_path) }}"
            alt="icon"
          >
        @else
          <div id="avatarPlaceholder" class="profile__icon-placeholder"></div>
          <img
            id="avatarPreviewImage"
            class="profile__icon-img"
            src=""
            alt="icon"
            style="display: none;"
          >
        @endif
      </div>

      <label class="profile__image-button">
        画像を選択する
        <input type="file" name="avatar" id="avatarInput" accept="image/*" hidden>
      </label>
    </div>

    <label class="profile__label">ユーザー名</label>
    <input
      class="profile__input"
      type="text"
      name="users_name"
      value="{{ old('users_name', $profile->users_name ?? '') }}"
    >

    <label class="profile__label">郵便番号</label>
    <input
      class="profile__input"
      type="text"
      name="post_code"
      value="{{ old('post_code', $profile->post_code ?? '') }}"
    >

    <label class="profile__label">住所</label>
    <input
      class="profile__input"
      type="text"
      name="address"
      value="{{ old('address', $profile->address ?? '') }}"
    >

    <label class="profile__label">建物名</label>
    <input
      class="profile__input"
      type="text"
      name="building"
      value="{{ old('building', $profile->building ?? '') }}"
    >

    <button class="profile__submit" type="submit">更新する</button>
  </form>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('avatarInput');
    const previewImage = document.getElementById('avatarPreviewImage');
    const placeholder = document.getElementById('avatarPlaceholder');

    input.addEventListener('change', function (event) {
      const file = event.target.files[0];
      if (!file) return;

      if (!file.type.startsWith('image/')) {
        alert('画像ファイルを選択してください。');
        input.value = '';
        return;
      }

      const reader = new FileReader();

      reader.onload = function (e) {
        previewImage.src = e.target.result;
        previewImage.style.display = 'block';

        if (placeholder) {
          placeholder.style.display = 'none';
        }
      };

      reader.readAsDataURL(file);
    });
  });
</script>
@endsection