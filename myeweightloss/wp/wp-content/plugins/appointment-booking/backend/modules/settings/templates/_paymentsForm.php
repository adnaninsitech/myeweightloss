<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<form method="post" action="<?php echo esc_url( add_query_arg( 'type', '_payments' ) ) ?>" class="ab-settings-form">
    <table class="form-horizontal">
        <tr>
            <td style="width: 170px;">
                <label for="ab_currency"><?php _e( 'Currency', 'bookly' ) ?></label>
            </td>
            <td>
                <select id="ab_currency" class="form-control" name="ab_currency">
                    <?php foreach ( AB_Config::getCurrencyCodes() as $code ) : ?>
                        <option value="<?php echo $code ?>" <?php selected( get_option( 'ab_currency' ), $code ) ?> ><?php echo $code ?></option>
                    <?php endforeach ?>
                </select>
            </td>
        </tr>
        <tr>
            <td style="width: 170px;">
                <label for="ab_settings_coupons"><?php _e( 'Coupons', 'bookly' ) ?></label>
            </td>
            <td>
                <?php AB_Utils::optionToggle( 'ab_settings_coupons' ) ?>
            </td>
        </tr>
        <tr>
            <td colspan="2"><div class="ab-payments-title"><?php _e( 'Service paid locally', 'bookly' ) ?></div></td>
        </tr>
        <tr>
            <td colspan="2">
                <?php AB_Utils::optionToggle( 'ab_settings_pay_locally', array( 'f' => array( 'disabled', __( 'Disabled', 'bookly' ) ), 't' => array( '1', __( 'Enabled', 'bookly' ) ) ) ) ?>
            </td>
        </tr>
        <tr>
            <td><div class="ab-payments-title">2Checkout</div></td>
            <td></td>
        </tr>
        <tr>
            <td>
                <?php AB_Utils::optionToggle( 'ab_2checkout', array( 'f' => array( 'disabled', __( 'Disabled', 'bookly' ) ), 't' => array( 'standard_checkout', __( '2Checkout Standard Checkout', 'bookly' ) ) ) ) ?>
            </td>
            <td></td>
        </tr>
        <tr class="ab-2checkout">
            <td colspan="2">
                <fieldset class="ab-instruction">
                    <legend><?php _e( 'Instructions', 'bookly' ) ?></legend>
                    <div>
                        <div style="margin-bottom: 10px">
                            <?php _e( 'In <b>Checkout Options</b> of your 2Checkout account do the following steps:', 'bookly' ) ?>
                        </div>
                        <ol>
                            <li><?php _e( 'In <b>Direct Return</b> select <b>Header Redirect (Your URL)</b>.', 'bookly' ) ?></li>
                            <li><?php _e( 'In <b>Approved URL</b> enter the URL of your booking page.', 'bookly' ) ?></li>
                        </ol>
                        <div style="margin-top: 10px">
                            <?php _e( 'Finally provide the necessary information in the form below.', 'bookly' ) ?>
                        </div>
                    </div>
                </fieldset>
            </td>
        </tr>
        <tr class="ab-2checkout">
            <td><label for="ab_2checkout_api_seller_id"><?php _e( 'Account Number', 'bookly' ) ?></label></td>
            <td><input id="ab_2checkout_api_seller_id" class="form-control" type="text" name="ab_2checkout_api_seller_id" value="<?php echo get_option( 'ab_2checkout_api_seller_id' ) ?>"/></td>
        </tr>
        <tr class="ab-2checkout">
            <td><label for="ab_2checkout_api_secret_word"><?php _e( 'Secret Word', 'bookly' ) ?></label></td>
            <td><input id="ab_2checkout_api_secret_word" class="form-control" type="text" name="ab_2checkout_api_secret_word" value="<?php echo get_option( 'ab_2checkout_api_secret_word' ) ?>"/></td>
        </tr>
        <tr class="ab-2checkout">
            <td><label for="ab_2checkout_sandbox"><?php _e( 'Sandbox Mode', 'bookly' ) ?></label></td>
            <td>
                <?php AB_Utils::optionToggle( 'ab_2checkout_sandbox', array( 'f' => array( 0, __( 'No', 'bookly' ) ), 't' => array( 1, __( 'Yes', 'bookly' ) ) ) ) ?>
            </td>
        </tr>
        <tr>
            <td><div class="ab-payments-title">PayPal</div></td>
            <td></td>
        </tr>
        <tr>
            <td>
                <?php AB_Utils::optionToggle( 'ab_paypal_type', array( 'f' => array( 'disabled', __( 'Disabled', 'bookly' ) ), 't' => array( 'ec', 'PayPal Express Checkout' ) ) ) ?>
            </td>
            <td></td>
        </tr>
        <tr class="ab-paypal-ec">
            <td><label for="ab_paypal_api_username"><?php _e( 'API Username', 'bookly' ) ?></label></td>
            <td><input id="ab_paypal_api_username" class="form-control" type="text" size="33" name="ab_paypal_api_username" value="<?php echo get_option( 'ab_paypal_api_username' ) ?>"/></td>
        </tr>
        <tr class="ab-paypal-ec">
            <td><label for="ab_paypal_api_password"><?php _e( 'API Password', 'bookly' ) ?></label></td>
            <td><input id="ab_paypal_api_password" class="form-control" type="text" size="33" name="ab_paypal_api_password" value="<?php echo get_option( 'ab_paypal_api_password' ) ?>"/></td>
        </tr>
        <tr class="ab-paypal-ec">
            <td><label for="ab_paypal_api_signature"><?php _e( 'API Signature', 'bookly' ) ?></label></td>
            <td><input id="ab_paypal_api_signature" class="form-control" type="text" size="33" name="ab_paypal_api_signature" value="<?php echo get_option( 'ab_paypal_api_signature' ) ?>"/></td>
        </tr>
        <tr class="ab-paypal-ec">
            <td><label for="ab_paypal_ec_mode"><?php _e( 'Sandbox Mode', 'bookly' ) ?></label></td>
            <td>
                <?php AB_Utils::optionToggle( 'ab_paypal_ec_mode', array( 't' => array( '.sandbox', __( 'Yes', 'bookly' ) ), 'f' => array( '', __( 'No', 'bookly' ) ) ) ) ?>
            </td>
        </tr>
        <tr>
            <td><div class="ab-payments-title">Authorize.Net</div></td>
            <td></td>
        </tr>
        <tr>
            <td>
                <?php AB_Utils::optionToggle( 'ab_authorizenet_type', array( 'f' => array( 'disabled', __( 'Disabled', 'bookly' ) ), 't' => array( 'aim', 'Authorize.Net AIM' ) ) ) ?>
            </td>
            <td></td>
        </tr>
        <tr class="authorizenet">
            <td><label for="ab_authorizenet_api_login_id"><?php _e( 'API Login ID', 'bookly' ) ?></label></td>
            <td><input id="ab_authorizenet_api_login_id" class="form-control" type="text" size="33" name="ab_authorizenet_api_login_id" value="<?php echo get_option( 'ab_authorizenet_api_login_id' ) ?>"/></td>
        </tr>
        <tr class="authorizenet">
            <td><label for="ab_authorizenet_transaction_key"><?php _e( 'API Transaction Key', 'bookly' ) ?></label></td>
            <td><input id="ab_authorizenet_transaction_key" class="form-control" type="text" size="33" name="ab_authorizenet_transaction_key" value="<?php echo get_option( 'ab_authorizenet_transaction_key' ) ?>"/></td>
        </tr>
        <tr class="authorizenet">
            <td><label for="ab_authorizenet_sandbox"><?php _e( 'Sandbox Mode', 'bookly' ) ?></label></td>
            <td>
                <?php AB_Utils::optionToggle( 'ab_authorizenet_sandbox', array( 't' => array( 1, __( 'Yes', 'bookly' ) ), 'f' => array( 0, __( 'No', 'bookly' ) ) ) ) ?>
            </td>
        </tr>
        <tr>
            <td><div class="ab-payments-title">Stripe</div></td>
            <td></td>
        </tr>
        <tr>
            <td>
                <?php AB_Utils::optionToggle( 'ab_stripe', array( 'f' => array( 'disabled', __( 'Disabled', 'bookly' ) ), 't' => array( '1', __( 'Enabled', 'bookly' ) ) ) ) ?>
            </td>
            <td></td>
        </tr>
        <tr class="ab-stripe">
            <td colspan="2">
                <fieldset class="ab-instruction">
                    <legend><?php _e( 'Instructions', 'bookly' ) ?></legend>
                    <div>
                        <div style="margin-bottom: 10px">
                            <?php _e( 'If <b>Publishable Key</b> is provided then Bookly will use <a href="https://stripe.com/docs/stripe.js" target="_blank">Stripe.js</a><br/>for collecting credit card details.', 'bookly' ) ?>
                        </div>
                    </div>
                </fieldset>
            </td>
        </tr>
        <tr class="ab-stripe">
            <td><label for="ab_stripe_secret_key"><?php _e( 'Secret Key', 'bookly' ) ?></label></td>
            <td><input id="ab_stripe_secret_key" class="form-control" type="text" size="33" name="ab_stripe_secret_key" value="<?php echo get_option( 'ab_stripe_secret_key' ) ?>"/></td>
        </tr>
        <tr class="ab-stripe">
            <td><label for="ab_stripe_publishable_key"><?php _e( 'Publishable Key', 'bookly' ) ?></label></td>
            <td><input id="ab_stripe_publishable_key" class="form-control" type="text" name="ab_stripe_publishable_key" value="<?php echo get_option( 'ab_stripe_publishable_key' ) ?>"/></td>
        </tr>
        <tr>
            <td><div class="ab-payments-title">PayU Latam</div></td>
            <td></td>
        </tr>
        <tr>
            <td>
                <?php AB_Utils::optionToggle( 'ab_payulatam', array( 'f' => array( 'disabled', __( 'Disabled', 'bookly' ) ), 't' => array( '1', __( 'Enabled', 'bookly' ) ) ) ) ?>
            </td>
            <td></td>
        </tr>
        <tr class="ab-payulatam">
            <td><label for="ab_payulatam_api_key"><?php _e( 'API Key', 'bookly' ) ?></label></td>
            <td><input id="ab_payulatam_api_key" class="form-control" type="text" name="ab_payulatam_api_key" value="<?php echo get_option( 'ab_payulatam_api_key' ) ?>"/></td>
        </tr>
        <tr class="ab-payulatam">
            <td><label for="ab_payulatam_api_account_id"><?php _e( 'Account ID', 'bookly' ) ?></label></td>
            <td><input id="ab_payulatam_api_account_id" class="form-control" type="text" name="ab_payulatam_api_account_id" value="<?php echo get_option( 'ab_payulatam_api_account_id' ) ?>"/></td>
        </tr>
        <tr class="ab-payulatam">
            <td><label for="ab_payulatam_api_merchant_id"><?php _e( 'Merchant ID', 'bookly' ) ?></label></td>
            <td><input id="ab_payulatam_api_merchant_id" class="form-control" type="text" name="ab_payulatam_api_merchant_id" value="<?php echo get_option( 'ab_payulatam_api_merchant_id' ) ?>"/></td>
        </tr>
        <tr class="ab-payulatam">
            <td><label for="ab_payulatam_sandbox"><?php _e( 'Sandbox Mode', 'bookly' ) ?></label></td>
            <td>
                <?php AB_Utils::optionToggle( 'ab_payulatam_sandbox', array( 'f' => array( 0, __( 'No', 'bookly' ) ), 't' => array( 1, __( 'Yes', 'bookly' ) ) ) ) ?>
            </td>
        </tr>
        <tr>
            <td><div class="ab-payments-title">Payson</div></td>
            <td></td>
        </tr>
        <tr>
            <td>
                <?php AB_Utils::optionToggle( 'ab_payson', array( 'f' => array( 'disabled', __( 'Disabled', 'bookly' ) ), 't' => array( '1', __( 'Enabled', 'bookly' ) ) ) ) ?>
            </td>
            <td></td>
        </tr>
        <tr class="ab-payson">
            <td><label for="ab_payson_api_agent_id"><?php _e( 'Agent ID', 'bookly' ) ?></label></td>
            <td><input id="ab_payson_api_agent_id" class="form-control" type="text" name="ab_payson_api_agent_id" value="<?php echo get_option( 'ab_payson_api_agent_id' ) ?>"/></td>
        </tr>
        <tr class="ab-payson">
            <td><label for="ab_payson_api_key"><?php _e( 'API Key', 'bookly' ) ?></label></td>
            <td><input id="ab_payson_api_key" class="form-control" type="text" name="ab_payson_api_key" value="<?php echo get_option( 'ab_payson_api_key' ) ?>"/></td>
        </tr>
        <tr class="ab-payson">
            <td><label for="ab_payson_api_receiver_email"><?php _e( 'Receiver Email (login)', 'bookly' ) ?></label></td>
            <td><input id="ab_payson_api_receiver_email" class="form-control" type="text" name="ab_payson_api_receiver_email" value="<?php echo get_option( 'ab_payson_api_receiver_email' ) ?>"/></td>
        </tr>
        <tr class="ab-payson">
            <td><label for="ab_payson_fees_payer"><?php _e( 'Fees Payer', 'bookly' ) ?></label></td>
            <td>
                <?php AB_Utils::optionToggle( 'ab_payson_fees_payer', array( 'f' => array( 'PRIMARYRECEIVER', __( 'I am', 'bookly' ) ), 't' => array( 'SENDER', __( 'Client', 'bookly' ) ) ) ) ?>
            </td>
        </tr>
        <tr class="ab-payson">
            <td><label for="ab_payson_sandbox"><?php _e( 'Sandbox Mode', 'bookly' ) ?></label></td>
            <td>
                <?php AB_Utils::optionToggle( 'ab_payson_sandbox', array( 'f' => array( 0, __( 'No', 'bookly' ) ), 't' => array( 1, __( 'Yes', 'bookly' ) ) ) ) ?>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <?php AB_Utils::submitButton() ?>
                <?php AB_Utils::resetButton( 'ab-payments-reset' ) ?>
            </td>
            <td></td>
        </tr>
    </table>
</form>