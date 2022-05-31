<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Class AB_PayPalController
 */
class AB_PayPalController extends AB_Controller
{
    const SIGNUP = 'https://www.paypal.com/signup/';
    const HOME   = 'https://paypal.com/';

    protected function getPermissions()
    {
        return array( '_this' => 'anonymous' );
    }

    public function expressCheckout()
    {
        $form_id = $this->getParameter( 'ab_fid' );
        if ( $form_id ) {
            // Create a paypal object.
            $paypal   = new AB_PayPal();
            $userData = new AB_UserBookingData( $form_id );
            $userData->load();

            if ( $userData->get( 'service_id' ) ) {
                $service = $userData->getService();

                // Get the products information from the $_POST and create the Product objects.
                $product = new stdClass();
                $product->name  = $service->get( 'title' );
                $product->desc  = $service->getTitleWithDuration();
                $product->price = $userData->getTotalPrice();
                $product->qty   = 1;
                $paypal->addProduct( $product );

                // and send the payment request.
                try {
                    $paypal->send_EC_Request( $form_id );
                } catch ( Exception $e ) {
                    $userData->setPaymentStatus( 'error', $this->getParameter( 'error_msg' ), AB_Payment::TYPE_PAYPAL );
                    @wp_redirect( remove_query_arg( AB_PayPal::$remove_parameters, AB_Utils::getCurrentPageURL() ) );
                    exit;
                }
            }
        }
    }

    /**
     * Express Checkout 'CANCELURL' process
     */
    public function cancel()
    {
        $userData = new AB_UserBookingData( $this->getParameter( 'ab_fid' ) );
        $userData->load();
        $userData->setPaymentStatus( 'cancelled', null, AB_Payment::TYPE_PAYPAL );
        @wp_redirect( remove_query_arg( AB_PayPal::$remove_parameters, AB_Utils::getCurrentPageURL() ) );
        exit;
    }

    /**
     * Express Checkout 'ERRORURL' process
     */
    public function error()
    {
        $userData = new AB_UserBookingData( $this->getParameter( 'ab_fid' ) );
        $userData->load();
        $userData->setPaymentStatus( 'error', $this->getParameter( 'error_msg' ), AB_Payment::TYPE_PAYPAL );
        @wp_redirect( remove_query_arg( AB_PayPal::$remove_parameters, AB_Utils::getCurrentPageURL() ) );
        exit;
    }

    /**
     * Process the Express Checkout RETURNURL
     */
    public function success()
    {
        $form_id = $this->getParameter( 'ab_fid' );
        $paypal  = new AB_PayPal();

        if ( $this->hasParameter( 'token' ) && $this->hasParameter( 'PayerID' ) ) {
            $token    = $this->getParameter( 'token' );
            $payer_id = $this->getParameter( 'PayerID' );

            // Send the request to PayPal.
            $response = $paypal->sendNvpRequest( 'GetExpressCheckoutDetails', sprintf( '&TOKEN=%s', $token ) );

            if ( strtoupper( $response['ACK'] ) == 'SUCCESS' ) {
                $data = sprintf( '&TOKEN=%s&PAYERID=%s&PAYMENTREQUEST_0_PAYMENTACTION=Sale', $token, $payer_id );

                // Response keys containing useful data to send via DoExpressCheckoutPayment operation.
                $response_data_keys_pattern = sprintf( '/^(%s)/', implode( '|', array(
                    'PAYMENTREQUEST_0_AMT',
                    'PAYMENTREQUEST_0_ITEMAMT',
                    'PAYMENTREQUEST_0_CURRENCYCODE',
                    'L_PAYMENTREQUEST_0',
                ) ) );

                foreach ( $response as $key => $value ) {
                    // Collect product data from response using defined response keys.
                    if ( preg_match( $response_data_keys_pattern, $key ) ) {
                        $data .= sprintf( '&%s=%s', $key, $value );
                    }
                }

                // We need to execute the "DoExpressCheckoutPayment" at this point to Receive payment from user.
                $response = $paypal->sendNvpRequest( 'DoExpressCheckoutPayment', $data );
                if ( 'SUCCESS' == strtoupper( $response['ACK'] ) || 'SUCCESSWITHWARNING' == strtoupper( $response['ACK'] ) ) {
                    // Get transaction info
                    $response = $paypal->sendNvpRequest( 'GetTransactionDetails', '&TRANSACTIONID=' . urlencode( $response['PAYMENTINFO_0_TRANSACTIONID'] ) );
                    if ( 'SUCCESS' == strtoupper( $response['ACK'] ) || 'SUCCESSWITHWARNING' == strtoupper( $response['ACK'] ) ) {
                        // Need session to get Total and Token.

                        $token = $_SESSION['bookly'][ $form_id ]['paypal_response'][0]['TOKEN'];

                        $userData = new AB_UserBookingData( $form_id );
                        $userData->load();

                        if ( $userData->get( 'service_id' ) ) {
                            $payment = AB_Payment::query( 'p' )
                                ->select( 'p.id' )
                                ->where( 'p.type', AB_Payment::TYPE_PAYPAL )
                                ->where( 'p.transaction_id', $response['TRANSACTIONID'] )
                                ->findOne();
                            if ( empty ( $payment ) ) {
                                $customer_appointment = $userData->save();

                                $payment = new AB_Payment();
                                $payment->set( 'customer_appointment_id', $customer_appointment->get( 'id' ) );
                                $payment->set( 'transaction_id', $response['TRANSACTIONID'] );
                                $payment->set( 'total',   $userData->getTotalPrice() );
                                $payment->set( 'token',   $token );
                                $payment->set( 'created', current_time( 'mysql' ) );
                                $payment->set( 'type',    AB_Payment::TYPE_PAYPAL );
                                $payment->set( 'status',  AB_Payment::STATUS_COMPLETED );
                                $payment->save();
                            }

                            $userData->setPaymentStatus( 'success' );
                        }

                        @wp_redirect( remove_query_arg( AB_PayPal::$remove_parameters, AB_Utils::getCurrentPageURL() ) );
                        exit;
                    } else {
                        header( 'Location: ' . wp_sanitize_redirect( add_query_arg( array(
                                'action'    => 'ab-paypal-error',
                                'ab_fid'    => $form_id,
                                'error_msg' => str_replace( ' ', '%20', $response['L_LONGMESSAGE0'] )
                            ), AB_Utils::getCurrentPageURL()
                        ) ) );
                        exit;
                    }
                } else {
                    header( 'Location: ' . wp_sanitize_redirect( add_query_arg( array(
                            'action'    => 'ab-paypal-error',
                            'ab_fid'    => $form_id,
                            'error_msg' => str_replace( ' ', '%20', $response['L_LONGMESSAGE0'] )
                        ), AB_Utils::getCurrentPageURL()
                    ) ) );
                    exit;
                }
            }
        } else {
            header( 'Location: ' . wp_sanitize_redirect( add_query_arg( array(
                    'action'    => 'ab-paypal-error',
                    'ab_fid'    => $form_id,
                    'error_msg' => str_replace( ' ', '%20', __( 'Invalid token provided', 'bookly' ) )
                ), AB_Utils::getCurrentPageURL()
                ) ) );
            exit;
        }
    }

}