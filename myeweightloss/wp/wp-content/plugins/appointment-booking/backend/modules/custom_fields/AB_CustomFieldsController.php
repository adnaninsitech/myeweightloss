<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Class AB_CustomFieldsController
 */
class AB_CustomFieldsController extends AB_Controller
{
    /**
     *  Default Action
     */
    public function index()
    {
        $this->enqueueStyles( array(
            'module'   => array( 'css/custom_fields.css' ),
            'frontend' => array( 'css/ladda.min.css' ),
            'backend'  => array(
                'css/bookly.main-backend.css',
                'bootstrap/css/bootstrap.min.css',
            )
        ) );

        $this->enqueueScripts( array(
            'module'   => array( 'js/custom_fields.js' => array( 'jquery-ui-sortable' ) ),
            'frontend' => array(
                'js/spin.min.js'  => array( 'jquery' ),
                'js/ladda.min.js' => array( 'jquery' ),
            )
        ) );

        wp_localize_script( 'ab-custom_fields.js', 'BooklyL10n', array(
            'custom_fields' => get_option( 'ab_custom_fields' )
        ) );

        $this->render( 'index' );
    } // index

    /**
     * Save custom fields.
     */
    public function executeSaveCustomFields()
    {
        $custom_fields = $this->getParameter( 'fields' );
        foreach ( json_decode( $custom_fields ) as $custom_field ) {
            switch ( $custom_field->type ) {
                case 'textarea':
                case 'text-field':
                case 'captcha':
                    do_action( 'wpml_register_single_string', 'bookly', 'custom_field_' . $custom_field->id . '_' .sanitize_title( $custom_field->label ), $custom_field->label );
                    break;
                case 'checkboxes':
                case 'radio-buttons':
                case 'drop-down':
                    do_action( 'wpml_register_single_string', 'bookly', 'custom_field_' . $custom_field->id . '_' . sanitize_title( $custom_field->label ), $custom_field->label );
                    foreach ( $custom_field->items as $label ) {
                        do_action( 'wpml_register_single_string', 'bookly', 'custom_field_' . $custom_field->id . '_' . sanitize_title( $custom_field->label ) . '=' . sanitize_title( $label ), $label );
                    }
                    break;
            }
        }
        update_option( 'ab_custom_fields', $custom_fields );
        wp_send_json_success();
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