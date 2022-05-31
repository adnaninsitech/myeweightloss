<?php


add_action( 'admin_init', 'theme_options_init' );
add_action( 'admin_menu', 'theme_options_add_page' );

/**
 * Init plugin options to white list our options
 */
function theme_options_init(){
	register_setting( 'sample_options', 'cOptn', 'theme_options_validate' );
}

/**
 * Load up the menu page
 */
function theme_options_add_page() {
	add_theme_page( __( 'Theme Options', 'sampletheme' ), __( 'Quick Links', 'sampletheme' ), 'edit_theme_options', 'theme_options', 'theme_options_do_page' );
}

/**
 * Create arrays for our select and radio options
 */
$select_options = array(
	'0' => array(
		'value' =>	'0',
		'label' => __( 'Zero', 'sampletheme' )
	),
	'1' => array(
		'value' =>	'1',
		'label' => __( 'One', 'sampletheme' )
	),
	'2' => array(
		'value' => '2',
		'label' => __( 'Two', 'sampletheme' )
	),
	'3' => array(
		'value' => '3',
		'label' => __( 'Three', 'sampletheme' )
	),
	'4' => array(
		'value' => '4',
		'label' => __( 'Four', 'sampletheme' )
	),
	'5' => array(
		'value' => '3',
		'label' => __( 'Five', 'sampletheme' )
	)
);

$radio_options = array(
	'yes' => array(
		'value' => 'yes',
		'label' => __( 'Yes', 'sampletheme' )
	),
	'no' => array(
		'value' => 'no',
		'label' => __( 'No', 'sampletheme' )
	),
	'maybe' => array(
		'value' => 'maybe',
		'label' => __( 'Maybe', 'sampletheme' )
	)
);

/**
 * Create the options page
 */
