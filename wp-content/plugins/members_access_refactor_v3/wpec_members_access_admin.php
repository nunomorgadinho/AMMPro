<?php
/*
 * This file contains all the admin functions required to add edit and update users / members and subscriptions.
 This file is divided into thre parts - Members / user functions, subscription functions and then general functions.
*/


/* User functions */

/*
This function will remove the capability from 
users if the subscription is deleted
*/
function wpec_members_remove_capability_from_users($capability) {

	$users = new WP_User_Search('', '', $capability);
	
	$user_ids = $users->results;

	foreach ($user_ids as $user_id) {
	
		$user = new WP_User($user_id);
	
		$user->remove_cap($capability);

		$members_lengths = get_user_meta($user_id, '_subscription_ends', true);
		$subscription_lengths = get_user_meta($user_id, '_subscription_length', true);
		$subscription_starts = get_user_meta($user_id, '_subscription_starts', true);
	
		unset($subscription_lengths[$capability]);
		unset($members_lengths[$capability]);
		unset($subscription_starts[$capability]);
		
		//if the array is now empty then we know they only had one sub
		if ( empty($members_lengths) ){
			delete_user_meta($user_id, '_subscription_starts');
			delete_user_meta($user_id, '_subscription_ends');
			delete_user_meta($user_id, '_subscription_length');
			delete_user_meta($user_id, '_has_current_subscription');
		} else {
		
		update_user_meta($user_id, '_subscription_starts', $subscription_starts);
		update_user_meta($user_id, '_subscription_ends', $members_lengths);
		update_user_meta($user_id, '_subscription_length', $subscription_lengths);
		
		}

	}
}



/* This will remove all subscriptions from a user */
function wpec_members_remove_all_capabilities($user_id) {
	
	global $wp_roles;
	$remove_user = new WP_User($user_id);
	$remove_user = sanitize_user_object($remove_user, 'display');

	foreach ($remove_user->caps as $cap => $value ) {
		if (!$wp_roles->is_role($cap)) 
			$remove_user->remove_cap($cap);
	};

	if (count($remove_user->caps) > 1) {
		$remove_user->remove_role( 'subscriber' );
	} elseif (count($remove_user->caps) < 1) {
		$remove_user->add_role( 'subscriber' );
	};

	delete_user_meta( $user_id, '_subscription_ends' );
	delete_user_meta( $user_id, '_subscription_length' );
	delete_user_meta( $user_id, '_subscription_starts' );
	delete_user_meta( $user_id, '_has_current_subscription' );

}


// Function Saves the capabilitites to the user (Both bulk add and single add)
function wpec_members_save_user($user_id, $length, $role) {
		$add_user = new WP_User($user_id);
		
		//$members_lengths = array();
		$members_lengths = get_user_meta($user_id, '_subscription_ends',true);
		$members_starts = get_user_meta($user_id, '_subscription_starts',true);

		$future_time = mktime(date('h'),date('m'),date('s')+$length,date('m'),(date('d')),date('Y'));	
		$current_time = time();
				           
		$members_lengths[$role]= $future_time;	
		$members_starts[$role]= $current_time;
		$add_user->add_cap($role, true);					
		
		//$subscription_lengths = array();
		$subscription_lengths = get_user_meta($user_id, '_subscription_length', true);	
		$subscription_lengths[$role]= $length;
		
		// dont think we need this line...
		$add_user->add_role( 'subscriber' );
		
		update_user_meta($user_id,'_subscription_ends', $members_lengths);
		update_user_meta($user_id,'_subscription_length', $subscription_lengths);
		update_user_meta($user_id,'_subscription_starts', $members_starts);
		update_user_meta($user_id,'_has_current_subscription', 'true');
}

function wpec_members_mail_subscription_end_notification($to)
{
	$subscription_end_email = get_site_option('subscription_end_email');
	$subscription_end_email_subject = get_site_option('subscription_end_email_subject');
	
	$subject = $subscription_end_email_subject;
	$message = $subscription_end_email;

	wp_mail($to, $subject, $message);
}

