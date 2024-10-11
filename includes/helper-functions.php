<?php
/**
 * Helper Functions for Coupons for Bundles by Macbay
 *
 * @package Coupons_For_Bundles_By_Macbay
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Check if a product is a yearly subscription
 *
 * @param WC_Product $product
 * @return bool
 */
function cfbbm_is_yearly_subscription( $product ) {
    if ( $product->is_type( 'subscription' ) ) {
        $subscription_period = $product->get_meta( '_subscription_period' );
        $subscription_period_interval = $product->get_meta( '_subscription_period_interval' );
        return $subscription_period === 'year' && $subscription_period_interval == 1;
    }
    return false;
}

/**
 * Get all hosting products
 *
 * @return array
 */
function cfbbm_get_hosting_products() {
    $args = array(
        'category' => array( 'hosting' ),
        'type' => 'subscription',
        'limit' => -1,
    );
    $products = wc_get_products( $args );
    $hosting_products = array();
    foreach ( $products as $product ) {
        if ( cfbbm_is_yearly_subscription( $product ) ) {
            $hosting_products[$product->get_id()] = $product->get_name();
        }
    }
    return $hosting_products;
}

/**
 * Get all domain products
 *
 * @return array
 */
function cfbbm_get_domain_products() {
    $args = array(
        'category' => array( 'domains' ),
        'type' => 'subscription',
        'limit' => -1,
    );
    $products = wc_get_products( $args );
    $domain_products = array();
    foreach ( $products as $product ) {
        if ( cfbbm_is_yearly_subscription( $product ) ) {
            $domain_products[$product->get_id()] = $product->get_name();
        }
    }
    return $domain_products;
}
