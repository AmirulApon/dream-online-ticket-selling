<?php
/**
 * Admin functionality
 */

if (!defined('ABSPATH')) {
    exit;
}

class DOTS_Admin {
    
    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        add_action('admin_init', array($this, 'register_settings'));
    }
    
    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_menu_page(
            __('Dream Tickets', 'dream-ticket'),
            __('Dream Tickets', 'dream-ticket'),
            'manage_options',
            'dream-tickets',
            array($this, 'dashboard_page'),
            'dashicons-tickets-alt',
            30
        );
        
        add_submenu_page(
            'dream-tickets',
            __('Dashboard', 'dream-ticket'),
            __('Dashboard', 'dream-ticket'),
            'manage_options',
            'dream-tickets',
            array($this, 'dashboard_page')
        );
        
        add_submenu_page(
            'dream-tickets',
            __('Events', 'dream-ticket'),
            __('Events', 'dream-ticket'),
            'manage_options',
            'dream-tickets-events',
            array($this, 'events_page')
        );
        
        add_submenu_page(
            'dream-tickets',
            __('Ticket Forms', 'dream-ticket'),
            __('Ticket Forms', 'dream-ticket'),
            'manage_options',
            'dream-tickets-forms',
            array($this, 'forms_page')
        );
        
        add_submenu_page(
            'dream-tickets',
            __('Customers', 'dream-ticket'),
            __('Customers', 'dream-ticket'),
            'manage_options',
            'dream-tickets-customers',
            array($this, 'customers_page')
        );
        
        add_submenu_page(
            'dream-tickets',
            __('Sales', 'dream-ticket'),
            __('Sales', 'dream-ticket'),
            'manage_options',
            'dream-tickets-sales',
            array($this, 'sales_page')
        );
        
        add_submenu_page(
            'dream-tickets',
            __('Promo Codes', 'dream-ticket'),
            __('Promo Codes', 'dream-ticket'),
            'manage_options',
            'dream-tickets-promo-codes',
            array($this, 'promo_codes_page')
        );
        
        add_submenu_page(
            'dream-tickets',
            __('Settings', 'dream-ticket'),
            __('Settings', 'dream-ticket'),
            'manage_options',
            'dream-tickets-settings',
            array($this, 'settings_page')
        );
        
        add_submenu_page(
            'dream-tickets',
            __('Test SSLCommerz', 'dream-ticket'),
            __('Test SSLCommerz', 'dream-ticket'),
            'manage_options',
            'dream-tickets-test-sslcommerz',
            array($this, 'test_sslcommerz_page')
        );
    }
    
    /**
     * Enqueue admin scripts and styles
     */
    public function enqueue_admin_scripts($hook) {
        if (strpos($hook, 'dream-tickets') === false) {
            return;
        }
        
        wp_enqueue_style('dots-admin-style', DOTS_PLUGIN_URL . 'assets/css/admin.css', array(), DOTS_VERSION);
        wp_enqueue_script('dots-admin-script', DOTS_PLUGIN_URL . 'assets/js/admin.js', array('jquery', 'jquery-ui-sortable'), DOTS_VERSION, true);
        
        wp_localize_script('dots-admin-script', 'dotsAdmin', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('dots_admin_nonce'),
            'strings' => array(
                'confirm_delete' => __('Are you sure you want to delete this?', 'dream-ticket'),
                'saving' => __('Saving...', 'dream-ticket'),
                'saved' => __('Saved!', 'dream-ticket'),
                'error' => __('An error occurred.', 'dream-ticket'),
            )
        ));
    }
    
    /**
     * Register settings
     */
    public function register_settings() {
        register_setting('dots_settings_group', 'dots_settings');
    }
    
    /**
     * Dashboard page
     */
    public function dashboard_page() {
        global $wpdb;
        
        $table_events = $wpdb->prefix . 'dots_events';
        $table_sales = $wpdb->prefix . 'dots_sales';
        
        // Get statistics
        $total_events = $wpdb->get_var("SELECT COUNT(*) FROM $table_events WHERE status = 'published'");
        $total_sales = $wpdb->get_var("SELECT COUNT(*) FROM $table_sales WHERE payment_status = 'completed'");
        $total_revenue = $wpdb->get_var("SELECT SUM(total_price) FROM $table_sales WHERE payment_status = 'completed'");
        $upcoming_events = $wpdb->get_results("SELECT * FROM $table_events WHERE status = 'published' AND event_date >= CURDATE() ORDER BY event_date ASC LIMIT 5");
        $recent_sales = DOTS_Database::get_sales(array('limit' => 10));
        
        include DOTS_PLUGIN_DIR . 'admin/views/dashboard.php';
    }
    
    /**
     * Events page
     */
    public function events_page() {
        $action = isset($_GET['action']) ? sanitize_text_field($_GET['action']) : 'list';
        
        if ($action === 'edit' || $action === 'add') {
            $event_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
            $event = $event_id > 0 ? DOTS_Database::get_event($event_id) : null;
            include DOTS_PLUGIN_DIR . 'admin/views/event-edit.php';
        } else {
            $events = DOTS_Database::get_events();
            include DOTS_PLUGIN_DIR . 'admin/views/events-list.php';
        }
    }
    
    /**
     * Forms page
     */
    public function forms_page() {
        // Ensure default fields exist
        DOTS_Database::create_default_fields();
        
        $fields = DOTS_Database::get_custom_fields();
        include DOTS_PLUGIN_DIR . 'admin/views/forms.php';
    }
    
    /**
     * Customers page
     */
    public function customers_page() {
        $sales = DOTS_Database::get_sales();
        include DOTS_PLUGIN_DIR . 'admin/views/customers.php';
    }
    
    /**
     * Sales page
     */
    public function sales_page() {
        // Handle QR code regeneration
        if (isset($_POST['regenerate_qr']) && check_admin_referer('dots_regenerate_qr')) {
            $order_number = sanitize_text_field($_POST['order_number']);
            global $wpdb;
            $sale = $wpdb->get_row($wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}dots_sales WHERE order_number = %s",
                $order_number
            ));
            if ($sale) {
                $ticket_url = home_url('/dream-tickets/ticket/' . urlencode($order_number));
                $qr_url = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&margin=2&data=' . urlencode($ticket_url);
                $wpdb->update($wpdb->prefix . 'dots_sales', array('qr_code' => $qr_url), array('id' => $sale->id));
                echo '<div class="notice notice-success"><p>' . __('QR code regenerated successfully.', 'dream-ticket') . '</p></div>';
            }
        }
        
        // Flush rewrite rules if needed
        if (isset($_POST['flush_rewrite_rules']) && check_admin_referer('dots_flush_rewrite')) {
            flush_rewrite_rules(false);
            delete_option('dots_rewrite_rules_flushed');
            echo '<div class="notice notice-success"><p>' . __('Rewrite rules flushed successfully.', 'dream-ticket') . '</p></div>';
        }
        
        $sales = DOTS_Database::get_sales();
        include DOTS_PLUGIN_DIR . 'admin/views/sales.php';
    }
    
    /**
     * Settings page
     */
    public function settings_page() {
        $settings = get_option('dots_settings', array());
        include DOTS_PLUGIN_DIR . 'admin/views/settings.php';
    }
    
    /**
     * Promo codes page
     */
    public function promo_codes_page() {
        $action = isset($_GET['action']) ? sanitize_text_field($_GET['action']) : 'list';
        
        if ($action === 'edit' || $action === 'add') {
            $promo_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
            $promo = $promo_id > 0 ? $this->get_promo_code_by_id($promo_id) : null;
            include DOTS_PLUGIN_DIR . 'admin/views/promo-code-edit.php';
        } else {
            $promo_codes = DOTS_Database::get_promo_codes();
            include DOTS_PLUGIN_DIR . 'admin/views/promo-codes-list.php';
        }
    }
    
    /**
     * Get promo code by ID
     */
    private function get_promo_code_by_id($id) {
        global $wpdb;
        $table = $wpdb->prefix . 'dots_promo_codes';
        return $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id = %d", $id));
    }
    
    /**
     * Test SSLCommerz page
     */
    public function test_sslcommerz_page() {
        $settings = get_option('dots_settings', array());
        $store_id = isset($settings['sslcommerz_store_id']) ? $settings['sslcommerz_store_id'] : '';
        $store_password = isset($settings['sslcommerz_store_password']) ? $settings['sslcommerz_store_password'] : '';
        $mode = isset($settings['sslcommerz_mode']) ? $settings['sslcommerz_mode'] : 'sandbox';
        
        // Handle test payment
        $test_result = null;
        if (isset($_POST['test_sslcommerz']) && check_admin_referer('dots_test_sslcommerz')) {
            $test_amount = isset($_POST['test_amount']) ? floatval($_POST['test_amount']) : 100.00;
            $test_result = $this->test_sslcommerz_payment($test_amount, $store_id, $store_password, $mode);
        }
        
        include DOTS_PLUGIN_DIR . 'admin/views/test-sslcommerz.php';
    }
    
    /**
     * Test SSLCommerz payment
     */
    private function test_sslcommerz_payment($amount, $store_id, $store_password, $mode) {
        if (empty($store_id) || empty($store_password)) {
            return array('success' => false, 'message' => __('Please configure SSLCommerz credentials in Settings first.', 'dream-ticket'));
        }
        
        $api_url = $mode === 'live' 
            ? 'https://securepay.sslcommerz.com' 
            : 'https://sandbox.sslcommerz.com';
        
        $tran_id = 'TEST-' . time();
        $success_url = admin_url('admin.php?page=dream-tickets-test-sslcommerz&test=success');
        $fail_url = admin_url('admin.php?page=dream-tickets-test-sslcommerz&test=failed');
        $cancel_url = admin_url('admin.php?page=dream-tickets-test-sslcommerz&test=cancelled');
        $ipn_url = admin_url('admin.php?page=dream-tickets-test-sslcommerz&test=ipn');
        
        $post_data = array(
            'store_id' => $store_id,
            'store_passwd' => $store_password,
            'total_amount' => number_format($amount, 2, '.', ''),
            'currency' => 'BDT',
            'tran_id' => $tran_id,
            'success_url' => $success_url,
            'fail_url' => $fail_url,
            'cancel_url' => $cancel_url,
            'ipn_url' => $ipn_url,
            'cus_name' => 'Test Customer',
            'cus_email' => get_option('admin_email'),
            'cus_add1' => 'Test Address',
            'cus_city' => 'Dhaka',
            'cus_postcode' => '1000',
            'cus_country' => 'Bangladesh',
            'cus_phone' => '01700000000',
            'product_name' => 'Test Payment',
            'product_category' => 'Test',
            'product_profile' => 'general',
            'shipping_method' => 'NO',
            'num_of_item' => 1
        );
        
        $response = wp_remote_post($api_url . '/gwprocess/v4/api.php', array(
            'method' => 'POST',
            'body' => http_build_query($post_data),
            'timeout' => 30,
            'sslverify' => true,
            'headers' => array(
                'Content-Type' => 'application/x-www-form-urlencoded'
            )
        ));
        
        if (is_wp_error($response)) {
            return array('success' => false, 'message' => __('Connection Error: ', 'dream-ticket') . $response->get_error_message());
        }
        
        $response_code = wp_remote_retrieve_response_code($response);
        $response_body = wp_remote_retrieve_body($response);
        
        parse_str($response_body, $response_data);
        
        if (isset($response_data['status']) && strtoupper($response_data['status']) === 'SUCCESS' && isset($response_data['GatewayPageURL'])) {
            return array(
                'success' => true,
                'message' => __('Payment session created successfully!', 'dream-ticket'),
                'redirect_url' => $response_data['GatewayPageURL'],
                'tran_id' => $tran_id
            );
        } else {
            $error = isset($response_data['failedreason']) ? $response_data['failedreason'] : __('Unknown error', 'dream-ticket');
            return array('success' => false, 'message' => __('Error: ', 'dream-ticket') . $error, 'response' => $response_body);
        }
    }
}

