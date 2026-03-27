<header class="header">
  <div class="header__inner header__inner--authed">
    <div class="header__brand">
      <a href="{{ route('items.index') }}" class="header__logo-link">
        <img src="{{ asset('images/coachtech-logo.png') }}" alt="COACHTECH" class="header__logo-img">
      </a>
    </div>

    <form class="header__search" action="{{ route('items.index') }}" method="GET">
      <input type="hidden" name="tab" value="{{ request('tab', 'recommend') }}">
      <input
        class="header__search-input"
        type="text"
        name="keyword"
        value="{{ request('keyword') }}"
        placeholder="なにをお探しですか？"
      >
    </form>

    <nav class="header__nav">
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button class="header__nav-link" type="submit">ログアウト</button>
      </form>
      <a class="header__nav-link" href="/mypage">マイページ</a>
      <a class="header__nav-button" href="/sell">出品</a>
    </nav>
  </div>
</header>