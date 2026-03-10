<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wrap dots-promo-codes">
    <h1 class="wp-heading-inline"><?php esc_html_e('Promo Codes', 'dream-online-ticket-selling'); ?></h1>
    <a href="<?php echo esc_url(admin_url('admin.php?page=dream-tickets-promo-codes&action=add')); ?>" class="page-title-action">
        <?php esc_html_e('Add New', 'dream-online-ticket-selling'); ?>
    </a>
    <hr class="wp-header-end">
    
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th><?php esc_html_e('Code', 'dream-online-ticket-selling'); ?></th>
                <th><?php esc_html_e('Discount Type', 'dream-online-ticket-selling'); ?></th>
                <th><?php esc_html_e('Discount Value', 'dream-online-ticket-selling'); ?></th>
                <th><?php esc_html_e('Min Amount', 'dream-online-ticket-selling'); ?></th>
                <th><?php esc_html_e('Usage Limit', 'dream-online-ticket-selling'); ?></th>
                <th><?php esc_html_e('Used', 'dream-online-ticket-selling'); ?></th>
                <th><?php esc_html_e('Valid From', 'dream-online-ticket-selling'); ?></th>
                <th><?php esc_html_e('Valid Until', 'dream-online-ticket-selling'); ?></th>
                <th><?php esc_html_e('Status', 'dream-online-ticket-selling'); ?></th>
                <th><?php esc_html_e('Actions', 'dream-online-ticket-selling'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($promo_codes)): ?>
                <?php foreach ($promo_codes as $promo): // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound ?>
                    <tr>
                        <td><strong><?php echo esc_html($promo->code); ?></strong></td>
                        <td><?php echo esc_html(ucfirst($promo->discount_type)); ?></td>
                        <td>
                            <?php if ($promo->discount_type === 'percentage'): ?>
                                <?php echo esc_html($promo->discount_value); ?>%
                            <?php else: ?>
                                <?php 
                                // phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
                                // Template variables are intentionally non-prefixed for readability
                                $settings = get_option('dots_settings', array());
                                $currency_symbol = isset($settings['currency_symbol']) ? $settings['currency_symbol'] : '$';
                                // phpcs:enable
                                echo esc_html($currency_symbol . number_format($promo->discount_value, 2)); 
                                ?>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php 
                            // phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
                            // Template variables are intentionally non-prefixed for readability
                            $settings = get_option('dots_settings', array());
                            $currency_symbol = isset($settings['currency_symbol']) ? $settings['currency_symbol'] : '$';
                            // phpcs:enable
                            echo esc_html($currency_symbol . number_format($promo->min_amount, 2)); 
                            ?>
                        </td>
                        <td><?php echo $promo->usage_limit > 0 ? esc_html($promo->usage_limit) : esc_html__('Unlimited', 'dream-online-ticket-selling'); ?></td>
                        <td><?php echo esc_html($promo->used_count); ?></td>
                        <td><?php echo $promo->start_date ? esc_html(date_i18n(get_option('date_format'), strtotime($promo->start_date))) : '-'; ?></td>
                        <td><?php echo $promo->end_date ? esc_html(date_i18n(get_option('date_format'), strtotime($promo->end_date))) : '-'; ?></td>
                        <td>
                            <span class="dots-status dots-status-<?php echo esc_attr($promo->status); ?>">
                                <?php echo esc_html(ucfirst($promo->status)); ?>
                            </span>
                        </td>
                        <td>
                            <a href="<?php echo esc_url(admin_url('admin.php?page=dream-tickets-promo-codes&action=edit&id=' . $promo->id)); ?>">
                                <?php esc_html_e('Edit', 'dream-online-ticket-selling'); ?>
                            </a> |
                            <a href="#" class="dots-delete-promo" data-promo-id="<?php echo esc_attr($promo->id); ?>" style="color: #a00;">
                                <?php esc_html_e('Delete', 'dream-online-ticket-selling'); ?>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="10"><?php esc_html_e('No promo codes found.', 'dream-online-ticket-selling'); ?></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

