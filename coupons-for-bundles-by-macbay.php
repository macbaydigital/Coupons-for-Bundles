<?php
/**
 * Plugin Name: Coupons for Bundles by Macbay
 * Plugin URI: https://macbay.net
 * Description: Applies automatic discounts to domain products when purchased with specific hosting products.
 * Version: 0.1
 * Author: Macbay Digital
 * Author URI: https://macbay.net
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: coupons-for-bundles-by-macbay
 * Domain Path: /languages
 * WC requires at least: 3.0.0
 * WC tested up to: 5.5.2
 *
 * @package Coupons_For_Bundles_By_Macbay
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

// Define plugin constants.
define( 'CFBBM_VERSION', '0.1' );
define( 'CFBBM_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'CFBBM_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

/**
 * Check if WooCommerce is active
 */
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
    
    // Include necessary files
    require_once CFBBM_PLUGIN_DIR . 'includes/helper-functions.php';
    require_once CFBBM_PLUGIN_DIR . 'includes/discount-logic.php';
    require_once CFBBM_PLUGIN_DIR . 'includes/woocommerce-hooks.php';
    require_once CFBBM_PLUGIN_DIR . 'includes/plugin-updater.php';
    require_once CFBBM_PLUGIN_DIR . 'includes/compatibility-checks.php';
    
    if ( is_admin() ) {
        require_once CFBBM_PLUGIN_DIR . 'admin/settings.php';
    }

    /**
     * Initialize the plugin
     */
    function cfbbm_init() {
        // Load plugin textdomain
        load_plugin_textdomain( 'coupons-for-bundles-by-macbay', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
        
        // Initialize admin settings
        if ( is_admin() ) {
            new CFBBM_Admin_Settings();
        }

        // Initialize WooCommerce hooks
        CFBBM_WooCommerce_Hooks::init();

        // Initialize plugin updater
        CFBBM_Plugin_Updater::init();

        // Run compatibility checks
        CFBBM_Compatibility_Checks::run_checks();
    }
    add_action( 'plugins_loaded', 'cfbbm_init' );

} else {
    /**
     * Display admin notice if WooCommerce is not active
     */
    function cfbbm_woocommerce_not_active_notice() {
        ?>
        <div class="error">
            <p><?php esc_html_e( 'Coupons for Bundles by Macbay requires WooCommerce to be installed and active.', 'coupons-for-bundles-by-macbay' ); ?></p>
        </div>
        <?php
    }
    add_action( 'admin_notices', 'cfbbm_woocommerce_not_active_notice' );
}
