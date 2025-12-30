<?php
if (!defined('ABSPATH')) {
    exit;
}

$settings = get_option('dots_settings', array());
$currency_symbol = isset($settings['currency_symbol']) ? $settings['currency_symbol'] : '$';
?>

<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo esc_html($event->name); ?> - <?php bloginfo('name'); ?></title>
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
    <div class="dots-single-event">
        <?php if ($event->banner_url): ?>
            <div class="dots-event-banner">
                <img src="<?php echo esc_url($event->banner_url); ?>" alt="<?php echo esc_attr($event->name); ?>">
            </div>
        <?php endif; ?>
        
        <div class="dots-event-container">
            <div class="dots-event-main">
                <h1><?php echo esc_html($event->name); ?></h1>
                
                <div class="dots-event-info">
                    <div class="dots-info-item">
                        <span class="dashicons dashicons-calendar-alt"></span>
                        <strong><?php _e('Date:', 'dream-ticket'); ?></strong>
                        <?php echo date_i18n(get_option('date_format'), strtotime($event->event_date)); ?>
                    </div>
                    <div class="dots-info-item">
                        <span class="dashicons dashicons-clock"></span>
                        <strong><?php _e('Time:', 'dream-ticket'); ?></strong>
                        <?php echo date_i18n(get_option('time_format'), strtotime($event->event_time)); ?>
                    </div>
                    <div class="dots-info-item">
                        <span class="dashicons dashicons-location"></span>
                        <strong><?php _e('Location:', 'dream-ticket'); ?></strong>
                        <?php echo esc_html($event->location); ?>
                    </div>
                </div>
                
                <?php if ($event->description): ?>
                    <div class="dots-event-description">
                        <?php echo wp_kses_post($event->description); ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="dots-event-sidebar">
                <div class="dots-ticket-form-wrapper">
                    <h2><?php _e('Purchase Tickets', 'dream-ticket'); ?></h2>
                    <?php include DOTS_PLUGIN_DIR . 'frontend/views/ticket-form.php'; ?>
                </div>
            </div>
        </div>
    </div>
    <?php wp_footer(); ?>
</body>
</html>

