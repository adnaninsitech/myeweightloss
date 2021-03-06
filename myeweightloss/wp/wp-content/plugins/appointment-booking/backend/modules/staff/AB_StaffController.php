<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Class AB_StaffController
 */
class AB_StaffController extends AB_Controller
{
    const page_slug = 'ab-system-staff';

    protected function getPermissions()
    {
        return get_option( 'ab_settings_allow_staff_members_edit_profile' ) ? array( '_this' => 'user' ) : array();
    }

    public function index()
    {
        /** @var WP_Locale $wp_locale */
        global $wp_locale;

        $this->enqueueStyles( array(
            'frontend' => array_merge(
                array(
                    'css/ladda.min.css',
                ),
                get_option( 'ab_settings_phone_default_country' ) == 'disabled'
                    ? array()
                    : array( 'css/intlTelInput.css' )
            ),
            'backend'  => array(
                'css/bookly.main-backend.css',
                'bootstrap/css/bootstrap.min.css',
                'css/jCal.css',
            ),
            'module'   => array( 'css/staff.css' )
        ) );

        $this->enqueueScripts( array(
            'backend'  => array(
                'bootstrap/js/bootstrap.min.js' => array( 'jquery' ),
                'js/ab_popup.js' => array( 'jquery' ),
                'js/jCal.js'     => array( 'jquery' ),
            ),
            'module'   => array( 'js/staff.js' => array( 'jquery-ui-sortable', 'jquery' ) ),
            'frontend' => array_merge(
                array(
                    'js/spin.min.js'  => array( 'jquery' ),
                    'js/ladda.min.js' => array( 'jquery' ),
                ),
                get_option( 'ab_settings_phone_default_country' ) == 'disabled'
                    ? array()
                    : array( 'js/intlTelInput.min.js' => array( 'jquery' ) )
            ),
        ) );

        wp_localize_script( 'ab-staff.js', 'BooklyL10n',  array(
            'are_you_sure'       => __( 'Are you sure?', 'bookly' ),
            'we_are_not_working' => __( 'We are not working on this day', 'bookly' ),
            'repeat'             => __( 'Repeat every year', 'bookly' ),
            'months'             => array_values( $wp_locale->month ),
            'days'               => array_values( $wp_locale->weekday_abbrev ),
            'intlTelInput'       => array(
                'use'            => ( get_option( 'ab_settings_phone_default_country' ) != 'disabled' ),
                'utils'          => plugins_url( 'intlTelInput.utils.js', AB_PATH . '/frontend/resources/js/intlTelInput.utils.js' ),
                'country'        => get_option( 'ab_settings_phone_default_country' ),
            )
        ) );

        $staff_members = AB_Utils::isCurrentUserAdmin()
            ? AB_Staff::query()->sortBy( 'position' )->fetchArray()
            : AB_Staff::query()->where( 'wp_user_id', get_current_user_id() )->fetchArray();

        if ( $this->hasParameter( 'staff_id' ) ) {
            $active_staff_id = $this->getParameter( 'staff_id' );
        } else {
            $active_staff_id = empty ( $staff_members ) ? 0 : $staff_members[0]['id'];
        }
        // Update staff.
        if ( $this->hasParameter( 'staff' ) && $this->hasParameter( 'staff' ) == 'update' ) {
            $this->updateStaff();
            // Set staff id to load the form for.
            $active_staff_id = $this->getParameter( 'id' );
        }

        // Check if this request is the request after google auth, set the token-data to the staff.
        if ( $this->hasParameter( 'code' ) ) {
            $google = new AB_Google();
            $success_auth = $google->authCodeHandler( $this->getParameter( 'code' ) );

            if ( $success_auth ) {
                $staff_id = base64_decode( strtr( $this->getParameter( 'state' ), '-_,', '+/=' ) );
                $staff = new AB_Staff();
                $staff->load( $staff_id );
                $staff->set( 'google_data', $google->getAccessToken() );
                $staff->save();

                exit ( '<script>location.href="' . AB_Google::generateRedirectURI() . '&staff_id=' . $staff_id . '";</script>' );
            } else {
                $_SESSION['staff_google_auth_error'] = json_encode( $google->getErrors() );
            }
        }

        if ( $this->hasParameter( 'google_logout' ) ) {
            $google = new AB_Google();
            $active_staff_id = $google->logoutByStaffId( $this->getParameter( 'google_logout' ) );
        }
        $form = new AB_StaffMemberEditForm();
        $users_for_staff = $form->getUsersForStaff();

        $this->render( 'list', compact( 'staff_members', 'users_for_staff', 'active_staff_id' ) );
    }

