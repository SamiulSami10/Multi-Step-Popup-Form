function initBookOrderForm() {
    const $ = jQuery;

    // Reset & show only step 1
    $('#step1').show();
    $('#step2').hide();

   $('#next-step').off('click').on('click', function () {
    let valid = true;

    // Clear previous error messages
    $('.error-msg').remove();

    // Name validation
    const name = $('input[name="name"]');
    if (!/^[a-zA-Z\s]+$/.test(name.val().trim())) {
        name.after('<div class="error-msg">Please enter a valid name (letters only).</div>');
        valid = false;
    }

    // Phone validation
    const phone = $('input[name="phone"]');
    if (!/^\d{10,15}$/.test(phone.val().trim())) {
        phone.after('<div class="error-msg">Enter a valid phone number (10–15 digits).</div>');
        valid = false;
    }

    // Email validation
    const email = $('input[name="email"]');
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.val().trim())) {
        email.after('<div class="error-msg">Enter a valid email address.</div>');
        valid = false;
    }

    // Address validation
    const address = $('textarea[name="address"]');
    if (address.val().trim() === '') {
        address.after('<div class="error-msg">Address is required.</div>');
        valid = false;
    }

    if (valid) {
        $('#step1').hide();
        $('#step2').show();
    }
});


    $('#prev-step').off('click').on('click', function () {
    $('#step2').hide();
    $('#step1').show();
});

    $('#qty, #shipping').off('input change').on('input change', function () {
    let qty = parseInt($('#qty').val()) || 0;
    let delivery = parseInt($('#shipping').val()) || 0;
    let total = qty * 225 + delivery;

    $('#total').val(total);

    // Update bKash amount text
    const $bkashAmount = $('#dynamic-bkash-amount');
    if ($bkashAmount.length) {
        $bkashAmount.html(`<b>BDT ${total}</b>`);
    }
});


    $('#book-order-form').off('submit').on('submit', function (e) {
        e.preventDefault();
        let formData = $(this).serialize();
        $.post(bookOrder.ajax_url, formData + '&action=book_order_submit', function (res) {
            if (res.success) {
$('#book-order-form').html(res.data);
            }
        });
    });
}

// Elementor hook
jQuery(window).on('elementor/popup/show', function () {
    initBookOrderForm();
});
