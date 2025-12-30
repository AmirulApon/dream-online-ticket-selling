<?php
if (!defined('ABSPATH')) {
    exit;
}
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
                <th><?php _e('Event Name', 'dream-ticket'); ?></th>
                <th><?php _e('Date', 'dream-ticket'); ?></th>
                <th><?php _e('Location', 'dream-ticket'); ?></th>
                <th><?php _e('Status', 'dream-ticket'); ?></th>
                <th><?php _e('Actions', 'dream-ticket'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($events)): ?>
                <?php foreach ($events as $event): ?>
                    <tr>
                        <td><strong><?php echo esc_html($event->name); ?></strong></td>
                        <td><?php echo date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($event->event_date . ' ' . $event->event_time)); ?></td>
                        <td><?php echo esc_html($event->location); ?></td>
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
                    <td colspan="5"><?php _e('No events found.', 'dream-ticket'); ?></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

