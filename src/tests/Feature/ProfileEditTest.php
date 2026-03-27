<?php

namespace Tests\Feature;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileEditTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 変更項目が初期値として過去設定されていること
     * （プロフィール画像、ユーザー名、郵便番号、住所）
     */
    public function test_profile_edit_screen_displays_existing_profile_values(): void
    {
        // 1. ユーザー作成
        $user = User::factory()->create([
            'email' => 'test@example.com',
        ]);

        // 2. プロフィール作成
        Profile::create([
            'user_id' => $user->id,
            'users_name' => 'テストユーザー',
            'post_code' => '123-4567',
            'address' => '東京都新宿区1-2-3',
            'building' => 'テストマンション101',
            'avatar_path' => 'avatars/test.png',
        ]);

        // 3. ログインしてプロフィール編集画面を開く
        $response = $this->actingAs($user)->get('/mypage/profile');

        // 4. 画面表示確認
        $response->assertStatus(200);

        // 5. 初期値が表示されていることを確認
        $response->assertSee('テストユーザー');
        $response->assertSee('123-4567');
        $response->assertSee('東京都新宿区1-2-3');

        // building も表示されるなら確認
        $response->assertSee('テストマンション101');

        // 画像パスがHTML内に含まれることを確認
        $response->assertSee('avatars/test.png');
    }
}