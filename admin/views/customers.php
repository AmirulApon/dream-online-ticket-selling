<?php
if (!defined('ABSPATH')) {
    exit;
}

$settings = get_option('dots_settings', array());
$currency_symbol = isset($settings['currency_symbol']) ? $settings['currency_symbol'] : '$';
?>

<div class="wrap dots-customers">
    <h1><?php _e('Customers', 'dream-ticket'); ?></h1>
    
    <div class="dots-customers-actions">
        <a href="#" class="button" id="dots-export-customers"><?php _e('Export CSV', 'dream-ticket'); ?></a>
    </div>
    
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th><?php _e('Order Number', 'dream-ticket'); ?></th>
                <th><?php _e('Customer Name', 'dream-ticket'); ?></th>
                <th><?php _e('Email', 'dream-ticket'); ?></th>
                <th><?php _e('Phone', 'dream-ticket'); ?></th>
                <th><?php _e('Event', 'dream-ticket'); ?></th>
                <th><?php _e('Ticket Type', 'dream-ticket'); ?></th>
                <th><?php _e('Quantity', 'dream-ticket'); ?></th>
                <th><?php _e('Total Price', 'dream-ticket'); ?></th>
                <th><?php _e('Purchase Date', 'dream-ticket'); ?></th>
                <th><?php _e('Payment Status', 'dream-ticket'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($sales)): ?>
                <?php foreach ($sales as $sale): ?>
                    <tr>
                        <td><?php echo esc_html($sale->order_number); ?></td>
                        <td><?php echo esc_html($sale->customer_name); ?></td>
                        <td><?php echo esc_html($sale->customer_email); ?></td>
                        <td><?php echo esc_html($sale->customer_phone); ?></td>
                        <td><?php echo esc_html($sale->event_name); ?></td>
                        <td><?php echo esc_html($sale->ticket_category_name); ?></td>
                        <td><?php echo esc_html($sale->quantity); ?></td>
                        <td><?php echo $currency_symbol . number_format($sale->total_price, 2); ?></td>
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
                    <td colspan="10"><?php _e('No customers found.', 'dream-ticket'); ?></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

