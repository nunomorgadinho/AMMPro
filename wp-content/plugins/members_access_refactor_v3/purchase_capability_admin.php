<?php
/*
 * This here contains the admin page for the purchase capability plugin.
*/

function wpsc_display_purchasable_capabilities()
{
	global $wpdb;

	$action = $_POST['action'];
	if ( !$action ) {
		$action = $_GET['action'];
	}


	if (($_POST['page_action'] == 'add')) {
		if ( empty( $_POST['capability_list']['new']['capability']) && empty($_POST['capability_list']['new']['name']) )
			$message_flag = '<div class="error" id="message"><p><strong>OPPS</strong>. you need to enter in A name and display name for your subscription.</p></div>';
		else
			$message_flag = '<div class="updated fade" id="message"><p><strong>Success</strong>. Your new subscription has been added.</p></div>';
	} ?>

<div class="wrap">
<?php
	switch ( $action ) {
		//this case process the add new capability sav
	case 'new':
		echo '<div id="col-container" class="">';
		echo '<div id="poststuff" class="col-wrap">';
		display_cap_tabs();
		echo $message_flag;
		echo '<div id="pad-left">';
		echo '<p><h3>WP e-Commerce Subscriptions: Add Subscription</h3>';
		echo 'Add a new Capability / Subscription to sell or apply to your users</p><br /><br />';
		echo '<div id="custom-m-small">';
		add_meta_box("add-purchasable-capabilities-form", __('Add A Subscription', 'wpsc'), "wpsc_add_purchasable_capabilities_forum", "wpsc");
		do_meta_boxes('wpsc', 'advanced', null);
		echo '</div>'; //close custom m small
		echo '</div>'; //close pad-left
		echo '</div>'; //close post stuff
		echo '</div>'; //close col container
		break;
		wpsc_purchasable_capabilities_page();
		///edit user
	case 'edit':
		echo '<div id="col-container" class="">';
		echo '<div id="poststuff" class="col-wrap">';
		display_cap_tabs();
		echo '<div id="pad-left">';
		echo '<h3>WP e-Commerce Subscriptions: Edit Subscription <a href="admin.php?page=wpsc_display_purchasable_capabilities&action=new" class="button add-new-h2">Add new Subscription</a></h3><br /><br />';
		echo '<div id="custom-m-small">';
		add_meta_box("edit-purchasable-capabilities-form", __('Edit Subscription', 'wpsc'), "wpsc_edit_purchasable_capabilities_forum", "wpsc");
		do_meta_boxes('wpsc', 'advanced', null);
		echo '</div>'; //close custom m small
		echo '</div>'; //close pad-left
		echo '</div>'; //close post stuff
		echo '</div>'; //close col container
		break;
	case 'delete':
		global $wpdb, $wpsc_product_capability_list;
		$capability = $_GET['capability'];
		if (isset($wpsc_product_capability_list[$capability])) {
			unset($wpsc_product_capability_list[$capability]);
			update_option('wpsc_product_capability_list', $wpsc_product_capability_list);
			remove_capabilities_from_users($capability);
		}

		break;
	default:
		wpsc_purchasable_capabilities_page();
		break;

	} ?>
</div>
<?php
}