function theme_options_do_page() {
	global $select_options, $radio_options;

	if ( ! isset( $_REQUEST['settings-updated'] ) )
		$_REQUEST['settings-updated'] = false;

	?>
	<div class="wrap">
		<?php screen_icon(); echo "<h2>" . get_current_theme() . __( ' Theme Options', 'sampletheme' ) . "</h2>"; ?>

		<?php if ( false !== $_REQUEST['settings-updated'] ) : ?>
		<div class="updated fade"><p><strong><?php _e( 'Options saved', 'sampletheme' ); ?></strong></p></div>
		<?php endif; ?>
		
		<?php // echo $_SERVER['REQUEST_URI']; ?>
		<form id="file-form" enctype="multipart/form-data" action="<?php echo site_url(); ?>/wp-admin/themes.php?page=theme_options" method="POST">
            <table class="form-table">
    
                    <tr valign="top"><th scope="row"><?php _e( 'Logo Path', 'sampletheme' ); ?></th>
                        <td>
                            <p id="async-upload-wrap">
                            <label for="async-upload">Upload</label>
                            <input type="file" id="async-upload" name="async-upload"> <input type="submit" value="Upload" name="html-upload">
                            </p>
                        
                            <p>
                            <input type="hidden" name="post_id" id="post_id" value="1199" />
                            <?php wp_nonce_field('client-file-upload'); ?>
                            <input type="hidden" name="redirect_to" value="<?php echo $_SERVER['REQUEST_URI']; ?>" />
                            </p>
                        
                            <p>
                            <input type="submit" value="Save all changes" name="save" style="display: none;">
                            </p>
                        </td>
        </tr></table>
        </form>
    
    <?php 
	if ( isset( $_POST['html-upload'] ) && !empty( $_FILES ) ) {
	require_once(ABSPATH . 'wp-admin/includes/admin.php');
	$id = media_handle_upload('async-upload', 1199); //post id of Client Files page
	unset($_FILES);
	if ( is_wp_error($id) ) {
		$errors['upload_error'] = $id;
		$id = false;
	}

	if ($errors) {
		echo "<p>There was an error uploading your file.</p>";
	} else {
		echo "<p>Your file has been uploaded.</p>";
		//echo $id;
		//echo $_FILES['image']['name'][0];
		echo wp_get_attachment_image($options['logo'],'100');
	}
	
}
echo wp_get_attachment_image($options['logo'],'100');

?>
    
        
        
        <form method="post" action="options.php" >
			<?php settings_fields( 'sample_options' ); ?>
			<?php $options = get_option( 'cOptn' ); ?>

			<table class="form-table">

				<tr valign="top"><th scope="row"><?php _e( 'Preview', 'sampletheme' ); ?></th>
					<td>
                    	<?php 
							$options = get_option('cOptn');
							$oldId = $options['logo'];
							if( $id == NULL){
								$id = $oldId;	
							}
							echo wp_get_attachment_image($id,'100');
						 ?>
                        
						<input id="cOptn[logo]" class="regular-text" type="text" name="cOptn[logo]" value="<?php esc_attr_e( $id ); ?>" style=" width:1px"  />
						<label class="description" for="cOptn[logo]"><?php _e( 'Uploaded media Path', 'sampletheme' ); ?></label>
                        
					</td>
				</tr>
				
				<?php
				/**
				 * A sample textarea option
				 */
				?>
                
                <tr valign="top"><th scope="row"><?php _e( 'facebook', 'sampletheme' ); ?></th>
					<td>
						<input id="cOptn[facebook]" class="large-text" type="text" name="cOptn[facebook]" value="<?php echo esc_textarea( $options['facebook'] ); ?>">
                        <label class="description" for="cOptn[facebook]"><?php _e( 'Please Enter Facebook profile link', 'sampletheme' ); ?></label>
					</td>
				</tr>
                
                  <tr valign="top"><th scope="row"><?php _e( 'instagram', 'sampletheme' ); ?></th>
					<td>
						<input id="cOptn[instagram]" class="large-text" type="text" name="cOptn[instagram]" value="<?php echo esc_textarea( $options['instagram'] ); ?>">
                        <label class="description" for="cOptn[instagram]"><?php _e( 'Please Enter instagram profile link', 'sampletheme' ); ?></label>
					</td>
				</tr>
                
                  <tr valign="top"><th scope="row"><?php _e( 'youtube', 'sampletheme' ); ?></th>
					<td>
						<input id="cOptn[youtube]" class="large-text" type="text" name="cOptn[youtube]" value="<?php echo esc_textarea( $options['youtube'] ); ?>">
                        <label class="description" for="cOptn[youtube]"><?php _e( 'Please Enter youtube profile link', 'sampletheme' ); ?></label>
					</td>
				</tr>
                <tr valign="top"><th scope="row"><?php _e( 'twitter', 'sampletheme' ); ?></th>
					<td>
						<input id="cOptn[twitter]" class="large-text" type="text" name="cOptn[twitter]" value="<?php echo esc_textarea( $options['twitter'] ); ?>">
                        <label class="description" for="cOptn[twitter]"><?php _e( 'Please Enter Twitter profile link', 'sampletheme' ); ?></label>
					</td>
				</tr>
                <tr valign="top"><th scope="row"><?php _e( 'tumblr', 'sampletheme' ); ?></th>
					<td>
						<input id="cOptn[tumblr]" class="large-text" type="text" name="cOptn[tumblr]" value="<?php echo esc_textarea( $options['tumblr'] ); ?>">
                        <label class="description" for="cOptn[tumblr]"><?php _e( 'Please Enter Tumblr link', 'sampletheme' ); ?></label>
					</td>
				</tr>
                <tr valign="top"><th scope="row"><?php _e( 'googleplus ', 'sampletheme' ); ?></th>
					<td>
						<input id="cOptn[googleplus]" class="large-text" type="text" name="cOptn[googleplus]" value="<?php echo esc_textarea( $options['googleplus'] ); ?>">
                        <label class="description" for="cOptn[googleplus]"><?php _e( 'Please Enter googleplus', 'sampletheme' ); ?></label>
					</td>
				</tr>
                 <tr valign="top"><th scope="row"><?php _e( 'linkedin', 'sampletheme' ); ?></th>
					<td>
						<input id="cOptn[linkedin]" class="large-text" type="text" name="cOptn[linkedin]" value="<?php echo esc_textarea( $options['linkedin'] ); ?>">
                        <label class="description" for="cOptn[linkedin]"><?php _e( 'Please Enter linkedin link', 'sampletheme' ); ?></label>
					</td>
				</tr>
                
				<tr valign="top"><th scope="row"><?php _e( 'Copyright', 'sampletheme' ); ?></th>
					<td>
						<textarea id="cOptn[copyright]" class="large-text" cols="50" rows="10" name="cOptn[copyright]"><?php echo esc_textarea( $options['copyright'] ); ?></textarea>
						<label class="description" for="cOptn[copyright]"><?php _e( 'Footer Copyright Text', 'sampletheme' ); ?></label>
					</td>
				</tr>
                
                              
				<tr valign="top"><th scope="row"><?php _e( 'FooterAbout', 'sampletheme' ); ?></th>
					<td>
		<textarea id="cOptn[FooterAbout]" class="large-text" cols="50" rows="10" name="cOptn[FooterAbout]"><?php echo esc_textarea( $options['FooterAbout'] ); ?></textarea>
						<label class="description" for="cOptn[FooterAbout]"><?php _e( 'Footer About Text', 'sampletheme' ); ?></label>
					</td>
				</tr>
                <tr valign="top"><th scope="row"><?php _e( 'StaffName', 'sampletheme' ); ?></th>
					<td>
		<textarea id="cOptn[StaffName]" class="large-text" cols="50" rows="10" name="cOptn[StaffName]"><?php echo esc_textarea( $options['StaffName'] ); ?></textarea>
						<label class="description" for="cOptn[StaffName]"><?php _e( 'Staff Names', 'sampletheme' ); ?></label>
					</td>
				</tr>
                
                <tr valign="top"><th scope="row"><?php _e( 'branches', 'sampletheme' ); ?></th>
					<td>
						<input id="cOptn[branches]" class="large-text" type="text" name="cOptn[branches]" value="<?php echo esc_textarea( $options['branches'] ); ?>">
                        <label class="description" for="cOptn[branches]"><?php _e( 'Please Enter Facebook profile link', 'sampletheme' ); ?></label>
					</td>
				</tr>
                
                 
                <tr valign="top"><th scope="row"><?php _e( 'phone_number', 'sampletheme' ); ?></th>
					<td>
						<input id="cOptn[phone_number]" class="large-text" type="text" name="cOptn[phone_number]" value="<?php echo esc_textarea( $options['phone_number'] ); ?>">
                        <label class="description" for="cOptn[phone_number]"><?php _e( 'Please Enter Phone Number', 'sampletheme' ); ?></label>
					</td>
				</tr>
              
              
                <tr valign="top"><th scope="row"><?php _e( 'phone_number1', 'sampletheme' ); ?></th>
					<td>
						<input id="cOptn[phone_number1]" class="large-text" type="text" name="cOptn[phone_number1]" value="<?php echo esc_textarea( $options['phone_number1'] ); ?>">
                        <label class="description" for="cOptn[phone_number1]"><?php _e( 'Please Enter Phone Number', 'sampletheme' ); ?></label>
					</td>
				</tr>
              
                
                <tr valign="top"><th scope="row"><?php _e( 'email', 'sampletheme' ); ?></th>
					<td>
						<input id="cOptn[email]" class="large-text" type="text" name="cOptn[email]" value="<?php echo esc_textarea( $options['email'] ); ?>">
                        <label class="description" for="cOptn[email]"><?php _e( 'Please Enter Your Email Address', 'sampletheme' ); ?></label>
					</td>
				</tr>
                
                
                
                
                
                <tr valign="top"><th scope="row"><?php _e( 'fax', 'sampletheme' ); ?></th>
					<td>
						<input id="cOptn[fax]" class="large-text" type="text" name="cOptn[fax]" value="<?php echo esc_textarea( $options['fax'] ); ?>">
                        <label class="description" for="cOptn[fax]"><?php _e( 'Please Enter Your fax Address', 'sampletheme' ); ?></label>
					</td>
				</tr>

                
           <tr valign="top"><th scope="row"><?php _e( 'support-email', 'sampletheme' ); ?></th>
					<td>
						<input id="cOptn[support-email]" class="large-text" type="text" name="cOptn[support-email]" value="<?php echo esc_textarea( $options['support-email'] ); ?>">
                        <label class="description" for="cOptn[support-email]"><?php _e( 'Please Enter Your Email Address', 'sampletheme' ); ?></label>
					</td>
				</tr>
                
                
                
                    <tr valign="top"><th scope="row"><?php _e( 'website', 'sampletheme' ); ?></th>
					<td>
						<input id="cOptn[website]" class="large-text" type="text" name="cOptn[website]" value="<?php echo esc_textarea( $options['website'] ); ?>">
                        <label class="description" for="cOptn[website]"><?php _e( 'Please Enter Your Website Address', 'sampletheme' ); ?></label>
					</td>
				</tr>
                
                                
                <tr valign="top"><th scope="row"><?php _e( 'header-text', 'sampletheme' ); ?></th>
					<td>
						<input id="cOptn[header-text]" class="large-text" type="text" name="cOptn[header-text]" value="<?php echo esc_textarea( $options['header-text'] ); ?>">
                        <label class="description" for="cOptn[header-text]"><?php _e( 'Please Enter Your Header Text', 'sampletheme' ); ?></label>
					</td>
				</tr>
                
                <tr valign="top"><th scope="row"><?php _e( 'timings', 'sampletheme' ); ?></th>
					<td>
						<input id="cOptn[timings]" class="large-text" type="text" name="cOptn[timings]" value="<?php echo esc_textarea( $options['timings'] ); ?>">
                        <label class="description" for="cOptn[timings]"><?php _e( 'Please Enter Your Header Text', 'sampletheme' ); ?></label>
					</td>
				</tr>
                
                <tr valign="top"><th scope="row"><?php _e( 'timings2', 'sampletheme' ); ?></th>
					<td>
						<input id="cOptn[timings2]" class="large-text" type="text" name="cOptn[timings2]" value="<?php echo esc_textarea( $options['timings2'] ); ?>">
                        <label class="description" for="cOptn[timings2]"><?php _e( 'Please Enter Your Header Text', 'sampletheme' ); ?></label>
					</td>
				</tr>
                
                <tr valign="top"><th scope="row"><?php _e( 'timings3', 'sampletheme' ); ?></th>
					<td>
						<input id="cOptn[timings3]" class="large-text" type="text" name="cOptn[timings3]" value="<?php echo esc_textarea( $options['timings3'] ); ?>">
                        <label class="description" for="cOptn[timings3]"><?php _e( 'Please Enter Your Header Text', 'sampletheme' ); ?></label>
					</td>
				</tr>
                     
                       <tr valign="top"><th scope="row"><?php _e( 'footer-about', 'sampletheme' ); ?></th>
					<td>
						<input id="cOptn[footer-about]" class="large-text" type="text" name="cOptn[footer-about]" value="<?php echo esc_textarea( $options['footer-about'] ); ?>">
                        <label class="description" for="cOptn[footer-about]"><?php _e( 'Please Enter Your Footer About', 'sampletheme' ); ?></label>
					</td>
				</tr>
               
                
                   <tr valign="top"><th scope="row"><?php _e( 'Address', 'sampletheme' ); ?></th>
					<td>
						<input id="cOptn[Address]" class="large-text" type="text" name="cOptn[Address]" value="<?php echo esc_textarea( $options['Address'] ); ?>">
                        <label class="description" for="cOptn[Address]"><?php _e( 'Please Enter Address', 'sampletheme' ); ?></label>
					</td>
				</tr>
                
                  <tr valign="top"><th scope="row"><?php _e( 'Mailing Address', 'sampletheme' ); ?></th>
					<td>
						<input id="cOptn[Mailing Address]" class="large-text" type="text" name="cOptn[Mailing Address]" value="<?php echo esc_textarea( $options['Mailing Address'] ); ?>">
                        <label class="description" for="cOptn[Mailing Address]"><?php _e( 'Please Enter Mailing Address', 'sampletheme' ); ?></label>
					</td>
				</tr>
                
             
                   <tr valign="top"><th scope="row"><?php _e( 'footer-desc', 'sampletheme' ); ?></th>
					<td>
						<input id="cOptn[footer-desc]" class="large-text" type="text" name="cOptn[footer-desc]" value="<?php echo esc_textarea( $options['footer-desc'] ); ?>">
                        <label class="description" for="cOptn[footer-desc]"><?php _e( 'Please Enter footer-desc', 'sampletheme' ); ?></label>
					</td>
				</tr>
                
                 
                 
			</table>

			<p class="submit">
				<input type="submit" class="button-primary" value="<?php _e( 'Save Options', 'sampletheme' ); ?>" />
			</p>
		</form>
	</div>
	<?php
}

/**
 * Sanitize and validate input. Accepts an array, return a sanitized array.
 */
function theme_options_validate( $input ) {
	global $select_options, $radio_options;

	// Our checkbox value is either 0 or 1
	if ( ! isset( $input['option1'] ) )
		$input['option1'] = null;
	$input['option1'] = ( $input['option1'] == 1 ? 1 : 0 );

	// Say our text option must be safe text with no HTML tags
	$input['sometext'] = wp_filter_nohtml_kses( $input['sometext'] );

	// Our select option must actually be in our array of select options
	if ( ! array_key_exists( $input['selectinput'], $select_options ) )
		$input['selectinput'] = null;

	// Our radio option must actually be in our array of radio options
	if ( ! isset( $input['radioinput'] ) )
		$input['radioinput'] = null;
	if ( ! array_key_exists( $input['radioinput'], $radio_options ) )
		$input['radioinput'] = null;

	// Say our textarea option must be safe text with the allowed tags for posts
	$input['sometextarea'] = wp_filter_post_kses( $input['sometextarea'] );

	return $input;
}

// adapted from http://planetozh.com/blog/2009/05/handling-plugins-options-in-wordpress-28-with-register_setting/