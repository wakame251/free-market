<?php

namespace App\Providers;

use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;
use App\Actions\Fortify\CreateNewUser;
use Laravel\Fortify\Contracts\LogoutResponse;
use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;
use App\Http\Responses\RegisterResponse;


class FortifyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Fortify::registerView(fn () => view('register'));
        Fortify::loginView(fn () => view('login'));

        // 会員登録
        Fortify::createUsersUsing(CreateNewUser::class);

        // ログイン認証処理
        Fortify::authenticateUsing(function ($request) {

            // ★ 追加：自作 LoginRequest のバリデーションを走らせる（日本語メッセージ）
            $validated = app(LoginRequest::class)->validated();

            $user = User::where('email', $validated['email'])->first();

            if ($user && Hash::check($validated['password'], $user->password)) {
                return $user;
            }

            throw ValidationException::withMessages([
                'email' => 'ログイン情報が登録されていません',
            ]);
        });

        $this->app->bind(LogoutResponse::class, function () {
        return new class implements LogoutResponse {
            public function toResponse($request)
            {
                return redirect('/login');
            }
            };
        });

        $this->app->singleton(RegisterResponseContract::class, RegisterResponse::class);
    }
}
