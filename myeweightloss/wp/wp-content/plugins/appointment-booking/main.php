<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/*
Plugin Name: Bookly
Plugin URI: http://booking-wp-plugin.com
Description: Bookly Plugin – is a great easy-to-use and easy-to-manage booking tool for service providers who think about their customers. The plugin supports a wide range of services provided by business and individuals who offer reservations through websites. Set up any reservation quickly, pleasantly and easily with Bookly!
Version: 8.0
Author: Ladela Interactive
Author URI: http://www.ladela.com
Text Domain: bookly
Domain Path: /languages
License: Commercial
*/

define( 'AB_PATH', __DIR__ );

include_once 'includes.php';

// Fix possible errors (appearing if "Nextgen Gallery" Plugin is installed) when Bookly is being updated.
add_filter( 'http_request_args', function ( $args ) { $args['reject_unsafe_urls'] = false; return $args; } );

add_action( 'plugins_loaded', function () {
    // I10n.
    load_plugin_textdomain( 'bookly', false, basename( AB_PATH ) . '/languages' );
    // Update DB.
    bookly_plugin_update_db();
} );

AB_Plugin::registerHooks();

is_admin() ? new AB_Backend() : new AB_Frontend();