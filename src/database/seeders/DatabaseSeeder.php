<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            ProfileSeeder::class,

            // 基本データ
            ItemSeeder::class,

            // デモ用ストーリー
            DemoItemSeeder::class,
            DemoTransactionSeeder::class,
        ]);
    }
}