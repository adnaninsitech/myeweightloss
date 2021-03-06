<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Class AB_PaymentsForm
 */
class AB_PaymentsForm extends AB_Form
{
    public function __construct()
    {
        $this->setFields( array(
            'ab_currency',
            'ab_settings_coupons',
            'ab_settings_pay_locally',
            'ab_paypal_type',
            'ab_paypal_api_username',
            'ab_paypal_api_password',
            'ab_paypal_api_signature',
            'ab_paypal_ec_mode',
            'ab_paypal_id',
            'ab_authorizenet_api_login_id',
            'ab_authorizenet_transaction_key',
            'ab_authorizenet_sandbox',
            'ab_authorizenet_type',
            'ab_stripe',
            'ab_stripe_secret_key',
            'ab_stripe_publishable_key',
            'ab_2checkout',
            'ab_2checkout_sandbox',
            'ab_2checkout_api_seller_id',
            'ab_2checkout_api_secret_word',
            'ab_payulatam',
            'ab_payulatam_sandbox',
            'ab_payulatam_api_account_id',
            'ab_payulatam_api_key',
            'ab_payulatam_api_merchant_id',
            'ab_payson',
            'ab_payson_sandbox',
            'ab_payson_fees_payer',
            'ab_payson_api_agent_id',
            'ab_payson_api_key',
            'ab_payson_api_receiver_email',
        ) );
    }

    public function save()
    {
        foreach ( $this->data as $field => $value ) {
            update_option( $field, $value );
        }
    }

}