<?php
define( 'WP_USE_THEMES', false );
foreach ( $argv as $argument ) {
    if ( strpos( $argument, 'host=' ) === 0 ) {
        $_SERVER['HTTP_HOST'] = substr( $argument, 5 );
    }
}
require_once( __DIR__ . '/../../../../../wp-load.php' );
require_once( ABSPATH . WPINC . '/formatting.php' );
require_once( ABSPATH . WPINC . '/general-template.php' );
require_once( ABSPATH . WPINC . '/pluggable.php' );
require_once( ABSPATH . WPINC . '/link-template.php' );
require_once( __DIR__ . '/AB_Utils.php' );
require_once( __DIR__ . '/AB_DateTimeUtils.php' );
require_once( __DIR__ . '/../AB_Plugin.php' );
require_once( __DIR__ . '/../AB_NotificationCodes.php' );
require_once( __DIR__ . '/../AB_NotificationSender.php' );
require_once( __DIR__ . '/../AB_SMS.php' );
require_once( __DIR__ . '/../AB_Entity.php' );
require_once( __DIR__ . '/../AB_Query.php' );
require_once( __DIR__ . '/../curl/curl.php' );
require_once( __DIR__ . '/../curl/curl_response.php' );
require_once( __DIR__ . '/../entities/AB_Appointment.php' );
require_once( __DIR__ . '/../entities/AB_Category.php' );
require_once( __DIR__ . '/../entities/AB_Coupon.php' );
require_once( __DIR__ . '/../entities/AB_Customer.php' );
require_once( __DIR__ . '/../entities/AB_CustomerAppointment.php' );
require_once( __DIR__ . '/../entities/AB_SentNotification.php' );
require_once( __DIR__ . '/../entities/AB_Notification.php' );
require_once( __DIR__ . '/../entities/AB_Service.php' );
require_once( __DIR__ . '/../entities/AB_Staff.php' );
require_once( __DIR__ . '/../entities/AB_StaffService.php' );

/**
 * Class AB_Notifier
 */
class AB_Notifier
{
    private $mysql_now; // format: YYYY-MM-DD HH:MM:SS

    /**
     * @var AB_SMS
     */
    private $sms;

    private $sms_authorized = false;

