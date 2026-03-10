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

<div class="wrap dots-events-list">
    <h1 class="wp-heading-inline"><?php esc_html_e('Events', 'dream-online-ticket-selling'); ?></h1>
    <a href="<?php echo esc_url(admin_url('admin.php?page=dream-tickets-events&action=add')); ?>" class="page-title-action">
        <?php esc_html_e('Add New', 'dream-online-ticket-selling'); ?>
    </a>
    <hr class="wp-header-end">
    
    <table class="wp-list-table widefat fixed striped table-view-list">
        <thead>
            <tr>
                <th style="width: 80px;"><?php esc_html_e('Event ID', 'dream-online-ticket-selling'); ?></th>
                <th><?php esc_html_e('Event Name', 'dream-online-ticket-selling'); ?></th>
                <th><?php esc_html_e('Event Type', 'dream-online-ticket-selling'); ?></th>
                <th><?php esc_html_e('Date', 'dream-online-ticket-selling'); ?></th>
                <th><?php esc_html_e('Location', 'dream-online-ticket-selling'); ?></th>
                <th style="width: 120px; text-align: center;"><?php esc_html_e('Price', 'dream-online-ticket-selling'); ?></th>
                <th style="width: 130px; text-align: center;"><?php esc_html_e('Available Stock', 'dream-online-ticket-selling'); ?></th>
                <th><?php esc_html_e('Status', 'dream-online-ticket-selling'); ?></th>
                <th><?php esc_html_e('Actions', 'dream-online-ticket-selling'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($events)): ?>
                <?php foreach ($events as $event): // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound ?>
                    <?php
                    // phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
                    // Template loop variables are intentionally non-prefixed
                    $ticket_price = isset($event->ticket_price) ? floatval($event->ticket_price) : 0;
                    $tickets_available = isset($event->tickets_available) ? intval($event->tickets_available) : 0;
                    // phpcs:enable
                    ?>
                    <tr>
                        <td>
                            <div style="display: flex; align-items: center; gap: 5px;">
                                <code style="background: #f0f0f1; padding: 4px 8px; border-radius: 3px; font-size: 12px; font-weight: 600; color: #2271b1;">
                                    <?php echo esc_html($event->id); ?>
                                </code>
                                <button type="button" 
                                        class="button button-small dots-copy-event-id" 
                                        data-event-id="<?php echo esc_attr($event->id); ?>"
                                        title="<?php esc_attr_e('Copy Event ID', 'dream-online-ticket-selling'); ?>"
                                        style="padding: 2px 6px; min-width: auto; height: 24px;">
                                    <span class="dashicons dashicons-admin-page" style="font-size: 14px; width: 14px; height: 14px;"></span>
                                </button>
                            </div>
                        </td>
                        <td><strong><?php echo esc_html($event->name); ?></strong></td>
                        <td>
                            <?php if (isset($event->event_type) && !empty($event->event_type)): ?>
                                <span style="display: inline-block; padding: 4px 10px; background: #e7f3ff; color: #2271b1; border-radius: 12px; font-size: 12px; font-weight: 600;">
                                    <?php echo esc_html($event->event_type); ?>
                                </span>
                            <?php else: ?>
                                <span style="color: #999; font-style: italic;">-</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo esc_html(date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($event->event_date . ' ' . $event->event_time))); ?></td>
                        <td><?php echo esc_html($event->location); ?></td>
                        <td style="text-align: center;">
                            <?php if ($ticket_price > 0): ?>
                                <strong style="color: #2271b1; font-size: 14px;">
                                    <?php echo esc_html($currency_symbol . number_format($ticket_price, 2)); ?>
                                </strong>
                            <?php else: ?>
                                <span style="color: #999; font-style: italic;"><?php esc_html_e('Not set', 'dream-online-ticket-selling'); ?></span>
                            <?php endif; ?>
                        </td>
                        <td style="text-align: center;">
                            <?php if ($tickets_available > 0): ?>
                                <span style="display: inline-flex; align-items: center; gap: 5px; padding: 4px 10px; background: #d1e7dd; color: #0f5132; border-radius: 12px; font-weight: 600; font-size: 13px;">
                                    <span class="dashicons dashicons-tickets-alt" style="font-size: 14px; width: 14px; height: 14px;"></span>
                                    <?php echo esc_html(number_format($tickets_available)); ?>
                                </span>
                            <?php else: ?>
                                <span style="display: inline-flex; align-items: center; gap: 5px; padding: 4px 10px; background: #f8d7da; color: #842029; border-radius: 12px; font-weight: 600; font-size: 13px;">
                                    <span class="dashicons dashicons-dismiss" style="font-size: 14px; width: 14px; height: 14px;"></span>
                                    <?php esc_html_e('Sold Out', 'dream-online-ticket-selling'); ?>
                                </span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="dots-status dots-status-<?php echo esc_attr($event->status); ?>">
                                <?php echo esc_html(ucfirst($event->status)); ?>
                            </span>
                        </td>
                        <td>
                            <a href="<?php echo esc_url(admin_url('admin.php?page=dream-tickets-events&action=edit&id=' . $event->id)); ?>">
                                <?php esc_html_e('Edit', 'dream-online-ticket-selling'); ?>
                            </a> |
                            <a href="#" class="dots-duplicate-event" data-event-id="<?php echo esc_attr($event->id); ?>">
                                <?php esc_html_e('Duplicate', 'dream-online-ticket-selling'); ?>
                            </a> |
                            <a href="#" class="dots-toggle-status" data-event-id="<?php echo esc_attr($event->id); ?>" data-status="<?php echo esc_attr($event->status === 'published' ? 'draft' : 'published'); ?>">
                                <?php echo $event->status === 'published' ? esc_html__('Unpublish', 'dream-online-ticket-selling') : esc_html__('Publish', 'dream-online-ticket-selling'); ?>
                            </a> |
                            <a href="#" class="dots-delete-event" data-event-id="<?php echo esc_attr($event->id); ?>" style="color: #a00;">
                                <?php esc_html_e('Delete', 'dream-online-ticket-selling'); ?>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="9"><?php esc_html_e('No events found.', 'dream-online-ticket-selling'); ?></td>
                </tr>
            <?php endif; ?>
        </tbody>
</div>

