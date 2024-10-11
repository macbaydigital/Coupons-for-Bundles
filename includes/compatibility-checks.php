<?php
/**
 * Compatibility Checks for Coupons for Bundles by Macbay
 *
 * @package Coupons_For_Bundles_By_Macbay
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class CFBBM_Compatibility_Checks {

    /**
     * Run compatibility checks
     */
    public static function run_checks() {
        self::check_woocommerce_subscriptions();
        self::check_compatible_plugins();
    }

    /**
     * Check if WooCommerce Subscriptions is active
     */
    private static function check_woocommerce_subscriptions() {
        if ( ! class_exists( 'WC_Subscriptions' )
    private static function check_woocommerce_subscriptions() {
        if ( ! class_exists( 'WC_Subscriptions' ) ) {
            add_action( 'admin_notices', array( __CLASS__, 'woocommerce_subscriptions_missing_notice' ) );
        }
    }

    /**
     * Check for compatible plugins
     */
    private static function check_compatible_plugins() {
        $compatible_plugins = array(
            'polylang/polylang.php' => 'Polylang',
            'translatepress-multilingual/index.php' => 'TranslatePress',
            'woocommerce-germanized/woocommerce-germanized.php' => 'Germanized',
            'disable-dashboard-for-woocommerce/disable-bloat.php' => 'Disable Bloat for WordPress & WooCommerce',
            'groundhogg/groundhogg.php' => 'Groundhogg',
            'fluent-crm/fluent-crm.php' => 'FluentCRM',
            'fluent-support/fluent-support.php' => 'FluentSupport',
            'wp-fusion-lite/wp-fusion-lite.php' => 'WP Fusion Lite',
            'launchflowsio/launchflows.php' => 'Launchflows',
            'woofunnels-aero-checkout/woofunnels-aero-checkout.php' => 'FunnelKit',
            'role-based-prices-for-woocommerce/role-based-prices-for-woocommerce.php' => 'Role and Customer Based Pricing for WooCommerce',
            'wp24-domain-check/wp24-domain-check.php' => 'WP24 Domain Check'
        );

        foreach ( $compatible_plugins as $plugin => $name ) {
            if ( is_plugin_active( $plugin ) ) {
                add_action( 'admin_notices', function() use ( $name ) {
                    self::compatible_plugin_notice( $name );
                });
            }
        }
    }

    /**
     * Admin notice for missing WooCommerce Subscriptions
     */
    public static function woocommerce_subscriptions_missing_notice() {
        ?>
        <div class="error">
            <p><?php _e( 'Coupons for Bundles by Macbay requires WooCommerce Subscriptions to be installed and active.', 'coupons-for-bundles-by-macbay' ); ?></p>
        </div>
        <?php
    }

    /**
     * Admin notice for compatible plugins
     */
    public static function compatible_plugin_notice( $plugin_name ) {
        ?>
        <div class="notice notice-info is-dismissible">
            <p><?php printf( __( 'Coupons for Bundles by Macbay is compatible with %s.', 'coupons-for-bundles-by-macbay' ), $plugin_name ); ?></p>
        </div>
        <?php
    }
}