/* 
@todo need to remove the new meta key has subscriptions
but only if this is the only sub that a user has!! 
*/
function remove_capabilities_from_users($capability)
{

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

function display_cap_tabs()
{
	echo'<h2><div class="icon32" id="icon-tools"><br /></div><a href="admin.php?page=wpsc_display_ecom_subscribers" class="nav-tab">Manage Users</a>
		<a href="admin.php?page=wpsc-purchasable-capabilities" class="nav-tab nav-tab-active">Manage Subscriptions</a></h2>';
}

//this can be deleted
function wpsc_purchasable_capabilities_page()
{
	global $wpdb, $wpsc_product_capability_list, $user_ID;
	$columns = array(
		'title' => __('Name'),
		'edit' => __(''),

	);
	register_column_headers('display-capability-list', $columns);
?>

	<div class="wrap">
	<?php display_cap_tabs(); ?>

		<h3><?php echo wp_specialchars(__('WP e-Commerce: Subscriptions', 'wpsc')); ?> <a href="admin.php?page=wpsc_display_purchasable_capabilities&action=new" class="button add-new-h2">Add new Subscription</a></h3>
		<p>Listed below are all your current subscriptions</p>
		<div id="col-container" class="">
			<div id="col-right">
				<div id="poststuff" class="col-wrap">
					<?php

?>
				</div>
			</div>

			<div id="col-left">
				<div class="col-wrap">
					<table class="widefat page fixed" id='wpsc_variation_list' cellspacing="0">
						<thead>
							<tr>
								<?php print_column_headers('display-capability-list'); ?>
							</tr>
						</thead>

						<tfoot>
							<tr>
								<?php print_column_headers('display-capability-list', false); ?>
							</tr>
						</tfoot>

						<tbody>
							<?php
							/* exit('<pre>'.print_r($wpsc_product_capability_list,1).'</pre>'); */
	foreach ((array)$wpsc_product_capability_list as $capability => $capability_data) {
		if (current_user_can('administrator') or !$capability_data['owner'] or ($capability_data['owner'] == $user_ID)) {
?>
									<tr class="capability-edit" id="capability-<?php echo $product['id']?>">
											<td class="capability-name"><?php echo htmlentities(stripslashes($capability_data['name']), ENT_QUOTES, 'UTF-8'); ?></td>
											<td class="edit-capability">
											<a href="<?php echo add_query_arg('capability', $capability, 'admin.php?page=wpsc_display_purchasable_capabilities&action=edit');?>"><?php echo TXT_WPSC_EDIT; ?></a>

													<a href="<?php echo add_query_arg('capability', $capability, 'admin.php?page=wpsc_display_purchasable_capabilities&action=delete');?>" onclick="if ( confirm(' <?php echo js_escape( __("You are about to delete this subscription \n 'Cancel' to stop, 'OK' to delete.")) ?>') ) { return true;}return false;"><?php _e('Delete')?></a>


											</td>
									</tr>
								<?php
		}
	}
?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>

	<?php

}
function wpsc_add_purchasable_capabilities_forum($capability = false)
{
	global $wpdb, $wpsc_product_capability_list, $user_ID;
	//  exit('<pre>'.print_r($wpsc_product_capability_list,true ).'</pre>');
	$capability = $wpdb->escape($_GET['capability']);
?>
		<form class='purchasable-capabilities-form'  enctype="multipart/form-data" action="" method="post">
			<div class='capabilities-subbox'>
			  <?php
	$capability = 'new';
?>
				<table>
					<tr>
						<td><?php _e('Subscription', 'wpsc'); ?>:</td>
						<td colspan="2"><input name='capability_list[<?php echo $capability; ?>][capability]' value='' size='22' /></td>
					</tr>
					<tr>
						<td><?php _e('Display Name', 'wpsc') ?>: </td>
						<td colspan="2"><input name='capability_list[<?php echo $capability ?>][name]' value='<?php echo $capability_data['name']; ?>' size='22' />
						</td>
					</tr>
					<tr>
						<td colspan="3"><?php _e('Subscription Permission Message', 'wpsc'); ?>:</td>
					</tr>
					<tr>
						<td colspan="3"><textarea name='capability_list[<?php echo $capability; ?>][message-details]' cols="40" rows="20" /></textarea></td>
					</tr>
					<tr>
						<td><?php _e('Selected by Default', 'wpsc') ?>: </td>
						<td colspan="2"><input type="checkbox" name='capability_list[<?php echo $capability ?>][default]' value='true' />
						</td>
					</tr>
				</table>
				<?php _e('This is the message that will get displayed to people who do not have the required subscription or are not logged in.', 'wpsc'); ?>
			</div>

				<p><label>
					Type:
					<?php
	$selected_state = array('wordpress' => '', 'bbpress' => '');
	switch ($capability_data['capability-type']) {
	case 'bbpress':
		$selected_state['bbpress'] = "selected='selected'";
		break;

	case 'wordpress':
	default:
		$selected_state['wordpress'] = "selected='selected'";
		break;
	}

?>
					<select name='capability_list[<?php echo $capability ?>][capability-type]'>
						<option <?php echo $selected_state['wordpress']; ?>  value='wordpress'>Wordpress</option>
						<option <?php echo $selected_state['bbpress']; ?>  value='bbpress'>BBpress</option>
					</select>
				</label>
				</p>

				<?php wp_nonce_field('edit-capability', 'wpsc-edit-capability'); ?>
				<input type='hidden' name='wpsc_admin_action' value='capability_action' />
				<input type='hidden' name='page_action' value='add' />
				<input type='hidden' name='capability_list[<?php echo $capability ?>][owner]' value='<?php echo $user_ID ?>' />
				<input class='button-primary' type='submit' name='submit' value='<?php _e('Add Subscription', 'wpsc'); ?>' />
			</form>
	<?php
}



function wpsc_edit_purchasable_capabilities_forum($capability = false)
{
	global $wpdb, $wpsc_product_capability_list;
	//  exit('<pre>'.print_r($wpsc_product_capability_list,true ).'</pre>');
	$capability = $wpdb->escape($_GET['capability']);
	$capability_data = $wpsc_product_capability_list[$capability]; 
	$defaultchecked = '';
	if($capability_data['default']){
		$defaultchecked = 'checked="checked"';
	};		
	?>

<form class='purchasable-capabilities-form'  enctype="multipart/form-data" action="" method="post">
			<div class='capabilities-subbox'>
				<table>
					<tr>
						<td colspan="2"><?php _e('Subscription', 'wpsc'); ?>: <?php echo $capability; ?></td>
					</tr>
					<tr>
						<td><?php _e('Display Name', 'wpsc') ?>: </td>
						<td colspan="2"><input name='capability_list[<?php echo $capability ?>][name]' value='<?php echo htmlentities(stripslashes($capability_data['name']), ENT_QUOTES, 'UTF-8'); ?>' size='22' />
						</td>
					</tr>
					<tr>
						<td colspan="3"><?php _e('Subscription Permission Message', 'wpsc'); ?>:</td>
					</tr>
					<tr>
						<td colspan="3"><textarea name='capability_list[<?php echo $capability; ?>][message-details]' cols="40" rows="20" /><?php echo htmlentities(stripslashes($capability_data['message-details']), ENT_QUOTES, 'UTF-8'); ?></textarea></td>
					</tr>
					<tr>
						<td><?php _e('Selected by Default', 'wpsc') ?>: </td>
						<td colspan="2"><input type="checkbox" name='capability_list[<?php echo $capability ?>][default]'<?php echo $defaultchecked ?> value='true' />
						</td>
					</tr>
				</table>
				<?php _e('This is the message that will get displayed to people who do not have the required subscription or are not logged in.', 'wpsc'); ?>
			</div>
					Type:
					<?php
	$selected_state = array('wordpress' => '', 'bbpress' => '');
	switch ($capability_data['capability-type']) {
	case 'bbpress':
		$selected_state['bbpress'] = "selected='selected'";
		break;

	case 'wordpress':
	default:
		$selected_state['wordpress'] = "selected='selected'";
		break;
	}

?>
					<select name='capability_list[<?php echo $capability ?>][capability-type]'>
						<option <?php echo $selected_state['wordpress']; ?>  value='wordpress'>Wordpress</option>
						<option <?php echo $selected_state['bbpress']; ?>  value='bbpress'>BBpress</option>
					</select>
				</label>
				</p>
			</div>

			<?php wp_nonce_field('edit-capability', 'wpsc-edit-capability'); ?>
			<input type='hidden' name='wpsc_admin_action' value='capability_action' />
			<input type='hidden' name='page_action' value='edit' />
			<input class='button-primary' type='submit' name='submit' value='<?php _e('Edit Subscription', 'wpsc'); ?>' />
		</form>
	<?php
}


if (is_admin()) {
	function wpsc_add_purchasable_capabilities_page($page_hooks, $base_page){

		require_once 'ecom_subscribers.php';
		$page_hooks[] = add_submenu_page($base_page, __('', 'wpsc'), __('', 'wpsc'), 'wpsc_manage_subscriptions', 'wpsc-purchasable-capabilities', 'wpsc_purchasable_capabilities_page');
		$page_hooks[] = add_submenu_page($base_page, __('-Members', 'wpsc'), __('-Members', 'wpsc'), 'wpsc_manage_subscriptions', 'wpsc_display_ecom_subscribers', 'wpsc_display_ecom_subscribers');
		$page_hooks[] = add_submenu_page($base_page, __('', 'wpsc'), __('', 'wpsc'), 'wpsc_manage_subscriptions', 'wpsc_display_purchasable_capabilities', 'wpsc_display_purchasable_capabilities');
		add_action('admin_init', 'wpsc_capabilities_add_scripts');
		return $page_hooks;
	}
	add_filter('wpsc_additional_pages', 'wpsc_add_purchasable_capabilities_page', 10, 2);
}
?>