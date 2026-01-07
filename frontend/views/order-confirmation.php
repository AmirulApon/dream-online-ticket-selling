<?php
if (!defined('ABSPATH')) {
    exit;
}

$settings = get_option('dots_settings', array());
$currency_symbol = isset($settings['currency_symbol']) ? $settings['currency_symbol'] : '$';
?>

<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php _e('Order Confirmation', 'dream-ticket'); ?> - <?php bloginfo('name'); ?></title>
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
    <div class="dots-order-confirmation">
        <div class="dots-confirmation-container">
            <div class="dots-confirmation-header">
                <span class="dashicons dashicons-yes-alt"></span>
                <h1><?php _e('Order Confirmed!', 'dream-ticket'); ?></h1>
                <p><?php _e('Thank you for your purchase.', 'dream-ticket'); ?></p>
            </div>
            
            <div class="dots-order-details">
                <h2><?php _e('Order Details', 'dream-ticket'); ?></h2>
                
                <div class="dots-order-info">
                    <div class="dots-info-row">
                        <strong><?php _e('Order Number:', 'dream-ticket'); ?></strong>
                        <span><?php echo esc_html($sale->order_number); ?></span>
                    </div>
                    <div class="dots-info-row">
                        <strong><?php _e('Event:', 'dream-ticket'); ?></strong>
                        <span><?php echo esc_html($event->name); ?></span>
                    </div>
                    <div class="dots-info-row">
                        <strong><?php _e('Date:', 'dream-ticket'); ?></strong>
                        <span><?php echo date_i18n(get_option('date_format'), strtotime($event->event_date)); ?></span>
                    </div>
                    <div class="dots-info-row">
                        <strong><?php _e('Time:', 'dream-ticket'); ?></strong>
                        <span><?php echo date_i18n(get_option('time_format'), strtotime($event->event_time)); ?></span>
                    </div>
                    <div class="dots-info-row">
                        <strong><?php _e('Location:', 'dream-ticket'); ?></strong>
                        <span><?php echo esc_html($event->location); ?></span>
                    </div>
                    <div class="dots-info-row">
                        <strong><?php _e('Ticket Price:', 'dream-ticket'); ?></strong>
                        <span><?php echo $currency_symbol . number_format($sale->unit_price, 2); ?> <?php _e('per ticket', 'dream-ticket'); ?></span>
                    </div>
                    <div class="dots-info-row">
                        <strong><?php _e('Quantity:', 'dream-ticket'); ?></strong>
                        <span><?php echo esc_html($sale->quantity); ?></span>
                    </div>
                    <div class="dots-info-row">
                        <strong><?php _e('Total Price:', 'dream-ticket'); ?></strong>
                        <span><?php echo $currency_symbol . number_format($sale->total_price, 2); ?></span>
                    </div>
                    <div class="dots-info-row">
                        <strong><?php _e('Payment Status:', 'dream-ticket'); ?></strong>
                        <span class="dots-status dots-status-<?php echo esc_attr($sale->payment_status); ?>">
                            <?php echo esc_html(ucfirst($sale->payment_status)); ?>
                        </span>
                    </div>
                </div>
                
                <?php if ($sale->qr_code): ?>
                    <div class="dots-qr-code">
                        <h3><?php _e('Your Ticket QR Code', 'dream-ticket'); ?></h3>
                        <img src="<?php echo esc_url($sale->qr_code); ?>" 
                             alt="<?php _e('QR Code', 'dream-ticket'); ?>"
                             onerror="this.onerror=null; this.src='https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=<?php echo urlencode(home_url('/dream-tickets/ticket/' . $sale->order_number)); ?>';">
                        <p><?php _e('Present this QR code at the event for entry.', 'dream-ticket'); ?></p>
                        <p style="font-size: 12px; color: #666; margin-top: 10px;">
                            <strong><?php _e('Ticket URL:', 'dream-ticket'); ?></strong><br>
                            <a href="<?php echo home_url('/dream-tickets/ticket/' . $sale->order_number); ?>" target="_blank" style="word-break: break-all;">
                                <?php echo home_url('/dream-tickets/ticket/' . $sale->order_number); ?>
                            </a>
                        </p>
                    </div>
                <?php else: ?>
                    <div class="dots-qr-code">
                        <h3><?php _e('Your Ticket QR Code', 'dream-ticket'); ?></h3>
                        <?php 
                        // Generate QR code on the fly if not stored
                        $ticket_url = home_url('/dream-tickets/ticket/' . $sale->order_number);
                        $qr_url = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' . urlencode($ticket_url);
                        ?>
                        <img src="<?php echo esc_url($qr_url); ?>" alt="<?php _e('QR Code', 'dream-ticket'); ?>">
                        <p><?php _e('Present this QR code at the event for entry.', 'dream-ticket'); ?></p>
                    </div>
                <?php endif; ?>
                
                <div class="dots-customer-info">
                    <h3><?php _e('Customer Information', 'dream-ticket'); ?></h3>
                    <p><strong><?php _e('Name:', 'dream-ticket'); ?></strong> <?php echo esc_html($customer->name); ?></p>
                    <p><strong><?php _e('Email:', 'dream-ticket'); ?></strong> <?php echo esc_html($customer->email); ?></p>
                    <?php if ($customer->phone): ?>
                        <p><strong><?php _e('Phone:', 'dream-ticket'); ?></strong> <?php echo esc_html($customer->phone); ?></p>
                    <?php endif; ?>
                </div>
                
                <div class="dots-confirmation-actions">
                    <button onclick="window.print()" class="dots-button"><?php _e('Print Confirmation', 'dream-ticket'); ?></button>
                    <a href="<?php echo home_url(); ?>" class="dots-button"><?php _e('Back to Home', 'dream-ticket'); ?></a>
                </div>
            </div>
        </div>
    </div>
    <?php wp_footer(); ?>
</body>
</html>

