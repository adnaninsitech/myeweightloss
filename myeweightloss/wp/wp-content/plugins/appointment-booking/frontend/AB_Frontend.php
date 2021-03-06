<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class AB_Frontend
{
    public function __construct()
    {
        add_action( 'wp_loaded', array( $this, 'init' ) );
        add_action( get_option( 'ab_settings_link_assets_method' ) == 'enqueue' ? 'wp_enqueue_scripts' : 'wp_loaded', array( $this, 'linkAssets' ) );

        // Init controllers.
        $this->bookingController = new AB_BookingController();
        $this->paypalController  = new AB_PayPalController();
        $this->twocheckoutController     = new AB_2CheckoutController();
        $this->payulatamController       = new AB_PayuLatamController();
        $this->paysonController          = new AB_PaysonController();
        $this->wooCommerceController     = new AB_WooCommerceController();
        $this->customerProfileController = new AB_CustomerProfileController();
        // Register shortcodes.
        add_shortcode( 'bookly-form', array( $this->bookingController, 'renderShortCode' ) );
        /** @deprecated [ap-booking] */
        add_shortcode( 'ap-booking', array( $this->bookingController, 'renderShortCode' ) );
        add_shortcode( 'bookly-appointments-list', array( $this->customerProfileController, 'renderShortCode' ) );
    }

    /**
     * Link assets.
     */
    public function linkAssets()
    {
        /** @var WP_Locale $wp_locale */
        global $wp_locale;

        $link_style  = get_option( 'ab_settings_link_assets_method' ) == 'enqueue' ? 'wp_enqueue_style'  : 'wp_register_style';
        $link_script = get_option( 'ab_settings_link_assets_method' ) == 'enqueue' ? 'wp_enqueue_script' : 'wp_register_script';
        if ( get_option( 'ab_settings_phone_default_country' ) != 'disabled' ) {
            call_user_func( $link_style, 'ab-intlTelInput', plugins_url( 'resources/css/intlTelInput.css', __FILE__ ), array(), AB_Plugin::version() );
        }
        call_user_func( $link_style, 'ab-reset',        plugins_url( 'resources/css/ab-reset.css', __FILE__ ), array(), AB_Plugin::version() );
        call_user_func( $link_style, 'ab-ladda-min',    plugins_url( 'resources/css/ladda.min.css', __FILE__ ), array(), AB_Plugin::version() );
        call_user_func( $link_style, 'ab-main',         plugins_url( 'resources/css/bookly-main.css', __FILE__ ), get_option( 'ab_settings_phone_default_country' ) != 'disabled' ? array( 'ab-intlTelInput' ) : array(), AB_Plugin::version() );
        call_user_func( $link_style, 'ab-picker-classic-date', plugins_url( 'resources/css/picker.classic.date.css', __FILE__ ), array(), AB_Plugin::version() );
        call_user_func( $link_style, 'ab-picker-date',  plugins_url( 'resources/css/picker.classic.css', __FILE__ ), array(), AB_Plugin::version() );
        call_user_func( $link_style, 'ab-picker',       plugins_url( 'resources/css/ab-picker.css', __FILE__ ), array(), AB_Plugin::version() );
        call_user_func( $link_style, 'ab-columnizer',   plugins_url( 'resources/css/ab-columnizer.css', __FILE__ ), array(), AB_Plugin::version() );
        if ( is_rtl() ) {
            call_user_func( $link_style, 'ab-rtl',      plugins_url( 'resources/css/bookly-rtl.css', __FILE__ ), array(), AB_Plugin::version() );
        }
        call_user_func( $link_script, 'ab-spin',        plugins_url( 'resources/js/spin.min.js', __FILE__ ), array(), AB_Plugin::version() );
        call_user_func( $link_script, 'ab-ladda',       plugins_url( 'resources/js/ladda.min.js', __FILE__ ), array( 'ab-spin' ), AB_Plugin::version() );
        call_user_func( $link_script, 'ab-hammer',      plugins_url( 'resources/js/hammer.min.js', __FILE__ ), array( 'jquery' ), AB_Plugin::version() );
        call_user_func( $link_script, 'ab-jq-hammer',   plugins_url( 'resources/js/jquery.hammer.min.js', __FILE__ ), array( 'jquery' ), AB_Plugin::version() );
        call_user_func( $link_script, 'ab-picker',      plugins_url( 'resources/js/picker.js', __FILE__ ), array( 'jquery' ), AB_Plugin::version() );
        call_user_func( $link_script, 'ab-picker-date', plugins_url( 'resources/js/picker.date.js', __FILE__ ), array( 'ab-picker' ), AB_Plugin::version() );
        if ( get_option( 'ab_settings_phone_default_country' ) != 'disabled' ) {
            call_user_func( $link_script, 'ab-intlTelInput', plugins_url( 'resources/js/intlTelInput.min.js', __FILE__ ), array( 'jquery' ), AB_Plugin::version() );
        }
        call_user_func( $link_script, 'bookly',         plugins_url( 'resources/js/bookly.js', __FILE__ ), array( 'ab-ladda', 'ab-hammer', 'ab-picker-date' ), AB_Plugin::version() );
        wp_localize_script( 'bookly', 'BooklyL10n', array(
            'today'     => __( 'Today', 'bookly' ),
            'months'    => array_values( $wp_locale->month ),
            'days'      => array_values( $wp_locale->weekday ),
            'daysShort' => array_values( $wp_locale->weekday_abbrev ),
            'nextMonth' => __( 'Next month', 'bookly' ),
            'prevMonth' => __( 'Previous month', 'bookly' ),
        ) );

        // Android animation.
        if ( array_key_exists( 'HTTP_USER_AGENT', $_SERVER ) && stripos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'android' ) !== false ) {
            call_user_func( $link_script, 'ab-jquery-animate-enhanced', plugins_url( 'resources/js/jquery.animate-enhanced.min.js', __FILE__ ), array( 'jquery' ), AB_Plugin::version() );
        }
    }

    public function init()
    {
        if ( !session_id() ) {
            @session_start();
        }

        // Payments ( PayPal Express Checkout, 2Checkout, PayU Latam, Payson )
        if ( isset( $_REQUEST['action'] ) ) {
            switch ( $_REQUEST['action'] ) {
                // PayPal.
                case 'ab-paypal-checkout':
                    $this->paypalController->expressCheckout();
                    break;
                case 'ab-paypal-return':
                    $this->paypalController->success();
                    break;
                case 'ab-paypal-cancel':
                    $this->paypalController->cancel();
                    break;
                case 'ab-paypal-error':
                    $this->paypalController->error();
                    break;
                // 2Checkout.
                case 'ab-2checkout-approved':
                    $this->twocheckoutController->approved();
                    break;
                case 'ab-2checkout-error':
                    $this->twocheckoutController->error();
                    break;
                // PayU Latam.
                case 'ab-payulatam-checkout':
                    $this->payulatamController->checkout();
                    break;
                case 'ab-payulatam-ipn':
                    AB_PayuLatam::ipn();
                    break;
                case 'ab-payulatam-error':
                    $this->payulatamController->error();
                    break;
                // Payson.
                case 'ab-payson-checkout':
                    $this->paysonController->checkout();
                    break;
                case 'ab-payson-ipn':
                    AB_Payson::ipn();
                    break;
                case 'ab-payson-cancel':
                    $this->paysonController->cancel();
                    break;
                case 'ab-payson-response':
                    $this->paysonController->response();
                    break;
                case 'ab-payson-error':
                    $this->paysonController->error();
                    break;
            }
        }
    }

}