<?php
/**
 * Plugin Name: Dream Online Ticket Selling
 * Plugin URI: https://example.com/dream-online-ticket-selling
 * Description: Enable users to sell event tickets online through a WordPress site with comprehensive admin controls and customer management.
 * Version: 1.0.0
 * Author: Your Name
 * Author URI: https://example.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: dream-ticket
 * Domain Path: /languages
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('DOTS_VERSION', '1.0.0');
define('DOTS_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('DOTS_PLUGIN_URL', plugin_dir_url(__FILE__));
define('DOTS_PLUGIN_BASENAME', plugin_basename(__FILE__));

// Include required files
require_once DOTS_PLUGIN_DIR . 'includes/class-dots-database.php';
require_once DOTS_PLUGIN_DIR . 'includes/class-dots-admin.php';
require_once DOTS_PLUGIN_DIR . 'includes/class-dots-frontend.php';
require_once DOTS_PLUGIN_DIR . 'includes/class-dots-ajax.php';
require_once DOTS_PLUGIN_DIR . 'includes/class-dots-payment.php';

/**
 * Main plugin class
 */
class Dream_Online_Ticket_Selling {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        $this->init_hooks();
    }
    
    private function init_hooks() {
        // Activation and deactivation hooks
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
        
        // Initialize plugin
        add_action('plugins_loaded', array($this, 'init'));
    }
    
    public function activate() {
        // Create database tables
        DOTS_Database::create_tables();
        
        // Set default options
        $default_options = array(
            'currency' => 'USD',
            'currency_symbol' => '$',
            'timezone' => wp_timezone_string(),
            'max_tickets_per_customer' => 10,
            'enable_captcha' => false,
            'email_notifications' => true,
            'admin_email' => get_option('admin_email'),
        );
        
        add_option('dots_settings', $default_options);
        
        // Flush rewrite rules
        flush_rewrite_rules();
    }
    
    public function deactivate() {
        // Flush rewrite rules
        flush_rewrite_rules();
    }
    
    public function init() {
        // Load text domain for translations
        load_plugin_textdomain('dream-ticket', false, dirname(DOTS_PLUGIN_BASENAME) . '/languages');
        
        // Initialize admin
        if (is_admin()) {
            new DOTS_Admin();
        }
        
        // Initialize frontend (always, for shortcodes to work)
        new DOTS_Frontend();
        
        // Initialize AJAX handlers
        new DOTS_Ajax();
        
        // Initialize payment handler
        new DOTS_Payment();
    }
}

// Initialize the plugin
Dream_Online_Ticket_Selling::get_instance();
