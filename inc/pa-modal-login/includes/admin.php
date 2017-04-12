<?php

/*-----------------------------------------------------------------------------------*/
/* Create the admin menu */
/*-----------------------------------------------------------------------------------*/

function paml_admin_resources() {
	wp_enqueue_script( 'paml-admin-script', PAML_PLUGIN_ASSETS_URL . 'js/login-admin.js', array( 'jquery' ), '2.0.5', true );
}


/*-----------------------------------------------------------------------------------*/
/* Register the admin page with the 'admin_menu' */
/*-----------------------------------------------------------------------------------*/

function paml_admin_menu() {
	$page = add_submenu_page( 'options-general.php', __( 'PA Modal Login', 'pressapps' ), __( 'PA Modal Login', 'pressapps' ), 'manage_options', 'paml-options', 'paml_options', 99 );

	add_action( 'admin_print_styles-' . $page, 'paml_admin_resources' );
}
add_action( 'admin_menu', 'paml_admin_menu' );


/*-----------------------------------------------------------------------------------*/
/* Load HTML that will create the outter shell of the admin page */
/*-----------------------------------------------------------------------------------*/

function paml_options() {
    // Check that the user is able to view this page.
	if ( ! current_user_can( 'manage_options' ) )
		wp_die( __( 'You do not have sufficient permissions to access this page.', 'pressapps' ) ); ?>

	<div class="wrap">
		<div id="icon-themes" class="icon32"></div>
		<h2><?php _e( 'Modal Login Settings', 'pressapps' ); ?></h2>

		<?php // settings_errors(); ?>

		<form action="options.php" method="post">
			<?php settings_fields( 'paml_setup_options' ); ?>
			<?php do_settings_sections( 'paml_setup_options' ); ?>
			<?php settings_fields( 'paml_style_options' ); ?>
			<?php do_settings_sections( 'paml_style_options' ); ?>
            <?php settings_fields( 'paml_email_options' ); ?>
			<?php do_settings_sections( 'paml_email_options' ); ?>
			<?php submit_button(); ?>
		</form>

	</div>
<?php }

/*-----------------------------------------------------------------------------------*/
/* Registers all sections and fields with the Settings API */
/*-----------------------------------------------------------------------------------*/

