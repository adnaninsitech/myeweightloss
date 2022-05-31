<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Class AB_Payson
 */
class AB_Payson
{
    // Array for cleaning Payson request
    public static $remove_parameters = array( 'TOKEN', 'form_id', 'action', 'ab_fid', 'error_msg' );

    public static function renderForm( $form_id )
    {
        $userData = new AB_UserBookingData( $form_id );
        if ( $userData->load() ) {
            $html = '<form method="post" class="ab-payson-form">';
            $html .= '<input type="hidden" name="action" value="ab-payson-checkout"/>';
            $html .= '<input type="hidden" name="ab_fid" value="' . $form_id . '"/>';
            $html .= '<input type="hidden" name="response_url"/>';
            $html .= '<button class="ab-left ab-to-third-step ab-btn ladda-button" data-style="zoom-in" style="margin-right: 10px;" data-spinner-size="40"><span class="ladda-label">' . __( 'Back', 'bookly' ) . '</span></button>';
            $html .= '<button class="ab-right ab-final-step ab-btn ladda-button" data-style="zoom-in" data-spinner-size="40"><span class="ladda-label">' . __( 'Next', 'bookly' ) . '</span></button>';
            $html .= '</form>';

            echo $html;
        }
    }

    /**
     * Check gateway data and if ok save payment info
     *
     * @param PaymentDetails $details
     * @param bool|false $ipn           When ipn false, this is request from browser and we use _redirectTo for notification customer
     * @param null|AB_UserBookingData $userData
     */
    public static function handlePayment( PaymentDetails $details, $ipn = false, $userData = null )
    {
        $payment = AB_Payment::query()->where( 'customer_appointment_id', $details->getCustom() )->where( 'type', AB_Payment::TYPE_PAYSON )->findOne();
        if ( $payment == null ) {
            if ( ! $ipn ) {
                self::_redirectTo( $userData, 'error', __( 'Incorrect payment data', 'bookly' ) );
            }
        }
        if ( $payment->status == AB_Payment::STATUS_COMPLETED ) {
            if ( ! $ipn ) {
                self::_redirectTo( $userData, 'success' );
            }
            return;
        }

        if ( $details->getType() == 'TRANSFER' && $details->getStatus() == 'COMPLETED' ) {
            // Handle completed card & bank transfers here
            /** @var OrderItem $product */
            $product = current( $details->getOrderItems() );
            if ( get_option( 'ab_currency' ) == $details->getCurrencyCode()
                 && (float) $product->getUnitPrice() == (float) $payment->total
            ) {
                $payment->set( 'status', AB_Payment::STATUS_COMPLETED );
                $payment->set( 'token',  $details->getToken() );
                $payment->set( 'transaction_id', $details->getPurchaseId() );
                $payment->save();
                AB_NotificationSender::send( AB_NotificationSender::INSTANT_NEW_APPOINTMENT, AB_CustomerAppointment::query()->where( 'id', $payment->customer_appointment_id )->findOne() );
                if ( ! $ipn ) {
                    self::_redirectTo( $userData, 'success' );
                } else {
                    wp_send_json_success();
                }
                exit;
            }

        } elseif ( $details->getType() == 'INVOICE' && $details->getStatus() == 'PENDING' && $details->getInvoiceStatus() == 'ORDERCREATED' ) {
            // Handle accepted invoice purchases here

        } elseif ( ! $ipn && $details->getStatus() == 'ERROR' ) {
            self::_redirectTo( $userData, 'error', __( 'Error', 'bookly' ) );

        } elseif ( ! $ipn ) {
            if ( in_array( $details->getStatus(), array( 'CREATED', 'PENDING', 'PROCESSING', 'CREDITED' ) ) ) {
                self::_redirectTo( $userData, 'processing' );
            } else {
                header( 'Location: ' . wp_sanitize_redirect( add_query_arg( array(
                        'action'    => 'ab-payson-error',
                        'ab_fid'    => stripslashes( @$_REQUEST['ab_fid'] ),
                        'error_msg' => str_replace( ' ', '%20', __( 'Payment status', 'bookly' ) . ' ' . $details->getStatus() ),
                    ), AB_Utils::getCurrentPageURL()
                    ) ) );
                exit;
            }
        }
    }

    /**
     * Notification for customer
     *
     * @param AB_UserBookingData $userData
     * @param string $status    success || error || processing
     * @param string $message
     */
    private static function _redirectTo( AB_UserBookingData $userData, $status = 'success', $message = '' )
    {
        $userData->load();
        $userData->setPaymentStatus( $status, $message, AB_Payment::TYPE_PAYSON );
        @wp_redirect( remove_query_arg( AB_Payson::$remove_parameters, AB_Utils::getCurrentPageURL() ) );
        exit;
    }