// as we now allow a bulk update and can update all the caps at once we are
// best to run the remove all function then add the users cap back
//possible bug: subscription starts was not updated in the previous version nor this one??
function wpec_members_edit_user(){
		
	$length = $_POST['length'];
	$user_id = $_POST['user_id'];
	$roles = $_POST['roles'];

	//delete all the old stuff first
	wpec_members_remove_all_capabilities($user_id);
	delete_user_meta( $user_id, '_subscription_ends' );
	delete_user_meta( $user_id, '_subscription_length' );

	$add_user = new WP_User($user_id);
	
	foreach ( $roles as $role ){
		$add_user->add_cap($role, true);
	}
	
	$subscription_ends = array();
	
	foreach ($length as $key => $value){
		$future_time = mktime(date('h'),date('m'),date('s')+$value,date('m'),(date('d')),date('Y'));
		$subscription_ends[$key] = $future_time;
	}
		
	if ($length == 0) { // Subscription Deactivated
		
		$user = get_userdata($user_id);
		$to = $user->user_email;
		wpec_members_mail_subscription_end_notification($to);
		
		// cancel the subscription locally
		update_user_meta( $user_id, '_subscription_canceled', true );
	} else {
		update_user_meta( $user_id, '_subscription_canceled', false );
	}
	update_user_meta( $user_id, '_subscription_length', $length );
	update_user_meta( $user_id, '_subscription_ends', $subscription_ends );	
	
	wp_safe_redirect('?page=wpec_members');		
	//wpec_members_display_update('Subscriber Edited.');
}

function wpec_members_cancel_user_subscription()
{
	$user_id = $_GET['id'];

	//delete all the old stuff first
	wpec_members_remove_all_capabilities($user_id);
	delete_user_meta( $user_id, '_subscription_ends' );
	delete_user_meta( $user_id, '_subscription_length' );

	$user = get_userdata($user_id);
	$to = $user->user_email;
	wpec_members_mail_subscription_end_notification($to);

	// cancel the subscription locally
	update_user_meta( $user_id, '_subscription_canceled', true );
	
	update_user_meta( $user_id, '_subscription_length', 0 );
	update_user_meta( $user_id, '_subscription_ends', $subscription_ends );
	wp_safe_redirect('?page=wpec_members');
}

/*
currently not implimented corretly into the importer table need to do this
but for now we will use the old bulk action importer save function refactored a
bit.
*/
function wpec_members_import_wp_users() {
///bulk option value 1 = add sub 2 = remove sub 3=remove allcapabilities else = 'no bulk option chosen'
	$bulk_option = $_POST['bulkchange'];
	$user_ids = $_POST['import_user'];
	$subscription = $_POST['roles'];
	$length = $_POST['length'];
	
	switch ($bulk_option){
		case 1:
			if ( empty($user_ids) || empty($length) || empty($subscription) ){
				wpec_members_display_error('You Must select Users, Subscription and a subscription duration to use this bulk option!');
				return;
			}
			foreach ( $user_ids as $user_id )
				wpec_members_save_user(	$user_id, $length, $subscription);		
			
			//wpec_members_display_update('User\'s Subscriptions Added');
			wp_safe_redirect('?page=wpec_members');	
		break;
		
		case 2:
			if ( empty($user_ids)  ){
				wpec_members_display_error('You Must select some users in order to remove their subscriptions');
				return;
			}
			foreach ( $user_ids as $user_id )
				wpec_members_remove_all_capabilities($user_id);
				wp_safe_redirect('?page=wpec_members');				
		break;
		
		default:
		wpec_members_display_error('You must select a bulk option!');
	}	
	
}


/* Single add user function - should merge in with the import 
function above  which are both more validation functions*/
function wpec_members_single_add_wp_users(){
	
	$user_id = $_POST['add_user_subscription'];
	$subscription = $_POST['roles'];
	$length = $_POST['length'];
	if ( empty($user_id) || empty($length) || empty($subscription) ){
		wpec_members_display_error('You must select a subscription and duration to add this user.');
		return;
	}
		
	wpec_members_save_user(	$user_id, $length, $subscription);
	//user added so redirrect back to the table
	wp_safe_redirect('?page=wpec_members');			
}



/* Subscription functions */


/* remove the subscription - need to put a warning message in here */	
function wpec_members_delete_subscription($capability){

global $wpsc_product_capability_list;

	if (isset($wpsc_product_capability_list[$capability])) {
		unset($wpsc_product_capability_list[$capability]);
		update_option('wpsc_product_capability_list', $wpsc_product_capability_list);
		wpec_members_remove_capability_from_users($capability);
	}
	wpec_members_display_update('Subscription Deleted');
	return;
}

