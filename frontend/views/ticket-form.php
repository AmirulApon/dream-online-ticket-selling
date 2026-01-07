<?php
if (!defined('ABSPATH')) {
    exit;
}

$settings = get_option('dots_settings', array());
$currency_symbol = isset($settings['currency_symbol']) ? $settings['currency_symbol'] : '$';
?>

<?php if (!isset($event) || !$event): ?>
    <p><?php _e('Event not found.', 'dream-ticket'); ?></p>
<?php else: 
    // Get event ticket details
    $ticket_price = isset($event->ticket_price) ? floatval($event->ticket_price) : 0;
    $tickets_available = isset($event->tickets_available) ? intval($event->tickets_available) : 0;
    $max_tickets_per_customer = isset($event->max_tickets) ? intval($event->max_tickets) : 10;
    
    // Always show the form, but display warning if price/availability not set
    if ($ticket_price <= 0 || $tickets_available <= 0):
    ?>
        <div class="dots-no-tickets" style="padding: 15px; background: #fff3cd; border: 2px solid #ffc107; border-radius: 8px; margin-bottom: 20px;">
            <p style="margin: 0; color: #856404; font-weight: 600;">
                <span style="font-size: 18px;">⚠️</span> 
                <strong><?php _e('Tickets not configured.', 'dream-ticket'); ?></strong><br>
                <span style="font-size: 13px;"><?php _e('Please set ticket price and availability in Dream Tickets > Events > Edit Event', 'dream-ticket'); ?></span>
            </p>
        </div>
    <?php endif; ?>
    
    <?php if ($ticket_price > 0 && $tickets_available > 0): ?>
<form id="dots-ticket-purchase-form" class="dots-ticket-form" data-ticket-price="<?php echo esc_attr($ticket_price); ?>" data-tickets-available="<?php echo esc_attr($tickets_available); ?>" data-max-tickets="<?php echo esc_attr($max_tickets_per_customer); ?>">
    <input type="hidden" name="event_id" value="<?php echo $event->id; ?>">
    
    <div class="dots-ticket-price-display">
        <div class="dots-price-box">
            <div class="dots-price-label"><?php _e('Ticket Price', 'dream-ticket'); ?></div>
            <div class="dots-price-amount"><?php echo $currency_symbol . number_format($ticket_price, 2); ?></div>
            <div class="dots-price-per"><?php _e('per ticket', 'dream-ticket'); ?></div>
        </div>
        <div class="dots-availability-box-display">
            <span class="dots-availability-icon dashicons dashicons-tickets-alt"></span>
            <span class="dots-availability-text">
                <strong><?php echo $tickets_available; ?></strong> <?php _e('tickets available', 'dream-ticket'); ?>
            </span>
        </div>
    </div>
    
    <div class="dots-form-group">
        <label for="ticket_quantity"><?php _e('Quantity', 'dream-ticket'); ?> <span class="required">*</span></label>
        <input type="number" 
               id="ticket_quantity" 
               name="quantity" 
               min="1" 
               max="<?php echo min($max_tickets_per_customer, $tickets_available); ?>" 
               value="1" 
               required>
        <span class="dots-quantity-info">
            <?php _e('Max', 'dream-ticket'); ?> <?php echo min($max_tickets_per_customer, $tickets_available); ?> <?php _e('per customer', 'dream-ticket'); ?>
        </span>
    </div>
    
    <div class="dots-form-group">
        <label for="payment_method"><?php _e('Payment Method', 'dream-ticket'); ?> <span class="required">*</span></label>
        <select id="payment_method" name="payment_method" required>
            <option value=""><?php _e('-- Select Payment Method --', 'dream-ticket'); ?></option>
            <?php
            $settings = get_option('dots_settings', array());
            $paypal_enabled = isset($settings['paypal_enabled']) && $settings['paypal_enabled'];
            $stripe_enabled = isset($settings['stripe_enabled']) && $settings['stripe_enabled'];
            $sslcommerz_enabled = isset($settings['sslcommerz_enabled']) && $settings['sslcommerz_enabled'];
            $bank_transfer_enabled = isset($settings['bank_transfer_enabled']) && $settings['bank_transfer_enabled'];
            
            // If no payment methods enabled, show all options (for testing)
            if (!$paypal_enabled && !$stripe_enabled && !$sslcommerz_enabled && !$bank_transfer_enabled):
            ?>
                <option value="paypal"><?php _e('PayPal', 'dream-ticket'); ?></option>
                <option value="stripe"><?php _e('Credit/Debit Card (Stripe)', 'dream-ticket'); ?></option>
                <option value="sslcommerz"><?php _e('SSLCommerz', 'dream-ticket'); ?></option>
                <option value="bank_transfer"><?php _e('Bank Transfer', 'dream-ticket'); ?></option>
            <?php else: ?>
                <?php if ($paypal_enabled): ?>
                    <option value="paypal"><?php _e('PayPal', 'dream-ticket'); ?></option>
                <?php endif; ?>
                <?php if ($stripe_enabled): ?>
                    <option value="stripe"><?php _e('Credit/Debit Card (Stripe)', 'dream-ticket'); ?></option>
                <?php endif; ?>
                <?php if ($sslcommerz_enabled): ?>
                    <option value="sslcommerz"><?php _e('SSLCommerz', 'dream-ticket'); ?></option>
                <?php endif; ?>
                <?php if ($bank_transfer_enabled): ?>
                    <option value="bank_transfer"><?php _e('Bank Transfer', 'dream-ticket'); ?></option>
                <?php endif; ?>
            <?php endif; ?>
        </select>
        <?php if (!$paypal_enabled && !$stripe_enabled && !$sslcommerz_enabled && !$bank_transfer_enabled): ?>
            <p class="description" style="color: #856404; margin-top: 5px; font-size: 12px;">
                <?php _e('Note: Configure payment methods in Dream Tickets > Settings > Payment', 'dream-ticket'); ?>
            </p>
        <?php endif; ?>
    </div>
    
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
    
    <div class="dots-form-message" style="display: none;"></div>
</form>
    <?php else: ?>
        <div style="padding: 20px; text-align: center; color: #666; background: #f9f9f9; border-radius: 8px;">
            <p style="margin: 0;"><?php _e('Please configure ticket price and availability to enable purchases.', 'dream-ticket'); ?></p>
        </div>
    <?php endif; ?>
<?php endif; ?>

