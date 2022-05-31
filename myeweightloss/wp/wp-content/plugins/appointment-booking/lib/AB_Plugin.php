<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

abstract class AB_Plugin
{
    protected static $version;

    public static function activate( $register_hook = true )
    {
        $installer = new AB_Installer();
        $installer->install();
        $register_hook ? do_action( 'bookly_activate' ) : null;
    }

    public static function deactivate( $register_hook = true )
    {
        unload_textdomain( 'bookly' );
        $register_hook ? do_action( 'bookly_deactivate' ) : null;
    }

    public static function uninstall( $register_hook = true )
    {
        $installer = new AB_Installer();
        $installer->uninstall();
        $register_hook ? do_action( 'bookly_uninstall' ) : null;
    }

    public static function version()
    {
        if ( self::$version == null ) {
            if ( ! function_exists( 'get_plugin_data' ) ) {
                require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
            }
            $plugin_data = get_plugin_data( AB_PATH . '/main.php' );
            self::$version = $plugin_data['Version'];
        }

        return self::$version;
    }

    public static function registerHooks()
    {
        if ( is_admin() ) {
            register_activation_hook( AB_PATH . '/main.php',   array( __CLASS__, 'activate' ) );
            register_deactivation_hook( AB_PATH . '/main.php', array( __CLASS__, 'deactivate' ) );
            register_uninstall_hook( AB_PATH . '/main.php',    array( __CLASS__, 'uninstall' ) );
            if ( get_option( 'ab_envato_purchase_code' ) ) {
                new AB_PluginUpdateChecker(
                    'http://booking-wp-plugin.com/index.php',
                    AB_PATH . '/main.php',
                    basename( AB_PATH ),
                    24
                );
            } else {
                add_filter( 'plugin_row_meta', function ( $links, $plugin ) {
                    if ( $plugin == 'appointment-booking/main.php' )
                        return array_merge( $links, array( 0 => '<span class="dashicons dashicons-info"></span> ' . sprintf( __( 'To update - enter the <a href="%s">Purchase Code</a>', 'bookly' ), AB_Utils::escAdminUrl( AB_SettingsController::page_slug, array( 'type' => '_purchase_code' ) ) ) ) );
                    return $links;
                }, 10, 2 );
            }
        }
    }

}