/* edit/add subscriptions functions */
function wpec_members_edit_subscriptions() {
	  global $wpdb, $wpsc_product_capability_list;
		check_admin_referer('edit-capability','wpsc-edit-capability');
	//exit('<pre>'.print_r($_POST,1).'</pre>');
		$submitted_capability_data = $_POST['capability_list'];
		//check user has filled out required info before saving
		if($_POST['page_action'] == 'add' && !empty( $_POST['capability_list']['new']['capability']) && !empty($_POST['capability_list']['new']['name']) ) {

				$new_capability = $submitted_capability_data['new']['capability'];
				$new_capability_item = array();
				$new_capability_item[$new_capability]['name'] = $submitted_capability_data['new']['name'];
				$new_capability_item[$new_capability]['capability-type'] = $submitted_capability_data['new']['capability-type'];
				$new_capability_item[$new_capability]['message-details'] = $submitted_capability_data['new']['message-details'];
				if (is_array($wpsc_product_capability_list))
					$wpsc_product_capability_list = array_merge($wpsc_product_capability_list, $new_capability_item);
				else
					if (empty($wpsc_product_capability_list))
						$wpsc_product_capability_list = array($new_capability_item);
					else {
						error_log("There has been an error");
						$wpsc_product_capability_list = array_merge($wpsc_product_capability_list, $new_capability_item);
					}
			} else {
			  foreach($submitted_capability_data as $submitted_capability_key => $submitted_capability_item) {
					if(count($wpsc_product_capability_list[$submitted_capability_key]) < 2) {
						$wpsc_product_capability_list[$submitted_capability_key] = array();
					}
					$wpsc_product_capability_list[$submitted_capability_key]['name'] = $submitted_capability_data[$submitted_capability_key]['name'];
					$wpsc_product_capability_list[$submitted_capability_key]['capability-type'] = $submitted_capability_data[$submitted_capability_key]['capability-type'];
					$wpsc_product_capability_list[$submitted_capability_key]['message-details'] = $submitted_capability_data[$submitted_capability_key]['message-details'];
			  }
			}
			update_option('wpsc_product_capability_list', $wpsc_product_capability_list);

wp_safe_redirect('?page=wpec_members&tab=wpec_manage_subscriptions');
}
	
if($_REQUEST['wpsc_admin_action'] == 'capability_action') {
		add_action('admin_init', 'wpec_members_edit_subscriptions');
}

/* General Functions */


//creates the update css for all updates messages
function wpec_members_display_update($message) {
	echo '<div class="updated"><p>'. $message .'</p></div>';
}

///used to display the css for all error messages
function wpec_members_display_error($message) {
	echo '<div class="error"><p>'. $message .'</p></div>';
}

// save settings
function wpsc_members_save_settings()
{
	$subscription_end_email = $_POST['subscription_end_email'];
	$subscription_recurring_email = $_POST['subscription_recurring_email'];
	$subscription_end_email_subject = $_POST['subscription_end_email_subject'];
	$subscription_recurring_email_subject = $_POST['subscription_recurring_email_subject'];
	
	update_site_option('subscription_end_email', $subscription_end_email);
	update_site_option('subscription_recurring_email', $subscription_recurring_email);
	update_site_option('subscription_end_email_subject', $subscription_end_email_subject);
	update_site_option('subscription_recurring_email_subject', $subscription_recurring_email_subject);
}

/*
this function will check for forms been filled out and will call the function for processing,
 want to get rid of this and use all the bulk actions etc with the table. Or at least impliment it in a non ugly way
*/
function wpsc_members_check_for_form_updates(){
	if ( isset($_POST['action']) && $_POST['action'] == 'create_new' )
		wpec_members_single_add_wp_users();
	
	if ( isset($_POST['action']) && $_POST['action'] == 'bulksave' )
		wpec_members_import_wp_users();
	
	if ( isset($_POST['action']) && $_POST['action'] == 'update' )
		wpec_members_edit_user();
	
	if ( isset($_POST['action']) && $_POST['action'] == 'update-settings' )
		wpsc_members_save_settings();
	
}
add_action('admin_init', 'wpsc_members_check_for_form_updates');

?>