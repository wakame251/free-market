<?php

namespace Database\Seeders;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProfileSeeder extends Seeder
{
    public function run(): void
    {
        $user1 = User::where('email', 'test@userone.com')->firstOrFail();
        $user2 = User::where('email', 'test@usertwo.com')->firstOrFail();

        Profile::updateOrCreate(
            ['user_id' => $user1->id],
            [
                'avatar_path' => 'images/sample/profiles/user1.jpeg',
                'post_code'   => '000-0000',
                'address'     => '東京都渋谷区1-1-1',
                'building'    => null,
            ]
        );

        Profile::updateOrCreate(
            ['user_id' => $user2->id],
            [
                'avatar_path' => 'images/sample/profiles/user2.jpeg',
                'post_code'   => '111-1111',
                'address'     => '大阪府大阪市1-1-1',
                'building'    => null,
            ]
        );
    }
}