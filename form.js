function initBookOrderForm() {
    const $ = jQuery;

    // Reset & show only step 1
    $('#step1').show();
    $('#step2').hide();

    $('#next-step').off('click').on('click', function () {
        $('#step1').hide();
        $('#step2').show();
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