    public function executeCreateStaff()
    {
        $form = new AB_StaffMemberNewForm();
        $form->bind( $this->getPostParameters() );

        $staff = $form->save();
        if ( $staff ) {
            $this->render( 'list_item', compact( 'staff' ) );
            // Register string for translate in WPML.
            do_action( 'wpml_register_single_string', 'bookly', 'staff_' . $staff->get( 'id' ), $staff->get( 'full_name' ) );
        }
        exit;
    }

    public function executeUpdateStaffPosition()
    {
        $staff_sorts = $this->getParameter( 'position' );
        foreach ( $staff_sorts as $position => $staff_id ) {
            $staff_sort = new AB_Staff();
            $staff_sort->load( $staff_id );
            $staff_sort->set( 'position', $position );
            $staff_sort->save();
        }
    }

    public function executeStaffServices()
    {
        $form = new AB_StaffServicesForm();
        $form->load( $this->getParameter( 'id' ) );
        $staff_id   =  $this->getParameter( 'id' );
        $collection = $form->getCollection();
        $selected   = $form->getSelected();
        $uncategorized_services = $form->getUncategorizedServices();

        $this->render( 'services', compact( 'collection', 'selected', 'uncategorized_services', 'staff_id' ) );
        exit;
    }

    public function executeStaffSchedule()
    {
        $staff = new AB_Staff();
        $staff->load( $this->getParameter( 'id' ) );
        $schedule_items = $staff->getScheduleItems();
        $default_breaks_json = json_encode( array( 'staff_id' => $this->getParameter( 'id' ) ) );
        $this->render( 'schedule', compact( 'schedule_items', 'default_breaks_json' ) );
        exit;
    }

    public function executeStaffScheduleUpdate()
    {
        $form = new AB_StaffScheduleForm();
        $form->bind( $this->getPostParameters() );
        $form->save();

        wp_send_json_success();
    }

    /**
     *
     * @throws Exception
     */
    public function executeResetBreaks()
    {
        $breaks = $this->getParameter( 'breaks' );

        // Remove all breaks for staff member.
        $break = new AB_ScheduleItemBreak();
        $break->removeBreaksByStaffId( $breaks['staff_id'] );
        $html_breaks = array();

        // Restore previous breaks.
        if ( isset( $breaks['breaks'] ) && is_array( $breaks['breaks'] ) ) {
            foreach ( $breaks['breaks'] as $day ) {
                $schedule_item_break = new AB_ScheduleItemBreak();
                $schedule_item_break->setFields( $day );
                $schedule_item_break->save();
            }
        }

        $staff = new AB_Staff();
        $staff->load( $breaks['staff_id'] );

        // Make array with breaks (html) for each day.
        foreach ( $staff->getScheduleItems() as $item ) {
            /** @var AB_StaffScheduleItem $item */
            $html_breaks[ $item->get( 'id' ) ] = $this->render( '_breaks', array(
                'day_is_not_available' => null === $item->get( 'start_time' ),
                'item'                 => $item,
            ), false );
        }

        wp_send_json( $html_breaks );
    }