function paml_init_settings_registration() {
	$option_name = 'paml_options';

	// Check if settings options exist in the database. If not, add them.
	if ( get_option( 'paml_options' ) )
		add_option( 'paml_options' );
	// Define settings sections.
	add_settings_section( 'paml_setup_section', __( 'Setup', 'pressapps' )  , 'paml_setup_options', 'paml_setup_options' );
	add_settings_section( 'paml_style_section', __( 'Style', 'pressapps' )  , 'paml_style_options', 'paml_style_options' );
	add_settings_section( 'paml_email_section', __( 'Registration Email', 'pressapps' ) , 'paml_email_options', 'paml_email_options' );
	add_settings_field( 'widget_info', __( 'Login Widget', 'pressapps' ), 'paml_settings_field_info', 'paml_setup_options', 'paml_setup_section', array(
		'options-name'	=> $option_name,
		'id'			=> 'widget-info',
		'value'			=> __( 'To add login widget to a sidebar navigate to Appearance > Widgets and drag "Modal Widget" to a sidebar.', 'pressapps' ),
	) );
	add_settings_field( 'shortcode_info', __( 'Shortcode', 'pressapps' ), 'paml_settings_field_info', 'paml_setup_options', 'paml_setup_section', array(
		'options-name'	=> $option_name,
		'id'			=> 'shortcode-info',
		'value'			=> __( 'Add the following shortcode to a page: [modal_login]', 'pressapps' ),
	) );
	add_settings_field( 'php_info', __( 'PHP Code', 'pressapps' ), 'paml_settings_field_info', 'paml_setup_options', 'paml_setup_section', array(
		'options-name'	=> $option_name,
		'id'			=> 'php-info',
		'value'			=> sprintf( __( 'Add the following function in your theme file for example header file: %s', 'pressapps' ), '<?php add_modal_login_link(); ?>' ),
	) );
	add_settings_field( 'login_redirect_url', __( 'Login Redirect URL', 'pressapps' ), 'paml_settings_field_text', 'paml_setup_options', 'paml_setup_section', array(
		'options-name'	=> $option_name,
		'id'			=> 'login-redirect-url',
		'label'			=> __( 'Set optional login redirect URL, if not set you will be redirected to login page.', 'pressapps' ),
	) );
	add_settings_field( 'logout_redirect_url', __( 'Logout Redirect URL', 'pressapps' ), 'paml_settings_field_text', 'paml_setup_options', 'paml_setup_section', array(
		'options-name'	=> $option_name,
		'id'			=> 'logout-redirect-url',
		'label'			=> __( 'Set optional logout redirect URL, if not set you will be redirected to home page.', 'pressapps' ),
	) );
	if (function_exists('pa_init_user_roles')) {
		add_settings_field( 'add_to_user_menu', __( 'Add To Theme User Menu', 'pressapps' ), 'paml_settings_field_checkbox', 'paml_setup_options', 'paml_setup_section', array(
			'options-name'	=> $option_name,
			'id'			=> 'add_to_user_menu',
				'value'			=> 'true',
			'label'			=> __( 'Add Login/Logout link to theme user menu', 'pressapps' ),
		) );
	}
    add_settings_field( 'userdefine_password', __( 'User Genrated Password', 'pressapps' ), 'paml_settings_field_checkbox', 'paml_setup_options', 'paml_setup_section', array(
		'options-name'	=> $option_name,
		'id'			=> 'userdefine_password',
		'value'			=> 'true',
		'label'			=> __( 'Allow users to enter their own password during registration.', 'pressapps' ),
	) );
	add_settings_field( 'modal_theme', __( 'Select Layout', 'pressapps' ), 'paml_settings_field_select', 'paml_style_options', 'paml_style_section', array(
		'options-name'	=> $option_name,
		'id'			=> 'modal-theme',
		'value'			=> array(
			'default' => __( 'Default' , 'pressapps' ),
			'wide' => __( 'Wide', 'pressapps' ),
		),
		'label'			=> __( 'Select modal login box layout.', 'pressapps' ),
	) );
	add_settings_field( 'modal_labels', __( 'Display Labels', 'pressapps' ), 'paml_settings_field_select', 'paml_style_options', 'paml_style_section', array(
		'options-name'	=> $option_name,
		'id'			=> 'modal-labels',
		'value'			=> array(
			'labels' => __( 'Labels' , 'pressapps' ),
			'placeholders' => __( 'Placeholders', 'pressapps' ),
		),
		'label'			=> __( 'Display textfield labels or placeholders.', 'pressapps' ),
	) );
	add_settings_field( 'bkg_color', __( 'Background Color', 'pressapps' ), 'paml_settings_field_color', 'paml_style_options', 'paml_style_section', array(
		'options-name'	=> $option_name,
		'id'			=> 'bkg-color',
		'label'			=> __( 'Set modal box background color.', 'pressapps' ),
	) );
	add_settings_field( 'font_color', __( 'Font Color', 'pressapps' ), 'paml_settings_field_color', 'paml_style_options', 'paml_style_section', array(
		'options-name'	=> $option_name,
		'id'			=> 'font-color',
		'label'			=> __( 'Set modal box font color.', 'pressapps' ),
	) );
	add_settings_field( 'link_color', __( 'Link Color', 'pressapps' ), 'paml_settings_field_color', 'paml_style_options', 'paml_style_section', array(
		'options-name'	=> $option_name,
		'id'			=> 'link-color',
		'label'			=> __( 'Set modal box link color.', 'pressapps' ),
	) );
	add_settings_field( 'btn_color', __( 'Button Color', 'pressapps' ), 'paml_settings_field_color', 'paml_style_options', 'paml_style_section', array(
		'options-name'	=> $option_name,
		'id'			=> 'btn-color',
		'label'			=> __( 'Set modal box button color.', 'pressapps' ),
	) );
	add_settings_field( 'custom_css', __( 'Custom CSS', 'pressapps' ), 'paml_settings_field_textarea', 'paml_style_options', 'paml_style_section', array(
		'options-name'	=> $option_name,
		'id'			=> 'custom-css',
		'label'			=> __( 'Add custom CSS code.', 'pressapps' ),
	) );
	add_settings_field( 'reg_email_subject', __( 'Email Subject', 'pressapps' ), 'paml_settings_field_text', 'paml_email_options', 'paml_email_section', array(
		'options-name'	=> $option_name,
		'id'			=> 'reg_email_subject',
		'label'			=> '',
	) );
	add_settings_field( 'reg_email_template', __( 'Email Body', 'pressapps' ), 'paml_settings_field_textarea', 'paml_email_options', 'paml_email_section', array(
		'options-name'	=> $option_name,
		'id'			=> 'reg_email_template',
		'label'			=> __( 'Add new user registration email template: %username%, %password%, %loginlink%. Leave blank to use default template.', 'pressapps' ),
	) );


	// Register settings with WordPress so we can save to the Database
	register_setting( 'paml_setup_options' , 'paml_options', 'paml_options_sanitize' );
	register_setting( 'paml_style_options' , 'paml_options', 'paml_options_sanitize' );
	register_setting( 'paml_email_options' , 'paml_options', 'paml_options_sanitize' );
}
add_action( 'admin_init', 'paml_init_settings_registration' );

