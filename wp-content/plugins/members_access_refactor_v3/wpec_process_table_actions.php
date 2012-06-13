<?php
include('ecom_subscribers.php');

/* This file contains all the functions that handle the table actions for each table */

/* subscription table actions */

/* remove the subscription - need to put a warning message in here */	
function wpsc_members_delete_subscription($capability){

global $wpsc_product_capability_list;

	if (isset($wpsc_product_capability_list[$capability])) {
		unset($wpsc_product_capability_list[$capability]);
		update_option('wpsc_product_capability_list', $wpsc_product_capability_list);
		remove_capabilities_from_users($capability);
	}
	wpsc_display_update('Subscription Deleted');
	return;
}

/* this function needs to stay in the remodel */
function wpsc_edit_purchasable_capabilities() {
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
				$wpsc_product_capability_list = array_merge($wpsc_product_capability_list, $new_capability_item);
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
	/*
		if ( isset($_GET['action']) && $_GET['action'] = 'edit')
				wpsc_display_update('Subscription Updated!');
			else
				wpsc_display_update('New Subscription Created!');
*/
wp_safe_redirect('?page=wpec_members&tab=wpec_manage_subscriptions');
}
	
if($_REQUEST['wpsc_admin_action'] == 'capability_action') {
		add_action('admin_init', 'wpsc_edit_purchasable_capabilities');
}

/* Manage Subscribers table actions */

/*
currently not implimented corretly into the importer table need to do this
but for now we will use the old bulk action importer save function refactored a
bit.
*/
function wpsc_members_import_wp_users() {
///bulk option value 1 = add sub 2 = remove sub 3=remove allcapabilities else = 'no bulk option chosen'
	$bulk_option = $_POST['bulkchange'];
	$user_ids = $_POST['import_user'];
	$subscription = $_POST['roles'];
	$length = $_POST['length'];
	
	switch ($bulk_option){
		case 1:
			if ( empty($user_ids) || empty($length) || empty($subscription) ){
				wpsc_display_error('You Must select Users, Subscription and a subscription duration to use this bulk option!');
				return;
			}
			foreach ( $user_ids as $user_id )
				wpsc_save_user(	$user_id, $length, $subscription);		
			
			//wpsc_display_update('User\'s Subscriptions Added');
			wp_safe_redirect('?page=wpec_members');	
		break;
		
		case 2:
			if ( empty($user_ids)  ){
				wpsc_display_error('You Must select some users in order to remove their subscriptions');
				return;
			}
			foreach ( $user_ids as $user_id )
				wpsc_remove_all($user_id);
				wp_safe_redirect('?page=wpec_members');				
		break;
		
		default:
		wpsc_display_error('You must select a bulk option!');
	}	
	
}


// as we now allow a bulk update and can update all the caps at once we are
// best to run the remove all function then add the users cap back
//possibly bug: subscription starts was not updated in the previous version nor this one??
function wpsc_members_edit_user(){
		
	$length = $_POST['length'];
	$user_id = $_POST['user_id'];
	$roles = $_POST['roles'];

	//delete all the old stuff first
	wpsc_remove_all($user_id);
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
	
	update_user_meta( $user_id, '_subscription_length', $length );
	update_user_meta( $user_id, '_subscription_ends', $subscription_ends );	
	wp_safe_redirect('?page=wpec_members');		
	//wpsc_display_update('Subscriber Edited.');
}



//this function should really be merged with the inport function
function wpsc_members_single_add_wp_users(){
	
	$user_id = $_POST['add_user_subscription'];
	$subscription = $_POST['roles'];
	$length = $_POST['length'];
	if ( empty($user_id) || empty($length) || empty($subscription) ){
		wpsc_display_error('You must select a subscription and duration to add this user.');
		return;
	}
		
	wpsc_save_user(	$user_id, $length, $subscription);
	//user added so redirrect back to the table
	wp_safe_redirect('?page=wpec_members');			
}


/*
 * NOT IN USE: SEE wpec_members_access_admin.php line 314 instead.
this function will check for forms been filled out and will call the function for processing,
 want to get rid of this and use all the bulk actions etc with the table. Or at least impliment it in a non ugly way
*/
function wpsc_members_check_for_form_updates(){
	
	if ( isset($_POST['action']) && $_POST['action'] == 'create_new' )
		wpsc_members_single_add_wp_users();
	
	if ( isset($_POST['action']) && $_POST['action'] == 'bulksave' )
		wpsc_members_import_wp_users();
	
	
	if ( isset($_POST['action']) && $_POST['action'] == 'update' )
		wpsc_members_edit_user();
	
}
add_action('admin_init', 'wpsc_members_check_for_form_updates');


?>
