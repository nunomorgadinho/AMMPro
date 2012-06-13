<?php
/*
Plugin Name:WPSC Members Access
Plugin URI: http://www.instinct.co.nz
Description: A module that allows buying capabilities
Version: 2.3 Refactor - RK Tracking
Author: Instinct Entertainment
Author URI:  http://getshopped.org/extend/premium-upgrades/premium-upgrades/members-only-module/
*/

/* Received 7 April 2012 : 16:59 GMT BST */
/* 
@TODO before 3.0 gets launched
	Fix the email user when sub ends issues -michelle will fix currently only using 3.7 code
	Add messaging in for updated subscriptions and updated members when manually added
	PayPal - (Nuno is sorting PP)
	Canceling sub in PP must also cancel it on the wp site,
	Canceling your subscription on the WP site will cancel it in paypal.
	Deleting a subscription will remove that sub from the user should also delete the paypal auto payment
	Deleting a subscription from a user need to cancel it in PP
	Test buying more than one fo the same subscription
	
	Remove default capoabilities we dont need these
	Members widget - needs a revist and fix up
	Remove all reference to bbPress or will this support bbpress - if its going to support it then lets code this
	For now move bbpress code to a seperate file
	sort out including all these files correctly and having an admin init etc
	Users search implimented into the lsit table class
	Add Remove all subscriptions back into the bulk options for memebers list table
	Ensure we can filter product content / make this work with wpec pages to
	Jeff auto update stuff to be added
	ERROR WARNING / NOTICE DEBUG
	all function indeneted and named consistantly eg: wpec_members_
	fix div issue after memebers have been imported
	Check users subscriptions are running out etc when run out they should be ccanceleld from the site
	Update script to update current memebers on ppls sites to the new system add in the extra user_meta value
	product page metabox function needs rewrite - remove 3.7 compat issues this is now only 3.8
	Get the JS in the JS file.
	Fix the email sending when subscriptions have expried - currently using old 3.7 code.
	filter for filtering tables by different capabilities
*/

add_action('init', 'wpec_members_init');
function wpec_members_init(){
	//define constants
	$wpsc_plugin_url = plugins_url('',__FILE__);
	define('WPSC_MEMBERS_FOLDER', dirname(plugin_basename(__FILE__)));
	define('WPSC_MEMBERS_URL', $wpsc_plugin_url.'/plugins/'.WPSC_MEMBERS_FOLDER);
	
	//include files
	$files = array(
		'classes/wpec_subscribed_members_list_table.php', //subscribers tabel class
		'classes/wpec_subscriptions_list_table.php', //subscriptions table class
		'classes/wpec_subscribed_import_members_list_table.php', //import table class
		
		'wpec_members_access_admin.php', //contains admin functions for edit save etc
		'wpec_members_admin_display.php', // loads the views for each of the tabs and buttons
		
		'widgets/my-subscriptions.php', //my subscriptions widget
		
		'purchase_capability.php', // this will possbily be deleted
		'ecom_subscribers.php' // also possbily deleted
	);
	
	foreach ($files as $file)
		include_once($file);
}
$wpsc_product_capability_list = get_option('wpsc_product_capability_list');
/* Add the menu */
function wpec_members_menu_items(){
    add_menu_page('WPEC Members', 'Members', 'manage_options', 'wpec_members', 'wpec_members_rendor_list_page');
} 

add_action('admin_menu', 'wpec_members_menu_items');


function wpec_members_deactivation($subscr_id)
{
	global $wpdb;
	
	// what is the user_id
	$user_id = $wpdb->get_var( $wpdb->prepare("select user_ID from `".WPSC_TABLE_PURCHASE_LOGS."` WHERE transactid = %s", $subscr_id));
	
	//error_log("SUBSCR CANCELED USER ID = ".$user_id);
		
	// cancel the subscription locally
	update_user_meta( $user_id, '_subscription_canceled', true ); 
}
add_action('wpsc_deactivate_subscription', 'wpec_members_deactivation', 10, 1);

// display the subscription information on the my account page with a link to cancel
function wpec_members_add_cancel()
{
	$user_id = get_current_user_id();
	?>
	 | <a href="<?php echo get_option( 'user_account_url' ) . "&cancel_members_subscription=true&id=".$user_id; ?>"><?php _e('Cancel Subscription','wpsc'); ?></a>
	<?php 
}
add_action('wpsc_additional_user_profile_links', 'wpec_members_add_cancel');


function wpec_members_cancel_subscription()
{
	if ($_REQUEST['cancel_members_subscription'] == true)
	{
		wpec_members_cancel_user_subscription(); // this will remove the subscription entirely
	}
}
add_action('init', 'wpec_members_cancel_subscription');
?>