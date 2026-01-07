<?php
if (!defined('ABSPATH')) {
    exit;
}

$promo_id = isset($promo) && $promo ? $promo->id : 0;
$code = $promo ? $promo->code : '';
$discount_type = isset($promo->discount_type) ? $promo->discount_type : 'percentage';
$discount_value = isset($promo->discount_value) ? $promo->discount_value : 0;
$min_amount = isset($promo->min_amount) ? $promo->min_amount : 0;
$max_discount = isset($promo->max_discount) ? $promo->max_discount : 0;
$usage_limit = isset($promo->usage_limit) ? $promo->usage_limit : 0;
$start_date = isset($promo->start_date) ? $promo->start_date : '';
$end_date = isset($promo->end_date) ? $promo->end_date : '';
$status = isset($promo->status) ? $promo->status : 'active';

$settings = get_option('dots_settings', array());
$currency_symbol = isset($settings['currency_symbol']) ? $settings['currency_symbol'] : '$';
?>
<div class="wrap dots-promo-code-edit">
    <h1><?php echo $promo_id > 0 ? __('Edit Promo Code', 'dream-ticket') : __('Add New Promo Code', 'dream-ticket'); ?></h1>
    
    <form id="dots-promo-form" method="post">
        <table class="form-table">
            <tr>
                <th><label for="promo_code"><?php _e('Promo Code', 'dream-ticket'); ?> <span class="required">*</span></label></th>
                <td>
                    <input type="text" id="promo_code" name="code" value="<?php echo esc_attr($code); ?>" class="regular-text" required style="text-transform: uppercase;">
                    <p class="description"><?php _e('Enter the promo code (will be converted to uppercase).', 'dream-ticket'); ?></p>
                </td>
            </tr>
            <tr>
                <th><label for="discount_type"><?php _e('Discount Type', 'dream-ticket'); ?> <span class="required">*</span></label></th>
                <td>
                    <select id="discount_type" name="discount_type" required>
                        <option value="percentage" <?php selected($discount_type, 'percentage'); ?>><?php _e('Percentage (%)', 'dream-ticket'); ?></option>
                        <option value="fixed" <?php selected($discount_type, 'fixed'); ?>><?php _e('Fixed Amount', 'dream-ticket'); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="discount_value"><?php _e('Discount Value', 'dream-ticket'); ?> <span class="required">*</span></label></th>
                <td>
                    <input type="number" id="discount_value" name="discount_value" value="<?php echo esc_attr($discount_value); ?>" step="0.01" min="0" class="small-text" required>
                    <span id="discount_type_label"><?php echo $discount_type === 'percentage' ? '%' : $currency_symbol; ?></span>
                    <p class="description">
                        <span id="discount_description">
                            <?php if ($discount_type === 'percentage'): ?>
                                <?php _e('Enter percentage (e.g., 10 for 10% off).', 'dream-ticket'); ?>
                            <?php else: ?>
                                <?php _e('Enter fixed amount to deduct from total.', 'dream-ticket'); ?>
                            <?php endif; ?>
                        </span>
                    </p>
                </td>
            </tr>
            <tr>
                <th><label for="min_amount"><?php _e('Minimum Purchase Amount', 'dream-ticket'); ?></label></th>
                <td>
                    <input type="number" id="min_amount" name="min_amount" value="<?php echo esc_attr($min_amount); ?>" step="0.01" min="0" class="small-text">
                    <span><?php echo $currency_symbol; ?></span>
                    <p class="description"><?php _e('Minimum order amount required to use this code. Leave 0 for no minimum.', 'dream-ticket'); ?></p>
                </td>
            </tr>
            <tr>
                <th><label for="max_discount"><?php _e('Maximum Discount', 'dream-ticket'); ?></label></th>
                <td>
                    <input type="number" id="max_discount" name="max_discount" value="<?php echo esc_attr($max_discount); ?>" step="0.01" min="0" class="small-text">
                    <span><?php echo $currency_symbol; ?></span>
                    <p class="description"><?php _e('Maximum discount amount (for percentage discounts). Leave 0 for no limit.', 'dream-ticket'); ?></p>
                </td>
            </tr>
            <tr>
                <th><label for="usage_limit"><?php _e('Usage Limit', 'dream-ticket'); ?></label></th>
                <td>
                    <input type="number" id="usage_limit" name="usage_limit" value="<?php echo esc_attr($usage_limit); ?>" min="0" class="small-text">
                    <p class="description"><?php _e('Maximum number of times this code can be used. Leave 0 for unlimited.', 'dream-ticket'); ?></p>
                </td>
            </tr>
            <tr>
                <th><label for="start_date"><?php _e('Valid From', 'dream-ticket'); ?></label></th>
                <td>
                    <input type="date" id="start_date" name="start_date" value="<?php echo esc_attr($start_date); ?>">
                    <p class="description"><?php _e('Leave empty for no start date restriction.', 'dream-ticket'); ?></p>
                </td>
            </tr>
            <tr>
                <th><label for="end_date"><?php _e('Valid Until', 'dream-ticket'); ?></label></th>
                <td>
                    <input type="date" id="end_date" name="end_date" value="<?php echo esc_attr($end_date); ?>">
                    <p class="description"><?php _e('Leave empty for no expiration date.', 'dream-ticket'); ?></p>
                </td>
            </tr>
            <tr>
                <th><label for="status"><?php _e('Status', 'dream-ticket'); ?></label></th>
                <td>
                    <select id="status" name="status">
                        <option value="active" <?php selected($status, 'active'); ?>><?php _e('Active', 'dream-ticket'); ?></option>
                        <option value="inactive" <?php selected($status, 'inactive'); ?>><?php _e('Inactive', 'dream-ticket'); ?></option>
                    </select>
                </td>
            </tr>
        </table>
        
        <p class="submit">
            <input type="hidden" name="promo_id" value="<?php echo $promo_id; ?>">
            <button type="submit" class="button button-primary"><?php _e('Save Promo Code', 'dream-ticket'); ?></button>
            <a href="<?php echo admin_url('admin.php?page=dream-tickets-promo-codes'); ?>" class="button"><?php _e('Cancel', 'dream-ticket'); ?></a>
        </p>
    </form>
</div>

<script>
jQuery(document).ready(function($) {
    // Auto-uppercase promo code
    $('#promo_code').on('input', function() {
        $(this).val($(this).val().toUpperCase());
    });
    
    // Update discount value label when type changes
    $('#discount_type').on('change', function() {
        var type = $(this).val();
        var $label = $('#discount_type_label');
        var $description = $('#discount_description');
        
        if (type === 'percentage') {
            $label.text('%');
            $description.text('<?php echo esc_js(__('Enter percentage (e.g., 10 for 10% off).', 'dream-ticket')); ?>');
        } else {
            $label.text('<?php echo esc_js($currency_symbol); ?>');
            $description.text('<?php echo esc_js(__('Enter fixed amount to deduct from total.', 'dream-ticket')); ?>');
        }
    });
    
    // Save promo code
    $('#dots-promo-form').on('submit', function(e) {
        e.preventDefault();
        
        var formData = $(this).serialize();
        formData += '&action=dots_save_promo_code&nonce=' + dotsAdmin.nonce;
        
        $.ajax({
            url: dotsAdmin.ajax_url,
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    alert(dotsAdmin.strings.saved);
                    window.location.href = 'admin.php?page=dream-tickets-promo-codes';
                } else {
                    alert(response.data.message || dotsAdmin.strings.error);
                }
            }
        });
    });
});
</script>

