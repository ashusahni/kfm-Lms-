<?php

namespace App\PaymentChannels\Drivers\Razorpay;

use App\Models\Order;
use App\Models\PaymentChannel;
use App\PaymentChannels\BasePaymentChannel;
use App\PaymentChannels\IChannel;
use Illuminate\Http\Request;
use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;

class Channel extends BasePaymentChannel implements IChannel
{
    protected $currency;

    protected $api_key;

    protected $api_secret;

    protected $order_session_key = 'razorpay.payments.order_id';

    /**
     * Channel constructor.
     *
     * @param  PaymentChannel  $paymentChannel
     */
    public function __construct(PaymentChannel $paymentChannel)
    {
        $this->currency = currency();
        $this->api_key = config('payment.razorpay.api_key') ?: env('RAZORPAY_API_KEY');
        $this->api_secret = config('payment.razorpay.api_secret') ?: env('RAZORPAY_API_SECRET');
    }

    /**
     * Create Razorpay order and return HTML that opens Razorpay Checkout.
     * Amount is in currency subunits (paise for INR, cents for USD).
     */
    public function paymentRequest(Order $order)
    {
        $amount = $this->makeAmountByCurrency($order->total_amount, $this->currency);
        $amountInSubunits = (int) round($amount * 100); // Razorpay expects amount in smallest currency unit

        if ($amountInSubunits < 1) {
            throw new \Exception('Payment amount must be at least 1 subunit.');
        }

        $api = new Api($this->api_key, $this->api_secret);

        $razorpayOrder = $api->order->create([
            'amount' => $amountInSubunits,
            'currency' => strtoupper($this->currency),
            'receipt' => 'order_'.$order->id,
            'notes' => [
                'lms_order_id' => (string) $order->id,
            ],
        ]);

        $razorpayOrderId = $razorpayOrder['id'];

        session()->put($this->order_session_key, $order->id);

        $verifyUrl = route('payment_verify', ['gateway' => 'Razorpay']).'?order_id='.$order->id;
        $generalSettings = getGeneralSettings();

        $html = view('web.default.cart.razorpay_checkout', [
            'api_key' => $this->api_key,
            'razorpay_order_id' => $razorpayOrderId,
            'amount' => $amountInSubunits,
            'currency' => strtoupper($this->currency),
            'order_id' => $order->id,
            'verify_url' => $verifyUrl,
            'user_name' => $order->user->full_name ?? '',
            'user_email' => $order->user->email ?? '',
            'site_name' => $generalSettings['site_name'] ?? 'Payment',
            'logo' => $generalSettings['logo'] ?? '',
        ])->render();

        return response($html);
    }

    /**
     * Verify Razorpay payment using signature and mark order as paying on success.
     */
    public function verify(Request $request)
    {
        $razorpayPaymentId = $request->input('razorpay_payment_id');
        $razorpayOrderId = $request->input('razorpay_order_id');
        $razorpaySignature = $request->input('razorpay_signature');
        $orderId = $request->input('order_id');

        $user = auth()->user();
        $order = null;
        if ($user) {
            $order = Order::where('id', $orderId)
                ->where('user_id', $user->id)
                ->with('user')
                ->first();
        }
        if (! $order && session()->has($this->order_session_key)) {
            $orderId = session()->get($this->order_session_key);
            $order = $user ? Order::where('id', $orderId)->where('user_id', $user->id)->first() : Order::find($orderId);
        }
        if (! $order) {
            $order = Order::where('id', $orderId)->with('user')->first();
        }

        session()->forget($this->order_session_key);

        if (! $order || empty($razorpayPaymentId) || empty($razorpayOrderId) || empty($razorpaySignature)) {
            if ($order) {
                $order->update(['status' => Order::$fail]);
            }

            return $order;
        }

        try {
            $api = new Api($this->api_key, $this->api_secret);
            $api->utility->verifyPaymentSignature([
                'razorpay_order_id' => $razorpayOrderId,
                'razorpay_payment_id' => $razorpayPaymentId,
                'razorpay_signature' => $razorpaySignature,
            ]);
        } catch (SignatureVerificationError $e) {
            $order->update(['status' => Order::$fail]);

            return $order;
        }

        $order->update([
            'status' => Order::$paying,
            'payment_method' => Order::$paymentChannel,
        ]);

        return $order;
    }
}
