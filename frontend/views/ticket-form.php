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

<?php if (!isset($event) || !$event): ?>
    <p><?php esc_html_e('Event not found.', 'dream-online-ticket-selling'); ?></p>
<?php else: 
    // phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
    // Template variables are intentionally non-prefixed for readability
    // Get event ticket details
    $ticket_price = isset($event->ticket_price) ? floatval($event->ticket_price) : 0;
    $tickets_available = isset($event->tickets_available) ? intval($event->tickets_available) : 0;
    $max_tickets_per_customer = isset($event->max_tickets) ? intval($event->max_tickets) : 10;
    // phpcs:enable
    
    // Always show the form, but display warning if price/availability not set
    if ($ticket_price <= 0 || $tickets_available <= 0):
    ?>
        <div class="dots-no-tickets" style="padding: 15px; background: #fff3cd; border: 2px solid #ffc107; border-radius: 8px; margin-bottom: 20px;">
            <p style="margin: 0; color: #856404; font-weight: 600;">
                <span style="font-size: 18px;">⚠️</span> 
                <strong><?php esc_html_e('Tickets not configured.', 'dream-online-ticket-selling'); ?></strong><br>
                <span style="font-size: 13px;"><?php esc_html_e('Please set ticket price and availability in Dream Tickets > Events > Edit Event', 'dream-online-ticket-selling'); ?></span>
            </p>
        </div>
    <?php endif; ?>
    
    <?php if ($ticket_price > 0 && $tickets_available > 0): ?>