    public function executeStaffScheduleHandleBreak()
    {
        $start_time    = $this->getParameter( 'start_time' );
        $end_time      = $this->getParameter( 'end_time' );
        $working_start = $this->getParameter( 'working_start' );
        $working_end   = $this->getParameter( 'working_end' );

        if ( AB_DateTimeUtils::timeToSeconds( $start_time ) >= AB_DateTimeUtils::timeToSeconds( $end_time ) ) {
            wp_send_json( array(
                'success'   => false,
                'error_msg' => __( 'The start time must be less than the end one', 'bookly' ),
            ) );
        }

        $staffScheduleItem = new AB_StaffScheduleItem();
        $staffScheduleItem->load( $this->getParameter( 'staff_schedule_item_id' ) );

        $bound = array( $staffScheduleItem->get( 'start_time' ), $staffScheduleItem->get( 'end_time' ) );
        $break_id = $this->getParameter( 'break_id', 0 );

        $in_working_time = $working_start <= $start_time && $start_time <= $working_end
            && $working_start <= $end_time && $end_time <= $working_end;
        if ( !$in_working_time || ! $staffScheduleItem->isBreakIntervalAvailable( $start_time, $end_time, $break_id ) ) {
            wp_send_json( array(
                'success'   => false,
                'error_msg' => __( 'The requested interval is not available', 'bookly' ),
            ) );
        }

        $formatted_start    = AB_DateTimeUtils::formatTime( AB_DateTimeUtils::timeToSeconds( $start_time ) );
        $formatted_end      = AB_DateTimeUtils::formatTime( AB_DateTimeUtils::timeToSeconds( $end_time ) );
        $formatted_interval = $formatted_start . ' - ' . $formatted_end;

        if ( $break_id ) {
            $break = new AB_ScheduleItemBreak();
            $break->load( $break_id );
            $break->set( 'start_time', $start_time );
            $break->set( 'end_time', $end_time );
            $break->save();

            wp_send_json( array(
                'success'      => true,
                'new_interval' => $formatted_interval,
            ) );
        } else {
            $form = new AB_StaffScheduleItemBreakForm();
            $form->bind( $this->getPostParameters() );

            $staffScheduleItemBreak = $form->save();
            if ( $staffScheduleItemBreak ) {
                $breakStart = new AB_TimeChoiceWidget( array( 'use_empty' => false, 'type' => 'from',  'bound' => $bound ) );
                $break_start_choices = $breakStart->render(
                    '',
                    $start_time,
                    array( 'class'              => 'break-start form-control',
                           'data-default_value' => AB_StaffScheduleItem::WORKING_START_TIME
                    )
                );
                $breakEnd = new AB_TimeChoiceWidget( array( 'use_empty' => false, 'type' => 'bound',  'bound' => $bound ) );
                $break_end_choices = $breakEnd->render(
                    '',
                    $end_time,
                    array( 'class'              => 'break-end form-control',
                           'data-default_value' => date( 'H:i:s', strtotime( AB_StaffScheduleItem::WORKING_START_TIME . ' + 1 hour' ) )
                    )
                );
                wp_send_json( array(
                    'success'      => true,
                    'item_content' => $this->render( '_break', array(
                        'staff_schedule_item_break_id'  => $staffScheduleItemBreak->get( 'id' ),
                        'formatted_interval'            => $formatted_interval,
                        'break_start_choices'           => $break_start_choices,
                        'break_end_choices'             => $break_end_choices,
                    ), false ),
                ) );
            } else {
                wp_send_json( array(
                    'success'   => false,
                    'error_msg' => __( 'Error adding the break interval', 'bookly' ),
                ) );
            }
        }
    }

    public function executeDeleteStaffScheduleBreak()
    {
        $break = new AB_ScheduleItemBreak();
        $break->load( $this->getParameter( 'id' ) );
        $break->delete();

        wp_send_json_success();
    }

    public function executeStaffServicesUpdate()
    {
        $form = new AB_StaffServicesForm();
        $form->bind( $this->getPostParameters() );
        $form->save();

        wp_send_json_success();
    }

