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

  add_action('admin_menu', function () {
    remove_submenu_page('edit.php?post_type=book_order', 'post-new.php?post_type=book_order');
  });

});

add_action('add_meta_boxes', function () {
  add_meta_box(
    'book_order_meta_box',
    'Book Order Details',
    'render_book_order_meta_box',
    'book_order',
    'normal',
    'default'
  );
});

function render_book_order_meta_box($post)
{
  $meta = get_post_meta($post->ID);

  echo '<table class="widefat fixed striped">';
  foreach ($meta as $key => $val) {
    if (strpos($key, '_') === 0)
      continue;
    $label = ucwords(str_replace('_', ' ', $key));
    $value = esc_html($val[0]);
    echo "<tr><th style='width:180px;'>$label</th><td>$value</td></tr>";
  }
  echo '</table>';
}




add_action('wp_enqueue_scripts', function () {
  wp_enqueue_script('intl-tel-input', 'https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/intlTelInput.min.js', [], null, true);
  wp_enqueue_script('intl-tel-utils', 'https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/utils.js', [], null, true);
  // Enqueue intl-tel-input CSS
  wp_enqueue_style(
    'intl-tel-input-style',
    'https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/css/intlTelInput.min.css',
    [],
    null
  );



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

add_action('admin_init', function () {
  remove_post_type_support('book_order', 'editor');
});


add_action('wp_ajax_book_order_submit', 'handle_book_order');
add_action('wp_ajax_nopriv_book_order_submit', 'handle_book_order');

function handle_book_order()
{
  $data = array_map('sanitize_text_field', $_POST);
  $book_subtotal = $data['qty'] * 225;

  $post_id = wp_insert_post([
    'post_type' => 'book_order',
    'post_status' => 'publish',
    'post_title' => $data['name'],
    'meta_input' => $data,
  ]);


  // Send Email to Admin
  $to = 'samiul.pranto@viserx.net'; // ← Replace with your desired email

  $subject = 'New Book Order Received';

  $headers = [
    'Content-Type: text/html; charset=UTF-8',
    // 'From: Book Orders <no-reply@' . $_SERVER['SERVER_NAME'] . '>'
    'From: Book Orders <contact@faisalmustafa.me>'
  ];

  $message = '
<div
  style="
    height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: #f9f9f9;
  "
>
  <div
    style="
      font-family: Arial, sans-serif;
      max-width: 600px;
      margin: 0 auto;
      padding: 50px;
      box-shadow: rgba(100, 100, 111, 0.2) 0px 7px 29px 0px;
    "
  >
    <div style="display: flex; justify-content: center; align-items: center">
      <img
        style="width: 30%"
        src="https://faisalmustafa.me/wp-content/uploads/2023/01/Faisal-Mustafa.png"
        alt=""
      />
    </div>
    <h2 style="text-align: center">
      📚 New Book Order Received for
      <a
        href="https://faisalmustafa.me/freelancing-at-fiverr"
        style="color: #3c763d; text-decoration: none; font-weight: 700"
        ;
        >Freelancing at Fiverr!</a
      >
    </h2>
    <p style="font-size: 15px">
      A new book order has been received. After verifying the payment, please
      proceed with shipping to the address below:
    </p>

    <table style="width: 100%; border-collapse: collapse">
      <tr>
        <td style="padding: 8px; border-bottom: 1px solid #aef0af">
          <strong>Name:</strong>
        </td>
        <td>' . $data['name'] . '</td>
      </tr>
      <tr>
        <td style="padding: 8px; border-bottom: 1px solid #aef0af">
          <strong>Phone:</strong>
        </td>
        <td>' . $data['phone'] . '</td>
      </tr>
      <tr>
        <td style="padding: 8px; border-bottom: 1px solid #aef0af">
          <strong>Email:</strong>
        </td>
        <td>' . $data['email'] . '</td>
      </tr>
      <tr>
        <td style="padding: 8px; border-bottom: 1px solid #aef0af">
          <strong>Shipping Address:</strong>
        </td>
        <td>' . $data['address'] . '</td>
      </tr>
      <tr>
        <td style="padding: 8px; border-bottom: 1px solid #aef0af">
          <strong>Book Quantity:</strong>
        </td>
        <td>' . $data['qty'] . '</td>
      </tr>
      <tr>
  <td style="padding: 8px; border-bottom: 1px solid #aef0af">
    <strong>Book Subtotal:</strong>
  </td>
  <td>' . $book_subtotal . ' TK</td>
</tr>

      <tr>
        <td style="padding: 8px; border-bottom: 1px solid #aef0af">
          <strong>Shipping:</strong>
        </td>
        <td>' . $data['shipping'] . ' TK</td>
      </tr>
      <tr>
        <td style="padding: 8px; border-bottom: 1px solid #aef0af">
          <strong>Total:</strong>
        </td>
        <td><strong>' . $data['total'] . ' TK</strong></td>
      </tr>
      <tr>
        <td style="padding: 8px"><strong>bKash Txn ID:</strong></td>
        <td>' . $data['txn_id'] . '</td>
      </tr>
    </table>

    <p style="margin-top: 20px; font-size: 14px; color: #666">
      Please review the details below and proceed with shipping after verifying
      the payment.
    </p>
    <p style="font-size: 15px">Thank you.</p>
  </div>
  ';


  wp_mail($to, $subject, $message, $headers);





  // Send Confirmation Email to Customer
  $customer_email = $data['email'];
  $customer_subject = 'Your Book Order Confirmation';

  $customer_message = '
<div
  style="
    height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: #f9f9f9;
  "
>
  <div
    style="
      font-family: Arial, sans-serif;
      max-width: 600px;
      margin: 0 auto;
      padding: 50px;
      box-shadow: rgba(100, 100, 111, 0.2) 0px 7px 29px 0px;
    "
  >
    <div style="display: flex; justify-content: center; align-items: center">
      <img
        style="width: 30%"
        src="https://faisalmustafa.me/wp-content/uploads/2023/01/Faisal-Mustafa.png"
        alt=""
      />
    </div>
    <h2 style="text-align: center">
      ✅ Thank You for Ordering
      <a
        href="https://faisalmustafa.me/freelancing-at-fiverr"
        style="color: #3c763d; text-decoration: none; font-weight: 700"
        ;
        >Freelancing at Fiverr!</a
      >
    </h2>
    <p style="font-size: 15px">
      We’ve received your order. After verifying the payment, we’ll ship your
      books to the following address:
    </p>

    <table style="width: 100%; border-collapse: collapse">
      <tr>
        <td style="padding: 8px; border-bottom: 1px solid #aef0af">
          <strong>Name:</strong>
        </td>
        <td>' . $data['name'] . '</td>
      </tr>
      <tr>
        <td style="padding: 8px; border-bottom: 1px solid #aef0af">
          <strong>Phone:</strong>
        </td>
        <td>' . $data['phone'] . '</td>
      </tr>
      <tr>
        <td style="padding: 8px; border-bottom: 1px solid #aef0af">
          <strong>Email:</strong>
        </td>
        <td>' . $data['email'] . '</td>
      </tr>
      <tr>
        <td style="padding: 8px; border-bottom: 1px solid #aef0af">
          <strong>Shipping Address:</strong>
        </td>
        <td>' . $data['address'] . '</td>
      </tr>
      <tr>
        <td style="padding: 8px; border-bottom: 1px solid #aef0af">
          <strong>Book Quantity:</strong>
        </td>
        <td>' . $data['qty'] . '</td>
      </tr>
      <tr>
  <td style="padding: 8px; border-bottom: 1px solid #aef0af">
    <strong>Book Subtotal:</strong>
  </td>
  <td>' . $book_subtotal . ' TK</td>
</tr>

      <tr>
        <td style="padding: 8px; border-bottom: 1px solid #aef0af">
          <strong>Shipping:</strong>
        </td>
        <td>' . $data['shipping'] . ' TK</td>
      </tr>
      <tr>
        <td style="padding: 8px; border-bottom: 1px solid #aef0af">
          <strong>Total:</strong>
        </td>
        <td><strong>' . $data['total'] . ' TK</strong></td>
      </tr>
      <tr>
        <td style="padding: 8px"><strong>bKash Txn ID:</strong></td>
        <td>' . $data['txn_id'] . '</td>
      </tr>
    </table>

    <p style="margin-top: 20px; font-size: 14px; color: #666">
      If you have any questions, just reply to this email. We will be happy to
      help.
    </p>
    <p style="font-size: 15px">
      Thank you.
    </p>
  </div>
  ';


  wp_mail($customer_email, $customer_subject, $customer_message, $headers);


  // Add this line to send a response back to the frontend
  wp_send_json_success('
    <div style="
        padding: 20px;
        border: 2px solid #aef0af;
        background-color: #f0fff5;
        border-radius: 10px;
        text-align: center;
        font-family: Arial, sans-serif;
    ">
        <h2 style="color: #2e7d32;"> Order Received!</h2>
        <p style="font-size: 16px; color: #333;">
            Thank you! We’ve received your order successfully.<br>
            You will get a confirmation email shortly.
        </p>
    </div>
');
}

add_filter('manage_book_order_posts_columns', function ($columns) {
  return [
    'cb' => $columns['cb'],
    'title' => 'Name',
    'phone' => 'Phone',
    'qty' => 'Qty',
    'total' => 'Total',
    // 'date' => 'Date',
  ];
});

add_action('manage_book_order_posts_custom_column', function ($column, $post_id) {
  switch ($column) {
    case 'phone':
      echo esc_html(get_post_meta($post_id, 'phone', true));
      break;
    case 'qty':
      echo esc_html(get_post_meta($post_id, 'qty', true));
      break;
    case 'total':
      echo esc_html(get_post_meta($post_id, 'total', true)) . ' TK';
      break;
  }
}, 10, 2);
