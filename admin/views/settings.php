<?php
if (!defined('ABSPATH')) {
    exit;
}

if (isset($_POST['dots_save_settings']) && check_admin_referer('dots_settings_nonce')) {
    $settings = array(
        'currency' => sanitize_text_field($_POST['currency']),
        'currency_symbol' => sanitize_text_field($_POST['currency_symbol']),
        'timezone' => sanitize_text_field($_POST['timezone']),
        'max_tickets_per_customer' => intval($_POST['max_tickets_per_customer']),
        'enable_captcha' => isset($_POST['enable_captcha']) ? 1 : 0,
        'email_notifications' => isset($_POST['email_notifications']) ? 1 : 0,
        'admin_email' => sanitize_email($_POST['admin_email']),
        // Payment settings
        'paypal_enabled' => isset($_POST['paypal_enabled']) ? 1 : 0,
        'paypal_mode' => sanitize_text_field($_POST['paypal_mode']),
        'paypal_client_id' => sanitize_text_field($_POST['paypal_client_id']),
        'paypal_secret' => sanitize_text_field($_POST['paypal_secret']),
        'stripe_enabled' => isset($_POST['stripe_enabled']) ? 1 : 0,
        'stripe_mode' => sanitize_text_field($_POST['stripe_mode']),
        'stripe_secret_key' => sanitize_text_field($_POST['stripe_secret_key']),
        'stripe_publishable_key' => sanitize_text_field($_POST['stripe_publishable_key']),
        'sslcommerz_enabled' => isset($_POST['sslcommerz_enabled']) ? 1 : 0,
        'sslcommerz_mode' => sanitize_text_field($_POST['sslcommerz_mode']),
        'sslcommerz_store_id' => sanitize_text_field($_POST['sslcommerz_store_id']),
        'sslcommerz_store_password' => sanitize_text_field($_POST['sslcommerz_store_password']),
        'bank_transfer_enabled' => isset($_POST['bank_transfer_enabled']) ? 1 : 0,
        'bank_transfer_details' => wp_kses_post($_POST['bank_transfer_details']),
    );
    update_option('dots_settings', $settings);
    echo '<div class="notice notice-success"><p>' . __('Settings saved.', 'dream-ticket') . '</p></div>';
}

$settings = get_option('dots_settings', array());
$currency = isset($settings['currency']) ? $settings['currency'] : 'USD';
$currency_symbol = isset($settings['currency_symbol']) ? $settings['currency_symbol'] : '$';
$timezone = isset($settings['timezone']) ? $settings['timezone'] : wp_timezone_string();
$max_tickets = isset($settings['max_tickets_per_customer']) ? $settings['max_tickets_per_customer'] : 10;
$enable_captcha = isset($settings['enable_captcha']) ? $settings['enable_captcha'] : 0;
$email_notifications = isset($settings['email_notifications']) ? $settings['email_notifications'] : 1;
$admin_email = isset($settings['admin_email']) ? $settings['admin_email'] : get_option('admin_email');
?>

