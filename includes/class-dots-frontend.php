<?php
/**
 * Frontend functionality
 */

if (!defined('ABSPATH')) {
    exit;
}

class DOTS_Frontend {
    
    public function __construct() {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_shortcode('dream_tickets_list', array($this, 'events_list_shortcode'));
        add_shortcode('dream_ticket_form', array($this, 'ticket_form_shortcode'));
        add_shortcode('dream_tickets_test', array($this, 'test_shortcode')); // Test shortcode for debugging
        add_action('init', array($this, 'register_rewrite_rules'));
        add_filter('query_vars', array($this, 'add_query_vars'));
        add_action('template_redirect', array($this, 'template_redirect'));
    }
    
    /**
     * Test shortcode for debugging
     */
    public function test_shortcode($atts) {
        return '<div style="background: #00a32a; color: white; padding: 20px; border-radius: 5px; margin: 20px 0;">
            <strong>✓ Dream Tickets Plugin is Working!</strong><br>
            Shortcodes are registered and functioning correctly.
        </div>';
    }
    
    /**
     * Enqueue frontend scripts and styles
     */
    public function enqueue_scripts() {
        wp_enqueue_style('dots-frontend-style', DOTS_PLUGIN_URL . 'assets/css/frontend.css', array(), DOTS_VERSION);
        wp_enqueue_script('dots-frontend-script', DOTS_PLUGIN_URL . 'assets/js/frontend.js', array('jquery'), DOTS_VERSION, true);
        
        $settings = get_option('dots_settings', array());
        $currency_symbol = isset($settings['currency_symbol']) ? $settings['currency_symbol'] : '$';
        
        wp_localize_script('dots-frontend-script', 'dotsFrontend', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('dots_frontend_nonce'),
            'currency_symbol' => $currency_symbol,
        ));
    }
    
    /**
     * Register rewrite rules
     */
    public function register_rewrite_rules() {
        add_rewrite_rule('^dream-tickets/event/([0-9]+)/?$', 'index.php?dots_event_id=$matches[1]', 'top');
        add_rewrite_rule('^dream-tickets/order/([^/]+)/?$', 'index.php?dots_order_number=$matches[1]', 'top');
    }
    
    /**
     * Add query vars
     */
    public function add_query_vars($vars) {
        $vars[] = 'dots_event_id';
        $vars[] = 'dots_order_number';
        return $vars;
    }
    
    /**
     * Template redirect
     */
    public function template_redirect() {
        $event_id = get_query_var('dots_event_id');
        $order_number = get_query_var('dots_order_number');
        
        if ($event_id) {
            $this->display_event_page($event_id);
            exit;
        }
        
        if ($order_number) {
            $this->display_order_confirmation($order_number);
            exit;
        }
    }
    
    /**
     * Events list shortcode
     */
    public function events_list_shortcode($atts) {
        // Parse attributes
        $atts = shortcode_atts(array(
            'limit' => -1,
            'status' => 'published'
        ), $atts, 'dream_tickets_list');
        
        // Get events
        $events = DOTS_Database::get_events(array(
            'status' => $atts['status'],
            'limit' => intval($atts['limit']),
            'orderby' => 'event_date',
            'order' => 'ASC'
        ));
        
        // Start output buffering
        ob_start();
        
        // Check if view file exists
        $view_file = DOTS_PLUGIN_DIR . 'frontend/views/events-list.php';
        if (file_exists($view_file)) {
            include $view_file;
        } else {
            echo '<p style="color: red;">Error: Events list view file not found.</p>';
        }
        
        return ob_get_clean();
    }
    
    /**
     * Ticket form shortcode
     */
    public function ticket_form_shortcode($atts) {
        // Parse attributes
        $atts = shortcode_atts(array(
            'event_id' => 0
        ), $atts, 'dream_ticket_form');
        
        $event_id = intval($atts['event_id']);
        if (!$event_id) {
            return '<p style="color: red;">' . __('Event ID is required. Use: [dream_ticket_form event_id="1"]', 'dream-ticket') . '</p>';
        }
        
        $event = DOTS_Database::get_event($event_id);
        if (!$event) {
            return '<p style="color: red;">' . sprintf(__('Event with ID %d not found.', 'dream-ticket'), $event_id) . '</p>';
        }
        
        if ($event->status !== 'published') {
            return '<p style="color: orange;">' . __('This event is not published yet.', 'dream-ticket') . '</p>';
        }
        
        $categories = DOTS_Database::get_ticket_categories($event_id);
        $custom_fields = DOTS_Database::get_custom_fields();
        
        // Start output buffering
        ob_start();
        
        // Check if view file exists
        $view_file = DOTS_PLUGIN_DIR . 'frontend/views/ticket-form.php';
        if (file_exists($view_file)) {
            include $view_file;
        } else {
            echo '<p style="color: red;">Error: Ticket form view file not found.</p>';
        }
        
        return ob_get_clean();
    }
    
    /**
     * Display event page
     */
    private function display_event_page($event_id) {
        $event = DOTS_Database::get_event($event_id);
        if (!$event || $event->status !== 'published') {
            wp_die(__('Event not found.', 'dream-ticket'));
        }
        
        $categories = DOTS_Database::get_ticket_categories($event_id);
        $custom_fields = DOTS_Database::get_custom_fields();
        
        include DOTS_PLUGIN_DIR . 'frontend/views/single-event.php';
    }
    
    /**
     * Display order confirmation
     */
    private function display_order_confirmation($order_number) {
        global $wpdb;
        $table_sales = $wpdb->prefix . 'dots_sales';
        
        $sale = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_sales WHERE order_number = %s",
            $order_number
        ));
        
        if (!$sale) {
            wp_die(__('Order not found.', 'dream-ticket'));
        }
        
        $event = DOTS_Database::get_event($sale->event_id);
        $customer = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}dots_customers WHERE id = %d",
            $sale->customer_id
        ));
        
        include DOTS_PLUGIN_DIR . 'frontend/views/order-confirmation.php';
    }
}

