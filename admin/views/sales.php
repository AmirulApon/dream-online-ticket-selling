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

<div class="wrap dots-sales">
    <h1><?php esc_html_e('Sales & Reports', 'dream-online-ticket-selling'); ?></h1>
    
    <div style="margin: 20px 0;">
        <form method="post" action="" style="display: inline-block;">
            <?php wp_nonce_field('dots_flush_rewrite'); ?>
            <button type="submit" name="flush_rewrite_rules" class="button">
                <?php esc_html_e('Flush Rewrite Rules', 'dream-online-ticket-selling'); ?>
            </button>
        </form>
        <p class="description" style="display: inline-block; margin-left: 10px;">
            <?php esc_html_e('Click this if QR codes or ticket URLs are not working.', 'dream-online-ticket-selling'); ?>
        </p>
    </div>
    
    <div class="dots-sales-stats">
        <?php
        // phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
        // Template variables are intentionally non-prefixed for readability
        global $wpdb;
        $table_sales = $wpdb->prefix . 'dots_sales';
        // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared,PluginCheck.Security.DirectDB.UnescapedDBParameter -- Table name from $wpdb->prefix is safe
        $total_revenue = $wpdb->get_var("SELECT SUM(total_price) FROM $table_sales WHERE payment_status = 'completed'");
        // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared,PluginCheck.Security.DirectDB.UnescapedDBParameter -- Table name from $wpdb->prefix is safe
        $total_tickets = $wpdb->get_var("SELECT SUM(quantity) FROM $table_sales WHERE payment_status = 'completed'");
        // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared,PluginCheck.Security.DirectDB.UnescapedDBParameter -- Table name from $wpdb->prefix is safe
        $pending_payments = $wpdb->get_var("SELECT COUNT(*) FROM $table_sales WHERE payment_status = 'pending'");
        // phpcs:enable
        ?>
        
        <div class="dots-stat-card">
            <h3><?php echo esc_html($currency_symbol . number_format($total_revenue, 2)); ?></h3>
            <p><?php esc_html_e('Total Revenue', 'dream-online-ticket-selling'); ?></p>
        </div>
        <div class="dots-stat-card">
            <h3><?php echo esc_html(number_format($total_tickets)); ?></h3>
            <p><?php esc_html_e('Tickets Sold', 'dream-online-ticket-selling'); ?></p>
        </div>
        <div class="dots-stat-card">
            <h3><?php echo esc_html(number_format($pending_payments)); ?></h3>
            <p><?php esc_html_e('Pending Payments', 'dream-online-ticket-selling'); ?></p>
        </div>
    </div>
    
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th><?php esc_html_e('Order Number', 'dream-online-ticket-selling'); ?></th>
                <th><?php esc_html_e('Customer', 'dream-online-ticket-selling'); ?></th>
                <th><?php esc_html_e('Event', 'dream-online-ticket-selling'); ?></th>
                <th><?php esc_html_e('Event Type', 'dream-online-ticket-selling'); ?></th>
                <th><?php esc_html_e('Quantity', 'dream-online-ticket-selling'); ?></th>
                <th><?php esc_html_e('Unit Price', 'dream-online-ticket-selling'); ?></th>
                <th><?php esc_html_e('Total Price', 'dream-online-ticket-selling'); ?></th>
                <th><?php esc_html_e('Payment Method', 'dream-online-ticket-selling'); ?></th>
                <th><?php esc_html_e('Transaction ID', 'dream-online-ticket-selling'); ?></th>
                <th><?php esc_html_e('Date', 'dream-online-ticket-selling'); ?></th>
                <th><?php esc_html_e('Status', 'dream-online-ticket-selling'); ?></th>
                <th><?php esc_html_e('Actions', 'dream-online-ticket-selling'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($sales)): ?>
                <?php foreach ($sales as $sale): // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound ?>
                    <tr>
                        <td><?php echo esc_html($sale->order_number); ?></td>
                        <td><?php echo esc_html($sale->customer_name); ?></td>
                        <td><?php echo esc_html($sale->event_name); ?></td>
                        <td><?php echo esc_html(isset($sale->event_type) && !empty($sale->event_type) ? $sale->event_type : '-'); ?></td>
                        <td><?php echo esc_html($sale->quantity); ?></td>
                        <td><?php echo esc_html($currency_symbol . number_format($sale->unit_price, 2)); ?></td>
                        <td><?php echo esc_html($currency_symbol . number_format($sale->total_price, 2)); ?></td>
                        <td><?php echo esc_html($sale->payment_method ?: '-'); ?></td>
                        <td><?php echo esc_html($sale->transaction_id ?: '-'); ?></td>
                        <td><?php echo esc_html(date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($sale->created_at))); ?></td>
                        <td>
                            <span class="dots-status dots-status-<?php echo esc_attr($sale->payment_status); ?>">
                                <?php echo esc_html(ucfirst($sale->payment_status)); ?>
                            </span>
                        </td>
                        <td>
                            <a href="<?php echo esc_url(home_url('/dream-tickets/ticket/' . urlencode($sale->order_number))); ?>" target="_blank" class="button button-small">
                                <?php esc_html_e('View Ticket', 'dream-online-ticket-selling'); ?>
                            </a>
                            <form method="post" action="" style="display: inline-block; margin-left: 5px;">
                                <?php wp_nonce_field('dots_regenerate_qr'); ?>
                                <input type="hidden" name="order_number" value="<?php echo esc_attr($sale->order_number); ?>">
                                <button type="submit" name="regenerate_qr" class="button button-small">
                                    <?php esc_html_e('Regenerate QR', 'dream-online-ticket-selling'); ?>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="12"><?php esc_html_e('No sales found.', 'dream-online-ticket-selling'); ?></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

