@extends(getTemplate().'.layouts.app')

@section('content')
<section class="container mt-45">
    <div class="text-center py-5">
        <p class="font-16 text-gray">{{ trans('financial.redirecting_to_gateway') ?? 'Redirecting to payment gateway...' }}</p>
        <div class="spinner-border text-primary mt-3" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    </div>
</section>

<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
(function() {
    var options = {
        key: "{{ $api_key }}",
        amount: "{{ $amount }}",
        currency: "{{ $currency }}",
        order_id: "{{ $razorpay_order_id }}",
        name: "{{ $site_name }}",
        description: "Order #{{ $order_id }}",
        image: "{{ $logo }}",
        prefill: {
            name: "{{ $user_name }}",
            email: "{{ $user_email }}"
        },
        theme: { color: "#43d477" },
        handler: function(response) {
            var verifyUrl = "{{ $verify_url }}";
            verifyUrl += (verifyUrl.indexOf('?') !== -1 ? '&' : '?') + 'razorpay_payment_id=' + encodeURIComponent(response.razorpay_payment_id);
            verifyUrl += '&razorpay_order_id=' + encodeURIComponent(response.razorpay_order_id);
            verifyUrl += '&razorpay_signature=' + encodeURIComponent(response.razorpay_signature);
            verifyUrl += '&order_id=' + encodeURIComponent("{{ $order_id }}");
            window.location.href = verifyUrl;
        }
    };
    var rzp = new Razorpay(options);
    rzp.on('payment.failed', function(response) {
        window.location.href = "{{ url('/payments/status') }}?failed=1";
    });
    rzp.open();
})();
</script>
@endsection