/*-----------------------------------------------------------------------------------*/
/* add_settings_section() function for the widget options */
/*-----------------------------------------------------------------------------------*/

function paml_setup_options() {
	echo '<p>' . __( 'You can add login/logout link to your site in the following ways (for more setup information see plugin documentation included in the download package):', 'pressapps' ) . '.</p>';
}


/*-----------------------------------------------------------------------------------*/
/* add_settings_section() function for the widget options */
/*-----------------------------------------------------------------------------------*/

function paml_style_options() {
	echo '<p>' . __( 'Customize the look and feel of the modal login', 'pressapps' ) . '.</p>';
}

function paml_email_options(){
        echo '<p>' . __( 'Customize the content of new user registration email', 'pressapps' ) . '.</p>';
}
/*-----------------------------------------------------------------------------------*/
/* he callback function to display textareas */
/*-----------------------------------------------------------------------------------*/

function paml_settings_field_textarea( $args ) {
	// Set the options-name value to a variable
	$name = $args['options-name'] . '[' . $args['id'] . ']';

	// Get the options from the database
	$options = get_option( $args['options-name'] ); ?>

	<label for="<?php echo $args['id']; ?>"><?php esc_attr_e( $args['label'] ); ?></label><br />
	<textarea name="<?php echo $name; ?>" id="<?php echo $args['id']; ?>" class="<?php if ( ! empty( $args['class'] ) ) echo ' ' . $args['class']; ?>" cols="80" rows="8"><?php esc_attr_e( $options[ $args['id'] ] ); ?></textarea>
<?php }


/*-----------------------------------------------------------------------------------*/
/* The callback function to display checkboxes */
/*-----------------------------------------------------------------------------------*/

function paml_settings_field_checkbox( $args ) {
	// Set the options-name value to a variable
	$name = $args['options-name'] . '[' . $args['id'] . ']';

	// Get the options from the database
	$options = get_option( $args['options-name'] ); ?>

	<input type="checkbox" name="<?php echo $name; ?>" id="<?php echo $args['id']; ?>" <?php if ( ! empty( $args['class'] ) ) echo 'class="' . $args['class'] . '" '; ?>value="<?php esc_attr_e( $args['value'] ); ?>" <?php if ( isset( $options[ $args['id'] ] ) ) checked( $args['value'], $options[ $args['id'] ], true ); ?> />
	<label for="<?php echo $args['id']; ?>"><?php esc_attr_e( $args['label'] ); ?></label>
<?php }


/*-----------------------------------------------------------------------------------*/
/* The callback function to display selection dropdown */
/*-----------------------------------------------------------------------------------*/

function paml_settings_field_select( $args ) {
	// Set the options-name value to a variable
	$name = $args['options-name'] . '[' . $args['id'] . ']';

	// Get the options from the database
	$options = get_option( $args['options-name'] ); ?>

	<select name="<?php echo $name; ?>" id="<?php echo $args['id']; ?>" <?php if ( ! empty( $args['class'] ) ) echo 'class="' . $args['class'] . '" '; ?>>
		<?php foreach ( $args['value'] as $key => $value ) : ?>
			<option value="<?php esc_attr_e( $key ); ?>"<?php if ( isset( $options[ $args['id'] ] ) ) selected( $key, $options[ $args['id'] ], true ); ?>><?php esc_attr_e( $value ); ?></option>
		<?php endforeach; ?>
	</select>
	<label for="<?php echo $args['id']; ?>" style="display:block;"><?php esc_attr_e( $args['label'] ); ?></label>
<?php }

