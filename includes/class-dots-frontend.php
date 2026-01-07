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
        $stripe_publishable_key = isset($settings['stripe_publishable_key']) ? $settings['stripe_publishable_key'] : '';
        
        // Get event data if on ticket form page
        $event_data = array();
        if (isset($GLOBALS['dots_event_data'])) {
            $event_data = $GLOBALS['dots_event_data'];
        }
        
        wp_localize_script('dots-frontend-script', 'dotsFrontend', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('dots_frontend_nonce'),
            'currency_symbol' => $currency_symbol,
            'stripe_publishable_key' => $stripe_publishable_key,
            'event_ticket_price' => isset($event_data['ticket_price']) ? floatval($event_data['ticket_price']) : 0,
            'event_tickets_available' => isset($event_data['tickets_available']) ? intval($event_data['tickets_available']) : 0,
            'event_max_tickets_per_customer' => isset($event_data['max_tickets']) ? intval($event_data['max_tickets']) : 10
        ));
        
        // Load Stripe.js if Stripe is enabled
        if (isset($settings['stripe_enabled']) && $settings['stripe_enabled'] && !empty($stripe_publishable_key)) {
            wp_enqueue_script('stripe-js', 'https://js.stripe.com/v3/', array(), null, true);
        }
    }
    
    /**
     * Register rewrite rules
     */
    public function register_rewrite_rules() {
        add_rewrite_rule('^dream-tickets/event/([0-9]+)/?$', 'index.php?dots_event_id=$matches[1]', 'top');
        add_rewrite_rule('^dream-tickets/order/([^/]+)/?$', 'index.php?dots_order_number=$matches[1]', 'top');
        add_rewrite_rule('^dream-tickets/ticket/([^/]+)/?$', 'index.php?dots_ticket_number=$matches[1]', 'top');
        
        // Flush rewrite rules when needed
        if (!get_option('dots_rewrite_rules_flushed')) {
            flush_rewrite_rules(false);
            update_option('dots_rewrite_rules_flushed', true);
        }
    }
    
    /**
     * Add query vars
     */
    public function add_query_vars($vars) {
        $vars[] = 'dots_event_id';
        $vars[] = 'dots_order_number';
        $vars[] = 'dots_ticket_number';
        return $vars;
    }
    
    /**
     * Template redirect
     */
    public function template_redirect() {
        $event_id = get_query_var('dots_event_id');
        $order_number = get_query_var('dots_order_number');
        $ticket_number = get_query_var('dots_ticket_number');
        
        // Handle PDF download
        if (isset($_GET['download']) && $_GET['download'] === 'pdf' && $ticket_number) {
            $this->generate_ticket_pdf($ticket_number);
            exit;
        }
        
        if ($event_id) {
            $this->display_event_page($event_id);
            exit;
        }
        
        if ($order_number) {
            $this->display_order_confirmation($order_number);
            exit;
        }
        
        if ($ticket_number) {
            $this->display_ticket($ticket_number);
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
        
        // Store event data for localization
        $GLOBALS['dots_event_data'] = array(
            'ticket_price' => isset($event->ticket_price) ? floatval($event->ticket_price) : 0,
            'tickets_available' => isset($event->tickets_available) ? intval($event->tickets_available) : 0,
            'max_tickets' => isset($event->max_tickets) ? intval($event->max_tickets) : 10
        );
        
        $custom_fields = DOTS_Database::get_custom_fields();
        
        // Make sure variables are available in the view
        $event = $event;
        $custom_fields = $custom_fields;
        
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
        
        // Make sure variables are available in the view
        $event = $event;
        $categories = $categories;
        $custom_fields = $custom_fields;
        
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
        if (!$event) {
            wp_die(__('Event not found.', 'dream-ticket'));
        }
        
        $customer = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}dots_customers WHERE id = %d",
            $sale->customer_id
        ));
        
        if (!$customer) {
            wp_die(__('Customer not found.', 'dream-ticket'));
        }
        
        // Make variables available to view
        $sale = $sale;
        $event = $event;
        $customer = $customer;
        
        include DOTS_PLUGIN_DIR . 'frontend/views/order-confirmation.php';
    }
    
    /**
     * Display ticket (when QR code is scanned)
     */
    private function display_ticket($order_number) {
        global $wpdb;
        $table_sales = $wpdb->prefix . 'dots_sales';
        
        // Decode order number in case it's URL encoded
        $order_number = urldecode($order_number);
        
        $sale = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_sales WHERE order_number = %s",
            $order_number
        ));
        
        if (!$sale) {
            // Try without URL decoding
            $sale = $wpdb->get_row($wpdb->prepare(
                "SELECT * FROM $table_sales WHERE order_number = %s",
                sanitize_text_field($order_number)
            ));
        }
        
        if (!$sale) {
            wp_die(__('Ticket not found. Order Number: ', 'dream-ticket') . esc_html($order_number));
        }
        
        $event = DOTS_Database::get_event($sale->event_id);
        if (!$event) {
            wp_die(__('Event not found.', 'dream-ticket'));
        }
        
        $customer = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}dots_customers WHERE id = %d",
            $sale->customer_id
        ));
        
        if (!$customer) {
            wp_die(__('Customer not found.', 'dream-ticket'));
        }
        
        // Make variables available to view
        $sale = $sale;
        $event = $event;
        $customer = $customer;
        $order_number = $order_number;
        
        include DOTS_PLUGIN_DIR . 'frontend/views/ticket-display.php';
    }
    
    /**
     * Generate ticket PDF
     */
    private function generate_ticket_pdf($order_number) {
        global $wpdb;
        $table_sales = $wpdb->prefix . 'dots_sales';
        
        $sale = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_sales WHERE order_number = %s",
            $order_number
        ));
        
        if (!$sale) {
            wp_die(__('Ticket not found.', 'dream-ticket'));
        }
        
        $event = DOTS_Database::get_event($sale->event_id);
        $customer = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}dots_customers WHERE id = %d",
            $sale->customer_id
        ));
        
        $settings = get_option('dots_settings', array());
        $currency_symbol = isset($settings['currency_symbol']) ? $settings['currency_symbol'] : '$';
        
        // Generate HTML for PDF
        ob_start();
        include DOTS_PLUGIN_DIR . 'frontend/views/ticket-pdf.php';
        $html = ob_get_clean();
        
        // Use browser print to PDF (simple solution)
        // For production, consider using a library like TCPDF or mPDF
        header('Content-Type: text/html; charset=utf-8');
        echo $html;
        echo '<script>window.onload = function() { window.print(); }</script>';
        exit;
    }
}

