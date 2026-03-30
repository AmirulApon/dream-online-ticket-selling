<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <title><?php esc_html_e('Ticket', 'dream-online-ticket-selling'); ?> - <?php echo esc_html($sale->order_number); ?></title>
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
    <div class="ticket">
        <div class="ticket-header">
            <h1><?php esc_html_e('Event Ticket', 'dream-online-ticket-selling'); ?></h1>
            <p><strong><?php esc_html_e('Order Number:', 'dream-online-ticket-selling'); ?></strong> <?php echo esc_html($sale->order_number); ?></p>
        </div>
        
        <div class="ticket-section">
            <h2><?php esc_html_e('Event Information', 'dream-online-ticket-selling'); ?></h2>
            <div class="ticket-row">
                <strong><?php esc_html_e('Event:', 'dream-online-ticket-selling'); ?></strong>
                <span><?php echo esc_html($event->name); ?></span>
            </div>
            <div class="ticket-row">
                <strong><?php esc_html_e('Date:', 'dream-online-ticket-selling'); ?></strong>
                <span><?php echo esc_html(date_i18n(get_option('date_format'), strtotime($event->event_date))); ?></span>
            </div>
            <div class="ticket-row">
                <strong><?php esc_html_e('Time:', 'dream-online-ticket-selling'); ?></strong>
                <span><?php echo esc_html(date_i18n(get_option('time_format'), strtotime($event->event_time))); ?></span>
            </div>
            <div class="ticket-row">
                <strong><?php esc_html_e('Location:', 'dream-online-ticket-selling'); ?></strong>
                <span><?php echo esc_html($event->location); ?></span>
            </div>
        </div>
        
        <div class="ticket-section">
            <h2><?php esc_html_e('Ticket Details', 'dream-online-ticket-selling'); ?></h2>
            <div class="ticket-row">
                <strong><?php esc_html_e('Quantity:', 'dream-online-ticket-selling'); ?></strong>
                <span><?php echo esc_html($sale->quantity); ?> <?php esc_html_e('ticket(s)', 'dream-online-ticket-selling'); ?></span>
            </div>
            <div class="ticket-row">
                <strong><?php esc_html_e('Total Amount:', 'dream-online-ticket-selling'); ?></strong>
                <span><?php echo esc_html($currency_symbol . number_format($sale->total_price, 2)); ?></span>
            </div>
        </div>
        
        <div class="ticket-section">
            <h2><?php esc_html_e('Customer Information', 'dream-online-ticket-selling'); ?></h2>
            <div class="ticket-row">
                <strong><?php esc_html_e('Name:', 'dream-online-ticket-selling'); ?></strong>
                <span><?php echo esc_html($customer->name); ?></span>
            </div>
            <div class="ticket-row">
                <strong><?php esc_html_e('Email:', 'dream-online-ticket-selling'); ?></strong>
                <span><?php echo esc_html($customer->email); ?></span>
            </div>
        </div>
        
        <?php
        $qr_url = $sale->qr_code;
        if (empty($qr_url)) {
            // Encode just the order number — scanners reject multiline plain text
            $qr_url = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&margin=2&data=' . rawurlencode($sale->order_number);
        }
        ?>
        <div class="ticket-qr">
            <img src="<?php echo esc_url($qr_url); ?>" alt="QR Code">
        </div>
    </div>
    <?php wp_footer(); ?>
</body>
</html>