    /**
     * Redirect to Payson Payment page, or step 4.
     *
     * @param $form_id
     * @param AB_UserBookingData $userData
     * @param $response_url
     */
    public static function paymentPage( $form_id, AB_UserBookingData $userData, $response_url )
    {
        $api = self::_getApi();
        $url = array(
            'return'  => $response_url . 'action=ab-payson-response&ab_fid=' . $form_id,
            'cancel'  => $response_url . 'action=ab-payson-cancel&ab_fid=' . $form_id,
            'ipn'     => $response_url . 'action=ab-payson-ipn'
        );
        $payson_email = get_option( 'ab_payson_api_receiver_email' );

        $total     = $userData->getTotalPrice();
        $receiver  = new Receiver( $payson_email, $total );
        $receivers = array( $receiver );
        $client    = array(
            'email'      => $userData->get( 'email' ),
            'first_name' => $userData->get( 'name' ),
            'last_name'  => ''
        );
        $sender   = new Sender( $client['email'], $client['first_name'], $client['last_name'] );
        $pay_data = new PayData( $url['return'], $url['cancel'], $url['ipn'], substr( $userData->getService()->getTitle() . ' ' . AB_DateTimeUtils::formatDateTime( $userData->get( 'appointment_datetime' ) ), 0, 128 ), $sender, $receivers );
        $products = array ( new OrderItem( $userData->getService()->getTitle(), $total, 1, 0, __( 'Service', 'bookly' ) ) );
        $pay_data->setOrderItems( $products );

        // $constraints = array( FundingConstraint::BANK, FundingConstraint::CREDITCARD, FundingConstraint::INVOICE, FundingConstraint::SMS ); // all available
        $constraints = array( FundingConstraint::CREDITCARD );
        $pay_data->setFundingConstraints( $constraints );
        $pay_data->setFeesPayer( get_option( 'ab_payson_fees_payer' ) );
        $pay_data->setGuaranteeOffered( 'NO' );  // Disable PaysonGuarantee™
        $pay_data->setCurrencyCode( get_option( 'ab_currency' ) );

        $customer_appointment = $userData->save();
        $payment = new AB_Payment();
        $payment->set( 'customer_appointment_id', $customer_appointment->get( 'id' ) );
        $payment->set( 'total',   $total );
        $payment->set( 'created', current_time( 'mysql' ) );
        $payment->set( 'type',    AB_Payment::TYPE_PAYSON );
        $payment->set( 'status',  AB_Payment::STATUS_PENDING );
        $payment->save();

        $pay_data->setCustom( $customer_appointment->get( 'id' ) );
        try {
            $pay_response = $api->pay( $pay_data );
            if ( $pay_response->getResponseEnvelope()->wasSuccessful() ) {
                header( 'Location: ' . $api->getForwardPayUrl( $pay_response ) );
                exit;
            } else {
                $customer_appointment->delete();
                /** @var PaysonApiError[] $errors */
                $errors = $pay_response->getResponseEnvelope()->getErrors();
                self::_redirectTo( $userData, 'error', $errors[0]->getMessage() );
            }
        } catch ( Exception $e ) {
            $customer_appointment->delete();
            $userData->setPaymentStatus( 'error', $e->getMessage(), AB_Payment::TYPE_PAYSON );
            @wp_redirect( remove_query_arg( AB_Payson::$remove_parameters, AB_Utils::getCurrentPageURL() ) );
        }
    }

    /**
     * Handles IPN messages
     */
    public static function ipn()
    {
        $api      = self::_getApi();
        $received = file_get_contents( 'php://input' );
        $response = $api->validate( $received );

        if ( $response->isVerified() ) {
            AB_Payson::handlePayment( $response->getPaymentDetails(), true, null );
        }
        wp_send_json_success();
    }

    /**
     * Response when payment form completed
     *
     * @return PaymentDetailsResponse or redirect
     */
    public static function response()
    {
        $api   = self::_getApi();
        $token = stripslashes( @$_GET['TOKEN'] );
        $details_response = $api->paymentDetails( new PaymentDetailsData( $token ) );

        if ( $details_response->getResponseEnvelope()->wasSuccessful() ) {
            AB_Payson::handlePayment( $details_response->getPaymentDetails(), false, new AB_UserBookingData( stripslashes( @$_GET['ab_fid'] ) ) );
        } else {
            return $details_response;
        }
    }

    /**
     * Handle cancel request
     */
    public static function cancel()
    {
        $api   = self::_getApi();
        $token = stripslashes( @$_GET['TOKEN'] );
        $payment_details = $api->paymentDetails( new PaymentDetailsData( $token ) )->getPaymentDetails();
        if ( $payment_details->getStatus() == 'ABORTED' ) {
            $customer_appointment = new AB_CustomerAppointment();
            $customer_appointment->load( $payment_details->getCustom() );
            $customer_appointment->delete();
        }
    }

    /**
     * Init Api
     *
     * @return PaysonApi
     */
    private static function _getApi()
    {
        include_once AB_PATH . '/lib/payment/payson/paysonapi.php';
        $agent       = get_option( 'ab_payson_api_agent_id' );
        $api_key     = get_option( 'ab_payson_api_key' );
        $credentials = new PaysonCredentials( $agent, $api_key );

        return new PaysonApi( $credentials, ( get_option( 'ab_payson_sandbox' ) == 1 ) );
    }

}