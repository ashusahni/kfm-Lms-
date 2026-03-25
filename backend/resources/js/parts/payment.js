(function ($) {
    "use strict";

    var gateway = 'other';
    $('body').on('change', 'input[name="gateway"]', function (e) {
        e.preventDefault();

        var submitButton = $('button#paymentSubmit');

        submitButton.removeAttr('disabled');

        $('html, body').animate({
            scrollTop: submitButton.offset().top - 250
        }, 600);

        gateway = $(this).attr('data-class');
    });

    $('body').on('click', '#paymentSubmit', function (e) {
        e.preventDefault();

        $(this).addClass('loadingbar primary').prop('disabled', true);

        // Razorpay: form submits to backend which creates order and returns checkout page
        $(this).closest('form').trigger('submit');
    });
})(jQuery);
