<?php

/*
Plugin Name: Envato Connect
Description: A plugin that allow users to connect using envato username, api and email.
Version: 1.0
Author: WizyLabs
Author URI: themeforest.net/user/wizylabs/?ref=wizylabs
*/

// constants paths
define( 'EC_PATH', dirname(__FILE__) );

// constants URIs
define( 'EC_URI', get_bloginfo( 'wpurl' ) . '/wp-content/plugins/envatoconnect' );
define( 'EC_ASSETS_URI', EC_URI . '/assets' );
define( 'EC_JS_URI', EC_ASSETS_URI . '/js' );
define( 'EC_CSS_URI', EC_ASSETS_URI . '/css' );
define( 'EC_IMG_URI', EC_ASSETS_URI . '/img' );


// =================================================
// LOGIN FORM BUTTON
// =================================================
add_action( 'login_form','show_envato_connect_field' );

function show_envato_connect_field() {
	?>
	<p>
		<link rel="stylesheet" type="text/css" href="<?php echo EC_CSS_URI; ?>/envatoconnect.css" media="screen">
		<link rel="stylesheet" type="text/css" href="<?php echo EC_CSS_URI; ?>/jquery.fancybox.css" media="screen">
		
		<script type="text/javascript"> var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>"; </script>
		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js"></script>
		<script type="text/javascript" src="<?php echo EC_JS_URI; ?>/jquery.fancybox.pack.js"></script>
		<script type="text/javascript" src="<?php echo EC_JS_URI; ?>/jquery.livequery.js"></script>
		<script type="text/javascript" src="<?php echo EC_JS_URI; ?>/envatoconnect.js"></script>
		
		<div id="envatoconnect">
			<a href="<?php echo EC_URI; ?>/overlay.php" class="envatoconnect_button">Envato Connect</a>
		</div>
		<!-- /.envatoconnect -->
	</p>
	<?php
}


// =================================================
// HANDLES FORM REQUESTS
// =================================================
add_action( 'wp_ajax_nopriv_envatoconnect_ajax_post_action', 'envatoconnect_ajax_callback' );
add_action( 'wp_ajax_envatoconnect_ajax_post_action', 'envatoconnect_ajax_callback' );

function envatoconnect_ajax_callback() {
	
	if( isset( $_POST['envatoconnect'] ) && $_POST['envatoconnect'] == 'signin' ) {
		
		wp_logout();
		
		// includes the magic!
		require_once( EC_PATH . '/class.envatoconnect.php' );
		
		$creds = array();
		$username = $_POST['envatoconnect_username'];
		$api = $_POST['envatoconnect_api'];
		$ec = new envatoconnect($username, $api);
		
		if( $ec->envato_user_exist() ) {
			
			if( $ec->user_exist() ) {
				if( $ec->api_changed() )
					$ec->update_api();
	
			} else {				
				$ec->add_user();				
			}
			
			// sets login details
			$creds['user_login'] = $username;
			$creds['user_password'] = $api;
			$creds['remember'] = false;
			
			// sign in
			$signin = wp_signon( $creds );
			
			// calls current user info
			global $current_user;
			get_currentuserinfo();
						
			// check for errors
			if ( is_wp_error($signin) ) {
				echo $user->get_error_message();
			} else {					
				echo admin_url('profile.php');
				exit();
			}	
			
		} else {
			echo 'ERROR1';
		}
	}
	
	exit();
}

?>