<?php

namespace Database\Seeders;

use App\Models\PaymentChannel;
use Illuminate\Database\Seeder;

class PaymentChannelsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Only Razorpay payment gateway is seeded.
     *
     * @return void
     */
    public function run()
    {
        foreach (PaymentChannel::$classes as $index => $class) {
            PaymentChannel::updateOrCreate(
                ['id' => $index + 1],
                [
                    'title' => $class,
                    'class_name' => $class,
                    'status' => 'active',
                    'image' => null,
                    'settings' => '',
                    'created_at' => time(),
                ]
            );
        }
    }
}
