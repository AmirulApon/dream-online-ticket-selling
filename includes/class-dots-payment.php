<?php
/**
 * Payment gateway integration
 */

if (!defined('ABSPATH')) {
    exit;
}

class DOTS_Payment {
    
    public function __construct() {
        add_action('wp_ajax_dots_create_payment', array($this, 'create_payment'));
        add_action('wp_ajax_nopriv_dots_create_payment', array($this, 'create_payment'));
        add_action('wp_ajax_dots_verify_payment', array($this, 'verify_payment'));
        add_action('wp_ajax_nopriv_dots_verify_payment', array($this, 'verify_payment'));
        add_action('wp_ajax_dots_sslcommerz_callback', array($this, 'sslcommerz_callback'));
        add_action('wp_ajax_nopriv_dots_sslcommerz_callback', array($this, 'sslcommerz_callback'));
    }
    
    /**
     * Process payment
     */
    public function process_payment($order_data) {
        $settings = get_option('dots_settings', array());
        $payment_method = isset($order_data['payment_method']) ? $order_data['payment_method'] : 'paypal';
        
        switch ($payment_method) {
            case 'paypal':
                return $this->process_paypal($order_data);
            case 'stripe':
                return $this->process_stripe($order_data);
            case 'sslcommerz':
                return $this->process_sslcommerz($order_data);
            case 'bank_transfer':
                return $this->process_bank_transfer($order_data);
            default:
                return array('status' => 'error', 'message' => __('Invalid payment method.', 'dream-ticket'));
        }
    }
    
    /**
     * Process PayPal payment
     */
    private function process_paypal($order_data) {
        $settings = get_option('dots_settings', array());
        $paypal_client_id = isset($settings['paypal_client_id']) ? $settings['paypal_client_id'] : '';
        $paypal_secret = isset($settings['paypal_secret']) ? $settings['paypal_secret'] : '';
        $paypal_mode = isset($settings['paypal_mode']) ? $settings['paypal_mode'] : 'sandbox';
        
        if (empty($paypal_client_id) || empty($paypal_secret)) {
            return array('status' => 'error', 'message' => __('PayPal credentials not configured.', 'dream-ticket'));
        }
        
        $api_url = $paypal_mode === 'live' 
            ? 'https://api-m.paypal.com' 
            : 'https://api-m.sandbox.paypal.com';
        
        // Create PayPal order
        $order_data_paypal = array(
            'intent' => 'CAPTURE',
            'purchase_units' => array(
                array(
                    'reference_id' => $order_data['order_number'],
                    'description' => $order_data['event_name'],
                    'amount' => array(
                        'currency_code' => isset($settings['currency']) ? $settings['currency'] : 'USD',
                        'value' => number_format($order_data['total_price'], 2, '.', '')
                    )
                )
            ),
            'application_context' => array(
                'return_url' => add_query_arg('payment', 'success', $order_data['return_url']),
                'cancel_url' => add_query_arg('payment', 'cancelled', $order_data['return_url'])
            )
        );
        
        // Get access token
        $access_token = $this->get_paypal_access_token($api_url, $paypal_client_id, $paypal_secret);
        
        if (!$access_token) {
            return array('status' => 'error', 'message' => __('Failed to authenticate with PayPal. Please check your credentials.', 'dream-ticket'));
        }
        
        // Create order
        $response = wp_remote_post($api_url . '/v2/checkout/orders', array(
            'headers' => array(
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $access_token,
                'PayPal-Request-Id' => $order_data['order_number'],
                'Prefer' => 'return=representation'
            ),
            'body' => json_encode($order_data_paypal),
            'timeout' => 30
        ));
        
        if (is_wp_error($response)) {
            error_log('PayPal Order Creation Error: ' . $response->get_error_message());
            return array('status' => 'error', 'message' => __('Failed to connect to PayPal: ', 'dream-ticket') . $response->get_error_message());
        }
        
        $response_code = wp_remote_retrieve_response_code($response);
        $body = json_decode(wp_remote_retrieve_body($response), true);
        
        if ($response_code !== 201 && $response_code !== 200) {
            $error_message = isset($body['message']) ? $body['message'] : __('Failed to create PayPal order.', 'dream-ticket');
            if (isset($body['details'])) {
                $error_details = array();
                foreach ($body['details'] as $detail) {
                    if (isset($detail['description'])) {
                        $error_details[] = $detail['description'];
                    }
                }
                if (!empty($error_details)) {
                    $error_message .= ' ' . implode(', ', $error_details);
                }
            }
            error_log('PayPal Order Creation Failed. Code: ' . $response_code . ', Response: ' . wp_remote_retrieve_body($response));
            return array('status' => 'error', 'message' => $error_message);
        }
        
        if (isset($body['id']) && isset($body['links'])) {
            $approve_url = '';
            foreach ($body['links'] as $link) {
                if ($link['rel'] === 'approve') {
                    $approve_url = $link['href'];
                    break;
                }
            }
            
            if (empty($approve_url)) {
                error_log('PayPal Order Created but no approve URL found. Response: ' . wp_remote_retrieve_body($response));
                return array('status' => 'error', 'message' => __('PayPal order created but approval URL not found.', 'dream-ticket'));
            }
            
            return array(
                'status' => 'success',
                'payment_id' => $body['id'],
                'approve_url' => $approve_url,
                'transaction_id' => $body['id']
            );
        }
        
        error_log('PayPal Order Creation - Invalid response: ' . wp_remote_retrieve_body($response));
        return array('status' => 'error', 'message' => __('Invalid response from PayPal.', 'dream-ticket'));
    }
    
