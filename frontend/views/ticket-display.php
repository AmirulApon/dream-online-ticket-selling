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
    <title><?php esc_html_e('Ticket', 'dream-online-ticket-selling'); ?> - <?php echo esc_html($sale->order_number); ?></title>
    <?php wp_head(); ?>
</head>
<body>
    <div class="ticket-container">
        <div class="ticket-header">
            <h1><?php esc_html_e('Event Ticket', 'dream-online-ticket-selling'); ?></h1>
            <div class="order-number"><?php echo esc_html($sale->order_number); ?></div>
        </div>
        
        <div class="ticket-body">
            <div class="ticket-section">
                <h2><?php esc_html_e('Event Information', 'dream-online-ticket-selling'); ?></h2>
                <div class="ticket-info-row">
                    <strong><?php esc_html_e('Event:', 'dream-online-ticket-selling'); ?></strong>
                    <span><?php echo esc_html($event->name); ?></span>
                </div>
                <div class="ticket-info-row">
                    <strong><?php esc_html_e('Date:', 'dream-online-ticket-selling'); ?></strong>
                    <span><?php echo esc_html(date_i18n(get_option('date_format'), strtotime($event->event_date))); ?></span>
                </div>
                <div class="ticket-info-row">
                    <strong><?php esc_html_e('Time:', 'dream-online-ticket-selling'); ?></strong>
                    <span><?php echo esc_html(date_i18n(get_option('time_format'), strtotime($event->event_time))); ?></span>
                </div>
                <div class="ticket-info-row">
                    <strong><?php esc_html_e('Location:', 'dream-online-ticket-selling'); ?></strong>
                    <span><?php echo esc_html($event->location); ?></span>
                </div>
            </div>
            
            <div class="ticket-section">
                <h2><?php esc_html_e('Ticket Details', 'dream-online-ticket-selling'); ?></h2>
                <div class="ticket-info-row">
                    <strong><?php esc_html_e('Quantity:', 'dream-online-ticket-selling'); ?></strong>
                    <span><?php echo esc_html($sale->quantity); ?> <?php esc_html_e('ticket(s)', 'dream-online-ticket-selling'); ?></span>
                </div>
                <div class="ticket-info-row">
                    <strong><?php esc_html_e('Price per Ticket:', 'dream-online-ticket-selling'); ?></strong>
                    <span><?php echo esc_html($currency_symbol . number_format($sale->unit_price, 2)); ?></span>
                </div>
                <div class="ticket-info-row">
                    <strong><?php esc_html_e('Total Amount:', 'dream-online-ticket-selling'); ?></strong>
                    <span><?php echo esc_html($currency_symbol . number_format($sale->total_price, 2)); ?></span>
                </div>
                <div class="ticket-info-row">
                    <strong><?php esc_html_e('Payment Status:', 'dream-online-ticket-selling'); ?></strong>
                    <span class="status-badge status-<?php echo esc_attr($sale->payment_status); ?>">
                        <?php echo esc_html(ucfirst($sale->payment_status)); ?>
                    </span>
                </div>
            </div>
            
            <div class="ticket-section">
                <h2><?php esc_html_e('Customer Information', 'dream-online-ticket-selling'); ?></h2>
                <div class="ticket-info-row">
                    <strong><?php esc_html_e('Name:', 'dream-online-ticket-selling'); ?></strong>
                    <span><?php echo esc_html($customer->name); ?></span>
                </div>
                <div class="ticket-info-row">
                    <strong><?php esc_html_e('Email:', 'dream-online-ticket-selling'); ?></strong>
                    <span><?php echo esc_html($customer->email); ?></span>
                </div>
                <?php if ($customer->phone): ?>
                <div class="ticket-info-row">
                    <strong><?php esc_html_e('Phone:', 'dream-online-ticket-selling'); ?></strong>
                    <span><?php echo esc_html($customer->phone); ?></span>
                </div>
                <?php endif; ?>
            </div>
            
            <?php
            $qr_url = $sale->qr_code;
            if (empty($qr_url)) {
                // Encode just the order number — scanners reject multiline plain text
                $qr_url = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&margin=2&data=' . rawurlencode($sale->order_number);
            }
            ?>
            <div class="ticket-qr">
                <h3><?php esc_html_e('QR Code', 'dream-online-ticket-selling'); ?></h3>
                <img src="<?php echo esc_url($qr_url); ?>" alt="<?php esc_attr_e('Ticket QR Code', 'dream-online-ticket-selling'); ?>">
                <p><?php esc_html_e('Scan this QR code for verification', 'dream-online-ticket-selling'); ?></p>
            </div>
            
            <div class="ticket-actions">
                <a href="<?php echo esc_url(home_url('/dream-tickets/ticket/' . $sale->order_number . '?download=pdf')); ?>" class="btn">
                    <?php esc_html_e('Download PDF', 'dream-online-ticket-selling'); ?>
                </a>
                <button onclick="window.print()" class="btn btn-secondary">
                    <?php esc_html_e('Print Ticket', 'dream-online-ticket-selling'); ?>
                </button>
            </div>
        </div>
    </div>
    <?php wp_footer(); ?>
</body>
</html>