    public function executeEditStaff()
    {
        $errors = array();
        $form   = new AB_StaffMemberEditForm();
        $staff  = new AB_Staff();
        $staff->load( $this->getParameter( 'id' ) );

        if ( isset( $_SESSION['staff_updated'] ) ) {
            unset( $_SESSION['staff_updated'] );
            $this->updated = true;
        } elseif ( isset ( $_SESSION['staff_google_calendar_error'] ) ) {
            $errors[] = __( 'Calendar ID is not valid.', 'bookly' ) . ' (' . $_SESSION['staff_google_calendar_error'] . ')';
            unset( $_SESSION['staff_google_calendar_error'] );
        }

        if ( isset( $_SESSION['staff_google_auth_error'] ) ) {
            foreach ( json_decode( $_SESSION['staff_google_auth_error'] ) as $error ) {
                $errors[] = $error;
            }
            unset( $_SESSION['staff_google_auth_error'] );
        }

        if ( $staff->get( 'google_data' ) == '' ) {
            if ( get_option( 'ab_settings_google_client_id' ) == '' ) {
                $authUrl = false;
            } else {
                $google = new AB_Google();
                $authUrl = $google->createAuthUrl( $this->getParameter( 'id' ) );
            }
        }
        // Register string for translate in WPML.
        do_action( 'wpml_register_single_string', 'bookly', 'staff_' . $staff->get( 'id' ), $staff->get( 'full_name' ) );

        $users_for_staff = AB_Utils::isCurrentUserAdmin() ? $form->getUsersForStaff( $staff->get( 'id' ) ) : array();

        $this->render( 'edit', compact( 'staff', 'users_for_staff', 'errors', 'authUrl' ) );
        exit;
    }

    /**
     * Update staff from POST request.
     */
    public function updateStaff()
    {
        if ( ! AB_Utils::isCurrentUserAdmin() ) {
            // Check permissions to prevent one staff member from updating profile of another staff member.
            do {
                if ( get_option( 'ab_settings_allow_staff_members_edit_profile' ) ) {
                    $staff = new AB_Staff();
                    $staff->load( $this->getParameter( 'id' ) );
                    if ( $staff->get( 'wp_user_id' ) == get_current_user_id() ) {
                        unset ( $_POST['wp_user_id'] );
                        break;
                    }
                }
                do_action( 'admin_page_access_denied' );
                wp_die( 'Bookly: ' . __( 'You do not have sufficient permissions to access this page.' ) );
            } while ( 0 );
        }
        $form = new AB_StaffMemberEditForm();
        $form->bind( $this->getPostParameters(), $_FILES );
        $result = $form->save();

        if ( $result === false && array_key_exists( 'google_calendar_error', $form->getErrors() ) ) {
            $errors = $form->getErrors();
            $_SESSION['staff_google_calendar_error'] = $errors['google_calendar_error'];
        } else {
            $_SESSION['staff_updated'] = true;
        }
    }

    public function executeDeleteStaff()
    {
        $staff = new AB_Staff();
        $staff->load( $this->getParameter( 'id' ) );
        $staff->delete();
        $form = new AB_StaffMemberForm();
        wp_send_json( $form->getUsersForStaff() );
    }

    public function executeDeleteStaffAvatar()
    {
        $staff = new AB_Staff();
        $staff->load( $this->getParameter( 'id' ) );
        if ( file_exists( $staff->get( 'avatar_path' ) ) ) {
            unlink( $staff->get( 'avatar_path' ) );
        }
        $staff->set( 'avatar_url', '' );
        $staff->set( 'avatar_path', '' );
        $staff->save();
        wp_send_json_success();
    }

    public function executeStaffHolidays()
    {
        $staff_id = $this->getParameter( 'id', 0 );
        $holidays = $this->getHolidays( $staff_id );
        $this->render( 'holidays', compact ( 'holidays', 'staff_id' ) );
        exit;
    }

