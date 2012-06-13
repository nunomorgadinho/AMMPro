<?php
/* switch the tabs to decide what table or page to display */
function wpec_members_rendor_list_page(){
	global $wpsc_product_capability_list;
	
/* 	The subscription table contains the subscription name (also pass via url with row actions) this is different to the array key in the global cap list so we need to loop through and return the key for processing */

	if ( isset($_GET['subscription']) ){
		$subscription_name = $_GET['subscription'];
		$capability = '';
		foreach ($wpsc_product_capability_list as  $key => $value){
			if ( $value['name'] == $subscription_name ){
				$capability = $key;
				continue;
			}
		}
	}	

	switch($_GET['tab']){
		case 'wpec_manage_members' :
			display_wpec_manage_members_table();
			break;
		case 'wpec_manage_subscriptions' :
			display_wpec_manage_subscriptions_table();
			break;
		case 'wpec_edit_subscription' :
			display_wpec_edit_subscription($capability);
			break;
		case 'wpec_add_subscription' :
			display_wpec_add_subscription($capability);
			break;
		case 'wpec_add_new_member' :
			display_wpec_add_new_member();
			break;
		case 'wpec_import_members':
			display_wpec_import_members();
			break;
		case 'edit_member' :
			display_wpec_edit_member();
			break;
		default:
			display_wpec_manage_members_table();
	}

}


function display_wpec_add_subscription($capability){
global $wpdb, $wpsc_product_capability_list, $user_ID;

?>
	<div class="wrap">
	<?php display_manage_subscription_tabs(); ?>
		<form class='purchasable-capabilities-form'  enctype="multipart/form-data" action="" method="post">
	
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
			</div>
	<?php


}
/* @TODO use labels!!!!
	formate html and indentation
	add in redirrect
*/
function display_wpec_edit_subscription($capability){
global $wpsc_product_capability_list;
	
	$capability_data = $wpsc_product_capability_list[$capability]; 
	$defaultchecked = ''; ?>
	
	<div class="wrap">
	<?php display_manage_subscription_tabs(); ?>
	
	<form class='purchasable-capabilities-form'  enctype="multipart/form-data" action="" method="post">
			
		<?php _e('Subscription', 'wpsc'); ?>: <?php echo $capability; ?> <br />
		
		<?php _e('Display Name', 'wpsc') ?>: 
			<input name='capability_list[<?php echo $capability ?>][name]' value='<?php echo htmlentities(stripslashes($capability_data['name']), ENT_QUOTES, 'UTF-8'); ?>' size='22' /> <br />
			
			<?php _e('Subscription Permission Message', 'wpsc'); ?>: <br />
			<textarea name='capability_list[<?php echo $capability; ?>][message-details]' cols="40" rows="20" /><?php echo htmlentities(stripslashes($capability_data['message-details']), ENT_QUOTES, 'UTF-8'); ?></textarea> <br />
		
		<?php _e('This is the message that will get displayed to people who do not have the required subscription or are not logged in.', 'wpsc'); ?> <br />
	
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
		Type:
		<select name='capability_list[<?php echo $capability ?>][capability-type]'>
			<option <?php echo $selected_state['wordpress']; ?>  value='wordpress'>Wordpress</option>
			<option <?php echo $selected_state['bbpress']; ?>  value='bbpress'>BBpress</option>
		</select>
		</label>
		</p>
		
		<?php wp_nonce_field('edit-capability', 'wpsc-edit-capability'); ?>
		<input type='hidden' name='wpsc_admin_action' value='capability_action' />
		<input type='hidden' name='page_action' value='edit' /> 
		<input class='button-primary' type='submit' name='submit' value='<?php _e('Edit Subscription', 'wpsc'); ?>' />
		</form>
	</div> <!-- close wrap  -->
	<?php
}


function display_wpec_manage_subscriptions_table(){

 $subscription_table = new Subscriptions_List_Table();
 $subscription_table->prepare_items();
    
    $class="nav-tab";
    ?>
    <div class="wrap">
<?php display_manage_subscription_tabs(); ?>
		<p>Listed below are all your current subscriptions</p>
        <!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
        <form id="members-filter" method="get">
            <!-- For plugins, we also need to ensure that the form posts back to our current page -->
            <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
            <!-- Now we can render the completed list table -->
            <?php $subscription_table->display() ?>
        </form>
        
    </div>
 <?php
}

