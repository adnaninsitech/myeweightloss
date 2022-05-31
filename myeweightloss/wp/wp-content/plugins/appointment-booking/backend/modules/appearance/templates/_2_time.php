<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<div id="ab-booking-form" class="ab-booking-form">
    <!-- Progress Tracker-->
    <?php $step = 2; include '_progress_tracker.php'; ?>

    <div class="ab-row-fluid">
      <span data-inputclass="input-xxlarge" data-notes="<?php echo esc_attr( $this->render( '_codes', array( 'step' => 2 ), false ) ) ?>" data-placement="bottom" data-default="<?php echo esc_attr( get_option( 'ab_appearance_text_info_second_step' ) ) ?>" class="ab-text-info-second-preview ab-row-fluid ab_editable" id="ab-text-info-second" data-type="textarea"><?php echo esc_html( get_option( 'ab_appearance_text_info_second_step' ) ) ?></span>
    </div>
    <!-- timeslots -->
    <div class="ab-columnizer-wrap" style="height: 400px;">
        <div class="ab-columnizer">
            <div class="ab-time-screen ab-day-columns" style="display: <?php echo get_option( 'ab_appearance_show_day_one_column' ) == 1 ? ' none' : 'block' ?>">
                <div class="ab-input-wrap ab-slot-calendar">
                    <span class="ab-date-wrap">
                        <input style="display: none" class="ab-selected-date ab-formElement" type="text" data-value="<?php echo date( 'Y-m-d' ) ?>" />
                    </span>
                </div>
                <div class="ab-column col1">
                    <button class="ab-available-day ab-first-child"><?php echo date_i18n( 'D, M d' ) ?></button>
                    <?php for ( $i = 28800; $i <= 57600; $i += 3600 ) : ?>
                        <button class="ab-available-hour ladda-button<?php if ( mt_rand( 0, 1 ) ) echo get_option( 'ab_appearance_show_blocked_timeslots' ) == 1 ? ' booked' : ' no-booked' ?>">
                            <span class="ladda-label">
                                <i class="ab-hour-icon"><span></span></i>
                                <?php echo AB_DateTimeUtils::formatTime( $i ) ?>
                            </span>
                        </button>
                    <?php endfor ?>
                </div>
                <div class="ab-column col2">
                    <button class="ab-available-hour ladda-button ab-last-child">
                        <span class="ladda-label">
                            <i class="ab-hour-icon"><span></span></i><?php echo AB_DateTimeUtils::formatTime( 61200 ) ?>
                        </span>
                    </button>
                    <button class="ab-available-day ab-first-child" style="display: <?php echo get_option( 'ab_appearance_show_calendar' ) == 1 ? ' none' : 'block' ?>"><?php echo date_i18n( 'D, M d', strtotime( '+1 day' ) ) ?></button>
                    <?php for ( $i = 28800; $i <= 54000; $i += 3600 ) : ?>
                        <button class="ab-available-hour ladda-button<?php if ( mt_rand( 0, 1 ) ) echo get_option( 'ab_appearance_show_blocked_timeslots' ) == 1 ? ' booked' : ' no-booked' ?>" style="display: <?php echo get_option( 'ab_appearance_show_calendar' ) == 1 ? ' none' : 'block' ?>">
                            <span class="ladda-label">
                                <i class="ab-hour-icon"><span></span></i><?php echo AB_DateTimeUtils::formatTime( $i ) ?>
                            </span>
                        </button>
                    <?php endfor ?>
                </div>
                <div class="ab-column col3" style="display: <?php echo get_option( 'ab_appearance_show_calendar' ) == 1 ? ' none' : 'inline-block' ?>">
                    <?php for ( $i = 57600; $i <= 61200; $i += 3600 ) : ?>
                        <button class="ab-available-hour ladda-button<?php if ( mt_rand( 0, 1 ) ) echo get_option( 'ab_appearance_show_blocked_timeslots' ) == 1 ? ' booked' : ' no-booked' ?>">
                            <span class="ladda-label">
                                <i class="ab-hour-icon"><span></span></i><?php echo AB_DateTimeUtils::formatTime( $i ) ?>
                            </span>
                        </button>
                    <?php endfor ?>
                    <button class="ab-available-day ab-first-child"><?php echo date_i18n( 'D, M d', strtotime( '+2 day' ) ) ?></button>
                    <?php for ( $i = 28800; $i <= 50400; $i += 3600 ) : ?>
                        <button class="ab-available-hour ladda-button<?php if ( mt_rand( 0, 1 ) ) echo get_option( 'ab_appearance_show_blocked_timeslots' ) == 1 ? ' booked' : ' no-booked' ?>">
                            <span class="ladda-label">
                                <i class="ab-hour-icon"><span></span></i><?php echo AB_DateTimeUtils::formatTime( $i ) ?>
                            </span>
                        </button>
                    <?php endfor ?>
                </div>
                <div class="ab-column col4" style="display: <?php echo get_option( 'ab_appearance_show_calendar' ) == 1 ? ' none' : 'inline-block' ?>">
                    <?php for ( $i = 54000; $i <= 61200; $i += 3600 ) : ?>
                        <button class="ab-available-hour ladda-button<?php if ( mt_rand( 0, 1 ) ) echo get_option( 'ab_appearance_show_blocked_timeslots' ) == 1 ? ' booked' : ' no-booked' ?>">
                            <span class="ladda-label">
                                <i class="ab-hour-icon"><span></span></i><?php echo AB_DateTimeUtils::formatTime( $i ) ?>
                            </span>
                        </button>
                    <?php endfor ?>
                    <button class="ab-available-day ab-first-child"><?php echo date_i18n( 'D, M d', strtotime( '+3 day' ) ) ?></button>
                    <?php for ( $i = 28800; $i <= 46800; $i += 3600 ) : ?>
                        <button class="ab-available-hour ladda-button<?php if ( mt_rand( 0, 1 ) ) echo get_option( 'ab_appearance_show_blocked_timeslots' ) == 1 ? ' booked' : ' no-booked' ?>">
                            <span class="ladda-label">
                                <i class="ab-hour-icon"><span></span></i><?php echo AB_DateTimeUtils::formatTime( $i ) ?>
                            </span>
                        </button>
                    <?php endfor ?>
                </div>
                <div class="ab-column col5" style="display:<?php echo get_option( 'ab_appearance_show_calendar' ) == 1 ? ' none' : ' inline-block' ?>">
                    <?php for ( $i = 50400; $i <= 61200; $i += 3600 ) : ?>
                        <button class="ab-available-hour ladda-button<?php if ( mt_rand( 0, 1 ) ) echo get_option( 'ab_appearance_show_blocked_timeslots' ) == 1 ? ' booked' : ' no-booked' ?>">
                            <span class="ladda-label">
                                <i class="ab-hour-icon"><span></span></i><?php echo AB_DateTimeUtils::formatTime( $i ) ?>
                            </span>
                        </button>
                    <?php endfor ?>
                    <button class="ab-available-day ab-first-child"><?php echo date_i18n( 'D, M d', strtotime( '+4 day' ) ) ?></button>
                    <?php for ( $i = 28800; $i <= 43200; $i += 3600 ) : ?>
                        <button class="ab-available-hour ladda-button<?php if ( mt_rand( 0, 1 ) ) echo get_option( 'ab_appearance_show_blocked_timeslots' ) == 1 ? ' booked' : ' no-booked' ?>">
                            <span class="ladda-label">
                                <i class="ab-hour-icon"><span></span></i><?php echo AB_DateTimeUtils::formatTime( $i ) ?>
                            </span>
                        </button>
                    <?php endfor ?>
                </div>
                <div class="ab-column col6" style="display: <?php echo get_option( 'ab_appearance_show_calendar' ) == 1 ? ' none' : 'inline-block' ?>">
                    <?php for ( $i = 46800; $i <= 61200; $i += 3600 ) : ?>
                        <button class="ab-available-hour ladda-button<?php if ( mt_rand( 0, 1 ) ) echo get_option( 'ab_appearance_show_blocked_timeslots' ) == 1 ? ' booked' : ' no-booked' ?>">
                            <span class="ladda-label">
                                <i class="ab-hour-icon"><span></span></i><?php echo AB_DateTimeUtils::formatTime( $i ) ?>
                            </span>
                        </button>
                    <?php endfor ?>
                    <button class="ab-available-day ab-first-child"><?php echo date_i18n( 'D, M d', strtotime( '+5 day' ) ) ?></button>
                    <?php for ( $i = 28800; $i <= 39600; $i += 3600 ) : ?>
                        <button class="ab-available-hour ladda-button<?php if ( mt_rand( 0, 1 ) ) echo get_option( 'ab_appearance_show_blocked_timeslots' ) == 1 ? ' booked' : ' no-booked' ?>">
                            <span class="ladda-label">
                                <i class="ab-hour-icon"><span></span></i><?php echo AB_DateTimeUtils::formatTime( $i ) ?>
                            </span>
                        </button>
                    <?php endfor ?>
                </div>
                <div class="ab-column col7" style="display:<?php echo get_option( 'ab_appearance_show_calendar' ) == 1 ? ' none' : ' inline-block' ?>">
                    <?php for ( $i = 43200; $i <= 61200; $i += 3600 ) : ?>
                        <button class="ab-available-hour ladda-button<?php if ( mt_rand( 0, 1 ) ) echo get_option( 'ab_appearance_show_blocked_timeslots' ) == 1 ? ' booked' : ' no-booked' ?>">
                            <span class="ladda-label">
                                <i class="ab-hour-icon"><span></span></i><?php echo AB_DateTimeUtils::formatTime( $i ) ?>
                            </span>
                        </button>
                    <?php endfor ?>
                    <button class="ab-available-day ab-first-child"><?php echo date_i18n( 'D, M d', strtotime( '+6 day' ) ) ?></button>
                    <?php for ( $i = 28800; $i <= 36000; $i += 3600 ) : ?>
                        <button class="ab-available-hour ladda-button<?php if ( mt_rand( 0, 1 ) ) echo get_option( 'ab_appearance_show_blocked_timeslots' ) == 1 ? ' booked' : ' no-booked' ?>">
                            <span class="ladda-label">
                                <i class="ab-hour-icon"><span></span></i><?php echo AB_DateTimeUtils::formatTime( $i ) ?>
                            </span>
                        </button>
                    <?php endfor ?>
                </div>
            </div>

            <div class="ab-time-screen ab-day-one-column" style="display: <?php echo get_option( 'ab_appearance_show_day_one_column' ) == 1 ? ' block' : 'none' ?>">
                <div style="margin-right: 40px;" class="ab-input-wrap ab-slot-calendar">
                    <span class="ab-date-wrap">
                        <input style="display: none" class="ab-selected-date ab-formElement" type="text" data-value="<?php echo date( 'Y-m-d' ) ?>" />
                    </span>
                </div>
                <?php for ( $i = 1; $i <= 7; ++ $i ) : ?>
                    <div class="ab-column col<?php echo $i ?>">
                        <button class="ab-available-day ab-first-child"><?php echo date_i18n( 'D, M d', strtotime( '+' . ( $i - 1 ) . ' day' ) ) ?></button>
                        <?php for ( $j = 28800; $j <= 61200; $j += 3600 ) : ?>
                            <button class="ab-available-hour ladda-button<?php if ( mt_rand( 0, 1 ) ) echo get_option( 'ab_appearance_show_blocked_timeslots' ) == 1 ? ' booked' : ' no-booked' ?>">
                            <span class="ladda-label">
                                <i class="ab-hour-icon"><span></span></i><?php echo AB_DateTimeUtils::formatTime( $j ) ?>
                            </span>
                            </button>
                        <?php endfor ?>
                    </div>
                <?php endfor ?>
            </div>
        </div>
    </div>
    <div class="ab-row-fluid ab-nav-steps last-row ab-clear">
        <button class="ab-time-next ab-btn ab-right ladda-button">
            <span class="ab_label">&gt;</span>
        </button>
        <button class="ab-time-prev ab-btn ab-right ladda-button">
            <span class="ab_label">&lt;</span>
        </button>
        <button class="ab-left ab-to-first-step ab-btn ladda-button">
            <span><?php _e( 'Back', 'bookly' ) ?></span>
        </button>
    </div>
</div>