function paml_settings_field_checkboxes( $args ) {
	// Set the options-name value to a variable
        $options = get_option( $args['options-name'],array() );
        foreach ( $args['value'] as $key => $value ) :
            $name_key   = $args['id'] . '_' . $key; 
            $name       = $args['options-name'] . '[' . $name_key . ']';
            ?>
            <label for="<?php echo $args['id'] . '_'  . $key; ?>">
                <input type="checkbox" name="<?php echo $name ?>" id="<?php echo $args['id'] . '_'  . $key; ?>" 
                    <?php if ( ! empty( $args['class'] ) ) echo 'class="' . $args['class'] . '" '; ?> 
                    <?php
                        echo (((key_exists($name_key,$options))?(bool)$options[$name_key]:FALSE)?'checked="checked"':'');
                    ?>   
                    value="<?php esc_attr_e( $key ); ?>"
                />
                <?php esc_attr_e( $value ); ?>
            </label>
            <br/>
            <?php 
        endforeach; 
        
        ?>
	<label style="display:block;"><?php esc_attr_e( $args['label'] ); ?></label>
<?php }
/*-----------------------------------------------------------------------------------*/
/* Color picker */
/*-----------------------------------------------------------------------------------*/

function wp_enqueue_color_picker( ) {
    wp_enqueue_style( 'wp-color-picker' );
    wp_enqueue_script( 'wp-color-picker-script', PAML_PLUGIN_ASSETS_URL . 'js/modal-login.js', array( 'wp-color-picker' ), false, true );
}
add_action( 'admin_enqueue_scripts', 'wp_enqueue_color_picker' );

/*-----------------------------------------------------------------------------------*/
/* The callback function to display color picker */
/*-----------------------------------------------------------------------------------*/

function paml_settings_field_color( $args ) {

	// Set the options-name value to a variable
	$name = $args['options-name'] . '[' . $args['id'] . ']';

	// Get the options from the database
	$options = get_option( $args['options-name'] ); ?>

	<input name="<?php echo $name; ?>" id="<?php echo $args['id']; ?>" class="wp-color-picker-field<?php if ( ! empty( $args['class'] ) ) echo ' ' . $args['class']; ?>" value="<?php if ( isset ( $options[ $args['id'] ] )) { esc_attr_e( $options[ $args['id'] ] ) ;} else { echo ''; } ?>"></input>

	<label for="<?php echo $args['id']; ?>" style="display:block;"><?php esc_attr_e( $args['label'] ); ?></label>
<?php }


/*-----------------------------------------------------------------------------------*/
/* The callback function to display text field */
/*-----------------------------------------------------------------------------------*/

function paml_settings_field_text( $args ) {

	// Set the options-name value to a variable
	$name = $args['options-name'] . '[' . $args['id'] . ']';

	// Get the options from the database
	$options = get_option( $args['options-name'] ); ?>

	<input name="<?php echo $name; ?>" id="<?php echo $args['id']; ?>" type="text" class="regular-text code<?php if ( ! empty( $args['class'] ) ) echo ' ' . $args['class']; ?>" value="<?php if ( isset ( $options[ $args['id'] ] )) { esc_attr_e( $options[ $args['id'] ] ) ;} else { echo ''; } ?>"></input>

	<label for="<?php echo $args['id']; ?>" style="display:block;"><?php esc_attr_e( $args['label'] ); ?></label>
<?php }


/*-----------------------------------------------------------------------------------*/
/* The callback function to display info */
/*-----------------------------------------------------------------------------------*/

function paml_settings_field_info( $args ) {
	// Set the options-name value to a variable
	$name = $args['options-name'] . '[' . $args['id'] . ']';

	// Get the options from the database
	$options = get_option( $args['options-name'] ); ?>

	<p><?php esc_attr_e( $args['value'] ); ?></p>

<?php }


/*-----------------------------------------------------------------------------------*/
/* Sanitization function */
/*-----------------------------------------------------------------------------------*/

function paml_options_sanitize( $input ) {

	// Set array for the sanitized options
	$output = array();

	// Loop through each of $input options and sanitize them.
	foreach ( $input as $key => $value ) {
		if ( isset( $input[ $key ] ) )
			$output[ $key ] = strip_tags( stripslashes( $input[ $key ] ) );
	}

	return apply_filters( 'paml_options_sanitize', $output, $input );
}