    public function executeStaffHolidaysUpdate()
    {
        $id       = $this->getParameter( 'id' );
        $holiday  = $this->getParameter( 'holiday' ) == 'true';
        $repeat   = $this->getParameter( 'repeat' ) == 'true';
        $day      = $this->getParameter( 'day', false );
        $staff_id = $this->getParameter( 'staff_id' );
        if ( $staff_id ) {
            // Update or delete the event.
            if ( $id ) {
                if ( $holiday ) {
                    $this->getWpdb()->update( AB_Holiday::getTableName(), array( 'repeat_event' => intval( $repeat ) ), array( 'id' => $id ), array( '%d' ) );
                } else {
                    AB_Holiday::query()->delete()->where( 'id', $id )->execute();
                }
                // Add the new event.
            } elseif ( $holiday && $day ) {
                $this->getWpdb()->insert( AB_Holiday::getTableName(), array( 'date' => $day, 'repeat_event' => intval( $repeat ), 'staff_id' => $staff_id ), array( '%s', '%d', '%d' ) );
            }

            // And return refreshed events.
            echo $this->getHolidays( $staff_id );
        }
        exit;
    }

    // Protected methods.

    protected function getHolidays( $staff_id )
    {
        $collection = AB_Holiday::query( 'h' )->where( 'h.staff_id', $staff_id )->fetchArray();
        $holidays = array();
        if ( count( $collection ) ) {
            foreach ( $collection as $holiday ) {
                $holidays[ $holiday['id'] ] = array(
                    'm' => intval( date( 'm', strtotime( $holiday['date'] ) ) ),
                    'd' => intval( date( 'd', strtotime( $holiday['date'] ) ) ),
                    'title' => $holiday['title'],
                );
                // if not repeated holiday, add the year
                if ( ! $holiday['repeat_event'] ) {
                    $holidays[ $holiday['id'] ]['y'] = intval( date( 'Y', strtotime( $holiday['date'] ) ) );
                }
            }
        }

        return json_encode( $holidays );
    }

    /**
     * Extend parent method to control access on staff member level.
     *
     * @param string $action
     * @return bool
     */
    protected function hasAccess( $action )
    {
        if ( parent::hasAccess( $action ) ) {

            if ( ! AB_Utils::isCurrentUserAdmin() ) {
                $staff = new AB_Staff();

                switch ( $action ) {
                    case 'executeEditStaff':
                    case 'executeDeleteStaffAvatar':
                    case 'executeStaffServices':
                    case 'executeStaffSchedule':
                    case 'executeStaffHolidays':
                        $staff->load( $this->getParameter( 'id' ) );
                        break;
                    case 'executeStaffServicesUpdate':
                    case 'executeStaffHolidaysUpdate':
                        $staff->load( $this->getParameter( 'staff_id' ) );
                        break;
                    case 'executeStaffScheduleHandleBreak':
                        $staffScheduleItem = new AB_StaffScheduleItem();
                        $staffScheduleItem->load( $this->getParameter( 'staff_schedule_item_id' ) );
                        $staff->load( $staffScheduleItem->get( 'staff_id' ) );
                        break;
                    case 'executeDeleteStaffScheduleBreak':
                        $break = new AB_ScheduleItemBreak();
                        $break->load( $this->getParameter( 'id' ) );
                        $staffScheduleItem = new AB_StaffScheduleItem();
                        $staffScheduleItem->load( $break->get( 'staff_schedule_item_id' ) );
                        $staff->load( $staffScheduleItem->get( 'staff_id' ) );
                        break;
                    case 'executeStaffScheduleUpdate':
                        if ( $this->hasParameter( 'days' ) ) {
                            foreach ( $this->getParameter( 'days' ) as $id => $day_index ) {
                                $staffScheduleItem = new AB_StaffScheduleItem();
                                $staffScheduleItem->load( $id );
                                $staff = new AB_Staff();
                                $staff->load( $staffScheduleItem->get( 'staff_id' ) );
                                if ( $staff->get( 'wp_user_id' ) != get_current_user_id() ) {
                                    return false;
                                }
                            }
                        }
                        break;
                    default:
                        return false;
                }

                return $staff->get( 'wp_user_id' ) == get_current_user_id();
            }

            return true;
        }

        return false;
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