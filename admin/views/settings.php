<?php
if (!defined('ABSPATH')) {
    exit;
}

if (isset($_POST['dots_save_settings']) && check_admin_referer('dots_settings_nonce')) {
    // phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
    // Template variables are intentionally non-prefixed for readability
    $settings = array(
        'currency' => isset($_POST['currency']) ? sanitize_text_field(wp_unslash($_POST['currency'])) : 'USD',
        'currency_symbol' => isset($_POST['currency_symbol']) ? sanitize_text_field(wp_unslash($_POST['currency_symbol'])) : '$',
        'timezone' => isset($_POST['timezone']) ? sanitize_text_field(wp_unslash($_POST['timezone'])) : 'UTC',
        'max_tickets_per_customer' => isset($_POST['max_tickets_per_customer']) ? intval($_POST['max_tickets_per_customer']) : 10,
        'enable_captcha' => isset($_POST['enable_captcha']) ? 1 : 0,
        'email_notifications' => isset($_POST['email_notifications']) ? 1 : 0,
        'admin_email' => isset($_POST['admin_email']) ? sanitize_email(wp_unslash($_POST['admin_email'])) : get_option('admin_email'),
        // Payment settings
        'paypal_enabled' => isset($_POST['paypal_enabled']) ? 1 : 0,
        'paypal_mode' => isset($_POST['paypal_mode']) ? sanitize_text_field(wp_unslash($_POST['paypal_mode'])) : 'sandbox',
        'paypal_client_id' => isset($_POST['paypal_client_id']) ? sanitize_text_field(wp_unslash($_POST['paypal_client_id'])) : '',
        'paypal_secret' => isset($_POST['paypal_secret']) ? sanitize_text_field(wp_unslash($_POST['paypal_secret'])) : '',
        'stripe_enabled' => isset($_POST['stripe_enabled']) ? 1 : 0,
        'stripe_mode' => isset($_POST['stripe_mode']) ? sanitize_text_field(wp_unslash($_POST['stripe_mode'])) : 'test',
        'stripe_secret_key' => isset($_POST['stripe_secret_key']) ? sanitize_text_field(wp_unslash($_POST['stripe_secret_key'])) : '',
        'stripe_publishable_key' => isset($_POST['stripe_publishable_key']) ? sanitize_text_field(wp_unslash($_POST['stripe_publishable_key'])) : '',
        'sslcommerz_enabled' => isset($_POST['sslcommerz_enabled']) ? 1 : 0,
        'sslcommerz_mode' => isset($_POST['sslcommerz_mode']) ? sanitize_text_field(wp_unslash($_POST['sslcommerz_mode'])) : 'sandbox',
        'sslcommerz_store_id' => isset($_POST['sslcommerz_store_id']) ? sanitize_text_field(wp_unslash($_POST['sslcommerz_store_id'])) : '',
        'sslcommerz_store_password' => isset($_POST['sslcommerz_store_password']) ? sanitize_text_field(wp_unslash($_POST['sslcommerz_store_password'])) : '',
        'bank_transfer_enabled' => isset($_POST['bank_transfer_enabled']) ? 1 : 0,
        'bank_transfer_details' => isset($_POST['bank_transfer_details']) ? wp_kses_post(wp_unslash($_POST['bank_transfer_details'])) : '',
    );
    update_option('dots_settings', $settings);
    // phpcs:enable
    echo '<div class="notice notice-success"><p>' . esc_html__('Settings saved.', 'dream-online-ticket-selling') . '</p></div>';
}

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
// Template variables are intentionally non-prefixed for readability
$settings = get_option('dots_settings', array());
$currency = isset($settings['currency']) ? $settings['currency'] : 'USD';
$currency_symbol = isset($settings['currency_symbol']) ? $settings['currency_symbol'] : '$';
$timezone = isset($settings['timezone']) ? $settings['timezone'] : wp_timezone_string();
$max_tickets = isset($settings['max_tickets_per_customer']) ? $settings['max_tickets_per_customer'] : 10;
$enable_captcha = isset($settings['enable_captcha']) ? $settings['enable_captcha'] : 0;
$email_notifications = isset($settings['email_notifications']) ? $settings['email_notifications'] : 1;
$admin_email = isset($settings['admin_email']) ? $settings['admin_email'] : get_option('admin_email');
// phpcs:enable
?>

