<?php
/**
 * Discount Logic for Coupons for Bundles by Macbay
 *
 * @package Coupons_For_Bundles_By_Macbay
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class CFBBM_Discount_Logic {

    /**
     * Apply discounts to cart
     */
    public static function apply_discounts( $cart ) {
        $options = get_option( 'cfbbm_options' );
        $hosting_products = isset( $options['hosting_products'] ) ? $options['hosting_products'] : array();

        $cart_hosting_products = self::get_cart_hosting_products( $cart );
        $cart_domain_products = self::get_cart_domain_products( $cart );

        foreach ( $cart_hosting_products as $hosting_product_id ) {
            if ( isset( $hosting_products[$hosting_product_id] ) ) {
                $discountable_domains = $hosting_products[$hosting_product_id];
                self::apply_domain_discounts( $cart, $discountable_domains, $cart_domain_products );
            }
        }
    }

    /**
     * Get hosting products in cart
     */
    private static function get_cart_hosting_products( $cart ) {
        $hosting_products = array();
        foreach ( $cart->get_cart() as $cart_item_key => $cart_item ) {
            $product_id = $cart_item['product_id'];
            if ( has_term( 'hosting', 'product_cat', $product_id ) && self::is_yearly_subscription( $cart_item['data'] ) ) {
                $hosting_products[] = $product_id;
            }
        }
        return $hosting_products;
    }

    /**
     * Get domain products in cart
     */
    private static function get_cart_domain_products( $cart ) {
        $domain_products = array();
        foreach ( $cart->get_cart() as $cart_item_key => $cart_item ) {
            $product_id = $cart_item['product_id'];
            if ( has_term( 'domains', 'product_cat', $product_id ) ) {
                $domain_products[$cart_item_key] = $product_id;
            }
        }
        return $domain_products;
    }

    /**
     * Apply discounts to domain products
     */
    private static function apply_domain_discounts( $cart, $discountable_domains, $cart_domain_products ) {
        foreach ( $cart_domain_products as $cart_item_key => $domain_product_id ) {
            if ( in_array( $domain_product_id, $discountable_domains ) ) {
                $cart_item = $cart->get_cart_item( $cart_item_key );
                $price = $cart_item['data']->get_price();
                $cart->add_fee( __( 'Domain Discount', 'coupons-for-bundles-by-macbay' ), -$price );
            }
        }
    }

    /**
     * Check if product is a yearly subscription
     */
    private static function is_yearly_subscription( $product ) {
        if ( $product->is_type( 'subscription' ) ) {
            $subscription_period = $product->get_meta( '_subscription_period' );
            $subscription_period_interval = $product->get_meta( '_subscription_period_interval' );
            return $subscription_period === 'year' && $subscription_period_interval == 1;
        }
        return false;
    }
}
