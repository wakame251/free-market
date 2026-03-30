<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 一般ユーザー①
        User::updateOrCreate(
            ['email' => 'test@userone.com'],
            [
                'user_name' => '一般ユーザー①',
                'password' => Hash::make('userone0000'),
                'email_verified_at' => now(), // Fortify対策
            ]
        );

        // 一般ユーザー②
        User::updateOrCreate(
            ['email' => 'test@usertwo.com'],
            [
                'user_name' => '一般ユーザー②',
                'password' => Hash::make('usertwo0000'),
                'email_verified_at' => now(),
            ]
        );
    }
}