    /**
     * Get PayPal access token
     */
    private function get_paypal_access_token($api_url, $client_id, $secret) {
        // PayPal requires Basic Auth with client_id:secret base64 encoded
        $auth = base64_encode($client_id . ':' . $secret);
        
        $response = wp_remote_post($api_url . '/v1/oauth2/token', array(
            'headers' => array(
                'Accept' => 'application/json',
                'Accept-Language' => 'en_US',
                'Content-Type' => 'application/x-www-form-urlencoded',
                'Authorization' => 'Basic ' . $auth
            ),
            'body' => 'grant_type=client_credentials',
            'timeout' => 30,
            'user-agent' => 'WordPress/' . get_bloginfo('version'),
            'httpversion' => '1.1'
        ));
        
        if (is_wp_error($response)) {
            error_log('PayPal Auth Error: ' . $response->get_error_message());
            return false;
        }
        
        $response_code = wp_remote_retrieve_response_code($response);
        if ($response_code !== 200) {
            $body = wp_remote_retrieve_body($response);
            error_log('PayPal Auth Failed. Code: ' . $response_code . ', Response: ' . $body);
            return false;
        }
        
        $body = json_decode(wp_remote_retrieve_body($response), true);
        return isset($body['access_token']) ? $body['access_token'] : false;
    }
    
