<?php

// Extends the WordPress widget class by specifying our Modal Login Widget
class PAML_Widget extends WP_Widget {

	// The widget constructor. Specifies the classname and description, instantiates the widget,
	// loads localization files, and includes necessary scripts and styles.
	function __construct() {
		$widget_opts = array(
			'classname'   => 'paml-widget',
			'description' => __( 'Display modal login and logout links', 'pressapps' ),
		);

		//parent::__construct( 'PAML_Widget', __( 'Modal Login', 'pressapps' ), $widget_opts );
		parent::__construct( 'PAML_Widget', 'ИД Логин/Регистрация', $widget_opts );
	}


	// Outputs the content of the form in the widgets page.
	function widget( $args, $instance ) {
		extract( $args, EXTR_SKIP );

		$widget_title = ( ! empty( $instance['widget-title'] ) ) ? apply_filters( 'widget-title', $instance['widget-title'] ) : '';
		$login_text   = ( ! empty( $instance['login-text'] ) )   ? apply_filters( 'login-text',   $instance['login-text'] )   : '';
		$logout_text  = ( ! empty( $instance['logout-text'] ) )  ? apply_filters( 'logout-text',  $instance['logout-text'] )  : '';
		$show_admin   = ( ! empty( $instance['show-admin'] ) )   ? apply_filters( 'show-admin',   $instance['show-admin'] )   : false;

		echo $before_widget;

		include_once( 'widget.php');

		echo $after_widget;
	}


	// Processes the widget's options to be saved.
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$instance['widget-title'] = strip_tags( stripslashes( $new_instance['widget-title'] ) );
		$instance['login-text']   = strip_tags( stripslashes( $new_instance['login-text'] ) );
		$instance['logout-text']  = strip_tags( stripslashes( $new_instance['logout-text'] ) );
		$instance['show-admin']   = intval( $new_instance['show-admin'] );

		return $instance;
	}


	//Generates the administration form for the widget.
	function form( $instance ) {
		$instance = wp_parse_args(
			(array) $instance,
			array(
				'widget-title' => '',
				'login-text' 	=> 'Login',
				'logout-text'  => 'Logout',
				'show-admin'	=> 0,
			)
		);

		$widget_title = strip_tags( stripslashes( $instance['widget-title'] ) );
		$login_text   = strip_tags( stripslashes( $instance['login-text'] ) );
		$logout_text  = strip_tags( stripslashes( $instance['logout-text'] ) );
		$show_admin	  = intval( $instance['show-admin'] );

		include( 'form.php' );
	}
}
