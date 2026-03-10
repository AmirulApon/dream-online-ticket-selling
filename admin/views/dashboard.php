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

<div class="wrap dots-dashboard">
    <h1><?php esc_html_e('Dream Tickets Dashboard', 'dream-online-ticket-selling'); ?></h1>
    
    <div class="dots-stats-grid">
        <div class="dots-stat-card">
            <div class="dots-stat-icon">
                <span class="dashicons dashicons-calendar-alt"></span>
            </div>
            <div class="dots-stat-content">
                <h3><?php echo esc_html(number_format($total_events)); ?></h3>
                <p><?php esc_html_e('Upcoming Events', 'dream-online-ticket-selling'); ?></p>
            </div>
        </div>
        
        <div class="dots-stat-card">
            <div class="dots-stat-icon">
                <span class="dashicons dashicons-tickets-alt"></span>
            </div>
            <div class="dots-stat-content">
                <h3><?php echo esc_html(number_format($total_sales)); ?></h3>
                <p><?php esc_html_e('Total Sales', 'dream-online-ticket-selling'); ?></p>
            </div>
        </div>
        
        <div class="dots-stat-card">
            <div class="dots-stat-icon">
                <span class="dashicons dashicons-money-alt"></span>
            </div>
            <div class="dots-stat-content">
                <h3><?php echo esc_html($currency_symbol . number_format($total_revenue, 2)); ?></h3>
                <p><?php esc_html_e('Total Revenue', 'dream-online-ticket-selling'); ?></p>
            </div>
        </div>
    </div>
    
    <div class="dots-dashboard-grid">
        <div class="dots-dashboard-widget">
            <h2><?php esc_html_e('Quick Actions', 'dream-online-ticket-selling'); ?></h2>
            <div class="dots-quick-actions">
                <a href="<?php echo esc_url(admin_url('admin.php?page=dream-tickets-events&action=add')); ?>" class="button button-primary">
                    <?php esc_html_e('Create New Event', 'dream-online-ticket-selling'); ?>
                </a>
                <a href="<?php echo esc_url(admin_url('admin.php?page=dream-tickets-customers')); ?>" class="button">
                    <?php esc_html_e('View Customers', 'dream-online-ticket-selling'); ?>
                </a>
                <a href="<?php echo esc_url(admin_url('admin.php?page=dream-tickets-sales')); ?>" class="button">
                    <?php esc_html_e('View Sales', 'dream-online-ticket-selling'); ?>
                </a>
                <a href="<?php echo esc_url(admin_url('admin.php?page=dream-tickets-settings')); ?>" class="button">
                    <?php esc_html_e('Settings', 'dream-online-ticket-selling'); ?>
                </a>
            </div>
        </div>
        
        <div class="dots-dashboard-widget">
            <h2><?php esc_html_e('Upcoming Events', 'dream-online-ticket-selling'); ?></h2>
            <?php if (!empty($upcoming_events)): ?>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th><?php esc_html_e('Event Name', 'dream-online-ticket-selling'); ?></th>
                            <th><?php esc_html_e('Date', 'dream-online-ticket-selling'); ?></th>
                            <th><?php esc_html_e('Location', 'dream-online-ticket-selling'); ?></th>
                            <th><?php esc_html_e('Actions', 'dream-online-ticket-selling'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($upcoming_events as $event): // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound ?>
                            <tr>
                                <td><?php echo esc_html($event->name); ?></td>
                                <td><?php echo esc_html(date_i18n(get_option('date_format'), strtotime($event->event_date))); ?></td>
                                <td><?php echo esc_html($event->location); ?></td>
                                <td>
                                    <a href="<?php echo esc_url(admin_url('admin.php?page=dream-tickets-events&action=edit&id=' . $event->id)); ?>">
                                        <?php esc_html_e('Edit', 'dream-online-ticket-selling'); ?>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p><?php esc_html_e('No upcoming events.', 'dream-online-ticket-selling'); ?></p>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="dots-dashboard-widget">
        <h2><?php esc_html_e('Recent Sales', 'dream-online-ticket-selling'); ?></h2>
        <?php if (!empty($recent_sales)): ?>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th><?php esc_html_e('Order Number', 'dream-online-ticket-selling'); ?></th>
                        <th><?php esc_html_e('Customer', 'dream-online-ticket-selling'); ?></th>
                        <th><?php esc_html_e('Event', 'dream-online-ticket-selling'); ?></th>
                        <th><?php esc_html_e('Amount', 'dream-online-ticket-selling'); ?></th>
                        <th><?php esc_html_e('Date', 'dream-online-ticket-selling'); ?></th>
                        <th><?php esc_html_e('Status', 'dream-online-ticket-selling'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recent_sales as $sale): // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound ?>
                        <tr>
                            <td><?php echo esc_html($sale->order_number); ?></td>
                            <td><?php echo esc_html($sale->customer_name); ?></td>
                            <td><?php echo esc_html($sale->event_name); ?></td>
                            <td><?php echo esc_html($currency_symbol . number_format($sale->total_price, 2)); ?></td>
                            <td><?php echo esc_html(date_i18n(get_option('date_format'), strtotime($sale->created_at))); ?></td>
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
            <p><?php esc_html_e('No recent sales.', 'dream-online-ticket-selling'); ?></p>
        <?php endif; ?>
    </div>
    
    <div class="dots-dashboard-widget" style="margin-top: 20px;">
        <h2><?php esc_html_e('Shortcodes', 'dream-online-ticket-selling'); ?></h2>
        <p><?php esc_html_e('Use these shortcodes to display events and ticket forms on your pages and posts:', 'dream-online-ticket-selling'); ?></p>
        
        <div class="dots-shortcode-section" style="background: #f9f9f9; padding: 20px; border-radius: 8px; margin-top: 15px;">
            <div class="dots-shortcode-item" style="margin-bottom: 20px;">
                <h3 style="margin-top: 0; margin-bottom: 10px;">
                    <span class="dashicons dashicons-list-view" style="vertical-align: middle;"></span>
                    <?php esc_html_e('Display Events List', 'dream-online-ticket-selling'); ?>
                </h3>
                <p style="margin-bottom: 10px; color: #666;"><?php esc_html_e('Shows a list of all published events:', 'dream-online-ticket-selling'); ?></p>
                <div style="background: #fff; padding: 12px; border: 1px solid #ddd; border-radius: 4px; display: flex; align-items: center; justify-content: space-between;">
                    <code id="shortcode-list" style="font-size: 14px; color: #2271b1; flex: 1; margin: 0;">[dream_tickets_list]</code>
                    <button type="button" class="button button-small dots-copy-shortcode" data-shortcode="[dream_tickets_list]" style="margin-left: 10px;">
                        <span class="dashicons dashicons-admin-page" style="vertical-align: middle;"></span> <?php esc_html_e('Copy', 'dream-online-ticket-selling'); ?>
                    </button>
                </div>
            </div>
            
            <div class="dots-shortcode-item" style="margin-bottom: 20px;">
                <h3 style="margin-top: 0; margin-bottom: 10px;">
                    <span class="dashicons dashicons-tickets-alt" style="vertical-align: middle;"></span>
                    <?php esc_html_e('Display Ticket Purchase Form', 'dream-online-ticket-selling'); ?>
                </h3>
                <p style="margin-bottom: 10px; color: #666;"><?php esc_html_e('Shows the ticket purchase form for a specific event. Replace EVENT_ID with your event ID:', 'dream-online-ticket-selling'); ?></p>
                <div style="background: #fff; padding: 12px; border: 1px solid #ddd; border-radius: 4px; display: flex; align-items: center; justify-content: space-between;">
                    <code id="shortcode-form" style="font-size: 14px; color: #2271b1; flex: 1; margin: 0;">[dream_ticket_form event_id="1"]</code>
                    <button type="button" class="button button-small dots-copy-shortcode" data-shortcode='[dream_ticket_form event_id="1"]' style="margin-left: 10px;">
                        <span class="dashicons dashicons-admin-page" style="vertical-align: middle;"></span> <?php esc_html_e('Copy', 'dream-online-ticket-selling'); ?>
                    </button>
                </div>
                <p style="margin-top: 8px; font-size: 12px; color: #666;">
                    <strong><?php esc_html_e('Tip:', 'dream-online-ticket-selling'); ?></strong> <?php esc_html_e('You can find the Event ID in the Events list. Edit an event to see its ID in the URL.', 'dream-online-ticket-selling'); ?>
                </p>
            </div>
        </div>
        
        <div style="margin-top: 15px; padding: 12px; background: #fff3cd; border-left: 4px solid #ffc107; border-radius: 4px;">
            <p style="margin: 0; color: #856404;">
                <strong><?php esc_html_e('How to use:', 'dream-online-ticket-selling'); ?></strong><br>
                <?php esc_html_e('1. Copy the shortcode you need', 'dream-online-ticket-selling'); ?><br>
                <?php esc_html_e('2. Edit any page or post in WordPress', 'dream-online-ticket-selling'); ?><br>
                <?php esc_html_e('3. Paste the shortcode where you want it to appear', 'dream-online-ticket-selling'); ?><br>
                <?php esc_html_e('4. For ticket forms, replace EVENT_ID with your actual event ID', 'dream-online-ticket-selling'); ?>
            </p>
        </div>
    </div>
</div>