function display_wpec_manage_members_table(){

	$subscribed_members_table = new Subscribed_Members_List_Table();
	$subscribed_members_table->prepare_items();
	
	?>
	<div class="wrap">
		<?php display_manage_subscribers_tabs(); ?>
		<h3>WP e-Commerce Subscribers: </strong> <a href="admin.php?page=wpec_members&tab=wpec_add_new_member" class="button add-new-h2">Add New</a> <a href="admin.php?page=wpec_members&tab=wpec_import_members" class="button add-new-h2">Import</a></h3> 
		<!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
		<form id="members-filter" method="get">
			<!-- For plugins, we also need to ensure that the form posts back to our current page -->
			<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
			<!-- Now we can render the completed list table -->
			<?php $subscribed_members_table->display() ?>
		</form>
		
	</div>
	<?php
}

function display_wpec_add_new_member(){
global $wpdb;
	
	$users_sql = "SELECT `ID`, `user_login` FROM ".$wpdb->prefix."users LIMIT 0,10000";
	$users = $wpdb->get_results( $users_sql ); 
	//exit('<pre>'.print_r($users,1).'</pre>');
	?>
	<div class="wrap">
		<?php display_manage_subscribers_tabs(); ?>
		<h3>WP e-Commerce Subscribers: Add New</strong>  <a href="admin.php?page=wpec_members&tab=wpec_import_members" class="button add-new-h2">Import</a></h3>
		<p>Select a site member from the list below to manualy assign a subscription to them.</p>

		<form id="your-profile-form" enctype="multipart/form-data" method="post" action="">
		
			<label for="user">User:</label>
			<select name="add_user_subscription"> 
				<?php 
				$i = 0;
				foreach( $users as $user ) {
				
					$user_object = new WP_User($user->ID);
					//don't want to display admin users
					if($user_object->has_cap('administrator'))
					continue;
					?>
					<option value="<?php echo $user->ID; ?>"><?php echo $user->user_login; ?></option>
					<?php
					$i++;
				}?>
			</select>
			<br />
			
			<label for="role">Subscription Type:</label>
			<select name="roles">
				<?php			
				$roles = get_option( 'wpsc_product_capability_list' ); 
				
				foreach( $roles as $role => $key ) { ?>
					<option value="<?php echo $role  ?>"><?php echo $role  ?></option><?php
				}?>
				
			</select> 
			<br />
			
			<label for="length">Subscription Length:</label>
			
			<select name="length">
				<option value="63113852">2 years</option>
				<option value="31556926">12 months</option>
				<option value="15778463">6 months</option>
				<option value="7889231">3 months</option>
				<option value="2629743">1 month</option>
			</select>
			
			<p class="submit">
				<input type="submit" value="Create Subscription" class="button-primary" />
				<input type="hidden" name="action" value="create_new" />
			</p>
		</form>
	</div>
<?php
}

/* This is where the importat list class table is going to go! */
function display_wpec_import_members(){
	$import_members_table = new Import_Members_List_Table();
	$import_members_table->prepare_items();
	
	?>
	<div class="wrap">
		<?php display_manage_subscribers_tabs(); ?>
		<h3>Import your WordPress users</h3>
	Use the bulk options to add subscriptions to your WordPress users, this will import them  your WP-e-Commerce subscribers <br />
	<br />
	<div class='tablenav'>
	<form id="bulk_updates" method="post" action="">
	<select name="bulkchange">
		<option value="0" selected="selected">Bulk Actions</option>
		<option value="1">Add Subscription</option>
		<option value="2">Remove all Subscriptions</option>
	</select>
	
<select name="roles">
	<option value="">Select a Subscription</option>
		<?php			
		$roles = get_option( 'wpsc_product_capability_list' ); 
			foreach( $roles as $role => $key ) { 
				?> <option value="<?php echo $role  ?>"><?php echo $role  ?></option><?php
			}?>
</select>

<select name="length">
	<option value="">Choose a Length</option>
	<option value="63113852">2 years</option>
	<option value="31556926">12 months</option>
	<option value="15778463">6 months</option>
	<option value="7889231">3 months</option>
	<option value="2629743">1 month</option>
	<option value="657436">1 week</option>
</select>
<input type="submit" value="Apply" class="button-secondary" />
<input type="hidden" name="action" value="bulksave" />




		
		<!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
		<form id="members-filter" method="get">
			<!-- For plugins, we also need to ensure that the form posts back to our current page -->
			<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
			<!-- Now we can render the completed list table -->
			<?php $import_members_table->display() ?>
		</form>
		
	</div>
	<?php



}

