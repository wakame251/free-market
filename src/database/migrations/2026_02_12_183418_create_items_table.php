<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();

            // 出品者
            $table->foreignId('seller_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('item_name', 255);
            $table->text('description');

            // 金額は整数(円)で持つのが定番（小数の誤差回避）
            $table->unsignedInteger('price');

            $table->string('image_path');

            $table->string('condition');

            $table->string('brand_name')->nullable();

            // 代表カテゴリ保持用。
            // 実際の複数カテゴリ管理は category_item テーブルで行う。
            // テスト要件に合わせ、先頭カテゴリを items.category_id にも保存する。
            $table->foreignId('category_id')
                ->constrained('categories')
                ->restrictOnDelete();

            $table->string('status')->default('on_sale');

            $table->timestamps();

            $table->index(['seller_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};