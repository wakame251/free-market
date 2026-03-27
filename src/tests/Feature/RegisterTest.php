<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 名前が入力されていない場合、
     * 「お名前を入力してください」というバリデーションメッセージが表示される
     */
    public function test_name_is_required(): void
    {
        $response = $this->from('/register')->post('/register', [
            'user_name' => '',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/register');
        $response->assertSessionHasErrors([
            'user_name' => 'お名前を入力してください',
        ]);
    }

    /**
     * メールアドレスが入力されていない場合、
     * 「メールアドレスを入力してください」というバリデーションメッセージが表示される
     */
    public function test_email_is_required(): void
    {
        $response = $this->from('/register')->post('/register', [
            'user_name' => 'テストユーザー',
            'email' => '',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/register');
        $response->assertSessionHasErrors([
            'email' => 'メールアドレスを入力してください',
        ]);
    }

    /**
     * パスワードが入力されていない場合、
     * 「パスワードを入力してください」というバリデーションメッセージが表示される
     */
    public function test_password_is_required(): void
    {
        $response = $this->from('/register')->post('/register', [
            'user_name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => '',
            'password_confirmation' => '',
        ]);

        $response->assertRedirect('/register');
        $response->assertSessionHasErrors([
            'password' => 'パスワードを入力してください',
        ]);
    }

    /**
     * パスワードが7文字以下の場合、
     * 「パスワードは8文字以上で入力してください」というバリデーションメッセージが表示される
     */
    public function test_password_must_be_at_least_8_characters(): void
    {
        $response = $this->from('/register')->post('/register', [
            'user_name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => '1234567',
            'password_confirmation' => '1234567',
        ]);

        $response->assertRedirect('/register');
        $response->assertSessionHasErrors([
            'password' => 'パスワードは8文字以上で入力してください',
        ]);
    }

    /**
     * パスワードが確認用パスワードと一致しない場合、
     * 「パスワードと一致しません」というバリデーションメッセージが表示される
     */
    public function test_password_confirmation_must_match(): void
    {
        $response = $this->from('/register')->post('/register', [
            'user_name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'different123',
        ]);

        $response->assertRedirect('/register');
        $response->assertSessionHasErrors([
            'password' => 'パスワードと一致しません',
        ]);
    }

    /**
     * 全ての項目が正しく入力されている場合、
     * 会員情報が登録され、メール認証誘導画面に遷移する
     */
    public function test_user_can_register_and_redirect_to_verification_notice(): void
    {
        Notification::fake();

        $response = $this->post('/register', [
            'user_name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        // 認証前は、プロフィール設定画面ではなく認証誘導画面へ行く前提
        $response->assertRedirect('/email/verify');

        $this->assertDatabaseHas('users', [
            'user_name' => 'テストユーザー',
            'email' => 'test@example.com',
        ]);

        $this->assertAuthenticated();

        $user = User::where('email', 'test@example.com')->first();

        $this->assertNotNull($user);
        $this->assertFalse($user->hasVerifiedEmail());

        Notification::assertSentTo($user, VerifyEmail::class);
    }

    /**
     * 会員登録後、認証メールが送信される
     */
    public function test_verification_email_is_sent_after_registration(): void
    {
        Notification::fake();

        $this->post('/register', [
            'user_name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $user = User::where('email', 'test@example.com')->first();

        $this->assertNotNull($user);

        Notification::assertSentTo($user, VerifyEmail::class);
    }

    /**
     * メール認証誘導画面が表示される
     */
    public function test_verification_notice_screen_can_be_displayed(): void
    {
        $user = User::factory()->unverified()->create();

        $response = $this->actingAs($user)->get('/email/verify');

        $response->assertStatus(200);
    }

    /**
     * 認証メール内の認証URLへアクセスすると、メール認証が完了し、
     * プロフィール設定画面に遷移する
     */
    public function test_user_can_verify_email_and_redirect_to_profile_edit(): void
    {
        $user = User::factory()->unverified()->create();

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            [
                'id' => $user->id,
                'hash' => sha1($user->email),
            ]
        );

        $response = $this->actingAs($user)->get($verificationUrl);

        $response->assertRedirect('/mypage/profile');

        $user->refresh();

        $this->assertTrue($user->hasVerifiedEmail());
    }
}