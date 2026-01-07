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
    <title><?php _e('Ticket', 'dream-ticket'); ?> - <?php echo esc_html($sale->order_number); ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
            background: #f5f5f5;
            padding: 20px;
        }
        .ticket-container {
            max-width: 600px;
            margin: 0 auto;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .ticket-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            padding: 30px;
            text-align: center;
        }
        .ticket-header h1 {
            font-size: 28px;
            margin-bottom: 10px;
        }
        .ticket-header .order-number {
            font-size: 18px;
            opacity: 0.9;
        }
        .ticket-body {
            padding: 30px;
        }
        .ticket-section {
            margin-bottom: 25px;
            padding-bottom: 25px;
            border-bottom: 1px solid #eee;
        }
        .ticket-section:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        .ticket-section h2 {
            font-size: 18px;
            color: #333;
            margin-bottom: 15px;
        }
        .ticket-info-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
        }
        .ticket-info-row strong {
            color: #666;
            font-weight: 600;
        }
        .ticket-qr {
            text-align: center;
            padding: 20px;
            background: #f9f9f9;
            border-radius: 8px;
            margin: 20px 0;
        }
        .ticket-qr img {
            max-width: 250px;
            height: auto;
            margin: 10px 0;
        }
        .ticket-actions {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }
        .btn {
            flex: 1;
            padding: 12px 24px;
            background: #667eea;
            color: #fff;
            text-decoration: none;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            text-align: center;
            cursor: pointer;
            transition: background 0.3s;
        }
        .btn:hover {
            background: #5568d3;
        }
        .btn-secondary {
            background: #6c757d;
        }
        .btn-secondary:hover {
            background: #5a6268;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
        }
        .status-completed {
            background: #d1e7dd;
            color: #0f5132;
        }
        .status-pending {
            background: #fff3cd;
            color: #856404;
        }
        .status-failed {
            background: #f8d7da;
            color: #842029;
        }
        @media print {
            body { background: #fff; padding: 0; }
            .ticket-actions { display: none; }
        }
    </style>
    <?php wp_head(); ?>
</head>
<body>
    <div class="ticket-container">
        <div class="ticket-header">
            <h1><?php _e('Event Ticket', 'dream-ticket'); ?></h1>
            <div class="order-number"><?php echo esc_html($sale->order_number); ?></div>
        </div>
        
        <div class="ticket-body">
            <div class="ticket-section">
                <h2><?php _e('Event Information', 'dream-ticket'); ?></h2>
                <div class="ticket-info-row">
                    <strong><?php _e('Event:', 'dream-ticket'); ?></strong>
                    <span><?php echo esc_html($event->name); ?></span>
                </div>
                <div class="ticket-info-row">
                    <strong><?php _e('Date:', 'dream-ticket'); ?></strong>
                    <span><?php echo date_i18n(get_option('date_format'), strtotime($event->event_date)); ?></span>
                </div>
                <div class="ticket-info-row">
                    <strong><?php _e('Time:', 'dream-ticket'); ?></strong>
                    <span><?php echo date_i18n(get_option('time_format'), strtotime($event->event_time)); ?></span>
                </div>
                <div class="ticket-info-row">
                    <strong><?php _e('Location:', 'dream-ticket'); ?></strong>
                    <span><?php echo esc_html($event->location); ?></span>
                </div>
            </div>
            
            <div class="ticket-section">
                <h2><?php _e('Ticket Details', 'dream-ticket'); ?></h2>
                <div class="ticket-info-row">
                    <strong><?php _e('Quantity:', 'dream-ticket'); ?></strong>
                    <span><?php echo esc_html($sale->quantity); ?> <?php _e('ticket(s)', 'dream-ticket'); ?></span>
                </div>
                <div class="ticket-info-row">
                    <strong><?php _e('Price per Ticket:', 'dream-ticket'); ?></strong>
                    <span><?php echo $currency_symbol . number_format($sale->unit_price, 2); ?></span>
                </div>
                <div class="ticket-info-row">
                    <strong><?php _e('Total Amount:', 'dream-ticket'); ?></strong>
                    <span><?php echo $currency_symbol . number_format($sale->total_price, 2); ?></span>
                </div>
                <div class="ticket-info-row">
                    <strong><?php _e('Payment Status:', 'dream-ticket'); ?></strong>
                    <span class="status-badge status-<?php echo esc_attr($sale->payment_status); ?>">
                        <?php echo esc_html(ucfirst($sale->payment_status)); ?>
                    </span>
                </div>
            </div>
            
            <div class="ticket-section">
                <h2><?php _e('Customer Information', 'dream-ticket'); ?></h2>
                <div class="ticket-info-row">
                    <strong><?php _e('Name:', 'dream-ticket'); ?></strong>
                    <span><?php echo esc_html($customer->name); ?></span>
                </div>
                <div class="ticket-info-row">
                    <strong><?php _e('Email:', 'dream-ticket'); ?></strong>
                    <span><?php echo esc_html($customer->email); ?></span>
                </div>
                <?php if ($customer->phone): ?>
                <div class="ticket-info-row">
                    <strong><?php _e('Phone:', 'dream-ticket'); ?></strong>
                    <span><?php echo esc_html($customer->phone); ?></span>
                </div>
                <?php endif; ?>
            </div>
            
            <?php if ($sale->qr_code): ?>
            <div class="ticket-qr">
                <h3><?php _e('QR Code', 'dream-ticket'); ?></h3>
                <img src="<?php echo esc_url($sale->qr_code); ?>" alt="<?php _e('Ticket QR Code', 'dream-ticket'); ?>">
                <p><?php _e('Scan this QR code for verification', 'dream-ticket'); ?></p>
            </div>
            <?php endif; ?>
            
            <div class="ticket-actions">
                <a href="<?php echo home_url('/dream-tickets/ticket/' . $sale->order_number . '?download=pdf'); ?>" class="btn">
                    <?php _e('Download PDF', 'dream-ticket'); ?>
                </a>
                <button onclick="window.print()" class="btn btn-secondary">
                    <?php _e('Print Ticket', 'dream-ticket'); ?>
                </button>
            </div>
        </div>
    </div>
    <?php wp_footer(); ?>
</body>
</html>

