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
                        </select>
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
            <table class="form-table">
                <tr>
                    <th><?php _e('Payment Gateways', 'dream-ticket'); ?></th>
                    <td>
                        <p><?php _e('Payment gateway integration will be available in future updates.', 'dream-ticket'); ?></p>
                    </td>
                </tr>
            </table>
        </div>
        
        <p class="submit">
            <input type="submit" name="dots_save_settings" class="button button-primary" value="<?php _e('Save Settings', 'dream-ticket'); ?>">
        </p>
    </form>
</div>

