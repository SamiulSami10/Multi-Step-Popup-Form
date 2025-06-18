function initBookOrderForm() {
    const $ = jQuery;

    // Reset & show only step 1
    $('#step1').show();
    $('#step2').hide();

    $('#next-step').off('click').on('click', function () {
    let valid = true;

    // Name: only letters and spaces
    const name = $('input[name="name"]');
    if (!/^[a-zA-Z\s]+$/.test(name.val().trim())) {
        name.css('border', '1px solid red');
        valid = false;
    } else {
        name.css('border', '');
    }

    // Phone: only digits (10 to 15 digits recommended)
    const phone = $('input[name="phone"]');
    if (!/^\d{10,15}$/.test(phone.val().trim())) {
        phone.css('border', '1px solid red');
        valid = false;
    } else {
        phone.css('border', '');
    }

    // Email: basic email pattern
    const email = $('input[name="email"]');
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.val().trim())) {
        email.css('border', '1px solid red');
        valid = false;
    } else {
        email.css('border', '');
    }

    // Address: required non-empty
    const address = $('textarea[name="address"]');
    if (address.val().trim() === '') {
        address.css('border', '1px solid red');
        valid = false;
    } else {
        address.css('border', '');
    }

    if (valid) {
        $('#step1').hide();
        $('#step2').show();
    } else {
        alert('Please fill in all fields correctly before continuing.');
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
                $('#book-order-form').html('<p>' + res.data + '</p>');
            }
        });
    });
}

// Elementor hook
jQuery(window).on('elementor/popup/show', function () {
    initBookOrderForm();
});
