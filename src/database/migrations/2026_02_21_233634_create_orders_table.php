<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            $table->foreignId('item_id')->constrained('items')->cascadeOnDelete();
            $table->unique('item_id');

            $table->string('stripe_session_id')->nullable()->unique();

            $table->foreignId('buyer_id')->constrained('users')->cascadeOnDelete();

            $table->string('payment_method');
            $table->string('post_code');
            $table->string('address');
            $table->string('building')->nullable();

            $table->unsignedInteger('price_at_purchase');
            $table->timestamp('purchased_at')->useCurrent();

            $table->timestamps();

            $table->index(['buyer_id', 'purchased_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
