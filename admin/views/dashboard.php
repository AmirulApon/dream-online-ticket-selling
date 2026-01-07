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
    
    <div class="dots-dashboard-widget" style="margin-top: 20px;">
        <h2><?php _e('Shortcodes', 'dream-ticket'); ?></h2>
        <p><?php _e('Use these shortcodes to display events and ticket forms on your pages and posts:', 'dream-ticket'); ?></p>
        
        <div class="dots-shortcode-section" style="background: #f9f9f9; padding: 20px; border-radius: 8px; margin-top: 15px;">
            <div class="dots-shortcode-item" style="margin-bottom: 20px;">
                <h3 style="margin-top: 0; margin-bottom: 10px;">
                    <span class="dashicons dashicons-list-view" style="vertical-align: middle;"></span>
                    <?php _e('Display Events List', 'dream-ticket'); ?>
                </h3>
                <p style="margin-bottom: 10px; color: #666;"><?php _e('Shows a list of all published events:', 'dream-ticket'); ?></p>
                <div style="background: #fff; padding: 12px; border: 1px solid #ddd; border-radius: 4px; display: flex; align-items: center; justify-content: space-between;">
                    <code id="shortcode-list" style="font-size: 14px; color: #2271b1; flex: 1; margin: 0;">[dream_tickets_list]</code>
                    <button type="button" class="button button-small dots-copy-shortcode" data-shortcode="[dream_tickets_list]" style="margin-left: 10px;">
                        <span class="dashicons dashicons-admin-page" style="vertical-align: middle;"></span> <?php _e('Copy', 'dream-ticket'); ?>
                    </button>
                </div>
            </div>
            
            <div class="dots-shortcode-item" style="margin-bottom: 20px;">
                <h3 style="margin-top: 0; margin-bottom: 10px;">
                    <span class="dashicons dashicons-tickets-alt" style="vertical-align: middle;"></span>
                    <?php _e('Display Ticket Purchase Form', 'dream-ticket'); ?>
                </h3>
                <p style="margin-bottom: 10px; color: #666;"><?php _e('Shows the ticket purchase form for a specific event. Replace EVENT_ID with your event ID:', 'dream-ticket'); ?></p>
                <div style="background: #fff; padding: 12px; border: 1px solid #ddd; border-radius: 4px; display: flex; align-items: center; justify-content: space-between;">
                    <code id="shortcode-form" style="font-size: 14px; color: #2271b1; flex: 1; margin: 0;">[dream_ticket_form event_id="1"]</code>
                    <button type="button" class="button button-small dots-copy-shortcode" data-shortcode='[dream_ticket_form event_id="1"]' style="margin-left: 10px;">
                        <span class="dashicons dashicons-admin-page" style="vertical-align: middle;"></span> <?php _e('Copy', 'dream-ticket'); ?>
                    </button>
                </div>
                <p style="margin-top: 8px; font-size: 12px; color: #666;">
                    <strong><?php _e('Tip:', 'dream-ticket'); ?></strong> <?php _e('You can find the Event ID in the Events list. Edit an event to see its ID in the URL.', 'dream-ticket'); ?>
                </p>
            </div>
        </div>
        
        <div style="margin-top: 15px; padding: 12px; background: #fff3cd; border-left: 4px solid #ffc107; border-radius: 4px;">
            <p style="margin: 0; color: #856404;">
                <strong><?php _e('How to use:', 'dream-ticket'); ?></strong><br>
                <?php _e('1. Copy the shortcode you need', 'dream-ticket'); ?><br>
                <?php _e('2. Edit any page or post in WordPress', 'dream-ticket'); ?><br>
                <?php _e('3. Paste the shortcode where you want it to appear', 'dream-ticket'); ?><br>
                <?php _e('4. For ticket forms, replace EVENT_ID with your actual event ID', 'dream-ticket'); ?>
            </p>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    $('.dots-copy-shortcode').on('click', function(e) {
        e.preventDefault();
        var shortcode = $(this).data('shortcode');
        var $temp = $('<textarea>');
        $('body').append($temp);
        $temp.val(shortcode).select();
        document.execCommand('copy');
        $temp.remove();
        
        // Show feedback
        var $btn = $(this);
        var originalText = $btn.html();
        $btn.html('<span class="dashicons dashicons-yes-alt" style="vertical-align: middle; color: #00a32a;"></span> <?php echo esc_js(__('Copied!', 'dream-ticket')); ?>');
        setTimeout(function() {
            $btn.html(originalText);
        }, 2000);
    });
});
</script>

