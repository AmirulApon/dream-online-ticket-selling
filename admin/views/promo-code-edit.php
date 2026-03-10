<?php
if (!defined('ABSPATH')) {
    exit;
}

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
// Template variables are intentionally non-prefixed for readability
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
// phpcs:enable
?>
<div class="wrap dots-promo-code-edit">
    <h1><?php echo $promo_id > 0 ? esc_html__('Edit Promo Code', 'dream-online-ticket-selling') : esc_html__('Add New Promo Code', 'dream-online-ticket-selling'); ?></h1>
    
    <form id="dots-promo-form" method="post">
        <table class="form-table">
            <tr>
                <th><label for="promo_code"><?php esc_html_e('Promo Code', 'dream-online-ticket-selling'); ?> <span class="required">*</span></label></th>
                <td>
                    <input type="text" id="promo_code" name="code" value="<?php echo esc_attr($code); ?>" class="regular-text" required style="text-transform: uppercase;">
                    <p class="description"><?php esc_html_e('Enter the promo code (will be converted to uppercase).', 'dream-online-ticket-selling'); ?></p>
                </td>
            </tr>
            <tr>
                <th><label for="discount_type"><?php esc_html_e('Discount Type', 'dream-online-ticket-selling'); ?> <span class="required">*</span></label></th>
                <td>
                    <select id="discount_type" name="discount_type" required>
                        <option value="percentage" <?php selected($discount_type, 'percentage'); ?>><?php esc_html_e('Percentage (%)', 'dream-online-ticket-selling'); ?></option>
                        <option value="fixed" <?php selected($discount_type, 'fixed'); ?>><?php esc_html_e('Fixed Amount', 'dream-online-ticket-selling'); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="discount_value"><?php esc_html_e('Discount Value', 'dream-online-ticket-selling'); ?> <span class="required">*</span></label></th>
                <td>
                    <input type="number" id="discount_value" name="discount_value" value="<?php echo esc_attr($discount_value); ?>" step="0.01" min="0" class="small-text" required>
                    <span id="discount_type_label"><?php echo $discount_type === 'percentage' ? '%' : esc_html($currency_symbol); ?></span>
                    <p class="description">
                        <span id="discount_description">
                            <?php if ($discount_type === 'percentage'): ?>
                                <?php esc_html_e('Enter percentage (e.g., 10 for 10% off).', 'dream-online-ticket-selling'); ?>
                            <?php else: ?>
                                <?php esc_html_e('Enter fixed amount to deduct from total.', 'dream-online-ticket-selling'); ?>
                            <?php endif; ?>
                        </span>
                    </p>
                </td>
            </tr>
            <tr>
                <th><label for="min_amount"><?php esc_html_e('Minimum Purchase Amount', 'dream-online-ticket-selling'); ?></label></th>
                <td>
                    <input type="number" id="min_amount" name="min_amount" value="<?php echo esc_attr($min_amount); ?>" step="0.01" min="0" class="small-text">
                    <span><?php echo esc_html($currency_symbol); ?></span>
                    <p class="description"><?php esc_html_e('Minimum order amount required to use this code. Leave 0 for no minimum.', 'dream-online-ticket-selling'); ?></p>
                </td>
            </tr>
            <tr>
                <th><label for="max_discount"><?php esc_html_e('Maximum Discount', 'dream-online-ticket-selling'); ?></label></th>
                <td>
                    <input type="number" id="max_discount" name="max_discount" value="<?php echo esc_attr($max_discount); ?>" step="0.01" min="0" class="small-text">
                    <span><?php echo esc_html($currency_symbol); ?></span>
                    <p class="description"><?php esc_html_e('Maximum discount amount (for percentage discounts). Leave 0 for no limit.', 'dream-online-ticket-selling'); ?></p>
                </td>
            </tr>
            <tr>
                <th><label for="usage_limit"><?php esc_html_e('Usage Limit', 'dream-online-ticket-selling'); ?></label></th>
                <td>
                    <input type="number" id="usage_limit" name="usage_limit" value="<?php echo esc_attr($usage_limit); ?>" min="0" class="small-text">
                    <p class="description"><?php esc_html_e('Maximum number of times this code can be used. Leave 0 for unlimited.', 'dream-online-ticket-selling'); ?></p>
                </td>
            </tr>
            <tr>
                <th><label for="start_date"><?php esc_html_e('Valid From', 'dream-online-ticket-selling'); ?></label></th>
                <td>
                    <input type="date" id="start_date" name="start_date" value="<?php echo esc_attr($start_date); ?>">
                    <p class="description"><?php esc_html_e('Leave empty for no start date restriction.', 'dream-online-ticket-selling'); ?></p>
                </td>
            </tr>
            <tr>
                <th><label for="end_date"><?php esc_html_e('Valid Until', 'dream-online-ticket-selling'); ?></label></th>
                <td>
                    <input type="date" id="end_date" name="end_date" value="<?php echo esc_attr($end_date); ?>">
                    <p class="description"><?php esc_html_e('Leave empty for no expiration date.', 'dream-online-ticket-selling'); ?></p>
                </td>
            </tr>
            <tr>
                <th><label for="status"><?php esc_html_e('Status', 'dream-online-ticket-selling'); ?></label></th>
                <td>
                    <select id="status" name="status">
                        <option value="active" <?php selected($status, 'active'); ?>><?php esc_html_e('Active', 'dream-online-ticket-selling'); ?></option>
                        <option value="inactive" <?php selected($status, 'inactive'); ?>><?php esc_html_e('Inactive', 'dream-online-ticket-selling'); ?></option>
                    </select>
                </td>
            </tr>
        </table>
        
        <p class="submit">
            <input type="hidden" name="promo_id" value="<?php echo esc_attr($promo_id); ?>">
            <button type="submit" class="button button-primary"><?php esc_html_e('Save Promo Code', 'dream-online-ticket-selling'); ?></button>
            <a href="<?php echo esc_url(admin_url('admin.php?page=dream-tickets-promo-codes')); ?>" class="button"><?php esc_html_e('Cancel', 'dream-online-ticket-selling'); ?></a>
        </p>
    </form>
</div>

