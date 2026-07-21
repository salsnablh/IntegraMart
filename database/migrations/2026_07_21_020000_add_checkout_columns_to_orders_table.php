<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('id')->constrained()->nullOnDelete();
            $table->string('order_number')->nullable()->unique()->after('order_code');
            $table->decimal('total_amount', 15, 2)->nullable()->after('grand_total');
            $table->string('phone', 30)->nullable()->after('shipping_address');
            $table->string('payment_method')->nullable()->after('phone');
        });

        DB::table('orders')->orderBy('id')->chunkById(100, function ($orders) {
            foreach ($orders as $order) {
                DB::table('orders')
                    ->where('id', $order->id)
                    ->update([
                        'order_number' => $order->order_code,
                        'total_amount' => $order->grand_total,
                        'phone' => $order->recipient_phone,
                    ]);
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropUnique(['order_number']);
            $table->dropColumn(['user_id', 'order_number', 'total_amount', 'phone', 'payment_method']);
        });
    }
};
