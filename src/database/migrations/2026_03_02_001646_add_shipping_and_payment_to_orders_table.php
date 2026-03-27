<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddShippingAndPaymentToOrdersTable extends Migration
{
public function up(): void
{
    Schema::table('orders', function (Blueprint $table) {
        $table->string('payment_method')->after('buyer_id'); // konbini / card など
        $table->string('post_code')->after('payment_method');
        $table->string('address')->after('post_code');
        $table->string('building')->nullable()->after('address');
    });
}

public function down(): void
{
    Schema::table('orders', function (Blueprint $table) {
        $table->dropColumn(['payment_method', 'post_code', 'address', 'building']);
    });
}
}