<div class="wrap dots-settings">
    <h1><?php esc_html_e('Settings', 'dream-online-ticket-selling'); ?></h1>
    
    <form method="post" action="">
        <?php wp_nonce_field('dots_settings_nonce'); ?>
        
        <h2 class="nav-tab-wrapper">
            <a href="#general" class="nav-tab nav-tab-active"><?php esc_html_e('General', 'dream-online-ticket-selling'); ?></a>
            <a href="#email" class="nav-tab"><?php esc_html_e('Email Notifications', 'dream-online-ticket-selling'); ?></a>
            <a href="#security" class="nav-tab"><?php esc_html_e('Security', 'dream-online-ticket-selling'); ?></a>
            <a href="#payment" class="nav-tab"><?php esc_html_e('Payment', 'dream-online-ticket-selling'); ?></a>
        </h2>
        
        <div id="general" class="dots-tab-content">
            <table class="form-table">
                <tr>
                    <th><label for="currency"><?php esc_html_e('Currency', 'dream-online-ticket-selling'); ?></label></th>
                    <td>
                        <select id="currency" name="currency">
                            <option value="USD" <?php selected($currency, 'USD'); ?>>USD - US Dollar</option>
                            <option value="EUR" <?php selected($currency, 'EUR'); ?>>EUR - Euro</option>
                            <option value="GBP" <?php selected($currency, 'GBP'); ?>>GBP - British Pound</option>
                            <option value="BDT" <?php selected($currency, 'BDT'); ?>>BDT - Bangladeshi Taka</option>
                            <option value="INR" <?php selected($currency, 'INR'); ?>>INR - Indian Rupee</option>
                            <option value="AUD" <?php selected($currency, 'AUD'); ?>>AUD - Australian Dollar</option>
                            <option value="CAD" <?php selected($currency, 'CAD'); ?>>CAD - Canadian Dollar</option>
                            <option value="JPY" <?php selected($currency, 'JPY'); ?>>JPY - Japanese Yen</option>
                            <option value="CNY" <?php selected($currency, 'CNY'); ?>>CNY - Chinese Yuan</option>
                            <option value="SGD" <?php selected($currency, 'SGD'); ?>>SGD - Singapore Dollar</option>
                            <option value="AED" <?php selected($currency, 'AED'); ?>>AED - UAE Dirham</option>
                            <option value="SAR" <?php selected($currency, 'SAR'); ?>>SAR - Saudi Riyal</option>
                        </select>
                        <p class="description"><?php esc_html_e('Select currency and the symbol will update automatically.', 'dream-online-ticket-selling'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th><label for="currency_symbol"><?php esc_html_e('Currency Symbol', 'dream-online-ticket-selling'); ?></label></th>
                    <td><input type="text" id="currency_symbol" name="currency_symbol" value="<?php echo esc_attr($currency_symbol); ?>" class="small-text"></td>
                </tr>
                <tr>
                    <th><label for="timezone"><?php esc_html_e('Timezone', 'dream-online-ticket-selling'); ?></label></th>
                    <td>
                        <select id="timezone" name="timezone">
                            <?php echo wp_timezone_choice($timezone); ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><label for="max_tickets_per_customer"><?php esc_html_e('Max Tickets per Customer', 'dream-online-ticket-selling'); ?></label></th>
                    <td><input type="number" id="max_tickets_per_customer" name="max_tickets_per_customer" value="<?php echo esc_attr($max_tickets); ?>" min="1" class="small-text"></td>
                </tr>
            </table>
        </div>
        
        <div id="email" class="dots-tab-content" style="display: none;">
            <table class="form-table">
                <tr>
                    <th><label for="email_notifications"><?php esc_html_e('Enable Email Notifications', 'dream-online-ticket-selling'); ?></label></th>
                    <td><input type="checkbox" id="email_notifications" name="email_notifications" value="1" <?php checked($email_notifications, 1); ?>></td>
                </tr>
                <tr>
                    <th><label for="admin_email"><?php esc_html_e('Admin Email', 'dream-online-ticket-selling'); ?></label></th>
                    <td><input type="email" id="admin_email" name="admin_email" value="<?php echo esc_attr($admin_email); ?>" class="regular-text"></td>
                </tr>
            </table>
        </div>
        
        <div id="security" class="dots-tab-content" style="display: none;">
            <table class="form-table">
                <tr>
                    <th><label for="enable_captcha"><?php esc_html_e('Enable CAPTCHA', 'dream-online-ticket-selling'); ?></label></th>
                    <td><input type="checkbox" id="enable_captcha" name="enable_captcha" value="1" <?php checked($enable_captcha, 1); ?>></td>
                </tr>
            </table>
        </div>
        
        <div id="payment" class="dots-tab-content" style="display: none;">
            <h3><?php esc_html_e('PayPal Settings', 'dream-online-ticket-selling'); ?></h3>
            <table class="form-table">
                <tr>
                    <th><label for="paypal_enabled"><?php esc_html_e('Enable PayPal', 'dream-online-ticket-selling'); ?></label></th>
                    <td><input type="checkbox" id="paypal_enabled" name="paypal_enabled" value="1" <?php checked(isset($settings['paypal_enabled']) && $settings['paypal_enabled'], 1); ?>></td>
                </tr>
                <tr>
                    <th><label for="paypal_mode"><?php esc_html_e('PayPal Mode', 'dream-online-ticket-selling'); ?></label></th>
                    <td>
                        <select id="paypal_mode" name="paypal_mode">
                            <option value="sandbox" <?php selected(isset($settings['paypal_mode']) ? $settings['paypal_mode'] : 'sandbox', 'sandbox'); ?>><?php esc_html_e('Sandbox (Test)', 'dream-online-ticket-selling'); ?></option>
                            <option value="live" <?php selected(isset($settings['paypal_mode']) ? $settings['paypal_mode'] : '', 'live'); ?>><?php esc_html_e('Live', 'dream-online-ticket-selling'); ?></option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><label for="paypal_client_id"><?php esc_html_e('PayPal Client ID', 'dream-online-ticket-selling'); ?></label></th>
                    <td><input type="text" id="paypal_client_id" name="paypal_client_id" value="<?php echo esc_attr(isset($settings['paypal_client_id']) ? $settings['paypal_client_id'] : ''); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="paypal_secret"><?php esc_html_e('PayPal Secret', 'dream-online-ticket-selling'); ?></label></th>
                    <td><input type="password" id="paypal_secret" name="paypal_secret" value="<?php echo esc_attr(isset($settings['paypal_secret']) ? $settings['paypal_secret'] : ''); ?>" class="regular-text"></td>
                </tr>
            </table>
            
            <h3><?php esc_html_e('Stripe Settings', 'dream-online-ticket-selling'); ?></h3>
            <table class="form-table">
                <tr>
                    <th><label for="stripe_enabled"><?php esc_html_e('Enable Stripe', 'dream-online-ticket-selling'); ?></label></th>
                    <td><input type="checkbox" id="stripe_enabled" name="stripe_enabled" value="1" <?php checked(isset($settings['stripe_enabled']) && $settings['stripe_enabled'], 1); ?>></td>
                </tr>
                <tr>
                    <th><label for="stripe_mode"><?php esc_html_e('Stripe Mode', 'dream-online-ticket-selling'); ?></label></th>
                    <td>
                        <select id="stripe_mode" name="stripe_mode">
                            <option value="test" <?php selected(isset($settings['stripe_mode']) ? $settings['stripe_mode'] : 'test', 'test'); ?>><?php esc_html_e('Test Mode', 'dream-online-ticket-selling'); ?></option>
                            <option value="live" <?php selected(isset($settings['stripe_mode']) ? $settings['stripe_mode'] : '', 'live'); ?>><?php esc_html_e('Live Mode', 'dream-online-ticket-selling'); ?></option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><label for="stripe_secret_key"><?php esc_html_e('Stripe Secret Key', 'dream-online-ticket-selling'); ?></label></th>
                    <td><input type="password" id="stripe_secret_key" name="stripe_secret_key" value="<?php echo esc_attr(isset($settings['stripe_secret_key']) ? $settings['stripe_secret_key'] : ''); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="stripe_publishable_key"><?php esc_html_e('Stripe Publishable Key', 'dream-online-ticket-selling'); ?></label></th>
                    <td><input type="text" id="stripe_publishable_key" name="stripe_publishable_key" value="<?php echo esc_attr(isset($settings['stripe_publishable_key']) ? $settings['stripe_publishable_key'] : ''); ?>" class="regular-text"></td>
                </tr>
            </table>
            
            <h3><?php esc_html_e('SSLCommerz Settings', 'dream-online-ticket-selling'); ?></h3>
            <table class="form-table">
                <tr>
                    <th><label for="sslcommerz_enabled"><?php esc_html_e('Enable SSLCommerz', 'dream-online-ticket-selling'); ?></label></th>
                    <td><input type="checkbox" id="sslcommerz_enabled" name="sslcommerz_enabled" value="1" <?php checked(isset($settings['sslcommerz_enabled']) && $settings['sslcommerz_enabled'], 1); ?>></td>
                </tr>
                <tr>
                    <th><label for="sslcommerz_mode"><?php esc_html_e('SSLCommerz Mode', 'dream-online-ticket-selling'); ?></label></th>
                    <td>
                        <select id="sslcommerz_mode" name="sslcommerz_mode">
                            <option value="sandbox" <?php selected(isset($settings['sslcommerz_mode']) ? $settings['sslcommerz_mode'] : 'sandbox', 'sandbox'); ?>><?php esc_html_e('Sandbox (Test)', 'dream-online-ticket-selling'); ?></option>
                            <option value="live" <?php selected(isset($settings['sslcommerz_mode']) ? $settings['sslcommerz_mode'] : '', 'live'); ?>><?php esc_html_e('Live', 'dream-online-ticket-selling'); ?></option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><label for="sslcommerz_store_id"><?php esc_html_e('Store ID', 'dream-online-ticket-selling'); ?></label></th>
                    <td><input type="text" id="sslcommerz_store_id" name="sslcommerz_store_id" value="<?php echo esc_attr(isset($settings['sslcommerz_store_id']) ? $settings['sslcommerz_store_id'] : ''); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="sslcommerz_store_password"><?php esc_html_e('Store Password', 'dream-online-ticket-selling'); ?></label></th>
                    <td><input type="password" id="sslcommerz_store_password" name="sslcommerz_store_password" value="<?php echo esc_attr(isset($settings['sslcommerz_store_password']) ? $settings['sslcommerz_store_password'] : ''); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <td colspan="2">
                        <p class="description">
                            <?php esc_html_e('Get your Store ID and Store Password from your SSLCommerz merchant panel.', 'dream-online-ticket-selling'); ?><br>
                            <?php esc_html_e('Test credentials: Store ID: testbox, Store Password: qwerty', 'dream-online-ticket-selling'); ?>
                        </p>
                    </td>
                </tr>
            </table>
            
            <h3><?php esc_html_e('Bank Transfer', 'dream-online-ticket-selling'); ?></h3>
            <table class="form-table">
                <tr>
                    <th><label for="bank_transfer_enabled"><?php esc_html_e('Enable Bank Transfer', 'dream-online-ticket-selling'); ?></label></th>
                    <td><input type="checkbox" id="bank_transfer_enabled" name="bank_transfer_enabled" value="1" <?php checked(isset($settings['bank_transfer_enabled']) && $settings['bank_transfer_enabled'], 1); ?>></td>
                </tr>
                <tr>
                    <th><label for="bank_transfer_details"><?php esc_html_e('Bank Transfer Instructions', 'dream-online-ticket-selling'); ?></label></th>
                    <td>
                        <textarea id="bank_transfer_details" name="bank_transfer_details" class="large-text" rows="5"><?php echo esc_textarea(isset($settings['bank_transfer_details']) ? $settings['bank_transfer_details'] : ''); ?></textarea>
                        <p class="description"><?php esc_html_e('Instructions shown to customers when they select bank transfer.', 'dream-online-ticket-selling'); ?></p>
                    </td>
                </tr>
            </table>
        </div>
        
        <p class="submit">
            <input type="submit" name="dots_save_settings" class="button button-primary" value="<?php esc_html_e('Save Settings', 'dream-online-ticket-selling'); ?>">
        </p>
    </form>
</div>

