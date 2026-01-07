<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wrap">
    <h1><?php _e('Test SSLCommerz Payment Gateway', 'dream-ticket'); ?></h1>
    
    <?php if (isset($_GET['test'])): ?>
        <div class="notice notice-<?php echo $_GET['test'] === 'success' ? 'success' : 'error'; ?>">
            <p>
                <?php if ($_GET['test'] === 'success'): ?>
                    <strong><?php _e('Test Payment Successful!', 'dream-ticket'); ?></strong><br>
                    <?php _e('SSLCommerz is working correctly.', 'dream-ticket'); ?>
                <?php else: ?>
                    <strong><?php _e('Test Payment Failed/Cancelled', 'dream-ticket'); ?></strong><br>
                    <?php _e('Please check your SSLCommerz settings.', 'dream-ticket'); ?>
                <?php endif; ?>
            </p>
        </div>
    <?php endif; ?>
    
    <?php if ($test_result): ?>
        <div class="notice notice-<?php echo $test_result['success'] ? 'success' : 'error'; ?>">
            <p>
                <strong><?php echo $test_result['success'] ? __('Success:', 'dream-ticket') : __('Error:', 'dream-ticket'); ?></strong>
                <?php echo esc_html($test_result['message']); ?>
            </p>
            <?php if (isset($test_result['redirect_url'])): ?>
                <p>
                    <a href="<?php echo esc_url($test_result['redirect_url']); ?>" class="button button-primary" target="_blank">
                        <?php _e('Continue to Payment Gateway', 'dream-ticket'); ?>
                    </a>
                </p>
            <?php endif; ?>
            <?php if (isset($test_result['response'])): ?>
                <details>
                    <summary><?php _e('View Response', 'dream-ticket'); ?></summary>
                    <pre><?php echo esc_html($test_result['response']); ?></pre>
                </details>
            <?php endif; ?>
        </div>
    <?php endif; ?>
    
    <div class="card" style="max-width: 800px; margin-top: 20px;">
        <h2><?php _e('SSLCommerz Configuration', 'dream-ticket'); ?></h2>
        <table class="form-table">
            <tr>
                <th><?php _e('Store ID', 'dream-ticket'); ?></th>
                <td>
                    <?php if (empty($store_id)): ?>
                        <span style="color: #d63638;"><?php _e('Not configured', 'dream-ticket'); ?></span>
                    <?php else: ?>
                        <code><?php echo esc_html($store_id); ?></code>
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <th><?php _e('Store Password', 'dream-ticket'); ?></th>
                <td>
                    <?php if (empty($store_password)): ?>
                        <span style="color: #d63638;"><?php _e('Not configured', 'dream-ticket'); ?></span>
                    <?php else: ?>
                        <code>••••••••</code>
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <th><?php _e('Mode', 'dream-ticket'); ?></th>
                <td>
                    <strong><?php echo esc_html(ucfirst($mode)); ?></strong>
                    <?php if ($mode === 'sandbox'): ?>
                        <span class="description"><?php _e('(Test Mode)', 'dream-ticket'); ?></span>
                    <?php endif; ?>
                </td>
            </tr>
        </table>
        
        <?php if (empty($store_id) || empty($store_password)): ?>
            <p style="color: #d63638;">
                <strong><?php _e('Please configure SSLCommerz settings first:', 'dream-ticket'); ?></strong><br>
                <a href="<?php echo admin_url('admin.php?page=dream-tickets-settings#payment'); ?>" class="button">
                    <?php _e('Go to Settings', 'dream-ticket'); ?>
                </a>
            </p>
        <?php else: ?>
            <form method="post" action="">
                <?php wp_nonce_field('dots_test_sslcommerz'); ?>
                <h3><?php _e('Test Payment', 'dream-ticket'); ?></h3>
                <table class="form-table">
                    <tr>
                        <th><label for="test_amount"><?php _e('Test Amount', 'dream-ticket'); ?></label></th>
                        <td>
                            <input type="number" id="test_amount" name="test_amount" value="100.00" step="0.01" min="1" class="small-text">
                            <span class="description"><?php _e('BDT', 'dream-ticket'); ?></span>
                        </td>
                    </tr>
                </table>
                <p class="submit">
                    <input type="submit" name="test_sslcommerz" class="button button-primary" value="<?php _e('Test SSLCommerz Payment', 'dream-ticket'); ?>">
                </p>
            </form>
            
            <div class="notice notice-info">
                <p>
                    <strong><?php _e('Test Credentials (Sandbox):', 'dream-ticket'); ?></strong><br>
                    <?php _e('Store ID: testbox', 'dream-ticket'); ?><br>
                    <?php _e('Store Password: qwerty', 'dream-ticket'); ?>
                </p>
                <p>
                    <strong><?php _e('How to Test:', 'dream-ticket'); ?></strong><br>
                    1. <?php _e('Enter a test amount (e.g., 100.00)', 'dream-ticket'); ?><br>
                    2. <?php _e('Click "Test SSLCommerz Payment"', 'dream-ticket'); ?><br>
                    3. <?php _e('You will be redirected to SSLCommerz payment page', 'dream-ticket'); ?><br>
                    4. <?php _e('Use test card: 4111111111111111 (any future expiry date, any CVV)', 'dream-ticket'); ?><br>
                    5. <?php _e('Complete the payment to verify it works', 'dream-ticket'); ?>
                </p>
            </div>
        <?php endif; ?>
    </div>
</div>

