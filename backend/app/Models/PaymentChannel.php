<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentChannel extends Model
{
    protected $table = 'payment_channels';
    protected $guarded = ['id'];
    public $timestamps = false;

    /**
     * Only Razorpay is integrated. Other gateways have been removed.
     */
    static $classes = [
        'Razorpay',
    ];

    /**
     * Gateways that return HTML/response instead of redirect URL.
     */
    static $gatewayIgnoreRedirect = [
        'Razorpay',
    ];

    static $razorpay = 'Razorpay';

    /** Legacy constants (other gateways removed; kept to avoid errors in legacy routes/drivers). */
    static $payku = 'Payku';
    static $authorizenet = 'Authorizenet';


    public function getCurrenciesAttribute()
    {
        if (!empty($this->attributes['currencies'])) {
            return json_decode($this->attributes['currencies'], true);
        }

        return [];
    }
}
