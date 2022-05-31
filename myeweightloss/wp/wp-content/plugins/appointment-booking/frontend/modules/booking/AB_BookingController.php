<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Class AB_BookingController
 */
class AB_BookingController extends AB_Controller
{
    private $replacement = array();
    protected function getPermissions()
    {
        return array( '_this' => 'anonymous' );
    }

    /**
     * Render Bookly shortcode.
     *
     * @param $attributes
     * @return string
     */
    public function renderShortCode( $attributes )
    {
        global $sitepress;

        $assets = '';

        if ( get_option( 'ab_settings_link_assets_method' ) == 'print' ) {
            $print_assets = ! wp_script_is( 'bookly', 'done' );
            if ( $print_assets ) {
                ob_start();

                // The styles and scripts are registered in AB_Frontend.php
                wp_print_styles( 'ab-reset' );
                wp_print_styles( 'ab-picker-date' );
                wp_print_styles( 'ab-picker-classic-date' );
                wp_print_styles( 'ab-picker' );
                wp_print_styles( 'ab-ladda-themeless' );
                wp_print_styles( 'ab-ladda-min' );
                wp_print_styles( 'ab-main' );
                wp_print_styles( 'ab-columnizer' );
                wp_print_styles( 'ab-intlTelInput' );

                wp_print_scripts( 'ab-spin' );
                wp_print_scripts( 'ab-ladda' );
                wp_print_scripts( 'ab-picker' );
                wp_print_scripts( 'ab-picker-date' );
                wp_print_scripts( 'ab-hammer' );
                wp_print_scripts( 'ab-jq-hammer' );
                wp_print_scripts( 'ab-intlTelInput' );
                // Android animation.
                if ( stripos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'android' ) !== false ) {
                    wp_print_scripts( 'ab-jquery-animate-enhanced' );
                }
                wp_print_scripts( 'bookly' );

                $assets = ob_get_clean();
            }
        } else {
            $print_assets = true; // to print CSS in template.
        }

        // Find bookings with any of payment statuses ( PayPal, 2Checkout, PayU Latam ).
        $booking_finished = $booking_cancelled = false;
        $this->form_id = uniqid();
        if ( isset ( $_SESSION['bookly'] ) ) {
            foreach ( $_SESSION['bookly'] as $form_id => $data ) {
                if ( isset( $data['payment'] ) ) {
                    if ( ! isset ( $data['payment']['processed'] ) ) {
                        switch ( $data['payment']['status'] ) {
                            case 'success':
                            case 'processing':
                                $this->form_id = $form_id;
                                $booking_finished = true;
                                break;
                            case 'cancelled':
                            case 'error':
                                $this->form_id = $form_id;
                                $booking_cancelled = true;
                                break;
                        }
                        // Mark this form as processed for cases when there are more than 1 booking form on the page.
                        $_SESSION['bookly'][ $form_id ]['payment']['processed'] = true;
                    }
                } else {
                    unset ( $_SESSION['bookly'][ $form_id ] );
                }
            }
        }
        $hide_date_and_time = (bool) @$attributes['hide_date_and_time'];
        $hide      = @$attributes['hide'];
        $need_hide = ( $hide !== null ) ? explode( ',', $hide ) : array();
        $attributes = json_encode( array(
            'category_id'            =>  (int) @$attributes['category_id'],
            'service_id'             =>  (int) @$attributes['service_id'],
            'staff_member_id'        =>  (int) @$attributes['staff_member_id'],
            'hide_categories'        => in_array( 'categories',    $need_hide ) ? true : (bool) @$attributes['hide_categories'],
            'hide_services'          => in_array( 'services',      $need_hide ) ? true : (bool) @$attributes['hide_services'],
            'hide_staff_members'     => in_array( 'staff_members', $need_hide ) ? true : (bool) @$attributes['hide_staff_members'],
            'hide_date'              => $hide_date_and_time ? true : in_array( 'date',       $need_hide ),
            'hide_week_days'         => $hide_date_and_time ? true : in_array( 'week_days',  $need_hide ),
            'hide_time_range'        => $hide_date_and_time ? true : in_array( 'time_range', $need_hide ),
            'show_number_of_persons' => (bool) @$attributes['show_number_of_persons'],
        ) );