    /**
     * Process Stripe payment
     */
    private function process_stripe($order_data) {
        $settings = get_option('dots_settings', array());
        $stripe_secret_key = isset($settings['stripe_secret_key']) ? $settings['stripe_secret_key'] : '';
        $stripe_mode = isset($settings['stripe_mode']) ? $settings['stripe_mode'] : 'test';
        
        if (empty($stripe_secret_key)) {
            return array('status' => 'error', 'message' => __('Stripe credentials not configured.', 'dream-ticket'));
        }
        
        // For Stripe, we'll create a payment intent using form-encoded data
        // Metadata needs to be sent as separate parameters
        $body_data = array(
            'amount' => intval($order_data['total_price'] * 100), // Convert to cents
            'currency' => strtolower(isset($settings['currency']) ? $settings['currency'] : 'usd'),
            'description' => $order_data['event_name'],
            'metadata[order_number]' => $order_data['order_number'],
            'metadata[event_id]' => $order_data['event_id']
        );
        
        $response = wp_remote_post('https://api.stripe.com/v1/payment_intents', array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $stripe_secret_key,
                'Content-Type' => 'application/x-www-form-urlencoded'
            ),
            'body' => $body_data,
            'timeout' => 30
        ));
        
        if (is_wp_error($response)) {
            error_log('Stripe Error: ' . $response->get_error_message());
            return array('status' => 'error', 'message' => $response->get_error_message());
        }
        
        $response_code = wp_remote_retrieve_response_code($response);
        $body = json_decode(wp_remote_retrieve_body($response), true);
        
        if ($response_code !== 200) {
            $error_message = isset($body['error']['message']) ? $body['error']['message'] : __('Failed to create Stripe payment intent.', 'dream-ticket');
            error_log('Stripe API Error. Code: ' . $response_code . ', Response: ' . wp_remote_retrieve_body($response));
            return array('status' => 'error', 'message' => $error_message);
        }
        
        if (isset($body['id']) && isset($body['client_secret'])) {
            return array(
                'status' => 'success',
                'payment_intent_id' => $body['id'],
                'client_secret' => $body['client_secret'],
                'transaction_id' => $body['id']
            );
        }
        
        return array('status' => 'error', 'message' => __('Failed to create Stripe payment intent.', 'dream-ticket'));
    }
    
    /**
     * Process SSLCommerz payment
     */
    private function process_sslcommerz($order_data) {
        $settings = get_option('dots_settings', array());
        $store_id = isset($settings['sslcommerz_store_id']) ? $settings['sslcommerz_store_id'] : '';
        $store_password = isset($settings['sslcommerz_store_password']) ? $settings['sslcommerz_store_password'] : '';
        $mode = isset($settings['sslcommerz_mode']) ? $settings['sslcommerz_mode'] : 'sandbox';
        
        if (empty($store_id) || empty($store_password)) {
            return array('status' => 'error', 'message' => __('SSLCommerz credentials not configured.', 'dream-ticket'));
        }
        
        // Determine API URL based on mode
        $api_url = $mode === 'live' 
            ? 'https://securepay.sslcommerz.com' 
            : 'https://sandbox.sslcommerz.com';
        
        // Prepare payment data
        $total_amount = number_format($order_data['total_price'], 2, '.', '');
        $currency = isset($settings['currency']) ? $settings['currency'] : 'BDT';
        
        // Generate transaction ID
        $tran_id = $order_data['order_number'] . '-' . time();
        
        // Prepare callback URLs
        $success_url = add_query_arg(array(
            'action' => 'dots_sslcommerz_callback',
            'status' => 'success',
            'order_number' => $order_data['order_number']
        ), admin_url('admin-ajax.php'));
        
        $fail_url = add_query_arg(array(
            'action' => 'dots_sslcommerz_callback',
            'status' => 'failed',
            'order_number' => $order_data['order_number']
        ), admin_url('admin-ajax.php'));
        
        $cancel_url = add_query_arg(array(
            'action' => 'dots_sslcommerz_callback',
            'status' => 'cancelled',
            'order_number' => $order_data['order_number']
        ), admin_url('admin-ajax.php'));
        
        $ipn_url = add_query_arg(array(
            'action' => 'dots_sslcommerz_callback',
            'status' => 'ipn',
            'order_number' => $order_data['order_number']
        ), admin_url('admin-ajax.php'));
        
        // Prepare customer data with defaults
        $cus_name = !empty($order_data['customer_name']) ? sanitize_text_field($order_data['customer_name']) : 'Customer';
        $cus_email = !empty($order_data['customer_email']) ? sanitize_email($order_data['customer_email']) : '';
        $cus_phone = !empty($order_data['customer_phone']) ? sanitize_text_field($order_data['customer_phone']) : '01700000000';
        $cus_add1 = !empty($order_data['customer_address']) ? substr(sanitize_text_field($order_data['customer_address']), 0, 50) : 'Dhaka';
        
        // Ensure required fields are not empty
        if (empty($cus_email)) {
            $cus_email = get_option('admin_email');
        }
        if (empty($cus_phone)) {
            $cus_phone = '01700000000';
        }
        
        // Prepare post data - only include non-empty fields
        $post_data = array(
            'store_id' => $store_id,
            'store_passwd' => $store_password,
            'total_amount' => $total_amount,
            'currency' => $currency,
            'tran_id' => $tran_id,
            'success_url' => $success_url,
            'fail_url' => $fail_url,
            'cancel_url' => $cancel_url,
            'ipn_url' => $ipn_url,
            'cus_name' => $cus_name,
            'cus_email' => $cus_email,
            'cus_add1' => $cus_add1,
            'cus_city' => 'Dhaka',
            'cus_postcode' => '1000',
            'cus_country' => 'Bangladesh',
            'cus_phone' => $cus_phone,
            'product_name' => substr(sanitize_text_field($order_data['event_name']), 0, 100),
            'product_category' => 'Event Ticket',
            'product_profile' => 'general',
            'shipping_method' => 'NO',
            'num_of_item' => 1,
            'value_a' => $order_data['order_number'], // Store order number for verification
            'value_b' => $order_data['event_id']
        );
        
        // Log request data for debugging (without password)
        $debug_data = $post_data;
        $debug_data['store_passwd'] = '***HIDDEN***';
        error_log('SSLCommerz Request Data: ' . print_r($debug_data, true));
        
        // Create payment session - SSLCommerz expects form-encoded data
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
            $error_message = $response->get_error_message();
            error_log('SSLCommerz Connection Error: ' . $error_message);
            return array('status' => 'error', 'message' => __('Failed to connect to SSLCommerz: ', 'dream-ticket') . $error_message);
        }
        
        $response_code = wp_remote_retrieve_response_code($response);
        $response_body = wp_remote_retrieve_body($response);
        
        // Log response for debugging
        error_log('SSLCommerz Response Code: ' . $response_code);
        error_log('SSLCommerz Response Body: ' . $response_body);
        
        if ($response_code !== 200) {
            error_log('SSLCommerz API Error. Code: ' . $response_code . ', Response: ' . $response_body);
            return array('status' => 'error', 'message' => __('SSLCommerz API returned error code: ', 'dream-ticket') . $response_code);
        }
        
        // Parse response - SSLCommerz returns key-value pairs
        parse_str($response_body, $response_data);
        
        // Log parsed response for debugging
        error_log('SSLCommerz Parsed Response: ' . print_r($response_data, true));
        
        // Check for success
        if (isset($response_data['status']) && strtoupper($response_data['status']) === 'SUCCESS' && isset($response_data['GatewayPageURL'])) {
            return array(
                'status' => 'success',
                'redirect_url' => $response_data['GatewayPageURL'],
                'sessionkey' => isset($response_data['sessionkey']) ? $response_data['sessionkey'] : '',
                'transaction_id' => $tran_id
            );
        } else {
            // Get error message from response
            $error_message = __('Failed to create payment session.', 'dream-ticket');
            
            // Check various possible error fields
            if (isset($response_data['failedreason']) && !empty($response_data['failedreason'])) {
                $error_message = __('SSLCommerz Error: ', 'dream-ticket') . $response_data['failedreason'];
            } elseif (isset($response_data['error']) && !empty($response_data['error'])) {
                $error_message = __('SSLCommerz Error: ', 'dream-ticket') . $response_data['error'];
            } elseif (isset($response_data['errorDesc']) && !empty($response_data['errorDesc'])) {
                $error_message = __('SSLCommerz Error: ', 'dream-ticket') . $response_data['errorDesc'];
            } elseif (isset($response_data['status'])) {
                $status = $response_data['status'];
                $error_message = sprintf(__('Payment session creation failed. Status: %s', 'dream-ticket'), $status);
                
                // Add more details if available
                if (isset($response_data['APIConnect']) && $response_data['APIConnect'] === 'INVALID_REQUEST') {
                    $error_message .= ' - ' . __('Invalid request parameters. Please check your SSLCommerz settings.', 'dream-ticket');
                }
            }
            
            // If response body contains error info, try to extract it
            if (strpos($response_body, 'error') !== false || strpos($response_body, 'failed') !== false) {
                // Try to find error message in response
                if (preg_match('/error[=:]\s*([^\n\r]+)/i', $response_body, $matches)) {
                    $error_message = __('SSLCommerz Error: ', 'dream-ticket') . trim($matches[1]);
                }
            }
            
            error_log('SSLCommerz Payment Creation Failed. Response: ' . $response_body);
            error_log('SSLCommerz Error Details: ' . print_r($response_data, true));
            
            return array('status' => 'error', 'message' => $error_message);
        }
    }
    
    /**
     * Process bank transfer (manual payment)
     */
    private function process_bank_transfer($order_data) {
        // Bank transfer is always pending until manually approved
        // Return success status so the order is created, but payment_status will be set to 'pending'
        return array(
            'status' => 'success',
            'transaction_id' => 'bank-' . time(),
            'message' => __('Payment pending. Please complete bank transfer.', 'dream-ticket')
        );
    }
    
    /**
     * Create payment (AJAX handler)
     */
    public function create_payment() {
        check_ajax_referer('dots_frontend_nonce', 'nonce');
        
        $order_data = array(
            'order_number' => sanitize_text_field($_POST['order_number']),
            'event_id' => intval($_POST['event_id']),
            'event_name' => sanitize_text_field($_POST['event_name']),
            'total_price' => floatval($_POST['total_price']),
            'payment_method' => sanitize_text_field($_POST['payment_method']),
            'return_url' => esc_url_raw($_POST['return_url'])
        );
        
        $result = $this->process_payment($order_data);
        wp_send_json($result);
    }
    
    /**
     * Verify payment (AJAX handler)
     */
    public function verify_payment() {
        check_ajax_referer('dots_frontend_nonce', 'nonce');
        
        $payment_id = isset($_POST['payment_id']) ? sanitize_text_field($_POST['payment_id']) : '';
        $payment_method = isset($_POST['payment_method']) ? sanitize_text_field($_POST['payment_method']) : '';
        $order_number = isset($_POST['order_number']) ? sanitize_text_field($_POST['order_number']) : '';
        
        if (empty($payment_id) || empty($payment_method) || empty($order_number)) {
            wp_send_json_error(array('message' => __('Missing payment information.', 'dream-ticket')));
        }
        
        global $wpdb;
        $table_sales = $wpdb->prefix . 'dots_sales';
        
        // Get the sale record
        $sale = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_sales WHERE order_number = %s",
            $order_number
        ));
        
        if (!$sale) {
            wp_send_json_error(array('message' => __('Order not found.', 'dream-ticket')));
        }
        
        if ($payment_method === 'paypal') {
            $settings = get_option('dots_settings', array());
            $paypal_client_id = isset($settings['paypal_client_id']) ? $settings['paypal_client_id'] : '';
            $paypal_secret = isset($settings['paypal_secret']) ? $settings['paypal_secret'] : '';
            $paypal_mode = isset($settings['paypal_mode']) ? $settings['paypal_mode'] : 'sandbox';
            
            if (empty($paypal_client_id) || empty($paypal_secret)) {
                wp_send_json_error(array('message' => __('PayPal credentials not configured.', 'dream-ticket')));
            }
            
            $api_url = $paypal_mode === 'live' 
                ? 'https://api-m.paypal.com' 
                : 'https://api-m.sandbox.paypal.com';
            
            $access_token = $this->get_paypal_access_token($api_url, $paypal_client_id, $paypal_secret);
            
            if ($access_token) {
                $response = wp_remote_get($api_url . '/v2/checkout/orders/' . $payment_id, array(
                    'headers' => array(
                        'Authorization' => 'Bearer ' . $access_token,
                        'Content-Type' => 'application/json'
                    ),
                    'timeout' => 30
                ));
                
                if (!is_wp_error($response)) {
                    $body = json_decode(wp_remote_retrieve_body($response), true);
                    
                    if (isset($body['status']) && $body['status'] === 'COMPLETED') {
                        // Update sale record
                        $wpdb->update($table_sales, array(
                            'payment_status' => 'completed',
                            'payment_method' => 'paypal',
                            'transaction_id' => $payment_id
                        ), array('order_number' => $order_number));
                        
                        // Update tickets available
                        $wpdb->query($wpdb->prepare(
                            "UPDATE {$wpdb->prefix}dots_events SET tickets_available = tickets_available - %d WHERE id = %d",
                            $sale->quantity, $sale->event_id
                        ));
                        
                        wp_send_json_success(array(
                            'message' => __('Payment verified successfully.', 'dream-ticket'),
                            'redirect_url' => home_url('/dream-tickets/order/' . $order_number)
                        ));
                    } elseif (isset($body['status'])) {
                        // Payment is still pending or in another state
                        wp_send_json_error(array('message' => sprintf(__('Payment status: %s', 'dream-ticket'), $body['status'])));
                    }
                }
            }
        } elseif ($payment_method === 'stripe') {
            $settings = get_option('dots_settings', array());
            $stripe_secret_key = isset($settings['stripe_secret_key']) ? $settings['stripe_secret_key'] : '';
            
            if (empty($stripe_secret_key)) {
                wp_send_json_error(array('message' => __('Stripe credentials not configured.', 'dream-ticket')));
            }
            
            // Verify Stripe payment intent
            $response = wp_remote_get('https://api.stripe.com/v1/payment_intents/' . $payment_id, array(
                'headers' => array(
                    'Authorization' => 'Bearer ' . $stripe_secret_key
                ),
                'timeout' => 30
            ));
            
            if (!is_wp_error($response)) {
                $body = json_decode(wp_remote_retrieve_body($response), true);
                
                if (isset($body['status']) && $body['status'] === 'succeeded') {
                    // Update sale record
                    $wpdb->update($table_sales, array(
                        'payment_status' => 'completed',
                        'payment_method' => 'stripe',
                        'transaction_id' => $payment_id
                    ), array('order_number' => $order_number));
                    
                    // Update tickets available
                    $wpdb->query($wpdb->prepare(
                        "UPDATE {$wpdb->prefix}dots_events SET tickets_available = tickets_available - %d WHERE id = %d",
                        $sale->quantity, $sale->event_id
                    ));
                    
                    wp_send_json_success(array(
                        'message' => __('Payment verified successfully.', 'dream-ticket'),
                        'redirect_url' => home_url('/dream-tickets/order/' . $order_number)
                    ));
                } elseif (isset($body['status'])) {
                    wp_send_json_error(array('message' => sprintf(__('Payment status: %s', 'dream-ticket'), $body['status'])));
                }
            }
        }
        
        wp_send_json_error(array('message' => __('Payment verification failed.', 'dream-ticket')));
    }
    
    /**
     * SSLCommerz callback handler
     */
    public function sslcommerz_callback() {
        global $wpdb;
        $table_sales = $wpdb->prefix . 'dots_sales';
        
        // Get order number from request
        $order_number = isset($_GET['order_number']) ? sanitize_text_field($_GET['order_number']) : '';
        $status = isset($_GET['status']) ? sanitize_text_field($_GET['status']) : '';
        
        // Also check POST data (SSLCommerz sends data via POST)
        if (empty($order_number) && isset($_POST['value_a'])) {
            $order_number = sanitize_text_field($_POST['value_a']);
        }
        
        if (empty($order_number)) {
            wp_die(__('Invalid request.', 'dream-ticket'));
        }
        
        // Get the sale record
        $sale = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_sales WHERE order_number = %s",
            $order_number
        ));
        
        if (!$sale) {
            wp_die(__('Order not found.', 'dream-ticket'));
        }
        
        $settings = get_option('dots_settings', array());
        $store_id = isset($settings['sslcommerz_store_id']) ? $settings['sslcommerz_store_id'] : '';
        $store_password = isset($settings['sslcommerz_store_password']) ? $settings['sslcommerz_store_password'] : '';
        $mode = isset($settings['sslcommerz_mode']) ? $settings['sslcommerz_mode'] : 'sandbox';
        
        // Verify payment with SSLCommerz
        $tran_id = isset($_POST['tran_id']) ? sanitize_text_field($_POST['tran_id']) : '';
        $amount = isset($_POST['amount']) ? sanitize_text_field($_POST['amount']) : '';
        $currency = isset($_POST['currency']) ? sanitize_text_field($_POST['currency']) : '';
        $val_id = isset($_POST['val_id']) ? sanitize_text_field($_POST['val_id']) : '';
        
        if (!empty($tran_id) && !empty($val_id)) {
            // Verify payment with SSLCommerz validation API
            $api_url = $mode === 'live' 
                ? 'https://securepay.sslcommerz.com' 
                : 'https://sandbox.sslcommerz.com';
            
            $verify_data = array(
                'store_id' => $store_id,
                'store_passwd' => $store_password,
                'val_id' => $val_id,
                'format' => 'json'
            );
            
            $verify_response = wp_remote_post($api_url . '/validator/api/validationserverAPI.php', array(
                'method' => 'POST',
                'body' => $verify_data,
                'timeout' => 30,
                'sslverify' => true
            ));
            
            if (!is_wp_error($verify_response)) {
                $verify_body = json_decode(wp_remote_retrieve_body($verify_response), true);
                
                if (isset($verify_body['status']) && $verify_body['status'] === 'VALID' || $verify_body['status'] === 'VALIDATED') {
                    // Payment is valid
                    $wpdb->update($table_sales, array(
                        'payment_status' => 'completed',
                        'payment_method' => 'sslcommerz',
                        'transaction_id' => $val_id
                    ), array('order_number' => $order_number));
                    
                    // Update tickets available
                    $wpdb->query($wpdb->prepare(
                        "UPDATE {$wpdb->prefix}dots_events SET tickets_available = tickets_available - %d WHERE id = %d",
                        $sale->quantity, $sale->event_id
                    ));
                    
                    // Redirect to success page
                    wp_redirect(home_url('/dream-tickets/order/' . $order_number));
                    exit;
                } else {
                    // Payment validation failed
                    $wpdb->update($table_sales, array(
                        'payment_status' => 'failed',
                        'payment_method' => 'sslcommerz',
                        'transaction_id' => $val_id
                    ), array('order_number' => $order_number));
                    
                    wp_redirect(home_url('/dream-tickets/order/' . $order_number . '?payment=failed'));
                    exit;
                }
            }
        }
        
        // Handle status from URL parameter (for success/fail/cancel redirects)
        if ($status === 'success' || $status === 'ipn') {
            // If validation data is not available, mark as processing
            if ($sale->payment_status === 'processing' || $sale->payment_status === 'pending') {
                wp_redirect(home_url('/dream-tickets/order/' . $order_number));
                exit;
            }
        } elseif ($status === 'failed' || $status === 'cancelled') {
            $wpdb->update($table_sales, array(
                'payment_status' => 'failed',
                'payment_method' => 'sslcommerz'
            ), array('order_number' => $order_number));
            
            wp_redirect(home_url('/dream-tickets/order/' . $order_number . '?payment=failed'));
            exit;
        }
        
        // Default redirect
        wp_redirect(home_url('/dream-tickets/order/' . $order_number));
        exit;
    }
}

