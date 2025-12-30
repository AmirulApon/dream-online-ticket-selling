<?php
if (!defined('ABSPATH')) {
    exit;
}

$settings = get_option('dots_settings', array());
$currency_symbol = isset($settings['currency_symbol']) ? $settings['currency_symbol'] : '$';
?>

<div class="wrap dots-sales">
    <h1><?php _e('Sales & Reports', 'dream-ticket'); ?></h1>
    
    <div class="dots-sales-stats">
        <?php
        global $wpdb;
        $table_sales = $wpdb->prefix . 'dots_sales';
        $total_revenue = $wpdb->get_var("SELECT SUM(total_price) FROM $table_sales WHERE payment_status = 'completed'");
        $total_tickets = $wpdb->get_var("SELECT SUM(quantity) FROM $table_sales WHERE payment_status = 'completed'");
        $pending_payments = $wpdb->get_var("SELECT COUNT(*) FROM $table_sales WHERE payment_status = 'pending'");
        ?>
        
        <div class="dots-stat-card">
            <h3><?php echo $currency_symbol . number_format($total_revenue, 2); ?></h3>
            <p><?php _e('Total Revenue', 'dream-ticket'); ?></p>
        </div>
        <div class="dots-stat-card">
            <h3><?php echo number_format($total_tickets); ?></h3>
            <p><?php _e('Tickets Sold', 'dream-ticket'); ?></p>
        </div>
        <div class="dots-stat-card">
            <h3><?php echo number_format($pending_payments); ?></h3>
            <p><?php _e('Pending Payments', 'dream-ticket'); ?></p>
        </div>
    </div>
    
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th><?php _e('Order Number', 'dream-ticket'); ?></th>
                <th><?php _e('Customer', 'dream-ticket'); ?></th>
                <th><?php _e('Event', 'dream-ticket'); ?></th>
                <th><?php _e('Ticket Type', 'dream-ticket'); ?></th>
                <th><?php _e('Quantity', 'dream-ticket'); ?></th>
                <th><?php _e('Unit Price', 'dream-ticket'); ?></th>
                <th><?php _e('Total Price', 'dream-ticket'); ?></th>
                <th><?php _e('Payment Method', 'dream-ticket'); ?></th>
                <th><?php _e('Transaction ID', 'dream-ticket'); ?></th>
                <th><?php _e('Date', 'dream-ticket'); ?></th>
                <th><?php _e('Status', 'dream-ticket'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($sales)): ?>
                <?php foreach ($sales as $sale): ?>
                    <tr>
                        <td><?php echo esc_html($sale->order_number); ?></td>
                        <td><?php echo esc_html($sale->customer_name); ?></td>
                        <td><?php echo esc_html($sale->event_name); ?></td>
                        <td><?php echo esc_html($sale->ticket_category_name); ?></td>
                        <td><?php echo esc_html($sale->quantity); ?></td>
                        <td><?php echo $currency_symbol . number_format($sale->unit_price, 2); ?></td>
                        <td><?php echo $currency_symbol . number_format($sale->total_price, 2); ?></td>
                        <td><?php echo esc_html($sale->payment_method ?: '-'); ?></td>
                        <td><?php echo esc_html($sale->transaction_id ?: '-'); ?></td>
                        <td><?php echo date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($sale->created_at)); ?></td>
                        <td>
                            <span class="dots-status dots-status-<?php echo esc_attr($sale->payment_status); ?>">
                                <?php echo esc_html(ucfirst($sale->payment_status)); ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="11"><?php _e('No sales found.', 'dream-ticket'); ?></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

