<?php
/**
 * Plugin Updater for Coupons for Bundles by Macbay
 *
 * @package Coupons_For_Bundles_By_Macbay
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class CFBBM_Plugin_Updater {

    private $github_username;
    private $github_repo;

    public function __construct() {
        $this->github_username = 'your-github-username'; // Replace with your GitHub username
        $this->github_repo = 'your-github-repo'; // Replace with your GitHub repo name

        add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'check_for_update' ) );
    }

    public function check_for_update( $transient ) {
        if ( empty( $transient->checked ) ) {
            return $transient;
        }

        $remote_version = $this->get_remote_version();

        if ( $remote_version && version_compare( CFBBM_VERSION, $remote_version, '<' ) ) {
            $plugin_data = get_plugin_data( CFBBM_PLUGIN_DIR . 'coupons-for-bundles-by-macbay.php' );

            $obj = new stdClass();
            $obj->slug = 'coupons-for-bundles-by-macbay';
            $obj->new_version = $remote_version;
            $obj->url = 'https://github.com/' . $this->github_username . '/' . $this->github_repo;
            $obj->package = 'https://github.com/' . $this->github_username . '/' . $this->github_repo . '/archive/master.zip';
            $obj->name = $plugin_data['Name'];

            $transient->response['coupons-for-bundles-by-macbay/coupons-for-bundles-by-macbay.php'] = $obj;
        }

        return $transient;
    }

    private function get_remote_version() {
        $request = wp_remote_get( 'https://api.github.com/repos/' . $this->github_username . '/' . $this->github_repo . '/releases/latest' );

        if ( is_wp_error( $request ) ) {
            return false;
        }

        $response = wp_remote_retrieve_body( $request );
        $data = json_decode( $response );

        if ( empty( $data->tag_name ) ) {
            return false;
        }

        return $data->tag_name;
    }
}

new CFBBM_Plugin_Updater();
