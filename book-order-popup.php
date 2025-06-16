<?php
/**
 * Plugin Name: Book Order Multistep Form
 * Description: Multi-step book order form with live price calculation.
 * Author: Samiul H Pranto
 * Version: 1.0
 */

add_action('init', function () {
    register_post_type('book_order', [
        'labels' => [
            'name' => 'Book Orders',
            'singular_name' => 'Book Order'
        ],
        'public' => false,
        'show_ui' => true,
        'menu_position' => 25,
        'menu_icon' => 'dashicons-book',
        'supports' => ['title', 'editor'],
    ]);
});


add_action('wp_enqueue_scripts', function () {
    wp_enqueue_script('book-order-script', plugin_dir_url(__FILE__) . 'form.js', ['jquery'], '1.0', true);
    wp_enqueue_style('book-order-style', plugin_dir_url(__FILE__) . 'style.css');
    wp_localize_script('book-order-script', 'bookOrder', [
        'ajax_url' => admin_url('admin-ajax.php')
    ]);
});

add_action('wp_footer', function () {
    ?>
    <script>
        // For future use if needed
    </script>
    <?php
});

add_action('wp_ajax_book_order_submit', 'handle_book_order');
add_action('wp_ajax_nopriv_book_order_submit', 'handle_book_order');

function handle_book_order()
{
    $data = array_map('sanitize_text_field', $_POST);

    $post_id = wp_insert_post([
        'post_type' => 'book_order',
        'post_status' => 'publish',
        'post_title' => $data['name'],
        'meta_input' => $data,
    ]);

    // $message = "Name: {$data['name']}\nPhone: {$data['phone']}\nEmail: {$data['email']}\nAddress: {$data['address']}\nBooks: {$data['qty']}\nShipping: {$data['shipping']}\nTotal: {$data['total']}\nTxn ID: {$data['txn_id']}";

    // wp_mail(get_option('admin_email'), 'New Book Order', $message);

    // wp_send_json_success('Thank you! We’ve received your order.');


    // Send Email to Admin
    $to = 'samiul.pranto@viserx.net'; // ← Replace with your desired email

    $subject = 'New Book Order Received';

    $headers = [
        'Content-Type: text/html; charset=UTF-8',
        // 'From: Book Orders <no-reply@' . $_SERVER['SERVER_NAME'] . '>'
        'From: Book Orders <contact@faisalmustafa.me>'
    ];

    $message = "
    <h2>New Book Order</h2>
    <table cellpadding='6' cellspacing='0' border='1' style='border-collapse: collapse;'>
        <tr><th align='left'>Name</th><td>{$data['name']}</td></tr>
        <tr><th align='left'>Phone</th><td>{$data['phone']}</td></tr>
        <tr><th align='left'>Email</th><td>{$data['email']}</td></tr>
        <tr><th align='left'>Address</th><td>{$data['address']}</td></tr>
        <tr><th align='left'>Book Quantity</th><td>{$data['qty']}</td></tr>
        <tr><th align='left'>Shipping</th><td>{$data['shipping']} TK</td></tr>
        <tr><th align='left'>Total Amount</th><td><strong>{$data['total']} TK</strong></td></tr>
        <tr><th align='left'>bKash Txn ID</th><td>{$data['txn_id']}</td></tr>
    </table>
    <p style='margin-top:10px;'>Login to your dashboard to review this order.</p>
";

    wp_mail($to, $subject, $message, $headers);

    // Add this line to send a response back to the frontend
    wp_send_json_success('Thank you! We’ve received your order.');

}
