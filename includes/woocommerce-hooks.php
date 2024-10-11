<?php
/**
 * WooCommerce Hooks for Coupons for Bundles by Macbay
 *
 * @package Coupons_For_Bundles_By_Macbay
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class CFBBM_WooCommerce_Hooks {

    /**
     * Initialize the hooks
     */
    public static function init() {
        add_action( 'woocommerce_cart_calculate_fees', array( 'CFBBM_Discount_Logic', 'apply_discounts' ) );
        add_action( 'woocommerce_subscription_status_updated', array( __CLASS__, 'update_domain_discounts' ), 10, 3 );
    }

    /**
     * Update domain discounts when subscription status changes
     */
    public static function update_domain_discounts( $subscription, $new_status, $old_status ) {
        if ( $new_status === 'active' && $old_status !== 'active' ) {
            self::apply_domain_discounts( $subscription );
        } elseif ( $new_status !== 'active' && $old_status === 'active' ) {
            self::remove_domain_discounts( $subscription );
        }
    }

    /**
     * Apply domain discounts for active subscription
     */
    private static function apply_domain_discounts( $subscription ) {
        $options = get_option( 'cfbbm_options' );
        $hosting_products = isset( $options['hosting_products'] ) ? $options['hosting_products'] : array();

        foreach ( $subscription->get_items() as $item ) {
            $product_id = $item->get_product_id();
            if ( isset( $hosting_products[$product_id] ) ) {
                $discountable_domains = $hosting_products[$product_id];
                foreach ( $discountable_domains as $domain_id ) {
                    $domain_subscription = self::get_domain_subscription( $domain_id, $subscription->get_user_id() );
                    if ( $domain_subscription ) {
                        $domain_subscription->update_status( 'active' );
                        $domain_subscription->set_price( 0 );
                        $domain_subscription->save();
                    }
                }
            }
        }
    }

    /**
     * Remove domain discounts when subscription becomes inactive
     */
    private static function remove_domain_discounts( $subscription ) {
        $options = get_option( 'cfbbm_options' );
        $hosting_products = isset( $options['hosting_products'] ) ? $options['hosting_products'] : array();

        foreach ( $subscription->get_items() as $item ) {
            $product_id = $item->get_product_id();
            if ( isset( $hosting_products[$product_id] ) ) {
                $discountable_domains = $hosting_products[$product_id];
                foreach ( $discountable_domains as $domain_id ) {
                    $domain_subscription = self::get_domain_subscription( $domain_id, $subscription->get_user_id() );
                    if ( $domain_subscription ) {
                        $domain_product = wc_get_product( $domain_id );
                        $domain_subscription->set_price( $domain_product->get_price() );
                        $domain_subscription->save();
                    }
                }
            }
        }
    }

    /**
     * Get domain subscription for a user
     */
    private static function get_domain_subscription( $domain_id, $user_id ) {
        $subscriptions = wcs_get_subscriptions( array(
            'customer_id' => $user_id,
            'product_id' => $domain_id,
            'subscription_status' => 'any',
        ) );

        return !empty( $subscriptions ) ? current( $subscriptions ) : false;
    }
}
