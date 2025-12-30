<?php
if (!defined('ABSPATH')) {
    exit;
}

$settings = get_option('dots_settings', array());
$currency_symbol = isset($settings['currency_symbol']) ? $settings['currency_symbol'] : '$';
?>

<div class="wrap dots-dashboard">
    <h1><?php _e('Dream Tickets Dashboard', 'dream-ticket'); ?></h1>
    
    <div class="dots-stats-grid">
        <div class="dots-stat-card">
            <div class="dots-stat-icon">
                <span class="dashicons dashicons-calendar-alt"></span>
            </div>
            <div class="dots-stat-content">
                <h3><?php echo number_format($total_events); ?></h3>
                <p><?php _e('Upcoming Events', 'dream-ticket'); ?></p>
            </div>
        </div>
        
        <div class="dots-stat-card">
            <div class="dots-stat-icon">
                <span class="dashicons dashicons-tickets-alt"></span>
            </div>
            <div class="dots-stat-content">
                <h3><?php echo number_format($total_sales); ?></h3>
                <p><?php _e('Total Sales', 'dream-ticket'); ?></p>
            </div>
        </div>
        
        <div class="dots-stat-card">
            <div class="dots-stat-icon">
                <span class="dashicons dashicons-money-alt"></span>
            </div>
            <div class="dots-stat-content">
                <h3><?php echo $currency_symbol . number_format($total_revenue, 2); ?></h3>
                <p><?php _e('Total Revenue', 'dream-ticket'); ?></p>
            </div>
        </div>
    </div>
    
    <div class="dots-dashboard-grid">
        <div class="dots-dashboard-widget">
            <h2><?php _e('Quick Actions', 'dream-ticket'); ?></h2>
            <div class="dots-quick-actions">
                <a href="<?php echo admin_url('admin.php?page=dream-tickets-events&action=add'); ?>" class="button button-primary">
                    <?php _e('Create New Event', 'dream-ticket'); ?>
                </a>
                <a href="<?php echo admin_url('admin.php?page=dream-tickets-customers'); ?>" class="button">
                    <?php _e('View Customers', 'dream-ticket'); ?>
                </a>
                <a href="<?php echo admin_url('admin.php?page=dream-tickets-sales'); ?>" class="button">
                    <?php _e('View Sales', 'dream-ticket'); ?>
                </a>
                <a href="<?php echo admin_url('admin.php?page=dream-tickets-settings'); ?>" class="button">
                    <?php _e('Settings', 'dream-ticket'); ?>
                </a>
            </div>
        </div>
        
        <div class="dots-dashboard-widget">
            <h2><?php _e('Upcoming Events', 'dream-ticket'); ?></h2>
            <?php if (!empty($upcoming_events)): ?>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th><?php _e('Event Name', 'dream-ticket'); ?></th>
                            <th><?php _e('Date', 'dream-ticket'); ?></th>
                            <th><?php _e('Location', 'dream-ticket'); ?></th>
                            <th><?php _e('Actions', 'dream-ticket'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($upcoming_events as $event): ?>
                            <tr>
                                <td><?php echo esc_html($event->name); ?></td>
                                <td><?php echo date_i18n(get_option('date_format'), strtotime($event->event_date)); ?></td>
                                <td><?php echo esc_html($event->location); ?></td>
                                <td>
                                    <a href="<?php echo admin_url('admin.php?page=dream-tickets-events&action=edit&id=' . $event->id); ?>">
                                        <?php _e('Edit', 'dream-ticket'); ?>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p><?php _e('No upcoming events.', 'dream-ticket'); ?></p>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="dots-dashboard-widget">
        <h2><?php _e('Recent Sales', 'dream-ticket'); ?></h2>
        <?php if (!empty($recent_sales)): ?>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th><?php _e('Order Number', 'dream-ticket'); ?></th>
                        <th><?php _e('Customer', 'dream-ticket'); ?></th>
                        <th><?php _e('Event', 'dream-ticket'); ?></th>
                        <th><?php _e('Amount', 'dream-ticket'); ?></th>
                        <th><?php _e('Date', 'dream-ticket'); ?></th>
                        <th><?php _e('Status', 'dream-ticket'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recent_sales as $sale): ?>
                        <tr>
                            <td><?php echo esc_html($sale->order_number); ?></td>
                            <td><?php echo esc_html($sale->customer_name); ?></td>
                            <td><?php echo esc_html($sale->event_name); ?></td>
                            <td><?php echo $currency_symbol . number_format($sale->total_price, 2); ?></td>
                            <td><?php echo date_i18n(get_option('date_format'), strtotime($sale->created_at)); ?></td>
                            <td>
                                <span class="dots-status dots-status-<?php echo esc_attr($sale->payment_status); ?>">
                                    <?php echo esc_html(ucfirst($sale->payment_status)); ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p><?php _e('No recent sales.', 'dream-ticket'); ?></p>
        <?php endif; ?>
    </div>
</div>

