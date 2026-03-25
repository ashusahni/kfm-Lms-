<?php

/**
 * Payment configuration.
 *
 * This application uses only Razorpay for online payments.
 * Integration is implemented in app/PaymentChannels/Drivers/Razorpay/
 * and configured via .env (RAZORPAY_API_KEY, RAZORPAY_API_SECRET).
 *
 * @see backend/RAZORPAY_INTEGRATION.md
 */
return [
    'default' => 'razorpay',

    'razorpay' => [
        'api_key' => env('RAZORPAY_API_KEY', ''),
        'api_secret' => env('RAZORPAY_API_SECRET', ''),
    ],
];
