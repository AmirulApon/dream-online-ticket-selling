<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wrap">
    <h1><?php esc_html_e('Test SSLCommerz Payment Gateway', 'dream-online-ticket-selling'); ?></h1>
    
    <?php
    // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- GET parameter for displaying test results
    $test_status = isset($_GET['test']) ? sanitize_text_field(wp_unslash($_GET['test'])) : '';
    ?>
    <?php if (!empty($test_status)): ?>
        <div class="notice notice-<?php echo esc_attr($test_status === 'success' ? 'success' : 'error'); ?>">
            <p>
                <?php if ($test_status === 'success'): ?>
                    <strong><?php esc_html_e('Test Payment Successful!', 'dream-online-ticket-selling'); ?></strong><br>
                    <?php esc_html_e('SSLCommerz is working correctly.', 'dream-online-ticket-selling'); ?>
                <?php else: ?>
                    <strong><?php esc_html_e('Test Payment Failed/Cancelled', 'dream-online-ticket-selling'); ?></strong><br>
                    <?php esc_html_e('Please check your SSLCommerz settings.', 'dream-online-ticket-selling'); ?>
                <?php endif; ?>
            </p>
        </div>
    <?php endif; ?>
    
    <?php if ($test_result): ?>
        <div class="notice notice-<?php echo $test_result['success'] ? 'success' : 'error'; ?>">
            <p>
                <strong><?php echo $test_result['success'] ? esc_html__('Success:', 'dream-online-ticket-selling') : esc_html__('Error:', 'dream-online-ticket-selling'); ?></strong>
                <?php echo esc_html($test_result['message']); ?>
            </p>
            <?php if (isset($test_result['redirect_url'])): ?>
                <p>
                    <a href="<?php echo esc_url($test_result['redirect_url']); ?>" class="button button-primary" target="_blank">
                        <?php esc_html_e('Continue to Payment Gateway', 'dream-online-ticket-selling'); ?>
                    </a>
                </p>
            <?php endif; ?>
            <?php if (isset($test_result['response'])): ?>
                <details>
                    <summary><?php esc_html_e('View Response', 'dream-online-ticket-selling'); ?></summary>
                    <pre><?php echo esc_html($test_result['response']); ?></pre>
                </details>
            <?php endif; ?>
        </div>
    <?php endif; ?>
    
    <div class="card" style="max-width: 800px; margin-top: 20px;">
        <h2><?php esc_html_e('SSLCommerz Configuration', 'dream-online-ticket-selling'); ?></h2>
        <table class="form-table">
            <tr>
                <th><?php esc_html_e('Store ID', 'dream-online-ticket-selling'); ?></th>
                <td>
                    <?php if (empty($store_id)): ?>
                        <span style="color: #d63638;"><?php esc_html_e('Not configured', 'dream-online-ticket-selling'); ?></span>
                    <?php else: ?>
                        <code><?php echo esc_html($store_id); ?></code>
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <th><?php esc_html_e('Store Password', 'dream-online-ticket-selling'); ?></th>
                <td>
                    <?php if (empty($store_password)): ?>
                        <span style="color: #d63638;"><?php esc_html_e('Not configured', 'dream-online-ticket-selling'); ?></span>
                    <?php else: ?>
                        <code>••••••••</code>
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <th><?php esc_html_e('Mode', 'dream-online-ticket-selling'); ?></th>
                <td>
                    <strong><?php echo esc_html(ucfirst($mode)); ?></strong>
                    <?php if ($mode === 'sandbox'): ?>
                        <span class="description"><?php esc_html_e('(Test Mode)', 'dream-online-ticket-selling'); ?></span>
                    <?php endif; ?>
                </td>
            </tr>
        </table>
        
        <?php if (empty($store_id) || empty($store_password)): ?>
            <p style="color: #d63638;">
                <strong><?php esc_html_e('Please configure SSLCommerz settings first:', 'dream-online-ticket-selling'); ?></strong><br>
                <a href="<?php echo esc_url(admin_url('admin.php?page=dream-tickets-settings#payment')); ?>" class="button">
                    <?php esc_html_e('Go to Settings', 'dream-online-ticket-selling'); ?>
                </a>
            </p>
        <?php else: ?>
            <form method="post" action="">
                <?php wp_nonce_field('dots_test_sslcommerz'); ?>
                <h3><?php esc_html_e('Test Payment', 'dream-online-ticket-selling'); ?></h3>
                <table class="form-table">
                    <tr>
                        <th><label for="test_amount"><?php esc_html_e('Test Amount', 'dream-online-ticket-selling'); ?></label></th>
                        <td>
                            <input type="number" id="test_amount" name="test_amount" value="100.00" step="0.01" min="1" class="small-text">
                            <span class="description"><?php esc_html_e('BDT', 'dream-online-ticket-selling'); ?></span>
                        </td>
                    </tr>
                </table>
                <p class="submit">
                    <input type="submit" name="test_sslcommerz" class="button button-primary" value="<?php esc_html_e('Test SSLCommerz Payment', 'dream-online-ticket-selling'); ?>">
                </p>
            </form>
            
            <div class="notice notice-info">
                <p>
                    <strong><?php esc_html_e('Test Credentials (Sandbox):', 'dream-online-ticket-selling'); ?></strong><br>
                    <?php esc_html_e('Store ID: testbox', 'dream-online-ticket-selling'); ?><br>
                    <?php esc_html_e('Store Password: qwerty', 'dream-online-ticket-selling'); ?>
                </p>
                <p>
                    <strong><?php esc_html_e('How to Test:', 'dream-online-ticket-selling'); ?></strong><br>
                    1. <?php esc_html_e('Enter a test amount (e.g., 100.00)', 'dream-online-ticket-selling'); ?><br>
                    2. <?php esc_html_e('Click "Test SSLCommerz Payment"', 'dream-online-ticket-selling'); ?><br>
                    3. <?php esc_html_e('You will be redirected to SSLCommerz payment page', 'dream-online-ticket-selling'); ?><br>
                    4. <?php esc_html_e('Use test card: 4111111111111111 (any future expiry date, any CVV)', 'dream-online-ticket-selling'); ?><br>
                    5. <?php esc_html_e('Complete the payment to verify it works', 'dream-online-ticket-selling'); ?>
                </p>
            </div>
        <?php endif; ?>
    </div>
</div>

