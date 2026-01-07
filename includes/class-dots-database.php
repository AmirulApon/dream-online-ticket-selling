<?php
/**
 * Database management class
 */

if (!defined('ABSPATH')) {
    exit;
}

class DOTS_Database {
    
    /**
     * Create all database tables
     */
    public static function create_tables() {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        
        // Events table
        $table_events = $wpdb->prefix . 'dots_events';
        $sql_events = "CREATE TABLE IF NOT EXISTS $table_events (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            name varchar(255) NOT NULL,
            description longtext,
            event_date date NOT NULL,
            event_time time NOT NULL,
            location varchar(255) NOT NULL,
            banner_url varchar(500),
            ticket_price decimal(10,2) DEFAULT 0,
            max_tickets int(11) DEFAULT 0,
            tickets_available int(11) DEFAULT 0,
            status varchar(20) DEFAULT 'draft',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $charset_collate;";
        
        // Add ticket_price column if it doesn't exist
        $column_exists = $wpdb->get_results("SHOW COLUMNS FROM $table_events LIKE 'ticket_price'");
        if (empty($column_exists)) {
            $wpdb->query("ALTER TABLE $table_events ADD COLUMN ticket_price decimal(10,2) DEFAULT 0 AFTER location");
        }
        
        // Add tickets_available column if it doesn't exist
        $column_exists = $wpdb->get_results("SHOW COLUMNS FROM $table_events LIKE 'tickets_available'");
        if (empty($column_exists)) {
            $wpdb->query("ALTER TABLE $table_events ADD COLUMN tickets_available int(11) DEFAULT 0 AFTER max_tickets");
        }
        
        // Add event_type column if it doesn't exist
        $column_exists = $wpdb->get_results("SHOW COLUMNS FROM $table_events LIKE 'event_type'");
        if (empty($column_exists)) {
            $wpdb->query("ALTER TABLE $table_events ADD COLUMN event_type varchar(100) DEFAULT NULL AFTER name");
        }
        
        // Ticket categories table
        $table_categories = $wpdb->prefix . 'dots_ticket_categories';
        $sql_categories = "CREATE TABLE IF NOT EXISTS $table_categories (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            event_id bigint(20) NOT NULL,
            name varchar(255) NOT NULL,
            price decimal(10,2) NOT NULL,
            availability int(11) DEFAULT 0,
            max_per_customer int(11) DEFAULT 10,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY event_id (event_id)
        ) $charset_collate;";
        
        // Custom fields table
        $table_fields = $wpdb->prefix . 'dots_custom_fields';
        $sql_fields = "CREATE TABLE IF NOT EXISTS $table_fields (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            field_name varchar(255) NOT NULL,
            field_label varchar(255) NOT NULL,
            field_type varchar(50) NOT NULL,
            field_options longtext,
            is_required tinyint(1) DEFAULT 0,
            field_order int(11) DEFAULT 0,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $charset_collate;";
        
