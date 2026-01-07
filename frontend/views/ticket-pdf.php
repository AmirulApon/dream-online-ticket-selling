<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?php _e('Ticket', 'dream-ticket'); ?> - <?php echo esc_html($sale->order_number); ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: Arial, sans-serif;
            padding: 40px;
            background: #fff;
        }
        .ticket {
            max-width: 800px;
            margin: 0 auto;
            border: 2px solid #333;
            padding: 30px;
        }
        .ticket-header {
            text-align: center;
            border-bottom: 3px solid #333;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .ticket-header h1 {
            font-size: 32px;
            margin-bottom: 10px;
        }
        .ticket-section {
            margin-bottom: 25px;
            padding-bottom: 20px;
            border-bottom: 1px solid #ddd;
        }
        .ticket-section:last-child {
            border-bottom: none;
        }
        .ticket-section h2 {
            font-size: 20px;
            margin-bottom: 15px;
            color: #333;
        }
        .ticket-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
        }
        .ticket-qr {
            text-align: center;
            margin: 30px 0;
        }
        .ticket-qr img {
            max-width: 200px;
        }
        @media print {
            body { padding: 20px; }
        }
    </style>
</head>
<body>
    <div class="ticket">
        <div class="ticket-header">
            <h1><?php _e('Event Ticket', 'dream-ticket'); ?></h1>
            <p><strong><?php _e('Order Number:', 'dream-ticket'); ?></strong> <?php echo esc_html($sale->order_number); ?></p>
        </div>
        
        <div class="ticket-section">
            <h2><?php _e('Event Information', 'dream-ticket'); ?></h2>
            <div class="ticket-row">
                <strong><?php _e('Event:', 'dream-ticket'); ?></strong>
                <span><?php echo esc_html($event->name); ?></span>
            </div>
            <div class="ticket-row">
                <strong><?php _e('Date:', 'dream-ticket'); ?></strong>
                <span><?php echo date_i18n(get_option('date_format'), strtotime($event->event_date)); ?></span>
            </div>
            <div class="ticket-row">
                <strong><?php _e('Time:', 'dream-ticket'); ?></strong>
                <span><?php echo date_i18n(get_option('time_format'), strtotime($event->event_time)); ?></span>
            </div>
            <div class="ticket-row">
                <strong><?php _e('Location:', 'dream-ticket'); ?></strong>
                <span><?php echo esc_html($event->location); ?></span>
            </div>
        </div>
        
        <div class="ticket-section">
            <h2><?php _e('Ticket Details', 'dream-ticket'); ?></h2>
            <div class="ticket-row">
                <strong><?php _e('Quantity:', 'dream-ticket'); ?></strong>
                <span><?php echo esc_html($sale->quantity); ?> <?php _e('ticket(s)', 'dream-ticket'); ?></span>
            </div>
            <div class="ticket-row">
                <strong><?php _e('Total Amount:', 'dream-ticket'); ?></strong>
                <span><?php echo $currency_symbol . number_format($sale->total_price, 2); ?></span>
            </div>
        </div>
        
        <div class="ticket-section">
            <h2><?php _e('Customer Information', 'dream-ticket'); ?></h2>
            <div class="ticket-row">
                <strong><?php _e('Name:', 'dream-ticket'); ?></strong>
                <span><?php echo esc_html($customer->name); ?></span>
            </div>
            <div class="ticket-row">
                <strong><?php _e('Email:', 'dream-ticket'); ?></strong>
                <span><?php echo esc_html($customer->email); ?></span>
            </div>
        </div>
        
        <?php if ($sale->qr_code): ?>
        <div class="ticket-qr">
            <img src="<?php echo esc_url($sale->qr_code); ?>" alt="QR Code">
        </div>
        <?php endif; ?>
    </div>
</body>
</html>

