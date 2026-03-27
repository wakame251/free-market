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
            $table->text('description')->nullable();

            // 金額は整数(円)で持つのが定番（小数の誤差回避）
            $table->unsignedInteger('price');

            $table->string('image_path')->nullable();

            $table->string('condition')->nullable();

            $table->string('brand_name')->nullable();

            $table->foreignId('category_id')
                ->nullable()
                ->constrained('categories')
                ->nullOnDelete();

            $table->string('status')->nullable();

            $table->timestamps();

            $table->index(['seller_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};