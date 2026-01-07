<?php
/**
 * AJAX handlers
 */

if (!defined('ABSPATH')) {
    exit;
}

class DOTS_Ajax {
    
    public function __construct() {
        // Admin AJAX
        add_action('wp_ajax_dots_save_event', array($this, 'save_event'));
        add_action('wp_ajax_dots_delete_event', array($this, 'delete_event'));
        add_action('wp_ajax_dots_duplicate_event', array($this, 'duplicate_event'));
        add_action('wp_ajax_dots_toggle_event_status', array($this, 'toggle_event_status'));
        add_action('wp_ajax_dots_save_custom_field', array($this, 'save_custom_field'));
        add_action('wp_ajax_dots_delete_custom_field', array($this, 'delete_custom_field'));
        add_action('wp_ajax_dots_update_field_order', array($this, 'update_field_order'));
        add_action('wp_ajax_dots_export_customers', array($this, 'export_customers'));
        
        // Frontend AJAX
        add_action('wp_ajax_dots_process_purchase', array($this, 'process_purchase'));
        add_action('wp_ajax_nopriv_dots_process_purchase', array($this, 'process_purchase'));
        add_action('wp_ajax_dots_apply_promo', array($this, 'apply_promo'));
        add_action('wp_ajax_nopriv_dots_apply_promo', array($this, 'apply_promo'));
        add_action('wp_ajax_dots_save_promo_code', array($this, 'save_promo_code'));
        add_action('wp_ajax_dots_delete_promo_code', array($this, 'delete_promo_code'));
    }
    
