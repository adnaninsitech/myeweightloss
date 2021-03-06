<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Class AB_2Checkout
 */
class AB_2Checkout
{
    // Array for cleaning 2Checkout request
    public static $remove_parameters = array( 'sid', 'middle_initial', 'li_0_name', 'form_id', 'key', 'email', 'li_0_type', 'lang', 'currency_code', 'invoice_id', 'li_0_price', 'total', 'credit_card_processed', 'zip', 'li_0_quantity', 'cart_weight', 'fixed', 'last_name', 'li_0_product_id', 'street_address', 'city', 'li_0_tangible', 'li_0_description', 'ip_country', 'country', 'merchant_order_id', 'pay_method', 'cart_tangible', 'phone', 'street_address2', 'x_receipt_link_url', 'first_name', 'card_holder_name', 'action', 'state', 'order_number', 'type', 'ab_fid' );

    public static function renderForm( $form_id )
    {
        $userData = new AB_UserBookingData( $form_id );
        if ( $userData->load() ) {
            $replacement = array(
                '%seller_id%' => get_option( 'ab_2checkout_api_seller_id' ),
                '%name%'      => esc_attr( $userData->getService()->getTitle() ),
                '%price%'     => $userData->getTotalPrice(),
                '%action%'    => ( get_option( 'ab_2checkout_sandbox' ) == 1 ) ? 'https://sandbox.2checkout.com/checkout/purchase' : 'https://www.2checkout.com/checkout/purchase',
                '%currency_code%' => get_option( 'ab_currency' ),
                '%back%'      => __( 'Back', 'bookly' ),
                '%next%'      => __( 'Next', 'bookly' ),
                '%form_id%'   => $form_id
            );

            $twocheckout_form = '<form action="%action%" method="post" class="ab-2checkout-form">
                <input type="hidden" name="sid" value="%seller_id%">
                <input type="hidden" name="li_0_name" value="%name%">
                <input type="hidden" name="li_0_price" value="%price%" class="ab--coupon-change-price">
                <input type="hidden" name="currency_code" value="%currency_code%">
                <input type="hidden" name="mode" value="2CO">
                <input type="hidden" name="li_0_type" value="product">
                <input type="hidden" name="li_0_quantity" value="1">
                <input type="hidden" name="li_0_tangible" value="N">
                <input type="hidden" name="action" value="ab-2checkout-approved">
                <input type="hidden" name="ab_fid" value="%form_id%">
                <input type="hidden" name="x_receipt_link_url" value="">
                <button class="ab-left ab-to-third-step ab-btn ladda-button" data-style="zoom-in" style="margin-right: 10px;" data-spinner-size="40"><span class="ladda-label">%back%</span></button>
                <button class="ab-right ab-final-step ab-btn ladda-button" data-style="zoom-in" data-spinner-size="40"><span class="ladda-label">%next%</span></button>
            </form>';

            echo strtr( $twocheckout_form, $replacement );
        }
    }

}