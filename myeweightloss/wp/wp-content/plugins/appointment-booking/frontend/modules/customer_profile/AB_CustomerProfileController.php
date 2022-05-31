<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Class AB_CustomerProfileController
 */
class AB_CustomerProfileController extends AB_Controller
{
    public function renderShortCode( $attributes )
    {
        $this->enqueueStyles( array(
            'module' => array( 'css/customer_profile.css' )
        ) );

        $customer = new AB_Customer();
        $customer->loadBy( array( 'wp_user_id' => get_current_user_id() ) );

        $appointments = $customer->getAppointmentsForProfile();
        $allow_cancel = current_time( 'timestamp' );
        $minimum_time_prior_cancel = (int) get_option( 'ab_settings_minimum_time_prior_cancel', 0 );
        if ( $minimum_time_prior_cancel > 0 ) {
            $allow_cancel += $minimum_time_prior_cancel * HOUR_IN_SECONDS;
        }

        return $this->render( 'short_code', array( 'appointments' => $appointments, 'attributes' => $attributes, 'allow_cancel' => $allow_cancel ), false );
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
    }

}