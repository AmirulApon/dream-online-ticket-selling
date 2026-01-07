<?php
if (!defined('ABSPATH')) {
    exit;
}

$event_id = isset($event) && $event ? $event->id : 0;
$event_name = $event ? $event->name : '';
$event_type = isset($event->event_type) ? $event->event_type : '';
$event_description = $event ? $event->description : '';
$event_date = $event ? $event->event_date : date('Y-m-d');
$event_time = $event ? $event->event_time : '19:00';
$event_location = $event ? $event->location : '';
$event_banner = $event ? $event->banner_url : '';
$event_ticket_price = isset($event->ticket_price) ? $event->ticket_price : 0;
$event_max_tickets = isset($event->max_tickets) ? $event->max_tickets : 0;
$event_tickets_available = isset($event->tickets_available) ? $event->tickets_available : 0;
$event_status = $event ? $event->status : 'draft';
?>

<div class="wrap dots-event-edit">
    <h1><?php echo $event_id > 0 ? __('Edit Event', 'dream-ticket') : __('Add New Event', 'dream-ticket'); ?></h1>
    
    <?php if ($event_id > 0): ?>
        <div style="background: #f0f6fc; border-left: 4px solid #2271b1; padding: 12px 15px; margin: 15px 0; border-radius: 4px;">
            <strong><?php _e('Event ID:', 'dream-ticket'); ?></strong>
            <code style="background: #fff; padding: 4px 8px; border-radius: 3px; font-size: 14px; font-weight: 600; color: #2271b1; margin: 0 10px;">
                <?php echo esc_html($event_id); ?>
            </code>
            <button type="button" 
                    class="button button-small dots-copy-event-id-edit" 
                    data-event-id="<?php echo esc_attr($event_id); ?>"
                    style="margin-left: 10px;">
                <span class="dashicons dashicons-admin-page" style="vertical-align: middle;"></span>
                <?php _e('Copy Shortcode', 'dream-ticket'); ?>
            </button>
            <p style="margin: 8px 0 0 0; font-size: 12px; color: #646970;">
                <?php _e('Use this ID in the shortcode:', 'dream-ticket'); ?> 
                <code>[dream_ticket_form event_id="<?php echo esc_html($event_id); ?>"]</code>
            </p>
        </div>
    <?php endif; ?>
    
    <form id="dots-event-form" method="post">
        <div class="dots-form-grid">
            <div class="dots-form-main">
                <div class="dots-form-section">
                    <h2><?php _e('Event Details', 'dream-ticket'); ?></h2>
                    
                    <table class="form-table">
                        <tr>
                            <th><label for="event_name"><?php _e('Event Name', 'dream-ticket'); ?> <span class="required">*</span></label></th>
                            <td><input type="text" id="event_name" name="name" value="<?php echo esc_attr($event_name); ?>" class="regular-text" required></td>
                        </tr>
                        <tr>
                            <th><label for="event_type"><?php _e('Event Type', 'dream-ticket'); ?></label></th>
                            <td>
                                <select id="event_type" name="event_type" class="regular-text">
                                    <option value=""><?php _e('-- Select Event Type (Optional) --', 'dream-ticket'); ?></option>
                                    <option value="Concert" <?php selected($event_type, 'Concert'); ?>><?php _e('Concert', 'dream-ticket'); ?></option>
                                    <option value="Conference" <?php selected($event_type, 'Conference'); ?>><?php _e('Conference', 'dream-ticket'); ?></option>
                                    <option value="Workshop" <?php selected($event_type, 'Workshop'); ?>><?php _e('Workshop', 'dream-ticket'); ?></option>
                                    <option value="Seminar" <?php selected($event_type, 'Seminar'); ?>><?php _e('Seminar', 'dream-ticket'); ?></option>
                                    <option value="Sports" <?php selected($event_type, 'Sports'); ?>><?php _e('Sports', 'dream-ticket'); ?></option>
                                    <option value="Festival" <?php selected($event_type, 'Festival'); ?>><?php _e('Festival', 'dream-ticket'); ?></option>
                                    <option value="Exhibition" <?php selected($event_type, 'Exhibition'); ?>><?php _e('Exhibition', 'dream-ticket'); ?></option>
                                    <option value="Theater" <?php selected($event_type, 'Theater'); ?>><?php _e('Theater', 'dream-ticket'); ?></option>
                                    <option value="Comedy Show" <?php selected($event_type, 'Comedy Show'); ?>><?php _e('Comedy Show', 'dream-ticket'); ?></option>
                                    <option value="Networking" <?php selected($event_type, 'Networking'); ?>><?php _e('Networking', 'dream-ticket'); ?></option>
                                    <option value="Other" <?php selected($event_type, 'Other'); ?>><?php _e('Other', 'dream-ticket'); ?></option>
                                </select>
                                <p class="description"><?php _e('Optional: Categorize your event for better organization.', 'dream-ticket'); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="event_description"><?php _e('Description', 'dream-ticket'); ?></label></th>
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
                            <th><label for="event_date"><?php _e('Event Date', 'dream-ticket'); ?> <span class="required">*</span></label></th>
                            <td><input type="date" id="event_date" name="event_date" value="<?php echo esc_attr($event_date); ?>" required></td>
                        </tr>
                        <tr>
                            <th><label for="event_time"><?php _e('Event Time', 'dream-ticket'); ?> <span class="required">*</span></label></th>
                            <td><input type="time" id="event_time" name="event_time" value="<?php echo esc_attr($event_time); ?>" required></td>
                        </tr>
                        <tr>
                            <th><label for="event_location"><?php _e('Location', 'dream-ticket'); ?> <span class="required">*</span></label></th>
                            <td><input type="text" id="event_location" name="location" value="<?php echo esc_attr($event_location); ?>" class="regular-text" required></td>
                        </tr>
                        <tr>
                            <th><label for="event_banner"><?php _e('Event Banner URL', 'dream-ticket'); ?></label></th>
                            <td>
                                <input type="url" id="event_banner" name="banner_url" value="<?php echo esc_url($event_banner); ?>" class="regular-text">
                                <button type="button" class="button dots-upload-banner"><?php _e('Upload Image', 'dream-ticket'); ?></button>
                                <?php if ($event_banner): ?>
                                    <p><img src="<?php echo esc_url($event_banner); ?>" style="max-width: 300px; margin-top: 10px;"></p>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="event_ticket_price"><?php _e('Ticket Price', 'dream-ticket'); ?> <span class="required">*</span></label></th>
                            <td>
                                <?php
                                $settings = get_option('dots_settings', array());
                                $currency_symbol = isset($settings['currency_symbol']) ? $settings['currency_symbol'] : '$';
                                ?>
                                <span style="font-size: 18px; font-weight: 600; margin-right: 10px;"><?php echo $currency_symbol; ?></span>
                                <input type="number" id="event_ticket_price" name="ticket_price" value="<?php echo esc_attr($event_ticket_price); ?>" step="0.01" min="0" class="regular-text" required>
                                <p class="description"><?php _e('Price per ticket', 'dream-ticket'); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="event_tickets_available"><?php _e('Tickets Available', 'dream-ticket'); ?> <span class="required">*</span></label></th>
                            <td><input type="number" id="event_tickets_available" name="tickets_available" value="<?php echo esc_attr($event_tickets_available); ?>" min="0" class="regular-text" required></td>
                        </tr>
                        <tr>
                            <th><label for="event_max_tickets"><?php _e('Max Tickets per Customer', 'dream-ticket'); ?></label></th>
                            <td><input type="number" id="event_max_tickets" name="max_tickets" value="<?php echo esc_attr($event_max_tickets); ?>" min="1" class="small-text"></td>
                        </tr>
                        <tr>
                            <th><label for="event_status"><?php _e('Status', 'dream-ticket'); ?></label></th>
                            <td>
                                <select id="event_status" name="status">
                                    <option value="draft" <?php selected($event_status, 'draft'); ?>><?php _e('Draft', 'dream-ticket'); ?></option>
                                    <option value="published" <?php selected($event_status, 'published'); ?>><?php _e('Published', 'dream-ticket'); ?></option>
                                </select>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        
        <p class="submit">
            <input type="hidden" name="event_id" value="<?php echo $event_id; ?>">
            <button type="submit" class="button button-primary"><?php _e('Save Event', 'dream-ticket'); ?></button>
            <a href="<?php echo admin_url('admin.php?page=dream-tickets-events'); ?>" class="button"><?php _e('Cancel', 'dream-ticket'); ?></a>
        </p>
    </form>
</div>

<?php if ($event_id > 0): ?>
<script>
jQuery(document).ready(function($) {
    $('.dots-copy-event-id-edit').on('click', function(e) {
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
        var originalText = $btn.html();
        $btn.html('<span class="dashicons dashicons-yes-alt" style="vertical-align: middle; color: #00a32a;"></span> <?php echo esc_js(__('Copied!', 'dream-ticket')); ?>');
        $btn.css('color', '#00a32a');
        
        setTimeout(function() {
            $btn.html(originalText);
            $btn.css('color', '');
        }, 2000);
    });
});
</script>
<?php endif; ?>


