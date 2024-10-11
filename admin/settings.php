<?php
/**
 * Admin Settings for Coupons for Bundles by Macbay
 *
 * @package Coupons_For_Bundles_By_Macbay
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class CFBBM_Admin_Settings {

    /**
     * Constructor
     */
    public function __construct() {
        add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
        add_action( 'admin_init', array( $this, 'register_settings' ) );
    }

    /**
     * Add options page
     */
    public function add_admin_menu() {
        add_submenu_page(
            'woocommerce',
            __( 'Coupon Bundles', 'coupons-for-bundles-by-macbay' ),
            __( 'Coupon Bundles', 'coupons-for-bundles-by-macbay' ),
            'manage_woocommerce',
            'cfbbm-settings',
            array( $this, 'create_admin_page' )
        );
    }

    /**
     * Options page callback
     */
    public function create_admin_page() {
        ?>
        <div class="wrap">
            <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
            <form method="post" action="options.php">
            <?php
                settings_fields( 'cfbbm_options' );
                do_settings_sections( 'cfbbm-settings' );
                submit_button();
            ?>
            </form>
        </div>
        <?php
    }

    /**
     * Register and add settings
     */
    public function register_settings() {
        register_setting(
            'cfbbm_options',
            'cfbbm_options',
            array( $this, 'sanitize' )
        );

        add_settings_section(
            'cfbbm_hosting_section',
            __( 'Hosting Products', 'coupons-for-bundles-by-macbay' ),
            array( $this, 'print_hosting_section_info' ),
            'cfbbm-settings'
        );

        $hosting_products = $this->get_hosting_products();
        foreach ( $hosting_products as $product_id => $product_name ) {
            add_settings_field(
                'hosting_' . $product_id,
                $product_name,
                array( $this, 'hosting_product_callback' ),
                'cfbbm-settings',
                'cfbbm_hosting_section',
                array( 'product_id' => $
                'hosting_' . $product_id,
                $product_name,
                array( $this, 'hosting_product_callback' ),
                'cfbbm-settings',
                'cfbbm_hosting_section',
                array( 'product_id' => $product_id )
            );
        }
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize( $input ) {
        $new_input = array();
        if( isset( $input['hosting_products'] ) ) {
            foreach( $input['hosting_products'] as $product_id => $domains ) {
                $new_input['hosting_products'][$product_id] = array_map( 'sanitize_text_field', $domains );
            }
        }
        return $new_input;
    }

    /**
     * Print the Section text
     */
    public function print_hosting_section_info() {
        print __( 'Select the domain products that should be discounted for each hosting product:', 'coupons-for-bundles-by-macbay' );
    }

    /**
     * Get all hosting products
     */
    private function get_hosting_products() {
        $args = array(
            'category' => array( 'hosting' ),
            'type' => 'subscription',
            'limit' => -1,
        );
        $products = wc_get_products( $args );
        $hosting_products = array();
        foreach ( $products as $product ) {
            $hosting_products[$product->get_id()] = $product->get_name();
        }
        return $hosting_products;
    }

    /**
     * Get all domain products
     */
    private function get_domain_products() {
        $args = array(
            'category' => array( 'domains' ),
            'type' => 'subscription',
            'limit' => -1,
        );
        $products = wc_get_products( $args );
        $domain_products = array();
        foreach ( $products as $product ) {
            $domain_products[$product->get_id()] = $product->get_name();
        }
        return $domain_products;
    }

    /**
     * Callback for hosting product fields
     */
    public function hosting_product_callback( $args ) {
        $product_id = $args['product_id'];
        $options = get_option( 'cfbbm_options' );
        $domain_products = $this->get_domain_products();
        ?>
        <select name="cfbbm_options[hosting_products][<?php echo esc_attr( $product_id ); ?>][]" multiple style="width: 300px; height: 150px;">
            <?php foreach ( $domain_products as $domain_id => $domain_name ) : ?>
                <option value="<?php echo esc_attr( $domain_id ); ?>" <?php selected( isset( $options['hosting_products'][$product_id] ) && in_array( $domain_id, $options['hosting_products'][$product_id] ) ); ?>>
                    <?php echo esc_html( $domain_name ); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php
    }
}

new CFBBM_Admin_Settings();
