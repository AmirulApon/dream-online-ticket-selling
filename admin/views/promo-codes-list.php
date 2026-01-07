<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wrap dots-promo-codes">
    <h1 class="wp-heading-inline"><?php _e('Promo Codes', 'dream-ticket'); ?></h1>
    <a href="<?php echo admin_url('admin.php?page=dream-tickets-promo-codes&action=add'); ?>" class="page-title-action">
        <?php _e('Add New', 'dream-ticket'); ?>
    </a>
    <hr class="wp-header-end">
    
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th><?php _e('Code', 'dream-ticket'); ?></th>
                <th><?php _e('Discount Type', 'dream-ticket'); ?></th>
                <th><?php _e('Discount Value', 'dream-ticket'); ?></th>
                <th><?php _e('Min Amount', 'dream-ticket'); ?></th>
                <th><?php _e('Usage Limit', 'dream-ticket'); ?></th>
                <th><?php _e('Used', 'dream-ticket'); ?></th>
                <th><?php _e('Valid From', 'dream-ticket'); ?></th>
                <th><?php _e('Valid Until', 'dream-ticket'); ?></th>
                <th><?php _e('Status', 'dream-ticket'); ?></th>
                <th><?php _e('Actions', 'dream-ticket'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($promo_codes)): ?>
                <?php foreach ($promo_codes as $promo): ?>
                    <tr>
                        <td><strong><?php echo esc_html($promo->code); ?></strong></td>
                        <td><?php echo esc_html(ucfirst($promo->discount_type)); ?></td>
                        <td>
                            <?php if ($promo->discount_type === 'percentage'): ?>
                                <?php echo esc_html($promo->discount_value); ?>%
                            <?php else: ?>
                                <?php 
                                $settings = get_option('dots_settings', array());
                                $currency_symbol = isset($settings['currency_symbol']) ? $settings['currency_symbol'] : '$';
                                echo $currency_symbol . number_format($promo->discount_value, 2); 
                                ?>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php 
                            $settings = get_option('dots_settings', array());
                            $currency_symbol = isset($settings['currency_symbol']) ? $settings['currency_symbol'] : '$';
                            echo $currency_symbol . number_format($promo->min_amount, 2); 
                            ?>
                        </td>
                        <td><?php echo $promo->usage_limit > 0 ? esc_html($promo->usage_limit) : __('Unlimited', 'dream-ticket'); ?></td>
                        <td><?php echo esc_html($promo->used_count); ?></td>
                        <td><?php echo $promo->start_date ? date_i18n(get_option('date_format'), strtotime($promo->start_date)) : '-'; ?></td>
                        <td><?php echo $promo->end_date ? date_i18n(get_option('date_format'), strtotime($promo->end_date)) : '-'; ?></td>
                        <td>
                            <span class="dots-status dots-status-<?php echo esc_attr($promo->status); ?>">
                                <?php echo esc_html(ucfirst($promo->status)); ?>
                            </span>
                        </td>
                        <td>
                            <a href="<?php echo admin_url('admin.php?page=dream-tickets-promo-codes&action=edit&id=' . $promo->id); ?>">
                                <?php _e('Edit', 'dream-ticket'); ?>
                            </a> |
                            <a href="#" class="dots-delete-promo" data-promo-id="<?php echo $promo->id; ?>" style="color: #a00;">
                                <?php _e('Delete', 'dream-ticket'); ?>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="10"><?php _e('No promo codes found.', 'dream-ticket'); ?></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
jQuery(document).ready(function($) {
    $('.dots-delete-promo').on('click', function(e) {
        e.preventDefault();
        if (!confirm('<?php echo esc_js(__('Are you sure you want to delete this promo code?', 'dream-ticket')); ?>')) {
            return;
        }
        
        var promoId = $(this).data('promo-id');
        $.ajax({
            url: dotsAdmin.ajax_url,
            type: 'POST',
            data: {
                action: 'dots_delete_promo_code',
                nonce: dotsAdmin.nonce,
                promo_id: promoId
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert(response.data.message || dotsAdmin.strings.error);
                }
            }
        });
    });
});
</script>

