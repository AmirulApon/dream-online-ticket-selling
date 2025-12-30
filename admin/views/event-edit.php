<?php
if (!defined('ABSPATH')) {
    exit;
}

$event_id = isset($event) && $event ? $event->id : 0;
$event_name = $event ? $event->name : '';
$event_description = $event ? $event->description : '';
$event_date = $event ? $event->event_date : date('Y-m-d');
$event_time = $event ? $event->event_time : '19:00';
$event_location = $event ? $event->location : '';
$event_banner = $event ? $event->banner_url : '';
$event_max_tickets = $event ? $event->max_tickets : 0;
$event_status = $event ? $event->status : 'draft';
$categories = isset($categories) ? $categories : array();
?>

<div class="wrap dots-event-edit">
    <h1><?php echo $event_id > 0 ? __('Edit Event', 'dream-ticket') : __('Add New Event', 'dream-ticket'); ?></h1>
    
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
                            <th><label for="event_max_tickets"><?php _e('Maximum Tickets', 'dream-ticket'); ?></label></th>
                            <td><input type="number" id="event_max_tickets" name="max_tickets" value="<?php echo esc_attr($event_max_tickets); ?>" min="0"></td>
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
                
                <div class="dots-form-section">
                    <h2><?php _e('Ticket Categories', 'dream-ticket'); ?></h2>
                    <div id="dots-ticket-categories">
                        <?php if (!empty($categories)): ?>
                            <?php foreach ($categories as $index => $cat): ?>
                                <div class="dots-category-item">
                                    <input type="text" name="categories[<?php echo $index; ?>][name]" placeholder="<?php _e('Category Name', 'dream-ticket'); ?>" value="<?php echo esc_attr($cat->name); ?>" required>
                                    <input type="number" step="0.01" name="categories[<?php echo $index; ?>][price]" placeholder="<?php _e('Price', 'dream-ticket'); ?>" value="<?php echo esc_attr($cat->price); ?>" required>
                                    <input type="number" name="categories[<?php echo $index; ?>][availability]" placeholder="<?php _e('Availability', 'dream-ticket'); ?>" value="<?php echo esc_attr($cat->availability); ?>" required>
                                    <input type="number" name="categories[<?php echo $index; ?>][max_per_customer]" placeholder="<?php _e('Max per Customer', 'dream-ticket'); ?>" value="<?php echo esc_attr($cat->max_per_customer); ?>">
                                    <button type="button" class="button dots-remove-category"><?php _e('Remove', 'dream-ticket'); ?></button>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <button type="button" class="button" id="dots-add-category"><?php _e('Add Category', 'dream-ticket'); ?></button>
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

<script type="text/html" id="dots-category-template">
    <div class="dots-category-item">
        <input type="text" name="categories[{{index}}][name]" placeholder="<?php _e('Category Name', 'dream-ticket'); ?>" required>
        <input type="number" step="0.01" name="categories[{{index}}][price]" placeholder="<?php _e('Price', 'dream-ticket'); ?>" required>
        <input type="number" name="categories[{{index}}][availability]" placeholder="<?php _e('Availability', 'dream-ticket'); ?>" required>
        <input type="number" name="categories[{{index}}][max_per_customer]" placeholder="<?php _e('Max per Customer', 'dream-ticket'); ?>" value="10">
        <button type="button" class="button dots-remove-category"><?php _e('Remove', 'dream-ticket'); ?></button>
    </div>
</script>

