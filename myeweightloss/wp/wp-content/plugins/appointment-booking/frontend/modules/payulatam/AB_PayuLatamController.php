<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Class AB_PayuLatamController
 */
class AB_PayuLatamController extends AB_Controller
{
    const SIGNUP = 'http://corporate.payu.com/enquiry-form/';
    const HOME   = 'http://www.payulatam.com/';

    protected function getPermissions()
    {
        return array( '_this' => 'anonymous' );
    }

    public function checkout()
    {
        $transactionState = $this->getParameter( 'transactionState' );
        if ( false === AB_PayuLatam::paymentIsApproved( ( get_option( 'ab_payulatam_sandbox' ) == 1 ), $transactionState, $this->getParameter( 'referenceCode' ), $this->getParameter( 'transactionId' ), $this->getParameter( 'signature' ) ) ) {
            switch ( $transactionState ) {
                case 6:
                    $message = __( 'Transaction rejected', 'bookly' );
                    break;
                case 104:
                    $message = __( 'Error', 'bookly' );
                    break;
                case 7:
                    $message = __( 'Pending payment', 'bookly' );
                    break;
                default:
                    $message = $this->getParameter( 'message' ) . ' ' . __( 'Invalid token provided', 'bookly' );
                    break;
            }
            header( 'Location: ' . wp_sanitize_redirect( add_query_arg( array(
                    'action'    => 'ab-payulatam-error',
                    'ab_fid'    => $this->getParameter( 'ab_fid' ),
                    'error_msg' => str_replace( ' ', '%20', $message ),
                ), AB_Utils::getCurrentPageURL()
                ) ) );
            exit;
        } else {
            // Clean GET parameters from PayU Latam.
            $userData = new AB_UserBookingData( stripslashes( $this->getParameter( 'ab_fid' ) ) );
            $userData->load();
            $userData->setPaymentStatus( 'success' );
            @wp_redirect( remove_query_arg( AB_PayuLatam::$remove_parameters, AB_Utils::getCurrentPageURL() ) );
            exit;
        }
    }

    public function error()
    {
        $userData = new AB_UserBookingData( $this->getParameter( 'ab_fid' ) );
        $userData->load();
        $userData->setPaymentStatus( 'error', $this->getParameter( 'error_msg' ), AB_Payment::TYPE_PAYULATAM );
        @wp_redirect( remove_query_arg( AB_PayuLatam::$remove_parameters, AB_Utils::getCurrentPageURL() ) );
        exit;
    }

    /**
     * New CSRF tokens
     */
    public function executePayulatamRefreshTokens()
    {
        $replacement = get_option( 'ab_payulatam' ) ? AB_PayuLatam::replaceData( $this->getParameter( 'form_id' ) ) : false;

        empty ( $replacement ) ? wp_send_json_error() : wp_send_json_success( array ( 'signature' => $replacement['%signature%'], 'referenceCode' => $replacement['%referenceCode%'] ) );
    }

    /**
     * Override parent method to add 'wp_ajax_ab_' prefix
     * so current 'execute*' methods look nicer.
     *
     * @param string $prefix
     */
    protected function registerWpActions( $prefix = '' )
    {
        parent::registerWpActions( 'wp_ajax_ab_' );
        parent::registerWpActions( 'wp_ajax_nopriv_ab_' );
    }

}