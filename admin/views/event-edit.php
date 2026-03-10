<?php
if (!defined('ABSPATH')) {
    exit;
}

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
// Template variables are intentionally non-prefixed for readability
$event_id = isset($event) && $event ? $event->id : 0;
$event_name = $event ? $event->name : '';
$event_type = isset($event->event_type) ? $event->event_type : '';
$event_description = $event ? $event->description : '';
$event_date = $event ? $event->event_date : gmdate('Y-m-d');
$event_time = $event ? $event->event_time : '19:00';
$event_location = $event ? $event->location : '';
$event_banner = $event ? $event->banner_url : '';
$event_ticket_price = isset($event->ticket_price) ? $event->ticket_price : 0;
$event_max_tickets = isset($event->max_tickets) ? $event->max_tickets : 0;
$event_tickets_available = isset($event->tickets_available) ? $event->tickets_available : 0;
$event_status = $event ? $event->status : 'draft';
// phpcs:enable
?>

<div class="wrap dots-event-edit">
    <h1><?php echo $event_id > 0 ? esc_html__('Edit Event', 'dream-online-ticket-selling') : esc_html__('Add New Event', 'dream-online-ticket-selling'); ?></h1>
    
    <?php if ($event_id > 0): ?>
        <div style="background: #f0f6fc; border-left: 4px solid #2271b1; padding: 12px 15px; margin: 15px 0; border-radius: 4px;">
            <strong><?php esc_html_e('Event ID:', 'dream-online-ticket-selling'); ?></strong>
            <code style="background: #fff; padding: 4px 8px; border-radius: 3px; font-size: 14px; font-weight: 600; color: #2271b1; margin: 0 10px;">
                <?php echo esc_html($event_id); ?>
            </code>
            <button type="button" 
                    class="button button-small dots-copy-event-id-edit" 
                    data-event-id="<?php echo esc_attr($event_id); ?>"
                    style="margin-left: 10px;">
                <span class="dashicons dashicons-admin-page" style="vertical-align: middle;"></span>
                <?php esc_html_e('Copy Shortcode', 'dream-online-ticket-selling'); ?>
            </button>
            <p style="margin: 8px 0 0 0; font-size: 12px; color: #646970;">
                <?php esc_html_e('Use this ID in the shortcode:', 'dream-online-ticket-selling'); ?> 
                <code>[dream_ticket_form event_id="<?php echo esc_html($event_id); ?>"]</code>
            </p>
        </div>
    <?php endif; ?>
    
    <form id="dots-event-form" method="post">
        <div class="dots-form-grid">
            <div class="dots-form-main">
                <div class="dots-form-section">
                    <h2><?php esc_html_e('Event Details', 'dream-online-ticket-selling'); ?></h2>
                    
                    <table class="form-table">
                        <tr>
                            <th><label for="event_name"><?php esc_html_e('Event Name', 'dream-online-ticket-selling'); ?> <span class="required">*</span></label></th>
                            <td><input type="text" id="event_name" name="name" value="<?php echo esc_attr($event_name); ?>" class="regular-text" required></td>
                        </tr>
                        <tr>
                            <th><label for="event_type"><?php esc_html_e('Event Type', 'dream-online-ticket-selling'); ?></label></th>
                            <td>
                                <select id="event_type" name="event_type" class="regular-text">
                                    <option value=""><?php esc_html_e('-- Select Event Type (Optional) --', 'dream-online-ticket-selling'); ?></option>
                                    <option value="Concert" <?php selected($event_type, 'Concert'); ?>><?php esc_html_e('Concert', 'dream-online-ticket-selling'); ?></option>
                                    <option value="Conference" <?php selected($event_type, 'Conference'); ?>><?php esc_html_e('Conference', 'dream-online-ticket-selling'); ?></option>
                                    <option value="Workshop" <?php selected($event_type, 'Workshop'); ?>><?php esc_html_e('Workshop', 'dream-online-ticket-selling'); ?></option>
                                    <option value="Seminar" <?php selected($event_type, 'Seminar'); ?>><?php esc_html_e('Seminar', 'dream-online-ticket-selling'); ?></option>
                                    <option value="Sports" <?php selected($event_type, 'Sports'); ?>><?php esc_html_e('Sports', 'dream-online-ticket-selling'); ?></option>
                                    <option value="Festival" <?php selected($event_type, 'Festival'); ?>><?php esc_html_e('Festival', 'dream-online-ticket-selling'); ?></option>
                                    <option value="Exhibition" <?php selected($event_type, 'Exhibition'); ?>><?php esc_html_e('Exhibition', 'dream-online-ticket-selling'); ?></option>
                                    <option value="Theater" <?php selected($event_type, 'Theater'); ?>><?php esc_html_e('Theater', 'dream-online-ticket-selling'); ?></option>
                                    <option value="Comedy Show" <?php selected($event_type, 'Comedy Show'); ?>><?php esc_html_e('Comedy Show', 'dream-online-ticket-selling'); ?></option>
                                    <option value="Networking" <?php selected($event_type, 'Networking'); ?>><?php esc_html_e('Networking', 'dream-online-ticket-selling'); ?></option>
                                    <option value="Other" <?php selected($event_type, 'Other'); ?>><?php esc_html_e('Other', 'dream-online-ticket-selling'); ?></option>
                                </select>
                                <p class="description"><?php esc_html_e('Optional: Categorize your event for better organization.', 'dream-online-ticket-selling'); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="event_description"><?php esc_html_e('Description', 'dream-online-ticket-selling'); ?></label></th>
                            <td>
                                <?php
                                wp_editor($event_description, 'event_description', array(
                                    'textarea_name' => 'description',
                                    'textarea_rows' => 10,
                                    'media_buttons' => true
                                ));
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="event_date"><?php esc_html_e('Event Date', 'dream-online-ticket-selling'); ?> <span class="required">*</span></label></th>
                            <td><input type="date" id="event_date" name="event_date" value="<?php echo esc_attr($event_date); ?>" required></td>
                        </tr>
                        <tr>
                            <th><label for="event_time"><?php esc_html_e('Event Time', 'dream-online-ticket-selling'); ?> <span class="required">*</span></label></th>
                            <td><input type="time" id="event_time" name="event_time" value="<?php echo esc_attr($event_time); ?>" required></td>
                        </tr>
                        <tr>
                            <th><label for="event_location"><?php esc_html_e('Location', 'dream-online-ticket-selling'); ?> <span class="required">*</span></label></th>
                            <td><input type="text" id="event_location" name="location" value="<?php echo esc_attr($event_location); ?>" class="regular-text" required></td>
                        </tr>
                        <tr>
                            <th><label for="event_banner"><?php esc_html_e('Event Banner URL', 'dream-online-ticket-selling'); ?></label></th>
                            <td>
                                <input type="url" id="event_banner" name="banner_url" value="<?php echo esc_url($event_banner); ?>" class="regular-text">
                                <button type="button" class="button dots-upload-banner"><?php esc_html_e('Upload Image', 'dream-online-ticket-selling'); ?></button>
                                <?php if ($event_banner): ?>
                                    <p><img src="<?php echo esc_url($event_banner); ?>" style="max-width: 300px; margin-top: 10px;"></p>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="event_ticket_price"><?php esc_html_e('Ticket Price', 'dream-online-ticket-selling'); ?> <span class="required">*</span></label></th>
                            <td>
                                <?php
                                // phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
                                // Template variables are intentionally non-prefixed for readability
                                $settings = get_option('dots_settings', array());
                                $currency_symbol = isset($settings['currency_symbol']) ? $settings['currency_symbol'] : '$';
                                // phpcs:enable
                                ?>
                                <span style="font-size: 18px; font-weight: 600; margin-right: 10px;"><?php echo esc_html($currency_symbol); ?></span>
                                <input type="number" id="event_ticket_price" name="ticket_price" value="<?php echo esc_attr($event_ticket_price); ?>" step="0.01" min="0" class="regular-text" required>
                                <p class="description"><?php esc_html_e('Price per ticket', 'dream-online-ticket-selling'); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="event_tickets_available"><?php esc_html_e('Tickets Available', 'dream-online-ticket-selling'); ?> <span class="required">*</span></label></th>
                            <td><input type="number" id="event_tickets_available" name="tickets_available" value="<?php echo esc_attr($event_tickets_available); ?>" min="0" class="regular-text" required></td>
                        </tr>
                        <tr>
                            <th><label for="event_max_tickets"><?php esc_html_e('Max Tickets per Customer', 'dream-online-ticket-selling'); ?></label></th>
                            <td><input type="number" id="event_max_tickets" name="max_tickets" value="<?php echo esc_attr($event_max_tickets); ?>" min="1" class="small-text"></td>
                        </tr>
                        <tr>
                            <th><label for="event_status"><?php esc_html_e('Status', 'dream-online-ticket-selling'); ?></label></th>
                            <td>
                                <select id="event_status" name="status">
                                    <option value="draft" <?php selected($event_status, 'draft'); ?>><?php esc_html_e('Draft', 'dream-online-ticket-selling'); ?></option>
                                    <option value="published" <?php selected($event_status, 'published'); ?>><?php esc_html_e('Published', 'dream-online-ticket-selling'); ?></option>
                                </select>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        
        <p class="submit">
            <input type="hidden" name="event_id" value="<?php echo esc_attr($event_id); ?>">
            <button type="submit" class="button button-primary"><?php esc_html_e('Save Event', 'dream-online-ticket-selling'); ?></button>
            <a href="<?php echo esc_url(admin_url('admin.php?page=dream-tickets-events')); ?>" class="button"><?php esc_html_e('Cancel', 'dream-online-ticket-selling'); ?></a>
        </p>
    </form>
</div>

