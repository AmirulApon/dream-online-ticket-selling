<?php
if (!defined('ABSPATH')) {
    exit;
}

get_header();
?>

<div class="dots-container wrap" style="max-width: 600px; margin: 40px auto; font-family: sans-serif;">
    <div style="background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        <h2 style="margin-top: 0; color: #333;"><?php esc_html_e('Verify Your Ticket Order', 'dream-online-ticket-selling'); ?></h2>
        
        <?php if (!empty($error)) : ?>
            <div style="background: #fee; color: #c00; padding: 10px; border-left: 4px solid #c00; margin-bottom: 20px;">
                <?php echo esc_html($error); ?>
            </div>
        <?php else: ?>
            <p style="color: #666; margin-bottom: 20px;">
                <?php esc_html_e('For your security, please verify the email address used to purchase this ticket.', 'dream-online-ticket-selling'); ?>
            </p>
        <?php endif; ?>

        <form method="POST" action="">
            <?php wp_nonce_field('verify_order_' . $order_number, 'order_nonce'); ?>
            <div style="margin-bottom: 15px;">
                <label for="verify_email" style="display: block; font-weight: bold; margin-bottom: 5px; color: #555;"><?php esc_html_e('Email Address', 'dream-online-ticket-selling'); ?></label>
                <input type="email" id="verify_email" name="verify_email" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
            </div>
            
            <button type="submit" style="background: #0073aa; color: #fff; border: none; padding: 12px 20px; border-radius: 4px; cursor: pointer; font-size: 16px; width: 100%;">
                <?php esc_html_e('View Order', 'dream-online-ticket-selling'); ?>
            </button>
        </form>
    </div>
</div>

<?php 
get_footer(); 
?>
