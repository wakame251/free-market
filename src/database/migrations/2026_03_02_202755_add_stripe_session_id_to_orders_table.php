<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStripeSessionIdToOrdersTable extends Migration
{
    public function up(): void
{
    Schema::table('orders', function (Blueprint $table) {
        $table->string('stripe_session_id')->nullable()->unique()->after('item_id');
    });
}

public function down(): void
{
    Schema::table('orders', function (Blueprint $table) {
        $table->dropUnique(['stripe_session_id']);
        $table->dropColumn('stripe_session_id');
    });
}
}
