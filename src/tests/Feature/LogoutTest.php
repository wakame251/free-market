<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    /**
     * ログアウトができる
     */
    public function test_user_can_logout(): void
    {
        // 1. ユーザー作成
        $user = User::factory()->create();

        // 2. ログイン状態にする
        $this->actingAs($user);

        // 3. ログアウトリクエスト送信
        $response = $this->post(route('logout'));

        // 4. リダイレクト確認（遷移先は実装に合わせて）
        $response->assertRedirect('/login');

        // 5. 未ログイン状態になっているか確認
        $this->assertGuest();
    }
}