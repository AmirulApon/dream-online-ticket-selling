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

<div class="wrap dots-customers">
    <h1><?php esc_html_e('Customers', 'dream-online-ticket-selling'); ?></h1>
    
    <div class="dots-customers-actions">
        <a href="#" class="button" id="dots-export-customers"><?php esc_html_e('Export CSV', 'dream-online-ticket-selling'); ?></a>
    </div>
    
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th><?php esc_html_e('Order Number', 'dream-online-ticket-selling'); ?></th>
                <th><?php esc_html_e('Customer Name', 'dream-online-ticket-selling'); ?></th>
                <th><?php esc_html_e('Email', 'dream-online-ticket-selling'); ?></th>
                <th><?php esc_html_e('Phone', 'dream-online-ticket-selling'); ?></th>
                <th><?php esc_html_e('Event', 'dream-online-ticket-selling'); ?></th>
                <th><?php esc_html_e('Event Type', 'dream-online-ticket-selling'); ?></th>
                <th><?php esc_html_e('Quantity', 'dream-online-ticket-selling'); ?></th>
                <th><?php esc_html_e('Total Price', 'dream-online-ticket-selling'); ?></th>
                <th><?php esc_html_e('Purchase Date', 'dream-online-ticket-selling'); ?></th>
                <th><?php esc_html_e('Payment Status', 'dream-online-ticket-selling'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($sales)): ?>
                <?php foreach ($sales as $sale): // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound ?>
                    <tr>
                        <td><?php echo esc_html($sale->order_number); ?></td>
                        <td><?php echo esc_html($sale->customer_name); ?></td>
                        <td><?php echo esc_html($sale->customer_email); ?></td>
                        <td><?php echo esc_html($sale->customer_phone); ?></td>
                        <td><?php echo esc_html($sale->event_name); ?></td>
                        <td><?php echo esc_html(isset($sale->event_type) && !empty($sale->event_type) ? $sale->event_type : '-'); ?></td>
                        <td><?php echo esc_html($sale->quantity); ?></td>
                        <td><?php echo esc_html($currency_symbol . number_format($sale->total_price, 2)); ?></td>
                        <td><?php echo esc_html(date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($sale->created_at))); ?></td>
                        <td>
                            <span class="dots-status dots-status-<?php echo esc_attr($sale->payment_status); ?>">
                                <?php echo esc_html(ucfirst($sale->payment_status)); ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="10"><?php esc_html_e('No customers found.', 'dream-online-ticket-selling'); ?></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