    /**
     * Save event
     */
    public function save_event() {
        check_ajax_referer('dots_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Unauthorized.', 'dream-ticket')));
        }
        
        global $wpdb;
        $table_events = $wpdb->prefix . 'dots_events';
        $table_categories = $wpdb->prefix . 'dots_ticket_categories';
        
        $event_id = isset($_POST['event_id']) ? intval($_POST['event_id']) : 0;
        $name = sanitize_text_field($_POST['name']);
        $event_type = isset($_POST['event_type']) ? sanitize_text_field($_POST['event_type']) : '';
        $description = wp_kses_post($_POST['description']);
        $event_date = sanitize_text_field($_POST['event_date']);
        $event_time = sanitize_text_field($_POST['event_time']);
        $location = sanitize_text_field($_POST['location']);
        $banner_url = esc_url_raw($_POST['banner_url']);
        $ticket_price = isset($_POST['ticket_price']) ? floatval($_POST['ticket_price']) : 0;
        $tickets_available = isset($_POST['tickets_available']) ? intval($_POST['tickets_available']) : 0;
        $max_tickets = isset($_POST['max_tickets']) ? intval($_POST['max_tickets']) : 10;
        $status = sanitize_text_field($_POST['status']);
        
        $data = array(
            'name' => $name,
            'event_type' => $event_type,
            'description' => $description,
            'event_date' => $event_date,
            'event_time' => $event_time,
            'location' => $location,
            'banner_url' => $banner_url,
            'ticket_price' => $ticket_price,
            'tickets_available' => $tickets_available,
            'max_tickets' => $max_tickets,
            'status' => $status
        );
        
        if ($event_id > 0) {
            $wpdb->update($table_events, $data, array('id' => $event_id));
        } else {
            $wpdb->insert($table_events, $data);
            $event_id = $wpdb->insert_id;
        }
        
        wp_send_json_success(array('event_id' => $event_id, 'message' => __('Event saved successfully.', 'dream-ticket')));
    }
    
    /**
     * Delete event
     */
    public function delete_event() {
        check_ajax_referer('dots_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Unauthorized.', 'dream-ticket')));
        }
        
        global $wpdb;
        $event_id = intval($_POST['event_id']);
        
        $wpdb->delete($wpdb->prefix . 'dots_events', array('id' => $event_id));
        $wpdb->delete($wpdb->prefix . 'dots_ticket_categories', array('event_id' => $event_id));
        
        wp_send_json_success(array('message' => __('Event deleted successfully.', 'dream-ticket')));
    }
    
    /**
     * Duplicate event
     */
    public function duplicate_event() {
        check_ajax_referer('dots_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Unauthorized.', 'dream-ticket')));
        }
        
        global $wpdb;
        $event_id = intval($_POST['event_id']);
        $event = DOTS_Database::get_event($event_id);
        
        if (!$event) {
            wp_send_json_error(array('message' => __('Event not found.', 'dream-ticket')));
        }
        
        $table_events = $wpdb->prefix . 'dots_events';
        $table_categories = $wpdb->prefix . 'dots_ticket_categories';
        
        // Duplicate event
        $new_event_data = array(
            'name' => $event->name . ' (Copy)',
            'description' => $event->description,
            'event_date' => $event->event_date,
            'event_time' => $event->event_time,
            'location' => $event->location,
            'banner_url' => $event->banner_url,
            'max_tickets' => $event->max_tickets,
            'status' => 'draft'
        );
        
        $wpdb->insert($table_events, $new_event_data);
        $new_event_id = $wpdb->insert_id;
        
        // Duplicate categories
        $categories = DOTS_Database::get_ticket_categories($event_id);
        foreach ($categories as $cat) {
            $wpdb->insert($table_categories, array(
                'event_id' => $new_event_id,
                'name' => $cat->name,
                'price' => $cat->price,
                'availability' => $cat->availability,
                'max_per_customer' => $cat->max_per_customer
            ));
        }
        
        wp_send_json_success(array('event_id' => $new_event_id, 'message' => __('Event duplicated successfully.', 'dream-ticket')));
    }
    
    /**
     * Toggle event status
     */
    public function toggle_event_status() {
        check_ajax_referer('dots_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Unauthorized.', 'dream-ticket')));
        }
        
        global $wpdb;
        $event_id = intval($_POST['event_id']);
        $status = sanitize_text_field($_POST['status']);
        
        $wpdb->update($wpdb->prefix . 'dots_events', array('status' => $status), array('id' => $event_id));
        
        wp_send_json_success(array('message' => __('Status updated.', 'dream-ticket')));
    }
    
    /**
     * Save custom field
     */
    public function save_custom_field() {
        check_ajax_referer('dots_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Unauthorized.', 'dream-ticket')));
        }
        
        global $wpdb;
        $table = $wpdb->prefix . 'dots_custom_fields';
        
        $field_id = isset($_POST['field_id']) ? intval($_POST['field_id']) : 0;
        $field_name = sanitize_key($_POST['field_name']);
        $field_label = sanitize_text_field($_POST['field_label']);
        $field_type = sanitize_text_field($_POST['field_type']);
        $field_options = isset($_POST['field_options']) ? sanitize_textarea_field($_POST['field_options']) : '';
        $is_required = isset($_POST['is_required']) ? 1 : 0;
        $field_order = isset($_POST['field_order']) ? intval($_POST['field_order']) : 0;
        
        $data = array(
            'field_name' => $field_name,
            'field_label' => $field_label,
            'field_type' => $field_type,
            'field_options' => $field_options,
            'is_required' => $is_required,
            'field_order' => $field_order
        );
        
        if ($field_id > 0) {
            $wpdb->update($table, $data, array('id' => $field_id));
        } else {
            $wpdb->insert($table, $data);
            $field_id = $wpdb->insert_id;
        }
        
        wp_send_json_success(array('field_id' => $field_id, 'message' => __('Field saved successfully.', 'dream-ticket')));
    }
    
    /**
     * Delete custom field
     */
    public function delete_custom_field() {
        check_ajax_referer('dots_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Unauthorized.', 'dream-ticket')));
        }
        
        global $wpdb;
        $field_id = intval($_POST['field_id']);
        $wpdb->delete($wpdb->prefix . 'dots_custom_fields', array('id' => $field_id));
        
        wp_send_json_success(array('message' => __('Field deleted successfully.', 'dream-ticket')));
    }
    
    /**
     * Update field order
     */
    public function update_field_order() {
        check_ajax_referer('dots_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Unauthorized.', 'dream-ticket')));
        }
        
        global $wpdb;
        $table = $wpdb->prefix . 'dots_custom_fields';
        $orders = $_POST['orders'];
        
        foreach ($orders as $order => $field_id) {
            $wpdb->update($table, array('field_order' => intval($order)), array('id' => intval($field_id)));
        }
        
        wp_send_json_success(array('message' => __('Order updated.', 'dream-ticket')));
    }
    
    /**
     * Process purchase
     */
    public function process_purchase() {
        check_ajax_referer('dots_frontend_nonce', 'nonce');
        
        global $wpdb;
        
        $event_id = intval($_POST['event_id']);
        $quantity = intval($_POST['quantity']);
        
        // Parse customer data (can be JSON string or array)
        $customer_data = array();
        if (isset($_POST['customer_data'])) {
            if (is_string($_POST['customer_data'])) {
                $customer_data = json_decode(stripslashes($_POST['customer_data']), true);
                if (!is_array($customer_data)) {
                    $customer_data = array();
                }
            } else {
                $customer_data = $_POST['customer_data'];
            }
        }
        
        $promo_code = isset($_POST['promo_code']) ? sanitize_text_field($_POST['promo_code']) : '';
        
        // Validate event
        $event = DOTS_Database::get_event($event_id);
        if (!$event || $event->status !== 'published') {
            wp_send_json_error(array('message' => __('Event not available.', 'dream-ticket')));
        }
        
        // Get ticket price and availability from event
        $ticket_price = isset($event->ticket_price) ? floatval($event->ticket_price) : 0;
        $tickets_available = isset($event->tickets_available) ? intval($event->tickets_available) : 0;
        $max_tickets_per_customer = isset($event->max_tickets) ? intval($event->max_tickets) : 10;
        
        if ($ticket_price <= 0) {
            wp_send_json_error(array('message' => __('Ticket price not set for this event.', 'dream-ticket')));
        }
        
        // Check availability
        if ($tickets_available < $quantity) {
            wp_send_json_error(array('message' => __('Not enough tickets available.', 'dream-ticket')));
        }
        
        // Check max per customer
        if ($quantity > $max_tickets_per_customer) {
            wp_send_json_error(array('message' => sprintf(__('Maximum %d tickets per customer.', 'dream-ticket'), $max_tickets_per_customer)));
        }
        
        // Calculate price
        $unit_price = $ticket_price;
        $total_price = $unit_price * $quantity;
        $discount_amount = 0;
        
        // Apply promo code if provided
        if (!empty($promo_code)) {
            $promo = DOTS_Database::get_promo_code($promo_code);
            
            if ($promo && $promo->status === 'active') {
                // Check date validity
                $today = date('Y-m-d');
                $valid_date = true;
                if (!empty($promo->start_date) && $today < $promo->start_date) {
                    $valid_date = false;
                }
                if (!empty($promo->end_date) && $today > $promo->end_date) {
                    $valid_date = false;
                }
                
                // Check minimum amount
                if ($promo->min_amount > 0 && $total_price < $promo->min_amount) {
                    $valid_date = false;
                }
                
                // Check usage limit
                if ($promo->usage_limit > 0 && $promo->used_count >= $promo->usage_limit) {
                    $valid_date = false;
                }
                
                if ($valid_date) {
                    // Calculate discount
                    if ($promo->discount_type === 'percentage') {
                        $discount_amount = ($total_price * $promo->discount_value) / 100;
                        // Apply max discount if set
                        if ($promo->max_discount > 0 && $discount_amount > $promo->max_discount) {
                            $discount_amount = $promo->max_discount;
                        }
                    } else {
                        // Fixed amount
                        $discount_amount = $promo->discount_value;
                    }
                    
                    // Ensure discount doesn't exceed total
                    if ($discount_amount > $total_price) {
                        $discount_amount = $total_price;
                    }
                    
                    // Increment usage count
                    DOTS_Database::increment_promo_usage($promo->id);
                }
            }
        }
        
        // Create or get customer
        $customer_email = isset($customer_data['email']) ? sanitize_email($customer_data['email']) : '';
        $customer_name = isset($customer_data['name']) ? sanitize_text_field($customer_data['name']) : '';
        $customer_phone = isset($customer_data['phone']) ? sanitize_text_field($customer_data['phone']) : '';
        $customer_address = isset($customer_data['address']) ? sanitize_textarea_field($customer_data['address']) : '';
        
        if (empty($customer_email)) {
            wp_send_json_error(array('message' => __('Email is required.', 'dream-ticket')));
        }
        
        if (empty($customer_name)) {
            wp_send_json_error(array('message' => __('Name is required.', 'dream-ticket')));
        }
        
        $customer = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}dots_customers WHERE email = %s",
            $customer_email
        ));
        
        if (!$customer) {
            $wpdb->insert($wpdb->prefix . 'dots_customers', array(
                'name' => $customer_name,
                'email' => $customer_email,
                'phone' => $customer_phone,
                'address' => $customer_address
            ));
            $customer_id = $wpdb->insert_id;
        } else {
            $customer_id = $customer->id;
            // Update customer info if provided
            if (!empty($customer_name) || !empty($customer_phone) || !empty($customer_address)) {
                $update_data = array();
                if (!empty($customer_name)) $update_data['name'] = $customer_name;
                if (!empty($customer_phone)) $update_data['phone'] = $customer_phone;
                if (!empty($customer_address)) $update_data['address'] = $customer_address;
                if (!empty($update_data)) {
                    $wpdb->update($wpdb->prefix . 'dots_customers', $update_data, array('id' => $customer_id));
                }
            }
        }
        
        // Generate order number
        $order_number = 'DOTS-' . time() . '-' . rand(1000, 9999);
        
        // Create sale record
        $sale_data = array(
            'event_id' => $event_id,
            'customer_id' => $customer_id,
            'ticket_category_id' => 0, // Not used anymore
            'quantity' => $quantity,
            'unit_price' => $unit_price,
            'total_price' => $total_price - $discount_amount,
            'promo_code' => $promo_code,
            'discount_amount' => $discount_amount,
            'payment_status' => 'pending',
            'order_number' => $order_number,
            'custom_data' => json_encode($customer_data)
        );
        
        $insert_result = $wpdb->insert($wpdb->prefix . 'dots_sales', $sale_data);
        
        if ($insert_result === false) {
            wp_send_json_error(array('message' => __('Failed to create order. Please try again.', 'dream-ticket')));
        }
        
        $sale_id = $wpdb->insert_id;
        
        // Generate QR code
        $qr_code = $this->generate_qr_code($order_number);
        $wpdb->update($wpdb->prefix . 'dots_sales', array('qr_code' => $qr_code), array('id' => $sale_id));
        
        // Get payment method
        $payment_method = isset($_POST['payment_method']) ? sanitize_text_field($_POST['payment_method']) : 'bank_transfer';
        
        $settings = get_option('dots_settings', array());
        $paypal_enabled = isset($settings['paypal_enabled']) && $settings['paypal_enabled'];
        $stripe_enabled = isset($settings['stripe_enabled']) && $settings['stripe_enabled'];
        $sslcommerz_enabled = isset($settings['sslcommerz_enabled']) && $settings['sslcommerz_enabled'];
        $bank_transfer_enabled = isset($settings['bank_transfer_enabled']) && $settings['bank_transfer_enabled'];
        
        // Initialize payment handler
        $payment_handler = new DOTS_Payment();
        $order_data = array(
            'order_number' => $order_number,
            'event_id' => $event_id,
            'event_name' => $event->name,
            'total_price' => $total_price - $discount_amount,
            'payment_method' => $payment_method,
            'return_url' => home_url('/dream-tickets/order/' . $order_number),
            'customer_name' => $customer_name,
            'customer_email' => $customer_email,
            'customer_phone' => $customer_phone,
            'customer_address' => $customer_address
        );
        
        // Process payment based on method
        $payment_result = $payment_handler->process_payment($order_data);
        
        // Initialize update data
        $update_data = array(
            'payment_method' => $payment_method
        );
        
        // Handle payment result
        if ($payment_result['status'] === 'success') {
            // Set payment status based on method
            if ($payment_method === 'bank_transfer') {
                $update_data['payment_status'] = 'pending';
                $update_data['transaction_id'] = isset($payment_result['transaction_id']) ? $payment_result['transaction_id'] : '';
            } else {
                // PayPal and Stripe are processing until confirmed
                $update_data['payment_status'] = 'processing';
                $update_data['transaction_id'] = isset($payment_result['payment_id']) ? $payment_result['payment_id'] : (isset($payment_result['payment_intent_id']) ? $payment_result['payment_intent_id'] : '');
            }
            
            // Update sale record with payment info
            $wpdb->update($wpdb->prefix . 'dots_sales', $update_data, array('id' => $sale_id));
            
            // Handle payment redirects
            if (isset($payment_result['redirect_url'])) {
                // SSLCommerz - redirect to payment gateway
                wp_send_json_success(array(
                    'redirect' => true,
                    'redirect_url' => $payment_result['redirect_url'],
                    'order_number' => $order_number
                ));
            } elseif (isset($payment_result['approve_url'])) {
                // PayPal - redirect to PayPal
                wp_send_json_success(array(
                    'redirect' => true,
                    'redirect_url' => $payment_result['approve_url'],
                    'order_number' => $order_number
                ));
            } elseif (isset($payment_result['client_secret'])) {
                // Stripe - return client secret for frontend handling
                wp_send_json_success(array(
                    'stripe' => true,
                    'client_secret' => $payment_result['client_secret'],
                    'payment_intent_id' => $payment_result['payment_intent_id'],
                    'order_number' => $order_number
                ));
            } elseif ($payment_method === 'bank_transfer') {
                // Bank transfer - redirect to confirmation (payment pending)
                // Don't update tickets available yet for bank transfer
                wp_send_json_success(array(
                    'order_number' => $order_number,
                    'redirect_url' => home_url('/dream-tickets/order/' . $order_number),
                    'message' => __('Order created. Payment pending bank transfer.', 'dream-ticket')
                ));
            } else {
                // Fallback - should not reach here
                wp_send_json_error(array('message' => __('Payment processing failed.', 'dream-ticket')));
            }
        } else {
            // Payment processing failed
            $error_message = isset($payment_result['message']) ? $payment_result['message'] : __('Payment processing failed.', 'dream-ticket');
            
            // Update sale with failed status
            $update_data['payment_status'] = 'failed';
            $wpdb->update($wpdb->prefix . 'dots_sales', $update_data, array('id' => $sale_id));
            
            wp_send_json_error(array('message' => $error_message));
        }
    }
    
    /**
     * Apply promo code
     */
    public function apply_promo() {
        check_ajax_referer('dots_frontend_nonce', 'nonce');
        
        $promo_code = strtoupper(sanitize_text_field($_POST['promo_code']));
        $total = floatval($_POST['total']);
        
        if (empty($promo_code)) {
            wp_send_json_error(array('message' => __('Please enter a promo code.', 'dream-ticket')));
        }
        
        // Get promo code from database
        $promo = DOTS_Database::get_promo_code($promo_code);
        
        if (!$promo) {
            wp_send_json_error(array('message' => __('Invalid promo code.', 'dream-ticket')));
        }
        
        // Check if promo code is active
        if ($promo->status !== 'active') {
            wp_send_json_error(array('message' => __('This promo code is not active.', 'dream-ticket')));
        }
        
        // Check date validity
        $today = date('Y-m-d');
        if (!empty($promo->start_date) && $today < $promo->start_date) {
            wp_send_json_error(array('message' => __('This promo code is not yet valid.', 'dream-ticket')));
        }
        if (!empty($promo->end_date) && $today > $promo->end_date) {
            wp_send_json_error(array('message' => __('This promo code has expired.', 'dream-ticket')));
        }
        
        // Check minimum amount
        if ($promo->min_amount > 0 && $total < $promo->min_amount) {
            $settings = get_option('dots_settings', array());
            $currency_symbol = isset($settings['currency_symbol']) ? $settings['currency_symbol'] : '$';
            wp_send_json_error(array('message' => sprintf(__('Minimum purchase amount of %s%s required.', 'dream-ticket'), $currency_symbol, number_format($promo->min_amount, 2))));
        }
        
        // Check usage limit
        if ($promo->usage_limit > 0 && $promo->used_count >= $promo->usage_limit) {
            wp_send_json_error(array('message' => __('This promo code has reached its usage limit.', 'dream-ticket')));
        }
        
        // Calculate discount
        $discount = 0;
        if ($promo->discount_type === 'percentage') {
            $discount = ($total * $promo->discount_value) / 100;
            // Apply max discount if set
            if ($promo->max_discount > 0 && $discount > $promo->max_discount) {
                $discount = $promo->max_discount;
            }
        } else {
            // Fixed amount
            $discount = $promo->discount_value;
        }
        
        // Ensure discount doesn't exceed total
        if ($discount > $total) {
            $discount = $total;
        }
        
        wp_send_json_success(array(
            'discount' => round($discount, 2),
            'discount_amount' => round($discount, 2),
            'new_total' => round($total - $discount, 2),
            'message' => __('Promo code applied successfully!', 'dream-ticket')
        ));
    }
    
    /**
     * Save promo code
     */
    public function save_promo_code() {
        check_ajax_referer('dots_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Unauthorized.', 'dream-ticket')));
        }
        
        global $wpdb;
        $table = $wpdb->prefix . 'dots_promo_codes';
        
        $promo_id = isset($_POST['promo_id']) ? intval($_POST['promo_id']) : 0;
        $code = strtoupper(sanitize_text_field($_POST['code']));
        $discount_type = sanitize_text_field($_POST['discount_type']);
        $discount_value = floatval($_POST['discount_value']);
        $min_amount = floatval($_POST['min_amount']);
        $max_discount = floatval($_POST['max_discount']);
        $usage_limit = intval($_POST['usage_limit']);
        $start_date = !empty($_POST['start_date']) ? sanitize_text_field($_POST['start_date']) : null;
        $end_date = !empty($_POST['end_date']) ? sanitize_text_field($_POST['end_date']) : null;
        $status = sanitize_text_field($_POST['status']);
        
        $data = array(
            'code' => $code,
            'discount_type' => $discount_type,
            'discount_value' => $discount_value,
            'min_amount' => $min_amount,
            'max_discount' => $max_discount,
            'usage_limit' => $usage_limit,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'status' => $status
        );
        
        if ($promo_id > 0) {
            $wpdb->update($table, $data, array('id' => $promo_id));
        } else {
            $wpdb->insert($table, $data);
            $promo_id = $wpdb->insert_id;
        }
        
        if ($wpdb->last_error) {
            wp_send_json_error(array('message' => __('Failed to save promo code: ', 'dream-ticket') . $wpdb->last_error));
        }
        
        wp_send_json_success(array('promo_id' => $promo_id, 'message' => __('Promo code saved successfully.', 'dream-ticket')));
    }
    
    /**
     * Delete promo code
     */
    public function delete_promo_code() {
        check_ajax_referer('dots_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Unauthorized.', 'dream-ticket')));
        }
        
        global $wpdb;
        $promo_id = intval($_POST['promo_id']);
        $wpdb->delete($wpdb->prefix . 'dots_promo_codes', array('id' => $promo_id));
        
        wp_send_json_success(array('message' => __('Promo code deleted successfully.', 'dream-ticket')));
    }
    
    /**
     * Export customers
     */
    public function export_customers() {
        check_ajax_referer('dots_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die(__('Unauthorized.', 'dream-ticket'));
        }
        
        $sales = DOTS_Database::get_sales();
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="customers-' . date('Y-m-d') . '.csv"');
        
        $output = fopen('php://output', 'w');
        fputcsv($output, array('Order Number', 'Customer Name', 'Email', 'Phone', 'Event', 'Ticket Type', 'Quantity', 'Total Price', 'Purchase Date', 'Payment Status'));
        
        foreach ($sales as $sale) {
            fputcsv($output, array(
                $sale->order_number,
                $sale->customer_name,
                $sale->customer_email,
                $sale->customer_phone,
                $sale->event_name,
                $sale->ticket_category_name,
                $sale->quantity,
                $sale->total_price,
                $sale->created_at,
                $sale->payment_status
            ));
        }
        
        fclose($output);
        exit;
    }
    
    /**
     * Generate QR code
     */
    private function generate_qr_code($order_number) {
        // Generate QR code that links to ticket verification page
        // Use absolute URL to ensure it works from anywhere
        $ticket_url = home_url('/dream-tickets/ticket/' . urlencode($order_number));
        
        // Ensure rewrite rules are flushed
        flush_rewrite_rules(false);
        
        // Generate QR code using multiple methods for reliability
        // Method 1: QR Server API (most reliable)
        $qr_url = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&margin=2&data=' . urlencode($ticket_url);
        
        // Store both the QR image URL and the ticket URL for reference
        // The QR code image URL is what we'll display
        return $qr_url;
    }
    
    /**
     * Send confirmation email
     */
    private function send_confirmation_email($email, $order_number) {
        $subject = __('Your Ticket Purchase Confirmation', 'dream-ticket');
        $message = sprintf(__('Thank you for your purchase. Your order number is: %s', 'dream-ticket'), $order_number);
        wp_mail($email, $subject, $message);
    }
}

