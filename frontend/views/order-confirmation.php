<?php
if (!defined('ABSPATH')) {
    exit;
}

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
// Template variables are intentionally non-prefixed for readability
$settings = get_option('dots_settings', array());
$currency_symbol = isset($settings['currency_symbol']) ? $settings['currency_symbol'] : '$';
// phpcs:enable
?>

<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php esc_html_e('Order Confirmation', 'dream-online-ticket-selling'); ?> - <?php bloginfo('name'); ?></title>
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
    <div class="dots-order-confirmation">
        <div class="dots-confirmation-container">
            <div class="dots-confirmation-header">
                <span class="dashicons dashicons-yes-alt"></span>
                <h1><?php esc_html_e('Order Confirmed!', 'dream-online-ticket-selling'); ?></h1>
                <p><?php esc_html_e('Thank you for your purchase.', 'dream-online-ticket-selling'); ?></p>
            </div>
            
            <div class="dots-order-details">
                <h2><?php esc_html_e('Order Details', 'dream-online-ticket-selling'); ?></h2>
                
                <div class="dots-order-info">
                    <div class="dots-info-row">
                        <strong><?php esc_html_e('Order Number:', 'dream-online-ticket-selling'); ?></strong>
                        <span><?php echo esc_html($sale->order_number); ?></span>
                    </div>
                    <div class="dots-info-row">
                        <strong><?php esc_html_e('Event:', 'dream-online-ticket-selling'); ?></strong>
                        <span><?php echo esc_html($event->name); ?></span>
                    </div>
                    <div class="dots-info-row">
                        <strong><?php esc_html_e('Date:', 'dream-online-ticket-selling'); ?></strong>
                        <span><?php echo esc_html(date_i18n(get_option('date_format'), strtotime($event->event_date))); ?></span>
                    </div>
                    <div class="dots-info-row">
                        <strong><?php esc_html_e('Time:', 'dream-online-ticket-selling'); ?></strong>
                        <span><?php echo esc_html(date_i18n(get_option('time_format'), strtotime($event->event_time))); ?></span>
                    </div>
                    <div class="dots-info-row">
                        <strong><?php esc_html_e('Location:', 'dream-online-ticket-selling'); ?></strong>
                        <span><?php echo esc_html($event->location); ?></span>
                    </div>
                    <div class="dots-info-row">
                        <strong><?php esc_html_e('Ticket Price:', 'dream-online-ticket-selling'); ?></strong>
                        <span><?php echo esc_html($currency_symbol . number_format($sale->unit_price, 2)); ?> <?php esc_html_e('per ticket', 'dream-online-ticket-selling'); ?></span>
                    </div>
                    <div class="dots-info-row">
                        <strong><?php esc_html_e('Quantity:', 'dream-online-ticket-selling'); ?></strong>
                        <span><?php echo esc_html($sale->quantity); ?></span>
                    </div>
                    <div class="dots-info-row">
                        <strong><?php esc_html_e('Total Price:', 'dream-online-ticket-selling'); ?></strong>
                        <span><?php echo esc_html($currency_symbol . number_format($sale->total_price, 2)); ?></span>
                    </div>
                    <div class="dots-info-row">
                        <strong><?php esc_html_e('Payment Status:', 'dream-online-ticket-selling'); ?></strong>
                        <span class="dots-status dots-status-<?php echo esc_attr($sale->payment_status); ?>">
                            <?php echo esc_html(ucfirst($sale->payment_status)); ?>
                        </span>
                    </div>
                </div>
                
                <?php if ($sale->qr_code): ?>
                    <div class="dots-qr-code">
                        <h3><?php esc_html_e('Your Ticket QR Code', 'dream-online-ticket-selling'); ?></h3>
                        <img src="<?php echo esc_url($sale->qr_code); ?>" 
                             alt="<?php esc_attr_e('QR Code', 'dream-online-ticket-selling'); ?>"
                             onerror="this.onerror=null; this.src='https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=<?php echo esc_js(rawurlencode($sale->order_number)); ?>';">
                        <?php unset($payload); ?>
                        <p><?php esc_html_e('Present this QR code at the event for entry.', 'dream-online-ticket-selling'); ?></p>
                        <p style="font-size: 12px; color: #666; margin-top: 10px;">
                            <strong><?php esc_html_e('Ticket URL:', 'dream-online-ticket-selling'); ?></strong><br>
                            <a href="<?php echo esc_url(home_url('/dream-tickets/ticket/' . $sale->order_number)); ?>" target="_blank" style="word-break: break-all;">
                                <?php echo esc_html(home_url('/dream-tickets/ticket/' . $sale->order_number)); ?>
                            </a>
                        </p>
                    </div>
                <?php else: ?>
                    <div class="dots-qr-code">
                        <h3><?php esc_html_e('Your Ticket QR Code', 'dream-online-ticket-selling'); ?></h3>
                        <?php 
                        // phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
                        // Generate QR code on the fly if not stored
                        $qr_url = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' . rawurlencode($sale->order_number);
                        // phpcs:enable
                        ?>
                        <img src="<?php echo esc_url($qr_url); ?>" alt="<?php esc_attr_e('QR Code', 'dream-online-ticket-selling'); ?>">
                        <p><?php esc_html_e('Present this QR code at the event for entry.', 'dream-online-ticket-selling'); ?></p>
                    </div>
                <?php endif; ?>
                
                <div class="dots-customer-info">
                    <h3><?php esc_html_e('Customer Information', 'dream-online-ticket-selling'); ?></h3>
                    <p><strong><?php esc_html_e('Name:', 'dream-online-ticket-selling'); ?></strong> <?php echo esc_html($customer->name); ?></p>
                    <p><strong><?php esc_html_e('Email:', 'dream-online-ticket-selling'); ?></strong> <?php echo esc_html($customer->email); ?></p>
                    <?php if ($customer->phone): ?>
                        <p><strong><?php esc_html_e('Phone:', 'dream-online-ticket-selling'); ?></strong> <?php echo esc_html($customer->phone); ?></p>
                    <?php endif; ?>
                </div>
                
                <div class="dots-confirmation-actions">
                    <button onclick="window.print()" class="dots-button"><?php esc_html_e('Print Confirmation', 'dream-online-ticket-selling'); ?></button>
                    <a href="<?php echo esc_url(home_url()); ?>" class="dots-button"><?php esc_html_e('Back to Home', 'dream-online-ticket-selling'); ?></a>
                </div>
            </div>
        </div>
    </div>
    <?php wp_footer(); ?>
</body>
</html>