<form id="dots-ticket-purchase-form" class="dots-ticket-form" data-ticket-price="<?php echo esc_attr($ticket_price); ?>" data-tickets-available="<?php echo esc_attr($tickets_available); ?>" data-max-tickets="<?php echo esc_attr($max_tickets_per_customer); ?>">
    <input type="hidden" name="event_id" value="<?php echo esc_attr($event->id); ?>">
    
    <div class="dots-ticket-price-display">
        <div class="dots-price-box">
            <div class="dots-price-label"><?php esc_html_e('Ticket Price', 'dream-online-ticket-selling'); ?></div>
            <div class="dots-price-amount"><?php echo esc_html($currency_symbol . number_format($ticket_price, 2)); ?></div>
            <div class="dots-price-per"><?php esc_html_e('per ticket', 'dream-online-ticket-selling'); ?></div>
        </div>
        <div class="dots-availability-box-display">
            <span class="dots-availability-icon dashicons dashicons-tickets-alt"></span>
            <span class="dots-availability-text">
                <strong><?php echo esc_html($tickets_available); ?></strong> <?php esc_html_e('tickets available', 'dream-online-ticket-selling'); ?>
            </span>
        </div>
    </div>
    
    <div class="dots-form-group">
        <label for="ticket_quantity"><?php esc_html_e('Quantity', 'dream-online-ticket-selling'); ?> <span class="required">*</span></label>
        <input type="number" 
               id="ticket_quantity" 
               name="quantity" 
               min="1" 
               max="<?php echo esc_attr(min($max_tickets_per_customer, $tickets_available)); ?>" 
               value="1" 
               required>
        <span class="dots-quantity-info">
            <?php esc_html_e('Max', 'dream-online-ticket-selling'); ?> <?php echo esc_html(min($max_tickets_per_customer, $tickets_available)); ?> <?php esc_html_e('per customer', 'dream-online-ticket-selling'); ?>
        </span>
    </div>
    
    <div class="dots-form-group">
        <label for="payment_method"><?php esc_html_e('Payment Method', 'dream-online-ticket-selling'); ?> <span class="required">*</span></label>
        <select id="payment_method" name="payment_method" required>
            <option value=""><?php esc_html_e('-- Select Payment Method --', 'dream-online-ticket-selling'); ?></option>
            <?php
            // phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
            // Template variables are intentionally non-prefixed for readability
            $settings = get_option('dots_settings', array());
            $paypal_enabled = isset($settings['paypal_enabled']) && $settings['paypal_enabled'];
            $stripe_enabled = isset($settings['stripe_enabled']) && $settings['stripe_enabled'];
            $sslcommerz_enabled = isset($settings['sslcommerz_enabled']) && $settings['sslcommerz_enabled'];
            $bank_transfer_enabled = isset($settings['bank_transfer_enabled']) && $settings['bank_transfer_enabled'];
            // phpcs:enable
            
            // If no payment methods enabled, show all options (for testing)
            if (!$paypal_enabled && !$stripe_enabled && !$sslcommerz_enabled && !$bank_transfer_enabled):
            ?>
                <option value="paypal"><?php esc_html_e('PayPal', 'dream-online-ticket-selling'); ?></option>
                <option value="stripe"><?php esc_html_e('Credit/Debit Card (Stripe)', 'dream-online-ticket-selling'); ?></option>
                <option value="sslcommerz"><?php esc_html_e('SSLCommerz', 'dream-online-ticket-selling'); ?></option>
                <option value="bank_transfer"><?php esc_html_e('Bank Transfer', 'dream-online-ticket-selling'); ?></option>
            <?php else: ?>
                <?php if ($paypal_enabled): ?>
                    <option value="paypal"><?php esc_html_e('PayPal', 'dream-online-ticket-selling'); ?></option>
                <?php endif; ?>
                <?php if ($stripe_enabled): ?>
                    <option value="stripe"><?php esc_html_e('Credit/Debit Card (Stripe)', 'dream-online-ticket-selling'); ?></option>
                <?php endif; ?>
                <?php if ($sslcommerz_enabled): ?>
                    <option value="sslcommerz"><?php esc_html_e('SSLCommerz', 'dream-online-ticket-selling'); ?></option>
                <?php endif; ?>
                <?php if ($bank_transfer_enabled): ?>
                    <option value="bank_transfer"><?php esc_html_e('Bank Transfer', 'dream-online-ticket-selling'); ?></option>
                <?php endif; ?>
            <?php endif; ?>
        </select>
        <?php if (!$paypal_enabled && !$stripe_enabled && !$sslcommerz_enabled && !$bank_transfer_enabled): ?>
            <p class="description" style="color: #856404; margin-top: 5px; font-size: 12px;">
                <?php esc_html_e('Note: Configure payment methods in Dream Tickets > Settings > Payment', 'dream-online-ticket-selling'); ?>
            </p>
        <?php endif; ?>
    </div>
    
    <div id="dots-stripe-element-container" class="dots-form-group" style="display: none;">
        <label for="card-element"><?php esc_html_e('Credit or debit card', 'dream-online-ticket-selling'); ?> <span class="required">*</span></label>
        <div id="card-element" style="padding: 12px; border: 1px solid #ddd; border-radius: 4px; background: #fff;">
            <!-- A Stripe Element will be inserted here. -->
        </div>
        <div id="card-errors" role="alert" style="color: #dc3232; margin-top: 5px; font-size: 13px;"></div>
    </div>
    
    <div class="dots-custom-fields">
        <?php if (!empty($custom_fields)): ?>
            <?php foreach ($custom_fields as $field): // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound ?>
                <div class="dots-form-group">
                    <label for="field_<?php echo esc_attr($field->id); ?>">
                        <?php echo esc_html($field->field_label); ?>
                        <?php if ($field->is_required): ?>
                            <span class="required">*</span>
                        <?php endif; ?>
                    </label>
                    
                    <?php
                    // phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
                    // Template loop variables are intentionally non-prefixed
                    $field_id = 'field_' . absint($field->id);
                    $field_name_raw = $field->field_name;
                    $required_attr = $field->is_required ? ' required' : '';
                    // phpcs:enable
                    ?>
                    
                    <?php if ($field->field_type === 'text' || $field->field_type === 'email' || $field->field_type === 'tel'): ?>
                        <input type="<?php echo esc_attr($field->field_type); ?>" 
                               id="<?php echo esc_attr($field_id); ?>" 
                               name="customer_data[<?php echo esc_attr($field_name_raw); ?>]" 
                               <?php echo $required_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Safe string: either " required" or empty ?>>
                    
                    <?php elseif ($field->field_type === 'textarea'): ?>
                        <textarea id="<?php echo esc_attr($field_id); ?>" 
                                  name="customer_data[<?php echo esc_attr($field_name_raw); ?>]" 
                                  rows="3" 
                                  <?php echo $required_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Safe string: either " required" or empty ?>></textarea>
                    
                    <?php elseif ($field->field_type === 'select'): ?>
                        <select id="<?php echo esc_attr($field_id); ?>" name="customer_data[<?php echo esc_attr($field_name_raw); ?>]" <?php echo $required_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Safe string: either " required" or empty ?>>
                            <option value=""><?php esc_html_e('-- Select --', 'dream-online-ticket-selling'); ?></option>
                            <?php
                            // phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
                            // Template loop variables are intentionally non-prefixed
                            $options = explode("\n", $field->field_options);
                            foreach ($options as $option):
                                $option = trim($option);
                                if (!empty($option)):
                            ?>
                                <option value="<?php echo esc_attr($option); ?>"><?php echo esc_html($option); ?></option>
                            <?php
                                endif;
                            endforeach;
                            // phpcs:enable
                            ?>
                        </select>
                    
                    <?php elseif ($field->field_type === 'checkbox'): ?>
                        <?php
                        // phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
                        // Template loop variables are intentionally non-prefixed
                        $options = explode("\n", $field->field_options);
                        foreach ($options as $option):
                            $option = trim($option);
                            if (!empty($option)):
                        ?>
                            <label>
                                <input type="checkbox" 
                                       name="customer_data[<?php echo esc_attr($field_name_raw); ?>][]" 
                                       value="<?php echo esc_attr($option); ?>">
                                <?php echo esc_html($option); ?>
                            </label>
                        <?php
                            endif;
                        endforeach;
                        // phpcs:enable
                        ?>
                    
                    <?php elseif ($field->field_type === 'date'): ?>
                        <input type="date" 
                               id="<?php echo esc_attr($field_id); ?>" 
                               name="customer_data[<?php echo esc_attr($field_name_raw); ?>]" 
                               <?php echo $required_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Safe string: either " required" or empty ?>>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    
    <div class="dots-form-group">
        <label for="promo_code"><?php esc_html_e('Promo Code (Optional)', 'dream-online-ticket-selling'); ?></label>
        <input type="text" id="promo_code" name="promo_code">
        <button type="button" class="button dots-apply-promo"><?php esc_html_e('Apply', 'dream-online-ticket-selling'); ?></button>
    </div>
    
    <div class="dots-price-summary">
        <div class="dots-price-row">
            <span><?php esc_html_e('Subtotal:', 'dream-online-ticket-selling'); ?></span>
            <span id="dots-subtotal"><?php echo esc_html($currency_symbol); ?>0.00</span>
        </div>
        <div class="dots-price-row dots-discount" style="display: none;">
            <span><?php esc_html_e('Discount:', 'dream-online-ticket-selling'); ?></span>
            <span id="dots-discount">-<?php echo esc_html($currency_symbol); ?>0.00</span>
        </div>
        <div class="dots-price-row dots-total">
            <strong><?php esc_html_e('Total:', 'dream-online-ticket-selling'); ?></strong>
            <strong id="dots-total"><?php echo esc_html($currency_symbol); ?>0.00</strong>
        </div>
    </div>
    
    <div class="dots-form-group">
        <label>
            <input type="checkbox" name="agree_terms" required>
            <?php esc_html_e('I agree to the terms and conditions', 'dream-online-ticket-selling'); ?>
        </label>
    </div>
    
    <button type="submit" class="dots-submit-button">
        <?php esc_html_e('Purchase Tickets', 'dream-online-ticket-selling'); ?>
    </button>
    
    <div class="dots-form-message" style="display: none;"></div>
</form>
    <?php else: ?>
        <div style="padding: 20px; text-align: center; color: #666; background: #f9f9f9; border-radius: 8px;">
            <p style="margin: 0;"><?php esc_html_e('Please configure ticket price and availability to enable purchases.', 'dream-online-ticket-selling'); ?></p>
        </div>
    <?php endif; ?>
<?php endif; ?>

