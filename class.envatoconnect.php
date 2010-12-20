<?php 

if ( !class_exists( 'envatoconnect' ) ) {

	class envatoconnect {
		
		var	$api;
		var $email;
		var $md5_api;
		var	$user_login;
		var	$first_name;
		var	$last_name;
		var	$user_url;
		
		
		function __construct( $user_login, $api ) {
		
			// sets entered data
			$this->api = $api;
			$this->email = '';
			$this->user_login = $user_login;
			$this->md5_api = md5($this->api);
		}
		
		
		// checks if the user exist as an Envato user
		// returns boolean
		function envato_user_exist() {
			
			$url = 'http://marketplace.envato.com/api/v2/' . $this->user_login . '/' . $this->api . '/account.json';
			$credentials = @file_get_contents( $url );
			
			if( $credentials ) {
				return true;			
			} else {
				return false;		
			}
		}
		
		
		// gets user credentials
		// returns object array
		function envato_user_credentials() {
			
			if( $this->envato_user_exist() ) {
				
				$url = 'http://marketplace.envato.com/api/v2/' . $this->user_login . '/' . $this->api . '/account.json';
				$credentials = @file_get_contents( $url );
				
				if( $credentials ) {
					$credentials = json_decode( $credentials );
					$credentials = $credentials->account;
					$this->first_name = $credentials->firstname;
					$this->last_name = $credentials->surname;
					
					return  $credentials->account;			
				}			
			}		
		}
		
		
		// check if the user exist in the wp DB depending on the username
		// returns boolean
		function user_exist() {
			
			if( username_exists( $this->user_login ) ) {
				return true;	
			} else {
				return false;	
			}		
		}
		
		
		// check if the user api has been changed
		// returns boolean
		function api_changed() {
			global $wpdb;
			
			$api_unchanged = $wpdb->get_results(
							 $wpdb->prepare( "SELECT * FROM $wpdb->users
											  WHERE	user_login = %s
											  AND	user_pass = %s", $this->user_login, $this->md5_api ));
											  
			if( empty( $api_unchanged ) ) {
				return true;
			} else {
				return false;		
			}		
		}
		
		
		// add user to the wp DB
		function add_user() {
			global $wpdb;
			
			if( !$this->user_exist() ) {
				
				// adds user to the db with fake email
				$insert = wp_insert_user( array(
					'user_url' => 'http://themeforest.net/user/' . $this->user_login,
					'user_login' => $this->user_login,
					'display_name' => $this->user_login,
					'user_nicename' => $this->user_login,
					'user_pass' => $this->api,
					'user_email' => $this->user_login . "@" . $this->user_login . ".com" )
				);
				
				$user_data = get_userdatabylogin( $this->user_login );
				$user_id = $user_data->ID;
				
				// removes the fake email
				$wpdb->update($wpdb->users, 
									array('user_email' => ''),
									array('ID' => $user_id, 'user_login' => $this->user_login),
									array('%s'),
									array('%d', '%s'));
			}		
		}
		
		
		// update user api if changed
		function update_api() {
			global $wpdb;
					
			if( $this->user_exist() ) {
				
				$user_data = get_userdatabylogin( $this->user_login );
				$user_id = $user_data->ID;
				
				// updates db row with new API
				$wpdb->update($wpdb->users, 
									array('user_pass' => $this->md5_api),
									array('ID' => $user_id, 'user_login' => $this->user_login),
									array('%s'),
									array('%d', '%s'));
			}
		}
	}
}

?>