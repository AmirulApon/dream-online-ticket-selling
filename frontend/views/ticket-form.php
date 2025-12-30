<?php
if (!defined('ABSPATH')) {
    exit;
}

$settings = get_option('dots_settings', array());
$currency_symbol = isset($settings['currency_symbol']) ? $settings['currency_symbol'] : '$';
?>

<?php if (!isset($event) || !$event): ?>
    <p><?php _e('Event not found.', 'dream-ticket'); ?></p>
<?php else: ?>
<form id="dots-ticket-purchase-form" class="dots-ticket-form">
    <input type="hidden" name="event_id" value="<?php echo $event->id; ?>">
    
    <?php if (!empty($categories)): ?>
        <div class="dots-form-group">
            <label for="ticket_category"><?php _e('Select Ticket Type', 'dream-ticket'); ?> <span class="required">*</span></label>
            <select id="ticket_category" name="ticket_category_id" required>
                <option value=""><?php _e('-- Select --', 'dream-ticket'); ?></option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?php echo $cat->id; ?>" 
                            data-price="<?php echo $cat->price; ?>"
                            data-availability="<?php echo $cat->availability; ?>"
                            data-max="<?php echo $cat->max_per_customer; ?>">
                        <?php echo esc_html($cat->name); ?> - <?php echo $currency_symbol . number_format($cat->price, 2); ?>
                        <?php if ($cat->availability > 0): ?>
                            (<?php echo $cat->availability; ?> <?php _e('available', 'dream-ticket'); ?>)
                        <?php else: ?>
                            (<?php _e('Sold Out', 'dream-ticket'); ?>)
                        <?php endif; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="dots-form-group">
            <label for="ticket_quantity"><?php _e('Quantity', 'dream-ticket'); ?> <span class="required">*</span></label>
            <input type="number" id="ticket_quantity" name="quantity" min="1" value="1" required>
            <span class="dots-quantity-info"></span>
        </div>
    <?php else: ?>
        <p><?php _e('No ticket categories available for this event.', 'dream-ticket'); ?></p>
    <?php endif; ?>
    
    <div class="dots-custom-fields">
        <?php if (!empty($custom_fields)): ?>
            <?php foreach ($custom_fields as $field): ?>
                <div class="dots-form-group">
                    <label for="field_<?php echo $field->id; ?>">
                        <?php echo esc_html($field->field_label); ?>
                        <?php if ($field->is_required): ?>
                            <span class="required">*</span>
                        <?php endif; ?>
                    </label>
                    
                    <?php
                    $field_id = 'field_' . $field->id;
                    $field_name = 'customer_data[' . $field->field_name . ']';
                    $required = $field->is_required ? 'required' : '';
                    ?>
                    
                    <?php if ($field->field_type === 'text' || $field->field_type === 'email' || $field->field_type === 'tel'): ?>
                        <input type="<?php echo esc_attr($field->field_type); ?>" 
                               id="<?php echo $field_id; ?>" 
                               name="<?php echo $field_name; ?>" 
                               <?php echo $required; ?>>
                    
                    <?php elseif ($field->field_type === 'textarea'): ?>
                        <textarea id="<?php echo $field_id; ?>" 
                                  name="<?php echo $field_name; ?>" 
                                  rows="3" 
                                  <?php echo $required; ?>></textarea>
                    
                    <?php elseif ($field->field_type === 'select'): ?>
                        <select id="<?php echo $field_id; ?>" name="<?php echo $field_name; ?>" <?php echo $required; ?>>
                            <option value=""><?php _e('-- Select --', 'dream-ticket'); ?></option>
                            <?php
                            $options = explode("\n", $field->field_options);
                            foreach ($options as $option):
                                $option = trim($option);
                                if (!empty($option)):
                            ?>
                                <option value="<?php echo esc_attr($option); ?>"><?php echo esc_html($option); ?></option>
                            <?php
                                endif;
                            endforeach;
                            ?>
                        </select>
                    
                    <?php elseif ($field->field_type === 'checkbox'): ?>
                        <?php
                        $options = explode("\n", $field->field_options);
                        foreach ($options as $option):
                            $option = trim($option);
                            if (!empty($option)):
                        ?>
                            <label>
                                <input type="checkbox" 
                                       name="<?php echo $field_name; ?>[]" 
                                       value="<?php echo esc_attr($option); ?>">
                                <?php echo esc_html($option); ?>
                            </label>
                        <?php
                            endif;
                        endforeach;
                        ?>
                    
                    <?php elseif ($field->field_type === 'date'): ?>
                        <input type="date" 
                               id="<?php echo $field_id; ?>" 
                               name="<?php echo $field_name; ?>" 
                               <?php echo $required; ?>>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    
    <?php if (!empty($categories)): ?>
        <div class="dots-form-group">
            <label for="promo_code"><?php _e('Promo Code (Optional)', 'dream-ticket'); ?></label>
            <input type="text" id="promo_code" name="promo_code">
            <button type="button" class="button dots-apply-promo"><?php _e('Apply', 'dream-ticket'); ?></button>
        </div>
        
        <div class="dots-price-summary">
            <div class="dots-price-row">
                <span><?php _e('Subtotal:', 'dream-ticket'); ?></span>
                <span id="dots-subtotal"><?php echo $currency_symbol; ?>0.00</span>
            </div>
            <div class="dots-price-row dots-discount" style="display: none;">
                <span><?php _e('Discount:', 'dream-ticket'); ?></span>
                <span id="dots-discount">-<?php echo $currency_symbol; ?>0.00</span>
            </div>
            <div class="dots-price-row dots-total">
                <strong><?php _e('Total:', 'dream-ticket'); ?></strong>
                <strong id="dots-total"><?php echo $currency_symbol; ?>0.00</strong>
            </div>
        </div>
        
        <div class="dots-form-group">
            <label>
                <input type="checkbox" name="agree_terms" required>
                <?php _e('I agree to the terms and conditions', 'dream-ticket'); ?>
            </label>
        </div>
        
        <button type="submit" class="dots-submit-button">
            <?php _e('Purchase Tickets', 'dream-ticket'); ?>
        </button>
    <?php endif; ?>
    
    <div class="dots-form-message" style="display: none;"></div>
</form>
<?php endif; ?>

