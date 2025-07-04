# Book Order Multistep Form

A lightweight WordPress plugin that adds a multi-step book ordering form using Elementor's popup widget. It includes automatic price calculation, admin dashboard order management, and email notifications for both admin and customer.

---

## 🔧 Features

- 📋 Multi-step book order form (Elementor compatible)
- 🧮 Live price calculation based on quantity and delivery location
- ✅ Form validation and AJAX submission
- 📩 Email notifications to both admin and customer
- 🗂️ Orders saved as custom post type in WordPress dashboard
- 🔐 Uses `wp_mail()` with SMTP compatibility
- 🎨 Clean and responsive design

---

## 📦 Installation

1. Download or clone the repository.
2. Upload the plugin to the `/wp-content/plugins/` directory.
3. Activate it from the **Plugins** menu in WordPress.
4. Make sure Elementor is installed and activated.
5. Use Elementor's **Popup** widget to include the form shortcode or HTML.

---

## 🛠️ Usage

- The form is automatically hooked into Elementor popups.
- The plugin registers a custom post type called `Book Orders`, accessible from the WP Admin.
- You can modify form layout or styles by editing:
  - `form.js` for JavaScript logic
  - `style.css` for form styling
  - `book-order-multistep-form.php` for backend handling

---

## 📤 Email Setup

To send and receive emails reliably:

- Use an SMTP plugin like:
  - WP Mail SMTP
  - Easy WP SMTP
- Set your sender email in the `$headers` of the plugin file:
  ```php
  'From: Book Orders <your@email.com>'