<div class="wrap dots-settings">
    <h1><?php _e('Settings', 'dream-ticket'); ?></h1>
    
    <form method="post" action="">
        <?php wp_nonce_field('dots_settings_nonce'); ?>
        
        <h2 class="nav-tab-wrapper">
            <a href="#general" class="nav-tab nav-tab-active"><?php _e('General', 'dream-ticket'); ?></a>
            <a href="#email" class="nav-tab"><?php _e('Email Notifications', 'dream-ticket'); ?></a>
            <a href="#security" class="nav-tab"><?php _e('Security', 'dream-ticket'); ?></a>
            <a href="#payment" class="nav-tab"><?php _e('Payment', 'dream-ticket'); ?></a>
        </h2>
        
        <div id="general" class="dots-tab-content">
            <table class="form-table">
                <tr>
                    <th><label for="currency"><?php _e('Currency', 'dream-ticket'); ?></label></th>
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
                        <p class="description"><?php _e('Select currency and the symbol will update automatically.', 'dream-ticket'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th><label for="currency_symbol"><?php _e('Currency Symbol', 'dream-ticket'); ?></label></th>
                    <td><input type="text" id="currency_symbol" name="currency_symbol" value="<?php echo esc_attr($currency_symbol); ?>" class="small-text"></td>
                </tr>
                <tr>
                    <th><label for="timezone"><?php _e('Timezone', 'dream-ticket'); ?></label></th>
                    <td>
                        <select id="timezone" name="timezone">
                            <?php echo wp_timezone_choice($timezone); ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><label for="max_tickets_per_customer"><?php _e('Max Tickets per Customer', 'dream-ticket'); ?></label></th>
                    <td><input type="number" id="max_tickets_per_customer" name="max_tickets_per_customer" value="<?php echo esc_attr($max_tickets); ?>" min="1" class="small-text"></td>
                </tr>
            </table>
        </div>
        
        <div id="email" class="dots-tab-content" style="display: none;">
            <table class="form-table">
                <tr>
                    <th><label for="email_notifications"><?php _e('Enable Email Notifications', 'dream-ticket'); ?></label></th>
                    <td><input type="checkbox" id="email_notifications" name="email_notifications" value="1" <?php checked($email_notifications, 1); ?>></td>
                </tr>
                <tr>
                    <th><label for="admin_email"><?php _e('Admin Email', 'dream-ticket'); ?></label></th>
                    <td><input type="email" id="admin_email" name="admin_email" value="<?php echo esc_attr($admin_email); ?>" class="regular-text"></td>
                </tr>
            </table>
        </div>
        
        <div id="security" class="dots-tab-content" style="display: none;">
            <table class="form-table">
                <tr>
                    <th><label for="enable_captcha"><?php _e('Enable CAPTCHA', 'dream-ticket'); ?></label></th>
                    <td><input type="checkbox" id="enable_captcha" name="enable_captcha" value="1" <?php checked($enable_captcha, 1); ?>></td>
                </tr>
            </table>
        </div>
        
        <div id="payment" class="dots-tab-content" style="display: none;">
            <h3><?php _e('PayPal Settings', 'dream-ticket'); ?></h3>
            <table class="form-table">
                <tr>
                    <th><label for="paypal_enabled"><?php _e('Enable PayPal', 'dream-ticket'); ?></label></th>
                    <td><input type="checkbox" id="paypal_enabled" name="paypal_enabled" value="1" <?php checked(isset($settings['paypal_enabled']) && $settings['paypal_enabled'], 1); ?>></td>
                </tr>
                <tr>
                    <th><label for="paypal_mode"><?php _e('PayPal Mode', 'dream-ticket'); ?></label></th>
                    <td>
                        <select id="paypal_mode" name="paypal_mode">
                            <option value="sandbox" <?php selected(isset($settings['paypal_mode']) ? $settings['paypal_mode'] : 'sandbox', 'sandbox'); ?>><?php _e('Sandbox (Test)', 'dream-ticket'); ?></option>
                            <option value="live" <?php selected(isset($settings['paypal_mode']) ? $settings['paypal_mode'] : '', 'live'); ?>><?php _e('Live', 'dream-ticket'); ?></option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><label for="paypal_client_id"><?php _e('PayPal Client ID', 'dream-ticket'); ?></label></th>
                    <td><input type="text" id="paypal_client_id" name="paypal_client_id" value="<?php echo esc_attr(isset($settings['paypal_client_id']) ? $settings['paypal_client_id'] : ''); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="paypal_secret"><?php _e('PayPal Secret', 'dream-ticket'); ?></label></th>
                    <td><input type="password" id="paypal_secret" name="paypal_secret" value="<?php echo esc_attr(isset($settings['paypal_secret']) ? $settings['paypal_secret'] : ''); ?>" class="regular-text"></td>
                </tr>
            </table>
            
            <h3><?php _e('Stripe Settings', 'dream-ticket'); ?></h3>
            <table class="form-table">
                <tr>
                    <th><label for="stripe_enabled"><?php _e('Enable Stripe', 'dream-ticket'); ?></label></th>
                    <td><input type="checkbox" id="stripe_enabled" name="stripe_enabled" value="1" <?php checked(isset($settings['stripe_enabled']) && $settings['stripe_enabled'], 1); ?>></td>
                </tr>
                <tr>
                    <th><label for="stripe_mode"><?php _e('Stripe Mode', 'dream-ticket'); ?></label></th>
                    <td>
                        <select id="stripe_mode" name="stripe_mode">
                            <option value="test" <?php selected(isset($settings['stripe_mode']) ? $settings['stripe_mode'] : 'test', 'test'); ?>><?php _e('Test Mode', 'dream-ticket'); ?></option>
                            <option value="live" <?php selected(isset($settings['stripe_mode']) ? $settings['stripe_mode'] : '', 'live'); ?>><?php _e('Live Mode', 'dream-ticket'); ?></option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><label for="stripe_secret_key"><?php _e('Stripe Secret Key', 'dream-ticket'); ?></label></th>
                    <td><input type="password" id="stripe_secret_key" name="stripe_secret_key" value="<?php echo esc_attr(isset($settings['stripe_secret_key']) ? $settings['stripe_secret_key'] : ''); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="stripe_publishable_key"><?php _e('Stripe Publishable Key', 'dream-ticket'); ?></label></th>
                    <td><input type="text" id="stripe_publishable_key" name="stripe_publishable_key" value="<?php echo esc_attr(isset($settings['stripe_publishable_key']) ? $settings['stripe_publishable_key'] : ''); ?>" class="regular-text"></td>
                </tr>
            </table>
            
            <h3><?php _e('SSLCommerz Settings', 'dream-ticket'); ?></h3>
            <table class="form-table">
                <tr>
                    <th><label for="sslcommerz_enabled"><?php _e('Enable SSLCommerz', 'dream-ticket'); ?></label></th>
                    <td><input type="checkbox" id="sslcommerz_enabled" name="sslcommerz_enabled" value="1" <?php checked(isset($settings['sslcommerz_enabled']) && $settings['sslcommerz_enabled'], 1); ?>></td>
                </tr>
                <tr>
                    <th><label for="sslcommerz_mode"><?php _e('SSLCommerz Mode', 'dream-ticket'); ?></label></th>
                    <td>
                        <select id="sslcommerz_mode" name="sslcommerz_mode">
                            <option value="sandbox" <?php selected(isset($settings['sslcommerz_mode']) ? $settings['sslcommerz_mode'] : 'sandbox', 'sandbox'); ?>><?php _e('Sandbox (Test)', 'dream-ticket'); ?></option>
                            <option value="live" <?php selected(isset($settings['sslcommerz_mode']) ? $settings['sslcommerz_mode'] : '', 'live'); ?>><?php _e('Live', 'dream-ticket'); ?></option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><label for="sslcommerz_store_id"><?php _e('Store ID', 'dream-ticket'); ?></label></th>
                    <td><input type="text" id="sslcommerz_store_id" name="sslcommerz_store_id" value="<?php echo esc_attr(isset($settings['sslcommerz_store_id']) ? $settings['sslcommerz_store_id'] : ''); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="sslcommerz_store_password"><?php _e('Store Password', 'dream-ticket'); ?></label></th>
                    <td><input type="password" id="sslcommerz_store_password" name="sslcommerz_store_password" value="<?php echo esc_attr(isset($settings['sslcommerz_store_password']) ? $settings['sslcommerz_store_password'] : ''); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <td colspan="2">
                        <p class="description">
                            <?php _e('Get your Store ID and Store Password from your SSLCommerz merchant panel.', 'dream-ticket'); ?><br>
                            <?php _e('Test credentials: Store ID: testbox, Store Password: qwerty', 'dream-ticket'); ?>
                        </p>
                    </td>
                </tr>
            </table>
            
            <h3><?php _e('Bank Transfer', 'dream-ticket'); ?></h3>
            <table class="form-table">
                <tr>
                    <th><label for="bank_transfer_enabled"><?php _e('Enable Bank Transfer', 'dream-ticket'); ?></label></th>
                    <td><input type="checkbox" id="bank_transfer_enabled" name="bank_transfer_enabled" value="1" <?php checked(isset($settings['bank_transfer_enabled']) && $settings['bank_transfer_enabled'], 1); ?>></td>
                </tr>
                <tr>
                    <th><label for="bank_transfer_details"><?php _e('Bank Transfer Instructions', 'dream-ticket'); ?></label></th>
                    <td>
                        <textarea id="bank_transfer_details" name="bank_transfer_details" class="large-text" rows="5"><?php echo esc_textarea(isset($settings['bank_transfer_details']) ? $settings['bank_transfer_details'] : ''); ?></textarea>
                        <p class="description"><?php _e('Instructions shown to customers when they select bank transfer.', 'dream-ticket'); ?></p>
                    </td>
                </tr>
            </table>
        </div>
        
        <p class="submit">
            <input type="submit" name="dots_save_settings" class="button button-primary" value="<?php _e('Save Settings', 'dream-ticket'); ?>">
        </p>
    </form>
</div>

