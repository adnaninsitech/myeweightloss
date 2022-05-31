<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Class AB_PaysonController
 */
class AB_PaysonController extends AB_Controller
{
    const SIGNUP = 'https://www.payson.se/myaccount/account/create/';
    const HOME   = 'https://www.payson.se/';

    protected function getPermissions()
    {
        return array( '_this' => 'anonymous' );
    }

    public function checkout()
    {
        $form_id  = $this->getParameter( 'ab_fid' );
        $userData = new AB_UserBookingData( $form_id );
        if ( $userData->load() ) {
            AB_Payson::paymentPage( $form_id, $userData, $this->getParameter( 'response_url' ) );
        }
    }

    /**
     * Redirect with token from Payment Form to Bookly page
     */
    public function response()
    {
        $details_response = AB_Payson::response();
        if ( $details_response instanceof PaymentDetailsResponse ) {
            // We have some errors from Payson
            $errors   = $details_response->getResponseEnvelope()->getErrors();
            $userData = new AB_UserBookingData( $this->getParameter( 'ab_fid' ) );
            $userData->load();
            $userData->setPaymentStatus( 'error', $errors[0]->getMessage(), AB_Payment::TYPE_PAYSON );
            @wp_redirect( remove_query_arg( AB_Payson::$remove_parameters, AB_Utils::getCurrentPageURL() ) );
            exit;
        }
    }

    public function cancel()
    {
        AB_Payson::cancel();
        $userData = new AB_UserBookingData( $this->getParameter( 'ab_fid' ) );
        $userData->load();
        $userData->setPaymentStatus( 'cancelled', null, AB_Payment::TYPE_PAYSON );
        @wp_redirect( remove_query_arg( AB_Payson::$remove_parameters, AB_Utils::getCurrentPageURL() ) );
        exit;
    }

    public function error()
    {
        $userData = new AB_UserBookingData( $this->getParameter( 'ab_fid' ) );
        $userData->load();
        $userData->setPaymentStatus( 'error', $this->getParameter( 'error_msg' ), AB_Payment::TYPE_PAYSON );
        @wp_redirect( remove_query_arg( AB_Payson::$remove_parameters, AB_Utils::getCurrentPageURL() ) );
        exit;
    }

}