    /**
     * @param AB_Notification $notification
     */
    public function processNotification( AB_Notification $notification )
    {
        /** @var wpdb $wpdb */
        global $wpdb;
        $date = new DateTime();

        switch ( $notification->get( 'type' ) ) {
            case 'staff_agenda':
                if ( $date->format( 'H' ) >= 18 ) {
                    $rows = $wpdb->get_results(
                        'SELECT
                            `a`.*,
                            `ca`.`locale`,
                            `c`.`name`       AS `customer_name`,
                            `s`.`title`      AS `service_title`,
                            `st`.`email`     AS `staff_email`,
                            `st`.`phone`     AS `staff_phone`,
                            `st`.`full_name` AS `staff_name`
                        FROM `' . AB_CustomerAppointment::getTableName() . '` `ca`
                        LEFT JOIN `' . AB_Appointment::getTableName() . '` `a`   ON `a`.`id` = `ca`.`appointment_id`
                        LEFT JOIN `' . AB_Customer::getTableName() . '` `c`      ON `c`.`id` = `ca`.`customer_id`
                        LEFT JOIN `' . AB_Service::getTableName() . '` `s`       ON `s`.`id` = `a`.`service_id`
                        LEFT JOIN `' . AB_Staff::getTableName() . '` `st`        ON `st`.`id` = `a`.`staff_id`
                        LEFT JOIN `' . AB_StaffService::getTableName() . '` `ss` ON `ss`.`staff_id` = `a`.`staff_id` AND `ss`.`service_id` = `a`.`service_id`
                        WHERE DATE(DATE_ADD("' . $this->mysql_now . '", INTERVAL 1 DAY)) = DATE(`a`.`start_date`) AND NOT EXISTS (
                            SELECT * FROM `' . AB_SentNotification::getTableName() . '` `sn` WHERE
                                DATE(`sn`.`created`) = DATE("' . $this->mysql_now . '") AND
                                `sn`.`gateway`       = "' . $notification->get( 'gateway' ) . '" AND
                                `sn`.`type`          = "staff_agenda" AND
                                `sn`.`staff_id`      = `a`.`staff_id`
                        )'
                    );

                    if ( $rows ) {
                        $appointments = array();
                        foreach ( $rows as $row ) {
                            $appointments[ $row->staff_id ][] = $row;
                        }

                        foreach ( $appointments as $staff_id => $collection ) {
                            $sent = false;
                            $staff_email = null;
                            $staff_phone = null;
                            $table = $notification->get( 'gateway' ) == 'email'
                                ? '<table>%s</table>'
                                : '%s';
                            $tr    = $notification->get( 'gateway' ) == 'email'
                                ? '<tr><td>%s</td><td>%s</td><td>%s</td></tr>'
                                : "%s %s %s\n";

                            $agenda = '';
                            foreach ( $collection as $appointment ) {
                                $startDate = new DateTime( $appointment->start_date );
                                $endDate   = new DateTime( $appointment->end_date );
                                $agenda   .= sprintf(
                                    $tr,
                                    $startDate->format( 'H:i' ) . '-' . $endDate->format( 'H:i' ),
                                    AB_Utils::getTranslatedString( 'service_' . $appointment->service_id, $appointment->service_title, $appointment->locale ),
                                    $appointment->customer_name
                                );
                                $staff_email = $appointment->staff_email;
                                $staff_phone = $appointment->staff_phone;
                            }
                            $agenda = sprintf( $table, $agenda );

                            if ( $staff_email || $staff_phone ) {
                                $replacement = new AB_NotificationCodes();
                                $replacement->set( 'next_day_agenda', $agenda );
                                $replacement->set( 'appointment_datetime', $appointment->start_date );
                                $replacement->set( 'staff_name', AB_Utils::getTranslatedString( 'staff_' . $appointment->staff_id, $appointment->staff_name, $appointment->locale ) );

                                $message_template = AB_Utils::getTranslatedString( $notification->get( 'gateway' ) . '_' . $notification->get( 'type' ), $notification->get( 'message' ), $appointment->locale );
                                $message = $replacement->replace( $message_template, $notification->get( 'gateway' ) );
                                if ( $notification->get( 'gateway' ) == 'email' && $staff_email ) {
                                    $subject = $replacement->replace( AB_Utils::getTranslatedString( $notification->get( 'gateway' ) . '_' . $notification->get( 'type' ) . '_subject', $notification->get( 'subject' ), $appointment->locale ) );
                                    $message = get_option( 'ab_email_content_type' ) == 'plain' ? $message : wpautop( $message );
                                    // Send email.
                                    $sent = wp_mail( $staff_email, $subject, $message, AB_Utils::getEmailHeaders() );
                                } elseif ( $notification->get( 'gateway' ) == 'sms' && $staff_phone ) {
                                    // Send sms.
                                    $sent = $this->sms->sendSms( $staff_phone, $message );
                                }
                            }

                            if ( $sent ) {
                                $sent_notification = new AB_SentNotification();
                                $sent_notification->set( 'staff_id', $staff_id );
                                $sent_notification->set( 'gateway',  $notification->get( 'gateway' ) );
                                $sent_notification->set( 'created',  $date->format( 'Y-m-d H:i:s' ) );
                                $sent_notification->set( 'type',     'staff_agenda' );
                                $sent_notification->save();
                            }
                        }
                    }
                }
                break;
            case 'client_follow_up':
                if ( $date->format( 'H' ) >= 21 ) {
                    $rows = $wpdb->get_results(
                        'SELECT
                            `a`.*,
                            `ca`.*
                        FROM `' . AB_CustomerAppointment::getTableName() . '` `ca`
                        LEFT JOIN `' . AB_Appointment::getTableName() . '` `a` ON `a`.`id` = `ca`.`appointment_id`
                        WHERE DATE("' . $this->mysql_now . '") = DATE(`a`.`start_date`) AND NOT EXISTS (
                            SELECT * FROM `' . AB_SentNotification::getTableName() . '` `sn` WHERE
                                DATE(`sn`.`created`)           = DATE("' . $this->mysql_now . '") AND
                                `sn`.`gateway`                 = "' . $notification->get( 'gateway' ) . '" AND
                                `sn`.`type`                    = "client_follow_up" AND
                                `sn`.`customer_appointment_id` = `ca`.`id`
                        )',
                        ARRAY_A
                    );

                    if ( $rows ) {
                        foreach ( $rows as $row ) {
                            $customer_appointment = new AB_CustomerAppointment();
                            $customer_appointment->load( $row['id'] );
                            if ( AB_NotificationSender::sendFromCron( AB_NotificationSender::CRON_FOLLOW_UP_EMAIL, $notification, $customer_appointment ) ) {
                                $sent_notification = new AB_SentNotification();
                                $sent_notification->set( 'customer_appointment_id', $customer_appointment->get( 'id' ) );
                                $sent_notification->set( 'gateway', $notification->get( 'gateway' ) );
                                $sent_notification->set( 'created', $date->format( 'Y-m-d H:i:s' ) );
                                $sent_notification->set( 'type',    'client_follow_up' );
                                $sent_notification->save();
                            }
                        }
                    }
                }
                break;
            case 'client_reminder':
                if ( $date->format( 'H' ) >= 18 ) {
                    $rows = $wpdb->get_results(
                        'SELECT
                            `ca`.`id`
                        FROM `' . AB_CustomerAppointment::getTableName() . '` `ca`
                        LEFT JOIN `' . AB_Appointment::getTableName() . '` `a` ON `a`.`id` = `ca`.`appointment_id`
                        WHERE DATE(DATE_ADD("' . $this->mysql_now . '", INTERVAL 1 DAY)) = DATE(`a`.`start_date`) AND NOT EXISTS (
                            SELECT * FROM `' . AB_SentNotification::getTableName() . '` `sn` WHERE
                                DATE(`sn`.`created`)           = DATE("' . $this->mysql_now . '") AND
                                `sn`.`gateway`                 = "' . $notification->get( 'gateway' ) . '" AND
                                `sn`.`type`                    = "client_reminder" AND
                                `sn`.`customer_appointment_id` = `ca`.`id`
                        )',
                        ARRAY_A
                    );

                    if ( $rows ) {
                        foreach ( $rows as $row ) {
                            $customer_appointment = new AB_CustomerAppointment();
                            $customer_appointment->load( $row['id'] );
                            if ( AB_NotificationSender::sendFromCron( AB_NotificationSender::CRON_NEXT_DAY_APPOINTMENT, $notification, $customer_appointment ) ) {
                                $sent_notification = new AB_SentNotification();
                                $sent_notification->set( 'customer_appointment_id', $customer_appointment->get( 'id' ) );
                                $sent_notification->set( 'gateway', $notification->get( 'gateway' ) );
                                $sent_notification->set( 'created', $date->format( 'Y-m-d H:i:s' ) );
                                $sent_notification->set( 'type',    'client_reminder' );
                                $sent_notification->save();
                            }
                        }
                    }
                }
                break;
        }
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        date_default_timezone_set( AB_Utils::getTimezoneString() );

        wp_load_translations_early();

        $now = new DateTime();
        $this->mysql_now = $now->format( 'Y-m-d H:i:s' );
        $this->sms = new AB_SMS();
        $this->sms_authorized = $this->sms->loadProfile();

        $query = AB_Notification::query()
            ->where( 'active', 1 )
            ->whereIn( 'type', array( 'staff_agenda', 'client_follow_up', 'client_reminder' ) );

        foreach ( $query->find() as $notification ) {
            $this->processNotification( $notification );
        }
    }

}

add_action( 'bookly_send_notifications', function() { new AB_Notifier(); } );

do_action( 'bookly_send_notifications' );