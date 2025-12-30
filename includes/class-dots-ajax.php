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
        $description = wp_kses_post($_POST['description']);
        $event_date = sanitize_text_field($_POST['event_date']);
        $event_time = sanitize_text_field($_POST['event_time']);
        $location = sanitize_text_field($_POST['location']);
        $banner_url = esc_url_raw($_POST['banner_url']);
        $max_tickets = intval($_POST['max_tickets']);
        $status = sanitize_text_field($_POST['status']);
        $categories = isset($_POST['categories']) ? $_POST['categories'] : array();
        
        $data = array(
            'name' => $name,
            'description' => $description,
            'event_date' => $event_date,
            'event_time' => $event_time,
            'location' => $location,
            'banner_url' => $banner_url,
            'max_tickets' => $max_tickets,
            'status' => $status
        );
        
        if ($event_id > 0) {
            $wpdb->update($table_events, $data, array('id' => $event_id));
        } else {
            $wpdb->insert($table_events, $data);
            $event_id = $wpdb->insert_id;
        }
        
        // Save categories
        if (!empty($categories)) {
            // Delete existing categories
            $wpdb->delete($table_categories, array('event_id' => $event_id));
            
            foreach ($categories as $cat) {
                $wpdb->insert($table_categories, array(
                    'event_id' => $event_id,
                    'name' => sanitize_text_field($cat['name']),
                    'price' => floatval($cat['price']),
                    'availability' => intval($cat['availability']),
                    'max_per_customer' => intval($cat['max_per_customer'])
                ));
            }
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
        $ticket_category_id = intval($_POST['ticket_category_id']);
        $quantity = intval($_POST['quantity']);
        $customer_data = $_POST['customer_data'];
        $promo_code = isset($_POST['promo_code']) ? sanitize_text_field($_POST['promo_code']) : '';
        
        // Validate event
        $event = DOTS_Database::get_event($event_id);
        if (!$event || $event->status !== 'published') {
            wp_send_json_error(array('message' => __('Event not available.', 'dream-ticket')));
        }
        
        // Get ticket category
        $category = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}dots_ticket_categories WHERE id = %d AND event_id = %d",
            $ticket_category_id, $event_id
        ));
        
        if (!$category) {
            wp_send_json_error(array('message' => __('Ticket category not found.', 'dream-ticket')));
        }
        
        // Check availability
        if ($category->availability < $quantity) {
            wp_send_json_error(array('message' => __('Not enough tickets available.', 'dream-ticket')));
        }
        
        // Calculate price
        $unit_price = floatval($category->price);
        $total_price = $unit_price * $quantity;
        $discount_amount = 0;
        
        // Apply promo code if provided
        if (!empty($promo_code)) {
            // Promo code logic here
            // For now, just a placeholder
        }
        
        // Create or get customer
        $customer_email = sanitize_email($customer_data['email']);
        $customer = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}dots_customers WHERE email = %s",
            $customer_email
        ));
        
        if (!$customer) {
            $wpdb->insert($wpdb->prefix . 'dots_customers', array(
                'name' => sanitize_text_field($customer_data['name']),
                'email' => $customer_email,
                'phone' => sanitize_text_field($customer_data['phone']),
                'address' => sanitize_textarea_field($customer_data['address'])
            ));
            $customer_id = $wpdb->insert_id;
        } else {
            $customer_id = $customer->id;
        }
        
        // Generate order number
        $order_number = 'DOTS-' . time() . '-' . rand(1000, 9999);
        
        // Create sale record
        $sale_data = array(
            'event_id' => $event_id,
            'customer_id' => $customer_id,
            'ticket_category_id' => $ticket_category_id,
            'quantity' => $quantity,
            'unit_price' => $unit_price,
            'total_price' => $total_price - $discount_amount,
            'promo_code' => $promo_code,
            'discount_amount' => $discount_amount,
            'payment_status' => 'pending',
            'order_number' => $order_number,
            'custom_data' => json_encode($customer_data)
        );
        
        $wpdb->insert($wpdb->prefix . 'dots_sales', $sale_data);
        $sale_id = $wpdb->insert_id;
        
        // Generate QR code
        $qr_code = $this->generate_qr_code($order_number);
        $wpdb->update($wpdb->prefix . 'dots_sales', array('qr_code' => $qr_code), array('id' => $sale_id));
        
        // Process payment (integrate with payment gateway)
        // For now, just mark as completed
        $payment_result = array('status' => 'success', 'transaction_id' => 'test-' . time());
        
        if ($payment_result['status'] === 'success') {
            $wpdb->update($wpdb->prefix . 'dots_sales', array(
                'payment_status' => 'completed',
                'payment_method' => 'test',
                'transaction_id' => $payment_result['transaction_id']
            ), array('id' => $sale_id));
            
            // Update availability
            $wpdb->query($wpdb->prepare(
                "UPDATE {$wpdb->prefix}dots_ticket_categories SET availability = availability - %d WHERE id = %d",
                $quantity, $ticket_category_id
            ));
            
            // Send confirmation email
            $this->send_confirmation_email($customer_email, $order_number);
        }
        
        wp_send_json_success(array(
            'order_number' => $order_number,
            'redirect_url' => home_url('/dream-tickets/order/' . $order_number)
        ));
    }
    
    /**
     * Apply promo code
     */
    public function apply_promo() {
        check_ajax_referer('dots_frontend_nonce', 'nonce');
        
        $promo_code = sanitize_text_field($_POST['promo_code']);
        $total = floatval($_POST['total']);
        
        // Promo code validation logic here
        // For now, return error
        wp_send_json_error(array('message' => __('Invalid promo code.', 'dream-ticket')));
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
    private function generate_qr_code($data) {
        // Simple QR code generation using a service or library
        // For now, return a placeholder URL
        return 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' . urlencode($data);
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