/* @todo tidy this html */
function display_manage_subscribers_tabs(){
global $wpsc_product_capability_list;
?>
<h2><div class="icon32" id="icon-users"><br /></div><a href="admin.php?page=wpec_members&tab=wpec_manage_members" class=" nav-tab nav-tab-active">Manage Suscribers</a>
		<a href="admin.php?page=wpec_members&tab=wpec_manage_subscriptions" class="nav-tab">Manage Subscriptions</a></h2>
		
<?php
/*
echo "<ul class='subsubsub'>";
			echo '<li><label for="capabilities">Filter:</label>
    <select name="capabilities_dropdown" style="width:200px" onchange="location.href=\'admin.php?page=wpsc_display_ecom_subscribers&cap_name=\'+document.getElementById(\'capabilities_dropdown\').value;" id="capabilities_dropdown">';
      echo '<option ' . $selected . ' value="">All</option>';
		foreach ((array)$wpsc_product_capability_list as $capability => $key) {
			$capability_data = $wpsc_product_capability_list[$capability];
			if (current_user_can('administrator') or !$capability_data['owner'] or ($capability_data['owner'] == $user_ID)) {
			$selected='';
			if( $capability == $cap )
				$selected="selected='selected'";
			echo '<option ' . $selected . ' value="' . $capability . '">' . htmlentities(stripslashes($capability_data['name']), ENT_QUOTES, 'UTF-8') . '</option>';
			}
		}
		echo '</select></li>';

	echo '</ul>';			
*/
}

function display_wpec_edit_member(){

	$user_id = $_GET['member'];
	$length = get_user_meta($user_id,'_subscription_ends',true);
	$subscription_length = get_user_meta($user_id,'_subscription_length',true); 
	$user_info = get_userdata($user_id);
	$current_subscriptions = array_keys($length);
	$roles = get_option( 'wpsc_product_capability_list' );
	
	display_manage_subscribers_tabs(); 
	?>
	<h3>WP e-Commerce Edit Subscriber: <?php echo ' '.$user_info->user_login; ?> </h3> 
	Here you can edit any of the members subscriptions, update the subscription as you wish then click update subscriptions to save.
	
	<form id="edit-profile" method="post" action="">
	
	<p><strong>User Name: </strong><?php echo ' '.$user_info->user_login; ?> </p>
	
	<?php 
	if ( !empty($user_info->first_name) ){?>
		<p> Name: <?php echo($user_info->first_name .  " " . $user_info->last_name ); ?> </p>
	<?php } ?>
	
	<p>
		<strong>Current Subscriptions:</strong><br />
		<?php 
		foreach ( $current_subscriptions as $subscription ){
			echo $subscription . '<br />';
		} 
		?>
	</p>

	<?php 
	foreach ( $current_subscriptions as $subscription ){
	 
		echo '<strong> Edit ' . $subscription . ' subscription </strong><br /><p>';
		?>
		
		<label for="roles_<?php echo $subscription ?>">Subscription:</label> 
		<select name="roles[<?php echo $subscription ?>]"> <?php
		foreach( $roles as $role => $key ) { 
			if ($subscription == $role){
				?> <option selected="selected" value="<?php echo $role  ?>"><?php echo $role  ?></option><?php
			} else {
				?> <option value="<?php echo $role  ?>"><?php echo $role  ?></option><?php
			}
		}?>
		</select><br />
		
		<label for="length">Subscription Length:</label>
		<select name="length[<?php echo $subscription ?>]">
			<option <?php selected ($subscription_length[$subscription], "63113852") ?> value="63113852">2 years</option>
			<option <?php selected( $subscription_length[$subscription], "31556926") ?> value="31556926">12 months</option>
			<option <?php selected( $subscription_length[$subscription], "15778463") ?> value="15778463">6 months</option>
			<option <?php selected( $subscription_length[$subscription], "7889231")  ?> value="7889231">3 months</option>
			<option <?php selected( $subscription_length[$subscription], "2629743")  ?> value="2629743">1 month</option>
			<option <?php selected( $subscription_length[$subscription], "0") 		 ?> value="0">Deactivate</option>
		</select> <br /></p>
	
<?php } ?>
	<p class="submit">
		<input type="submit" value="Update Subscription" class="button-primary" />
		<input type="hidden" name="action" value="update" />
		<input type="hidden" name="user_id" value="<?php echo $user_id ?>" />
	</p>
	</form>
	<?php
}

function display_manage_subscription_tabs(){
?>
<h2><div class="icon32" id="icon-tools"><br /></div><a href="admin.php?page=wpec_members&tab=wpec_manage_members" class=" nav-tab">Manage Suscribers</a>
		<a href="admin.php?page=wpec_members&tab=wpec_manage_subscriptions" class="nav-tab nav-tab-active">Manage Subscriptions</a></h2>
       <h3><?php echo esc_html(__('WP e-Commerce: Subscriptions', 'wpsc')); ?> <a href="admin.php?page=wpec_members&tab=wpec_add_subscription" class="button add-new-h2">Add new Subscription</a></h3>
      <?php
}