        // Prepare URL for AJAX requests.
        $ajax_url = admin_url( 'admin-ajax.php' );
        // Support WPML.
        if ( $sitepress instanceof SitePress ) {
            switch ( $sitepress->get_setting( 'language_negotiation_type' ) ) {
                case 1: // url: /de             Different languages in directories.
                    $ajax_url .= '/' . $sitepress->get_current_language();
                    break;
                case 2: // url: de.example.com  A different domain per language. Not available for Multisite
                    break;
                case 3: // url: ?lang=de        Language name added as a parameter.
                    $ajax_url .= '?lang=' . $sitepress->get_current_language();
                    break;
            }
        }

        return $assets . $this->render( 'short_code', compact( 'attributes', 'print_assets', 'ajax_url', 'booking_finished', 'booking_cancelled' ), false );
    }

    /**
     * 1. Render first step.
     *
     * @return string JSON
     */
    public function executeRenderService()
    {
        $response = null;
        $form_id  = $this->getParameter( 'form_id' );

        if ( $form_id ) {
            $userData = new AB_UserBookingData( $form_id );
            $userData->load();

            if ( get_option( 'ab_settings_use_client_time_zone' ) ) {
                $time_zone_offset = $this->getParameter( 'time_zone_offset' );
                $userData->saveData( array(
                    'time_zone_offset' => $time_zone_offset,
                    'date_from' => date( 'Y-m-d', current_time( 'timestamp' ) + AB_Config::getMinimumTimePriorBooking() - ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS + $time_zone_offset * 60 ) )
                ) );
            }

            $this->_prepareProgressTracker( 1, $userData->getServicePriceForStep( 1 ) );
            $this->info_text = $this->_prepareInfoText( 1, AB_Utils::getTranslatedOption( 'ab_appearance_text_info_first_step' ), $userData );

            // Available days and times.
            $days_times = AB_Config::getDaysAndTimes( $userData->get( 'time_zone_offset' ) );
            // Prepare week days that need to be checked.
            $days_checked = $userData->get( 'days' );
            if ( empty( $days_checked ) ) {
                // Check all available days.
                $days_checked = array_keys( $days_times['days'] );
            }
            $bounding = AB_Config::getBoundingDaysForPickadate( $userData->get( 'time_zone_offset' ) );
            $casest   = AB_Config::getCaSeSt();

            $response = array(
                'success'    => true,
                'html'       => $this->render( '1_service', array(
                    'userData'     => $userData,
                    'days'         => $days_times['days'],
                    'times'        => $days_times['times'],
                    'days_checked' => $days_checked
                ), false ),
                'categories' => $casest['categories'],
                'staff'      => $casest['staff'],
                'services'   => $casest['services'],
                'date_max'   => $bounding['date_max'],
                'date_min'   => $bounding['date_min'],
                'attributes' => $userData->get( 'service_id' )
                    ? array(
                        'service_id'        => $userData->get( 'service_id' ),
                        'staff_member_id'   => $userData->getStaffIdForStep( 1 ),
                        'number_of_persons' => $userData->get( 'number_of_persons' ),
                    )
                    : null
            );
        } else {
            $response = array( 'success' => false, 'error' => __( 'Form ID error.', 'bookly' ) );
        }

        // Output JSON response.
        wp_send_json( $response );
    }

    /**
     * 2. Render second step.
     *
     * @return string JSON
     */
    public function executeRenderTime()
    {
        $response = null;
        $userData = new AB_UserBookingData( $this->getParameter( 'form_id' ) );

        if ( $userData->load() ) {
            $availableTime = new AB_AvailableTime( $userData );
            if ( $this->hasParameter( 'selected_date' ) ) {
                $availableTime->setSelectedDate( $this->getParameter( 'selected_date' ) );
            } else {
                $availableTime->setSelectedDate( $userData->get( 'date_from' ) );
            }
            $availableTime->load();

            $this->_prepareProgressTracker( 2, $userData->getServicePriceForStep( 2 ) );
            $this->info_text = $this->_prepareInfoText( 2, AB_Utils::getTranslatedOption( 'ab_appearance_text_info_second_step' ), $userData );

            // Render slots by groups (day or month).
            $slots = array();
            foreach ( $availableTime->getSlots() as $group => $group_slots ) {
                $slots[ $group ] = preg_replace( '/>\s+</', '><', $this->render( '_time_slots', array(
                     'group' => $group,
                     'slots' => $group_slots,
                     'is_whole_day_service' => $availableTime->isWholeDayService(),
                ), false ) );
            }

            // Set response.
            $response = array(
                'success'        => true,
                'has_slots'      => ! empty ( $slots ),
                'has_more_slots' => $availableTime->hasMoreSlots(),
                'day_one_column' => AB_Config::showDayPerColumn(),
                'slots'          => $slots,
                'html'           => $this->render( '2_time', array(
                    'date'      => AB_Config::showCalendar() ? $availableTime->getSelectedDateForPickadate() : null,
                    'has_slots' => ! empty ( $slots )
                ), false ),

            );
            if ( AB_Config::showCalendar() ) {
                $bounding = AB_Config::getBoundingDaysForPickadate( $userData->get( 'time_zone_offset' ) );
                $response['date_max'] = $bounding['date_max'];
                $response['date_min'] = $bounding['date_min'];
                $response['disabled_days'] = $availableTime->getDisabledDaysForPickadate();
            }
        } else {
            $response = array( 'success' => false, 'error' => __( 'Session error.', 'bookly' ) );
        }

        // Output JSON response.
        wp_send_json( $response );
    }

    public function executeRenderNextTime()
    {
        $response = null;
        $userData = new AB_UserBookingData( $this->getParameter( 'form_id' ) );

        if ( $userData->load() ) {
            $availableTime = new AB_AvailableTime( $userData );
            $availableTime->setLastFetchedSlot( $this->getParameter( 'last_slot' ) );
            $availableTime->load();

            $html = '';
            foreach ( $availableTime->getSlots() as $group => $group_slots ) {
                $html .= $this->render( '_time_slots', array(
                    'group' => $group,
                    'slots' => $group_slots,
                    'is_whole_day_service' => $availableTime->isWholeDayService(),
                ), false );
            }

            // Set response.
            $response = array(
                'success'        => true,
                'html'           => preg_replace( '/>\s+</', '><', $html ),
                'has_slots'      => $html != '',
                'has_more_slots' => $availableTime->hasMoreSlots() // show/hide the next button
            );
        } else {
            $response = array( 'success' => false, 'error' => __( 'Session error.', 'bookly' ) );
        }

        // Output JSON response.
        wp_send_json( $response );
    }

    /**
     * 3. Render third step.
     *
     * @return string JSON
     */
    public function executeRenderDetails()
    {
        $response = null;
        $userData = new AB_UserBookingData( $this->getParameter( 'form_id' ) );

        if ( $userData->load() ) {
            // Prepare custom fields data.
            $cf_data = array();
            $custom_fields = $userData->get( 'custom_fields' );
            if ( $custom_fields !== null ) {
                foreach ( json_decode( $custom_fields, true ) as $field ) {
                    $cf_data[ $field['id'] ] = $field['value'];
                }
            }

            $this->info_text = $this->_prepareInfoText( 3, AB_Utils::getTranslatedOption( 'ab_appearance_text_info_third_step' ), $userData );
            $this->info_text_guest = ( get_current_user_id() == 0 ) ? $this->_prepareInfoText( 3, AB_Utils::getTranslatedOption( 'ab_appearance_text_info_third_step_guest' ), $userData ) : '';
            $this->_prepareProgressTracker( 3, $userData->getServicePriceForStep( 3 ) );
            if ( strpos( get_option( 'ab_custom_fields' ), '"captcha"' ) !== false ) {
                // Init Captcha.
                AB_Captcha::init( $this->getParameter( 'form_id' ) );
            }
            $response = array(
                'success' => true,
                'html'    => $this->render( '3_details', array(
                    'userData'      => $userData,
                    'custom_fields' => AB_Utils::getTranslatedCustomFields(),
                    'cf_data'       => $cf_data,
                    'captcha_url'   => admin_url( 'admin-ajax.php?action=ab_captcha&form_id=' ) . $this->getParameter( 'form_id' ) . '&' . microtime( true )
                ), false )
            );
        } else {
            $response = array( 'success' => false, 'error' => __( 'Session error.', 'bookly' ) );
        }

        // Output JSON response.
        wp_send_json( $response );
    }

    /**
     * 4. Render fourth step.
     *
     * @return string JSON
     */
    public function executeRenderPayment()
    {
        $response = null;
        $userData = new AB_UserBookingData( $this->getParameter( 'form_id' ) );

        if ( $userData->load() ) {
            $payment_disabled = AB_Config::isPaymentDisabled();

            if ( $userData->getServicePriceForStep( 4 ) <= 0 ) {
                $payment_disabled = true;
            }

            if ( $payment_disabled == false ) {
                $this->form_id   = $this->getParameter( 'form_id' );
                $this->info_text = $this->_prepareInfoText( 4, AB_Utils::getTranslatedOption( 'ab_appearance_text_info_fourth_step' ), $userData );
                $this->info_text_coupon = $this->_prepareInfoText( 4, AB_Utils::getTranslatedOption( 'ab_appearance_text_info_coupon' ), $userData );

                $this->_prepareProgressTracker( 4, $userData->getTotalPrice() );

                // Set response.
                $response = array(
                    'success'  => true,
                    'disabled' => false,
                    'html'     => $this->render( '4_payment', array(
                        'userData'           => $userData,
                        'payment_status'     => $userData->extractPaymentStatus(),
                        'pay_local'          => get_option( 'ab_settings_pay_locally' ) != 'disabled',
                        'pay_paypal'         => get_option( 'ab_paypal_type' ) != 'disabled',
                        'pay_stripe'         => get_option( 'ab_stripe' ) != 'disabled',
                        'pay_2checkout'      => get_option( 'ab_2checkout' ) != 'disabled',
                        'pay_authorizenet'   => get_option( 'ab_authorizenet_type' ) != 'disabled',
                        'pay_payulatam'      => get_option( 'ab_payulatam' ) != 'disabled',
                        'pay_payson'         => get_option( 'ab_payson' ) != 'disabled',
                    ), false )
                );
            } else {
                $response = array(
                    'success'  => true,
                    'disabled' => true,
                );
            }
        } else {
            $response = array( 'success' => false, 'error' => __( 'Session error.', 'bookly' ) );
        }

        // Output JSON response.
        wp_send_json( $response );
    }

    /**
     * 5. Render fifth step.
     *
     * @return string JSON
     */
    public function executeRenderComplete()
    {
        $response = null;
        $userData = new AB_UserBookingData( $this->getParameter( 'form_id' ) );

        if ( $userData->load() ) {
            $this->_prepareProgressTracker( 5, $userData->getServicePriceForStep( 5 ) );
            $success_html = $this->progress_tracker;
            $payment =  $userData->extractPaymentStatus();
            switch ( $payment['status'] ) {
                case 'processing':
                    $success_html .= $this->_prepareInfoText( 5, __( 'Your payment has been accepted for processing.', 'bookly' ), $userData );
                    break;
                default:
                    $success_html .= $this->_prepareInfoText( 5, AB_Utils::getTranslatedOption( 'ab_appearance_text_info_fifth_step' ), $userData );
                    break;
            }

            $response = array (
                'success' => true,
                'html'    => array (
                    'success' => $success_html,
                    'error'   => '<h3>' . __( 'The selected time is not available anymore. Please, choose another time slot.', 'bookly' ) . '</h3>'
                ),
                'final_step_url' => get_option( 'ab_settings_final_step_url' )
            );
        } else {
            $response = array( 'success' => false, 'error' => __( 'Session error.', 'bookly' ) );
        }

        // Output JSON response.
        wp_send_json( $response );
    }

    /**
     * Save booking data in session.
     */
    public function executeSessionSave()
    {
        $form_id = $this->getParameter( 'form_id' );
        $errors  = array();
        if ( $form_id ) {
            $userData = new AB_UserBookingData( $form_id );
            $userData->load();
            $parameters = $this->getParameters();
            $errors = $userData->validate( $parameters );
            if ( empty ( $errors ) ) {
                // Remove captcha from custom fields.
                if ( isset( $parameters['custom_fields'] ) && isset( $parameters['captcha_id'] ) ) {
                    $parameters['custom_fields'] = json_encode( array_filter( json_decode( $parameters['custom_fields'] ), function ( $value ) use ( $parameters ) {
                        return $value->id != $parameters['captcha_id'];
                    } ) );
                }

                $userData->saveData( $parameters );
            }
        }

        // Output JSON response.
        wp_send_json( $errors );
    }


    /**
     * Save appointment (final action).
     */
    public function executeSaveAppointment()
    {
        $form_id = $this->getParameter( 'form_id' );
        $time_is_available = false;
        $data = null;
        if ( $form_id ) {
            $userData = new AB_UserBookingData( $form_id );
            $userData->load();

            $total_price            = $userData->getTotalPrice();
            $is_payment_disabled    = AB_Config::isPaymentDisabled();
            $is_pay_locally_enabled = get_option( 'ab_settings_pay_locally' );

            if ( $is_payment_disabled || $is_pay_locally_enabled || $total_price <= 0 ) {
                $availableTime = new AB_AvailableTime( $userData );
                // Check if appointment's time is still available
                if ( $availableTime->checkBookingTime() ) {
                    // Pending appointment from bookly.js allowed only for [ AB_Payment::TYPE_PAYULATAM, ]
                    $pending = (bool) ( $this->getParameter( 'pending' )
                        && ( in_array( $this->getParameter( 'gateway' ), array( AB_Payment::TYPE_PAYULATAM ) ) )
                        && (bool) get_option( 'ab_' . $this->getParameter( 'gateway' ) )
                    );
                    // Create appointment.
                    $customer_appointment = $userData->save( $pending );
                    if ( ! $is_payment_disabled ) {
                        $payment = new AB_Payment();
                        $payment->set( 'customer_appointment_id', $customer_appointment->get( 'id' ) );
                        $payment->set( 'created', current_time( 'mysql' ) );
                        if ( $pending ) {
                            $payment->set( 'total',  $total_price );
                            $payment->set( 'type',   $this->getParameter( 'gateway' ) );
                            $payment->set( 'status', AB_Payment::STATUS_PENDING );
                            $payment->save();
                            $data = array( 'customer_appointment_id' => $customer_appointment->get( 'id' ) );
                        } else {
                            $coupon = $userData->getCoupon();
                            $payment->set( 'status', AB_Payment::STATUS_COMPLETED );
                            if ( $coupon && $total_price <= 0 ) {
                                // Create fake payment record for 100% discount coupons.
                                $payment->set( 'total', '0.00' );
                                $payment->set( 'type',  AB_Payment::TYPE_COUPON );
                                $payment->save();
                            } else if ( $is_pay_locally_enabled && $total_price > 0 ) {
                                // Create record for local payment.
                                $payment->set( 'total', $total_price );
                                $payment->set( 'type',  AB_Payment::TYPE_LOCAL );
                                $payment->save();
                            }
                        }
                    }
                    $time_is_available = true;
                }
            }
        }

        // Output JSON response.
        $time_is_available ? wp_send_json_success( $data ) : wp_send_json_error();
    }

    /**
     * render Progress Tracker for Backend Appearance
     */
    public function executeRenderProgressTracker()
    {
        $booking_step = $this->getParameter( 'booking_step' );

        if ( $booking_step ) {
            $this->_prepareProgressTracker( $booking_step );

            // Output JSON response.
            wp_send_json( array(
                'html' => $this->progress_tracker
            ) );
        }
        exit;
    }

    /**
     * Cancel Appointment using token.
     */
    public function executeCancelAppointment()
    {
        $customer_appointment = new AB_CustomerAppointment();

        if ( $customer_appointment->loadBy( array( 'token' => $this->getParameter( 'token' ) ) ) ) {
            $allow_cancel = true;
            $minimum_time_prior_cancel = (int) get_option( 'ab_settings_minimum_time_prior_cancel', 0 );
            if ( $minimum_time_prior_cancel > 0 ) {
                $appointment = new AB_Appointment();
                if ( $appointment->load( $customer_appointment->get( 'appointment_id' ) ) ) {
                    $allow_cancel_time = strtotime( $appointment->get( 'start_date' ) ) - $minimum_time_prior_cancel * HOUR_IN_SECONDS;
                    if ( current_time( 'timestamp' ) > $allow_cancel_time ) {
                        $allow_cancel = false;
                    }
                }
            }

            if ( $allow_cancel ) {
                // Send email.
                AB_NotificationSender::send( AB_NotificationSender::INSTANT_CANCELLED_APPOINTMENT, $customer_appointment );

                $customer_appointment->delete();
            }

            if ( $this->url = $allow_cancel ? get_option( 'ab_settings_cancel_page_url' ) : get_option( 'ab_settings_cancel_denied_page_url' ) ) {
                wp_redirect( $this->url );
                $this->render( 'cancel_appointment' );
                exit;
            }
        }

        $this->url = home_url();
        if ( isset ( $_SERVER['HTTP_REFERER'] ) ) {
            if ( parse_url( $_SERVER['HTTP_REFERER'], PHP_URL_HOST ) == parse_url( $this->url, PHP_URL_HOST ) ) {
                // Redirect back if user came from our site.
                $this->url = $_SERVER['HTTP_REFERER'];
            }
        }
        wp_redirect( $this->url );
        $this->render( 'cancel_appointment' );
        exit;
    }

    /**
     * Apply coupon
     */
    public function executeApplyCoupon()
    {
        if ( ! get_option( 'ab_settings_coupons' ) ) {
            exit;
        }

        $response = null;
        $userData = new AB_UserBookingData( $this->getParameter( 'form_id' ) );

        if ( $userData->load() ) {
            $coupon_code = $this->getParameter( 'coupon' );
            $price = $userData->getServicePriceForStep( 4 );

            $coupon = new AB_Coupon();
            $coupon->loadBy( array(
                'code' => $coupon_code,
            ) );

            if ( $coupon->isLoaded() && $coupon->get( 'used' ) < $coupon->get( 'usage_limit' ) ) {
                $userData->saveData( array( 'coupon' => $coupon_code ) );
                $total = $userData->getTotalPrice();
                $price = round( $total / $userData->get( 'number_of_persons' ), 2 );
                $price > 0 ?: $price = '0.00';
                $response = array(
                    'success' => true,
                    'state'   => 'applied',
                    'text'    => $this->_prepareInfoText( 4, AB_Utils::getTranslatedOption( 'ab_appearance_text_info_coupon' ), $userData, $price ),
                    'total'   => $total
                );
            } else {
                $userData->saveData( array( 'coupon' => null ) );
                $response = array(
                    'success' => true,
                    'state'   => 'invalid',
                    'error'   => __( 'This coupon code is invalid or has been used', 'bookly' ),
                    'text'    => $this->_prepareInfoText( 4, AB_Utils::getTranslatedOption( 'ab_appearance_text_info_coupon' ), $userData, $price )
                );
            }
        } else {
            $response = array( 'success' => false, 'error' => __( 'Session error.', 'bookly' ) );
        }

        // Output JSON response.
        wp_send_json( $response );
    }

    /**
     * Log in to WordPress in the Details step.
     */
    public function executeWpUserLogin()
    {
        /** @var WP_User $user */
        $user = wp_signon();
        if ( is_wp_error( $user ) ) {
            wp_send_json_error( array( 'message' => __( 'Incorrect username or password.' ) ) );
        } else {
            $customer = new AB_Customer();
            if ( $customer->loadBy( array( 'wp_user_id' => $user->ID ) ) ) {
                $user_info = array(
                    'name'  => $customer->get( 'name' ),
                    'email' => $customer->get( 'email' ),
                    'phone' => $customer->get( 'phone' )
                );
            } else {
                $user_info  = array(
                    'name'  =>  $user->display_name,
                    'email' =>  $user->user_email
                );
            }
            $userData = new AB_UserBookingData( $this->getParameter( 'form_id' ) );
            $userData->load();
            $userData->saveData( $user_info );
            wp_send_json_success( $user_info );
        }
    }

    /**
     * Get info for IP.
     */
    public function executeIpInfo()
    {
        $curl = new AB_Curl();
        $curl->options['CURLOPT_CONNECTTIMEOUT'] = 8;
        $curl->options['CURLOPT_TIMEOUT']        = 10;
        if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif ( ! empty( $_SERVER['REMOTE_ADDR'] ) ) {
            $ip = $_SERVER['REMOTE_ADDR'];
        } else {
            $ip = '';
        }
        @header( 'Content-Type: application/json; charset=UTF-8' );
        echo $curl->get( 'http://ipinfo.io/' . $ip .'/json' );
        wp_die();
    }

    /**
     * Output a PNG image of captcha to browser.
     */
    public function executeCaptcha()
    {
        AB_Captcha::draw( $this->getParameter( 'form_id' ) );
    }

    public function executeCaptchaRefresh()
    {
        AB_Captcha::init( $this->getParameter( 'form_id' ) );
        wp_send_json_success( array( 'captcha_url' => admin_url( 'admin-ajax.php?action=ab_captcha&form_id=' ) . $this->getParameter( 'form_id' ) . '&' . microtime( true ) ) );
    }

    /**
     * Render progress tracker into a variable.
     *
     * @param int $booking_step
     * @param int|bool $price
     */
    private function _prepareProgressTracker( $booking_step, $price = false )
    {
        if ( get_option( 'ab_appearance_show_progress_tracker' ) ) {
            $payment_disabled = (
                AB_Config::isPaymentDisabled()
                ||
                // If price is passed and it is zero then do not display payment step.
                $price !== false &&
                $price <= 0
            );

             $tracker = $this->render( '_progress_tracker', compact( 'booking_step', 'payment_disabled' ), false );
        } else {
            $tracker = '';
        }
        $this->progress_tracker = $tracker;
    }

    /**
     * Render info text into a variable.
     *
     * @param integer $step
     * @param string $text
     * @param AB_UserBookingData $userData
     * @param float $price
     * @return string
     */
    private function _prepareInfoText( $step, $text, $userData, $price = null )
    {
        if ( empty( $this->replacement ) ) {
            $service           = $userData->getService();
            $service_name      = $service->getTitle();
            $category_name     = $service->getCategoryName();
            $staff_name        = $userData->getStaffNameForStep( $step );
            $number_of_persons = $userData->get( 'number_of_persons' );
            $service_date      = AB_DateTimeUtils::formatDate( $userData->get( 'appointment_datetime' ) );
            if ( $price === null ) {
                $price = $userData->getServicePriceForStep( $step );
            }
            if ( get_option( 'ab_settings_use_client_time_zone' ) ) {
                $service_time  = AB_DateTimeUtils::formatTime( AB_DateTimeUtils::applyTimeZoneOffset( $userData->get( 'appointment_datetime' ), $userData->get( 'time_zone_offset' ) ) );
            } else {
                $service_time  = AB_DateTimeUtils::formatTime( $userData->get( 'appointment_datetime' ) );
            }

            $this->replacement  = array(
                '[[STAFF_NAME]]'        => '<b>' . $staff_name . '</b>',
                '[[SERVICE_NAME]]'      => '<b>' . $service_name . '</b>',
                '[[CATEGORY_NAME]]'     => '<b>' . $category_name . '</b>',
                '[[NUMBER_OF_PERSONS]]' => '<b>' . $number_of_persons . '</b>',
                '[[SERVICE_TIME]]'      => '<b>' . $service_time . '</b>',
                '[[SERVICE_DATE]]'      => '<b>' . $service_date . '</b>',
                '[[SERVICE_PRICE]]'     => '<b>' . AB_Utils::formatPrice( $price ) . '</b>',
                '[[TOTAL_PRICE]]'       => '<b>' . AB_Utils::formatPrice( $price * $number_of_persons ) . '</b>',
                '[[LOGIN_FORM]]'        => ( get_current_user_id() == 0 ) ? $this->render( '_login_form', array(), false ) : '',
            );
        }

        return strtr( nl2br( $text ), $this->replacement );
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