<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<div class="ab-popup-wrapper">
    <?php if ( AB_Utils::isCurrentUserAdmin() ) : ?>
    <a class="btn btn-info ab-popup-trigger" id="ab-newstaff-member"><?php _e( 'New Staff Member', 'bookly' ) ?></a>
    <?php endif ?>
    <div class="ab-popup" style="display: none; margin-top: 10px;">
        <div class="ab-arrow"></div>
        <div id="ab-new-satff" class="ab-content">
            <table class="form-horizontal" cellspacing="0">
                <tbody>
                <tr>
                    <td><label for="ab-newstaff-wpuser"><?php _e( 'User', 'bookly' ) ?></label></td>
                    <td>
                        <select class="ab-auto-w" name="ab_newstaff_wpuser" id="ab-newstaff-wpuser">
                            <option value=""><?php _e( 'Select from WP users', 'bookly' ) ?></option>
                            <?php foreach ( $users_for_staff as $user ) : ?>
                                <option value="<?php echo $user->ID ?>"><?php echo $user->display_name ?></option>
                            <?php endforeach ?>
                        </select>
                      <img src="<?php echo plugins_url( 'backend/resources/images/help.png', AB_PATH . '/main.php' ) ?>" alt="" class="ab-popover-ext" data-ext_id="ab-staff-popover-ext"/>
                    </td>
                </tr>
                <tr class="form-field form-required">
                    <td><label for="ab-newstaff-fullname"><?php _e( 'Full name', 'bookly' ) ?></label></td>
                    <td><input class="ab-clear-text" id="ab-newstaff-fullname" name="ab_newstaff_fullname" type="text"/><span class="ab-red" style="vertical-align: bottom;font-size: 26px;"> *</span></td>
                </tr>
                <tr>
                    <td></td>
                    <td>
                        <input id="ab-save-newstaff" type="submit" value="<?php _e( 'Save Member', 'bookly' ) ?>" class="btn btn-info">
                        <a class="ab-popup-close" href="javascript:void(0)"><?php _e( 'Cancel', 'bookly' ) ?></a>
                    </td>
                </tr>
                </tbody>
            </table>
          <a class="ab-popup-close ab-popup-close-icon" href="javascript:void(0)"></a>
        </div>
    </div>
</div>
