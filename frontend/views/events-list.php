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

<div class="dots-events-list-frontend">
    <div class="dots-events-header">
        <h2><?php esc_html_e('Upcoming Events', 'dream-online-ticket-selling'); ?></h2>
    </div>
    
    <?php if (!empty($events)): ?>
        <div class="dots-events-grid">
            <?php foreach ($events as $event): // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound ?>
                <?php
                // phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
                // Template loop variables are intentionally non-prefixed
                $ticket_price = isset($event->ticket_price) ? floatval($event->ticket_price) : 0;
                $tickets_available = isset($event->tickets_available) ? intval($event->tickets_available) : 0;
                // phpcs:enable
                ?>
                <div class="dots-event-card">
                    <?php if ($event->banner_url): ?>
                        <div class="dots-event-image">
                            <img src="<?php echo esc_url($event->banner_url); ?>" alt="<?php echo esc_attr($event->name); ?>">
                        </div>
                    <?php endif; ?>
                    <div class="dots-event-content">
                        <h3><a href="<?php echo esc_url(home_url('/dream-tickets/event/' . $event->id)); ?>"><?php echo esc_html($event->name); ?></a></h3>
                        <div class="dots-event-meta">
                            <span class="dots-event-date">
                                <span class="dashicons dashicons-calendar-alt"></span>
                                <?php echo esc_html(date_i18n(get_option('date_format'), strtotime($event->event_date))); ?>
                            </span>
                            <span class="dots-event-time">
                                <span class="dashicons dashicons-clock"></span>
                                <?php echo esc_html(date_i18n(get_option('time_format'), strtotime($event->event_time))); ?>
                            </span>
                            <span class="dots-event-location">
                                <span class="dashicons dashicons-location"></span>
                                <?php echo esc_html($event->location); ?>
                            </span>
                        </div>
                        <?php if ($event->description): ?>
                            <p class="dots-event-description"><?php echo esc_html(wp_trim_words(wp_strip_all_tags($event->description), 20)); ?></p>
                        <?php endif; ?>
                        <div class="dots-event-footer">
                            <?php if ($ticket_price > 0): ?>
                                <div class="dots-event-price-info">
                                    <span class="dots-event-price"><?php echo esc_html($currency_symbol . number_format($ticket_price, 2)); ?></span>
                                    <?php if ($tickets_available > 0): ?>
                                        <span class="dots-event-availability"><?php echo esc_html($tickets_available); ?> <?php esc_html_e('available', 'dream-online-ticket-selling'); ?></span>
                                    <?php else: ?>
                                        <span class="dots-event-sold-out"><?php esc_html_e('Sold Out', 'dream-online-ticket-selling'); ?></span>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                            <a href="<?php echo esc_url(home_url('/dream-tickets/event/' . $event->id)); ?>" class="dots-event-button">
                                <?php esc_html_e('View Details', 'dream-online-ticket-selling'); ?>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p><?php esc_html_e('No events found.', 'dream-online-ticket-selling'); ?></p>
    <?php endif; ?>
</div>