        // Customers table
        $table_customers = $wpdb->prefix . 'dots_customers';
        $sql_customers = "CREATE TABLE IF NOT EXISTS $table_customers (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            name varchar(255) NOT NULL,
            email varchar(255) NOT NULL,
            phone varchar(50),
            address text,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY email (email)
        ) $charset_collate;";
        
        // Promo codes table
        $table_promo_codes = $wpdb->prefix . 'dots_promo_codes';
        $sql_promo_codes = "CREATE TABLE IF NOT EXISTS $table_promo_codes (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            code varchar(50) NOT NULL,
            discount_type varchar(20) NOT NULL DEFAULT 'percentage',
            discount_value decimal(10,2) NOT NULL,
            min_amount decimal(10,2) DEFAULT 0,
            max_discount decimal(10,2) DEFAULT 0,
            usage_limit int(11) DEFAULT 0,
            used_count int(11) DEFAULT 0,
            start_date date,
            end_date date,
            status varchar(20) DEFAULT 'active',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY code (code)
        ) $charset_collate;";
        
        // Sales table
        $table_sales = $wpdb->prefix . 'dots_sales';
        $sql_sales = "CREATE TABLE IF NOT EXISTS $table_sales (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            event_id bigint(20) NOT NULL,
            customer_id bigint(20) NOT NULL,
            ticket_category_id bigint(20) NOT NULL,
            quantity int(11) NOT NULL,
            unit_price decimal(10,2) NOT NULL,
            total_price decimal(10,2) NOT NULL,
            promo_code varchar(50),
            discount_amount decimal(10,2) DEFAULT 0,
            payment_status varchar(20) DEFAULT 'pending',
            payment_method varchar(50),
            transaction_id varchar(255),
            custom_data longtext,
            order_number varchar(100),
            qr_code varchar(500),
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY event_id (event_id),
            KEY customer_id (customer_id),
            KEY ticket_category_id (ticket_category_id),
            KEY order_number (order_number)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql_events);
        dbDelta($sql_categories);
        dbDelta($sql_fields);
        dbDelta($sql_customers);
        dbDelta($sql_promo_codes);
        dbDelta($sql_sales);
    }
    
    /**
     * Get events
     */
    public static function get_events($args = array()) {
        global $wpdb;
        $table = $wpdb->prefix . 'dots_events';
        
        $defaults = array(
            'status' => '',
            'orderby' => 'event_date',
            'order' => 'ASC',
            'limit' => -1,
            'offset' => 0
        );
        
        $args = wp_parse_args($args, $defaults);
        
        $where = '';
        if (!empty($args['status'])) {
            $where = $wpdb->prepare("WHERE status = %s", $args['status']);
        }
        
        $orderby = sanitize_sql_orderby($args['orderby'] . ' ' . $args['order']);
        $limit = '';
        if ($args['limit'] > 0) {
            $limit = $wpdb->prepare("LIMIT %d OFFSET %d", $args['limit'], $args['offset']);
        }
        
        $sql = "SELECT * FROM $table $where ORDER BY $orderby $limit";
        return $wpdb->get_results($sql);
    }
    
    /**
     * Get single event
     */
    public static function get_event($id) {
        global $wpdb;
        $table = $wpdb->prefix . 'dots_events';
        return $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id = %d", $id));
    }
    
    /**
     * Get ticket categories for an event
     */
    public static function get_ticket_categories($event_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'dots_ticket_categories';
        return $wpdb->get_results($wpdb->prepare("SELECT * FROM $table WHERE event_id = %d ORDER BY id ASC", $event_id));
    }
    
    /**
     * Get custom fields
     */
    public static function get_custom_fields() {
        global $wpdb;
        $table = $wpdb->prefix . 'dots_custom_fields';
        return $wpdb->get_results("SELECT * FROM $table ORDER BY field_order ASC");
    }
    
    /**
     * Get promo code
     */
    public static function get_promo_code($code) {
        global $wpdb;
        $table = $wpdb->prefix . 'dots_promo_codes';
        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table WHERE code = %s AND status = 'active'",
            strtoupper($code)
        ));
    }
    
    /**
     * Get all promo codes
     */
    public static function get_promo_codes() {
        global $wpdb;
        $table = $wpdb->prefix . 'dots_promo_codes';
        return $wpdb->get_results("SELECT * FROM $table ORDER BY created_at DESC");
    }
    
    /**
     * Increment promo code usage
     */
    public static function increment_promo_usage($code_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'dots_promo_codes';
        $wpdb->query($wpdb->prepare(
            "UPDATE $table SET used_count = used_count + 1 WHERE id = %d",
            $code_id
        ));
    }
    
    /**
     * Create default custom fields (name, email, phone, address)
     */
    public static function create_default_fields() {
        global $wpdb;
        $table = $wpdb->prefix . 'dots_custom_fields';
        
        // Check if default fields already exist
        $existing_fields = $wpdb->get_col("SELECT field_name FROM $table WHERE field_name IN ('name', 'email', 'phone', 'address')");
        
        $default_fields = array(
            array(
                'field_name' => 'name',
                'field_label' => __('Full Name', 'dream-ticket'),
                'field_type' => 'text',
                'field_options' => '',
                'is_required' => 1,
                'field_order' => 1
            ),
            array(
                'field_name' => 'email',
                'field_label' => __('Email Address', 'dream-ticket'),
                'field_type' => 'email',
                'field_options' => '',
                'is_required' => 1,
                'field_order' => 2
            ),
            array(
                'field_name' => 'phone',
                'field_label' => __('Mobile Number', 'dream-ticket'),
                'field_type' => 'tel',
                'field_options' => '',
                'is_required' => 1,
                'field_order' => 3
            ),
            array(
                'field_name' => 'address',
                'field_label' => __('Address', 'dream-ticket'),
                'field_type' => 'textarea',
                'field_options' => '',
                'is_required' => 0,
                'field_order' => 4
            )
        );
        
        foreach ($default_fields as $field) {
            // Only insert if it doesn't exist
            if (!in_array($field['field_name'], $existing_fields)) {
                $wpdb->insert($table, $field);
            }
        }
    }
    
    /**
     * Get sales
     */
    public static function get_sales($args = array()) {
        global $wpdb;
        $table_sales = $wpdb->prefix . 'dots_sales';
        $table_events = $wpdb->prefix . 'dots_events';
        $table_customers = $wpdb->prefix . 'dots_customers';
        $table_categories = $wpdb->prefix . 'dots_ticket_categories';
        
        $defaults = array(
            'event_id' => 0,
            'customer_id' => 0,
            'payment_status' => '',
            'orderby' => 'created_at',
            'order' => 'DESC',
            'limit' => -1,
            'offset' => 0
        );
        
        $args = wp_parse_args($args, $defaults);
        
        $where = array('1=1');
        if ($args['event_id'] > 0) {
            $where[] = $wpdb->prepare("s.event_id = %d", $args['event_id']);
        }
        if ($args['customer_id'] > 0) {
            $where[] = $wpdb->prepare("s.customer_id = %d", $args['customer_id']);
        }
        if (!empty($args['payment_status'])) {
            $where[] = $wpdb->prepare("s.payment_status = %s", $args['payment_status']);
        }
        
        $where_clause = implode(' AND ', $where);
        $orderby = sanitize_sql_orderby($args['orderby'] . ' ' . $args['order']);
        $limit = '';
        if ($args['limit'] > 0) {
            $limit = $wpdb->prepare("LIMIT %d OFFSET %d", $args['limit'], $args['offset']);
        }
        
        $sql = "SELECT s.*, e.name as event_name, e.event_type as event_type, c.name as customer_name, c.email as customer_email, 
                c.phone as customer_phone, tc.name as ticket_category_name
                FROM $table_sales s
                LEFT JOIN $table_events e ON s.event_id = e.id
                LEFT JOIN $table_customers c ON s.customer_id = c.id
                LEFT JOIN $table_categories tc ON s.ticket_category_id = tc.id
                WHERE $where_clause
                ORDER BY $orderby $limit";
        
        return $wpdb->get_results($sql);
    }
}

