<?php
if (!defined('ABSPATH')) {
    exit;
}

$settings = get_option('dots_settings', array());
$currency_symbol = isset($settings['currency_symbol']) ? $settings['currency_symbol'] : '$';
?>

<div class="wrap dots-events-list">
    <h1 class="wp-heading-inline"><?php _e('Events', 'dream-ticket'); ?></h1>
    <a href="<?php echo admin_url('admin.php?page=dream-tickets-events&action=add'); ?>" class="page-title-action">
        <?php _e('Add New', 'dream-ticket'); ?>
    </a>
    <hr class="wp-header-end">
    
    <table class="wp-list-table widefat fixed striped table-view-list">
        <thead>
            <tr>
                <th style="width: 80px;"><?php _e('Event ID', 'dream-ticket'); ?></th>
                <th><?php _e('Event Name', 'dream-ticket'); ?></th>
                <th><?php _e('Event Type', 'dream-ticket'); ?></th>
                <th><?php _e('Date', 'dream-ticket'); ?></th>
                <th><?php _e('Location', 'dream-ticket'); ?></th>
                <th style="width: 120px; text-align: center;"><?php _e('Price', 'dream-ticket'); ?></th>
                <th style="width: 130px; text-align: center;"><?php _e('Available Stock', 'dream-ticket'); ?></th>
                <th><?php _e('Status', 'dream-ticket'); ?></th>
                <th><?php _e('Actions', 'dream-ticket'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($events)): ?>
                <?php foreach ($events as $event): ?>
                    <?php
                    $ticket_price = isset($event->ticket_price) ? floatval($event->ticket_price) : 0;
                    $tickets_available = isset($event->tickets_available) ? intval($event->tickets_available) : 0;
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
                                        title="<?php esc_attr_e('Copy Event ID', 'dream-ticket'); ?>"
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
                        <td><?php echo date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($event->event_date . ' ' . $event->event_time)); ?></td>
                        <td><?php echo esc_html($event->location); ?></td>
                        <td style="text-align: center;">
                            <?php if ($ticket_price > 0): ?>
                                <strong style="color: #2271b1; font-size: 14px;">
                                    <?php echo esc_html($currency_symbol . number_format($ticket_price, 2)); ?>
                                </strong>
                            <?php else: ?>
                                <span style="color: #999; font-style: italic;"><?php _e('Not set', 'dream-ticket'); ?></span>
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
                                    <?php _e('Sold Out', 'dream-ticket'); ?>
                                </span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="dots-status dots-status-<?php echo esc_attr($event->status); ?>">
                                <?php echo esc_html(ucfirst($event->status)); ?>
                            </span>
                        </td>
                        <td>
                            <a href="<?php echo admin_url('admin.php?page=dream-tickets-events&action=edit&id=' . $event->id); ?>">
                                <?php _e('Edit', 'dream-ticket'); ?>
                            </a> |
                            <a href="#" class="dots-duplicate-event" data-event-id="<?php echo $event->id; ?>">
                                <?php _e('Duplicate', 'dream-ticket'); ?>
                            </a> |
                            <a href="#" class="dots-toggle-status" data-event-id="<?php echo $event->id; ?>" data-status="<?php echo $event->status === 'published' ? 'draft' : 'published'; ?>">
                                <?php echo $event->status === 'published' ? __('Unpublish', 'dream-ticket') : __('Publish', 'dream-ticket'); ?>
                            </a> |
                            <a href="#" class="dots-delete-event" data-event-id="<?php echo $event->id; ?>" style="color: #a00;">
                                <?php _e('Delete', 'dream-ticket'); ?>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="9"><?php _e('No events found.', 'dream-ticket'); ?></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    
    <script>
    jQuery(document).ready(function($) {
        $('.dots-copy-event-id').on('click', function(e) {
            e.preventDefault();
            var eventId = $(this).data('event-id');
            var shortcode = '[dream_ticket_form event_id="' + eventId + '"]';
            
            // Copy to clipboard
            var $temp = $('<textarea>');
            $('body').append($temp);
            $temp.val(shortcode).select();
            document.execCommand('copy');
            $temp.remove();
            
            // Show feedback
            var $btn = $(this);
            var originalHtml = $btn.html();
            $btn.html('<span class="dashicons dashicons-yes-alt" style="font-size: 14px; width: 14px; height: 14px; color: #00a32a;"></span>');
            $btn.css('color', '#00a32a');
            
            // Show tooltip
            var $tooltip = $('<div style="position: absolute; background: #1d2327; color: #fff; padding: 8px 12px; border-radius: 4px; font-size: 12px; z-index: 10000; margin-top: 30px; white-space: nowrap;"><?php echo esc_js(__('Shortcode copied!', 'dream-ticket')); ?></div>');
            $btn.after($tooltip);
            
            setTimeout(function() {
                $btn.html(originalHtml);
                $btn.css('color', '');
                $tooltip.fadeOut(300, function() {
                    $(this).remove();
                });
            }, 2000);
        });
    });
    </script>
</div>

