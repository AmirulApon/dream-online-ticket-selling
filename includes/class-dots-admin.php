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
            __('Settings', 'dream-ticket'),
            __('Settings', 'dream-ticket'),
            'manage_options',
            'dream-tickets-settings',
            array($this, 'settings_page')
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
            $categories = $event_id > 0 ? DOTS_Database::get_ticket_categories($event_id) : array();
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
}

