<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class RazorpayOnlyPaymentChannel extends Migration
{
    /**
     * Replace all payment channels with Razorpay only.
     * Run after deploying Razorpay-only integration.
     *
     * @return void
     */
    public function up()
    {
        DB::table('payment_channels')->truncate();

        DB::table('payment_channels')->insert([
            'id' => 1,
            'title' => 'Razorpay',
            'class_name' => 'Razorpay',
            'image' => null,
            'settings' => '',
            'status' => 'active',
            'currencies' => null,
            'created_at' => time(),
        ]);
    }

    /**
     * Reverse the migration (restore would require re-seeding all previous gateways).
     *
     * @return void
     */
    public function down()
    {
        DB::table('payment_channels')->truncate();
        // Re-run PaymentChannelsTableSeeder to restore Razorpay if needed.
    }
}
