<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class AB_NotificationsController extends AB_Controller
{
    public function index()
    {
        $this->enqueueStyles( array(
            'frontend' => array( 'css/ladda.min.css' ),
            'module'   => array( 'css/notifications.css' ),
            'backend'  => array(
                'css/bookly.main-backend.css',
                'bootstrap/css/bootstrap.min.css',
            )
        ) );

        $this->enqueueScripts( array(
            'backend'  => array( 'bootstrap/js/bootstrap.min.js' => array( 'jquery' ) ),
            'module'   => array( 'js/notification.js' => array( 'jquery' ) ),
            'frontend' => array(
                'js/spin.min.js'  => array( 'jquery' ),
                'js/ladda.min.js' => array( 'jquery' ),
            )
        ) );

        $form = new AB_NotificationsForm( 'email' );
        $message = '';
        // Save action.
        if ( ! empty ( $_POST ) ) {
            $form->bind( $this->getPostParameters(), $_FILES );
            $form->save();
            $message = __( 'Notification settings were updated successfully.', 'bookly' );
            // sender name
            if ( $this->hasParameter( 'ab_settings_sender_name' ) ) {
                update_option( 'ab_settings_sender_name', $this->getParameter( 'ab_settings_sender_name' ) );
            }
            // sender email
            if ( $this->hasParameter( 'ab_settings_sender_email' ) ) {
                update_option( 'ab_settings_sender_email', $this->getParameter( 'ab_settings_sender_email' ) );
            }
            if ( $this->hasParameter( 'ab_email_notification_reply_to_customers' ) ) {
                update_option( 'ab_email_notification_reply_to_customers', $this->getParameter( 'ab_email_notification_reply_to_customers' ) );
            }
            if ( $this->hasParameter( 'ab_email_content_type' ) ) {
                update_option( 'ab_email_content_type', $this->getParameter( 'ab_email_content_type' ) );
            }
        }
        $cron_path = realpath( AB_PATH . '/lib/utils/send_notifications_cron.php' );

        $this->render( 'index', compact( 'form', 'message', 'cron_path' ) );
    }

    // Protected methods.

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