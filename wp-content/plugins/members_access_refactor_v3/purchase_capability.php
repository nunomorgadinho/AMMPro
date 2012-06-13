<?php
$role =& get_role('administrator');
$role->add_cap('wpsc_manage_subscriptions');
$wpsc_product_capability_list = get_option('wpsc_product_capability_list');

//Define the URL to the plugin folder


//include('ecom_subscribers.php');
// edit this line to add your bbpress prefix
define("WPSC_BB_PREFIX", "bb_");

/*
This file here should contain the product meta box for creating 
subscriptions and all the functions for limiting the subscription display etc
on posts and pages
*/


	/* 	product page meta box */
	function wpsc_purch_cap_product_forms($product_data='') {
		global $closed_postboxes, $wpsc_product_capability_list;
		
		$siteurl = get_option('siteurl');
		$output='';
		$meta_value='';
		if ($product_data == 'empty') {
			$display = "style='display:none;'";
		}
		if((float)WPSC_VERSION < 3.8 ){
		$meta_value = 'productmeta_values'; 
		
?>
		
		
		<div id='wpsc_purch_cap_product_forms' class='postbox <?php echo ((array_search('wpsc_purch_cap_product_forms', (array)$product_data['closed_postboxes']) !== false) ? 'closed' : '');	?>' <?php echo ((array_search('wpsc_purch_cap_product_forms', (array)$product_data['hidden_postboxes']) !== false) ? 'style="display: none;"' : ''); ?>>
			<h3 class='hndle'><?php _e("Membership Control",'wpsc'); ?></h3>
			<div class='inside'>
			<?php
			//this is messy but for some reason wp-e-c 3.8 is appending a prefix when get product meta is used but not appending the prefix when its saving so need to use get post meta for 3.8 to avoid the prefix issue
			$saved_product_capabilties = (array)get_product_meta($product_data['id'],'_wpsc_product-capabilities',true);
			$saved_membership_length = (array)get_product_meta($product_data['id'],'_wpsc_membership_length',true);
			$is_recurring = (array)get_product_meta($product_data['id'],'_wpsc_is_recurring',true);
			$is_permanent = (array)get_product_meta($product_data['id'],'_wpsc_is_permanent',true); 
			$number_times_rebill = (array)get_product_meta($product_data['id'],'_wpsc_rebill_number',true);
			$rebill_period = (array)get_product_meta($product_data['id'],'_wpsc_rebill_interval',true);
			$charge_to_expiry = (array)get_product_meta($product_data['id'],'_wpsc_charge_to_expiry',true);
			
	} else {
			$product_data = get_post($product_data->ID, ARRAY_A);
			$meta_value = 'meta';
			$product_data['id'] = $product_data['ID'];
			
			$saved_product_capabilties = (array)get_post_meta($product_data['id'],'_wpsc_product-capabilities',true);
			$saved_membership_length = (array)get_post_meta($product_data['id'],'_wpsc_membership_length',true);
			$is_recurring = (array)get_post_meta($product_data['id'],'_wpsc_is_recurring',true);
			$is_permanent = (array)get_post_meta($product_data['id'],'_wpsc_is_permanent',true); 
			$number_times_rebill = (array)get_post_meta($product_data['id'],'_wpsc_rebill_number',true);
			$rebill_period = (array)get_post_meta($product_data['id'],'_wpsc_rebill_interval',true);
			$charge_to_expiry = (array)get_post_meta($product_data['id'],'_wpsc_charge_to_expiry',true);

		} 
		?> <div>
			  <strong><?php _e("This product allows buyers to view pages and posts that have been associated with the following subscriptions:",'wpsc'); ?></strong>
				<?php
				//echo('<pre> cap list'.print_r($wpsc_product_capability_list,1).'</pre>');
				foreach($wpsc_product_capability_list as $product_capability => $product_capability_data) {
			
				  $is_checked = '';
				  if(array_search($product_capability, $saved_product_capabilties) !== false) {
						$is_checked = "checked='checked'";
				  }
					?>
					<div>
						<label>
						<input type="checkbox" <?php echo $is_checked; ?> name= "<?php echo $meta_value; ?>[_wpsc_product-capabilities][]" value="<?php echo $product_capability; ?>" />
						<?php echo $product_capability_data['name']; ?>
						</label>
					</div>
					<?php
				}
				?>
				<p><?php _e('Please note, you must ensure that the corresponding pages and posts also have these subscriptions checked'); ?></p>
				</div>
				
				<br />
	
	
	<?php //exit('Permanent?:<pre>'.print_r($is_permanent,true).'</pre>'); ?>
				<div>
				<label for="wpsc_membership_length">Membership Length</label>
				<input id="wpsc_membership_length" size="3" name="<?php echo $meta_value; ?>[_wpsc_membership_length][length]" value="<?php echo $saved_membership_length['length']; ?>" type="text">
				
				<select name="<?php echo $meta_value; ?>[_wpsc_membership_length][unit]">
					<option <?php if($saved_membership_length['unit'] == 'd'){echo 'selected="selected"';} ?> value="d">Days</option>
					<option <?php if($saved_membership_length['unit'] == 'w'){echo 'selected="selected"';} ?> value="w">Weeks</option>
					<option <?php if($saved_membership_length['unit'] == 'm'){echo 'selected="selected"';} ?> value="m">Months</option>
					<option  <?php if($saved_membership_length['unit'] == 'Y'){echo 'selected="selected"';} ?> value="Y">Years</option>
				</select>
				or <input type='hidden' value='0' name="<?php echo $meta_value; ?>[_wpsc_is_permanent]" /><input <?php if($is_permanent[0] == 1){echo 'checked="checked"';} ?> type="checkbox" value="1" name="<?php echo $meta_value; ?>[_wpsc_is_permanent]" id="wpsc_product_sub_type"/> Membership lasts forever
				</div>
	
	          
				<br />
				<br />
	



 <script type="text/javascript">
 
 	 jQuery(document).ready(function(){
		
		jQuery("#recurring_options").hide(10);
  		jQuery("#charging_options").hide(10);
  		jQuery("#keep_charging").hide(10);
        
         jQuery("#q_billing input:radio:eq(0)").click(function(){
             jQuery("#recurring_options").show(10);
             jQuery("#keep_charging").show(10);
             jQuery("#charging_options").show(10);
          });
          
          jQuery("#q_billing input:radio:eq(1)").click(function(){
               jQuery("#recurring_options").hide(10);
             jQuery("#keep_charging").hide(10);
             jQuery("#charging_options").hide(10);
          });
          
          jQuery("#keep_charging input:radio:eq(0)").click(function(){
             jQuery("#charging_options").hide(10);
          });

          jQuery("#keep_charging input:radio:eq(1)").click(function(){
             jQuery("#charging_options").show(10);
          });

  });</script>
				<div>
					<?php _e("Does this product use recurring billing:",'wpsc'); ?>
					<div id="q_billing">
						<label>	<input <?php if($is_recurring[0]==1){echo 'checked="checked"';} ?> type='radio' name='<?php echo $meta_value; ?>[_wpsc_is_recurring]' value='1' /> <?php _e("Yes",'wpsc'); ?></label>
						<label>	<input <?php if($is_recurring[0]==0){echo 'checked="checked"';} ?> type='radio' name='<?php echo $meta_value; ?>[_wpsc_is_recurring]' value='0' /> <?php _e("No",'wpsc'); ?></label>
					</div>
				</div>
				<br />
	
				<div id="recurring_options">
					<label><?php _e("Charge users credit card every: ",'wpsc'); ?></label>
					<input type='text' name ='<?php echo $meta_value; ?>[_wpsc_rebill_interval][number]' value='<?php echo $rebill_period['number'] ?>' size='3' />
					<select name='<?php echo $meta_value; ?>[_wpsc_rebill_interval][unit]'>
						<option <?php if($rebill_period['unit'] == 'day'){echo 'selected="selected"';} ?> value='day'><?php _e("Days",'wpsc'); ?></option>
						<option <?php if($rebill_period['unit'] == 'week'){echo 'selected="selected"';} ?> value='week'><?php _e("Weeks",'wpsc'); ?></option>
						<option <?php if($rebill_period['unit'] == 'month'){echo 'selected="selected"';} ?> value='month'><?php _e("Months",'wpsc'); ?></option>
						<option <?php if($rebill_period['unit'] == 'year'){echo 'selected="selected"';} ?> value='year'><?php _e("Years",'wpsc'); ?></option>

					</select>
				</div>
					
				<br />
				
				<div>
					<?php /* <strong><?php _e("Select the Recurring Billing Settings",'wpsc'); ?></strong> */ ?>
					
					<div id="keep_charging">
						<?php _e("Keep charging the users credit card until it expires: ",'wpsc'); ?><br />
						<label>	<input <?php if($charge_to_expiry[0]==1){echo 'checked="checked"';} ?> type='radio' name='<?php echo $meta_value; ?>[_wpsc_charge_to_expiry]' value='1' /> <?php _e("Yes",'wpsc'); ?></label>
						<label>	<input <?php if($charge_to_expiry[0]==0){echo 'checked="checked"';}?> type='radio' name='<?php echo $meta_value; ?>[_wpsc_charge_to_expiry]' value='0' /> <?php _e("No",'wpsc'); ?></label>
					</div>
	
					
					<div id="charging_options">
						<label><?php _e("Number of times to charge users credit card: ",'wpsc'); ?><input type='text' name ='<?php echo $meta_value; ?>[_wpsc_rebill_number]' value='<?php echo $number_times_rebill[0] ?>' size='3' /></label>
					</div>
	
				<br />
					<div>
						<label><input type='checkbox' name='<?php echo $meta_value; ?>[_wpsc_email_on_subscription_end]' /><?php _e("Email customer when subscription ends",'wpsc'); ?></label>
					</div>	
				<br />
				</div>
			<?php if((float)WPSC_VERSION < 3.8 ){?>	
			</div>
		</div>
		
		<?php }

	}
	

	
	
	
//add the meta box to the products page 3.8 does this much nicer than 3.7
function wpsc_add_purch_cap_product_form($order) {
	  if(array_search('wpsc_purch_cap_product_forms', $order) === false) {
	    $order[] = 'wpsc_purch_cap_product_forms';
	  }
	  return $order;
	}

add_filter('wpsc_products_page_forms', 'wpsc_add_purch_cap_product_form');


function wpsc_new_meta_boxes() {
add_meta_box( 'wpsc_add_meta_product_form', 'Members and Subscriptions', 'wpsc_purch_cap_product_forms', 'wpsc-product', 'normal', 'high' );
}
add_action( 'admin_menu', 'wpsc_new_meta_boxes' );

//add_filter('wpsc_products_page_forms', 'wpsc_purch_cap_product_forms');



	function wpsc_add_caps_to_user($purchase_data) {
		global $wpdb, $wpsc_product_capability_list;
		extract($purchase_data);
		$existing_defined_capabilities = array_keys($wpsc_product_capability_list);
		// Only add capabilities to aaccepted orders
		if(($purchase_log['processed'] >= 2)) {
			if((float)WPSC_VERSION < 3.8 ){
				$saved_product_capabilties = get_product_meta($cart_item['prodid'],'_wpsc_product-capabilities',true);
				$membership_length = (array)get_product_meta($cart_item['prodid'],'_wpsc_membership_length',true);
				$is_permanent = (int)get_product_meta($cart_item['prodid'],'_wpsc_is_permanent',true);
			}else{
				$saved_product_capabilties = get_post_meta($cart_item['prodid'],'_wpsc_product-capabilities',true);
				$membership_length = (array)get_post_meta($cart_item['prodid'],'_wpsc_membership_length',true);
				$is_permanent = (int)get_post_meta($cart_item['prodid'],'_wpsc_is_permanent',true);
				
				}
			$edit_user = new WP_User($purchase_log['user_ID']);
			$subscription_lengths = get_user_meta($edit_user->ID, '_subscription_length');
			$members_lengths = get_user_meta($edit_user->ID, '_subscription_ends');
			$members_starts = get_user_meta($edit_user->ID, '_subscription_starts');
			
			if(is_array($members_lengths) && !empty($members_lengths[0])){
			$members_lengths = $members_lengths[0];
			}
			if(is_array($members_starts) && !empty($members_starts[0])){
			$members_starts = $members_starts[0];
			}	
			if(is_array($subscription_lengths) && !empty($subscription_lengths[0])){
			$subscription_lengths = $subscription_lengths[0];
			}		
			
			foreach((array)$saved_product_capabilties as $saved_product_capability) {
				if(array_search($saved_product_capability, $existing_defined_capabilities) !== false) {
					switch($wpsc_product_capability_list[$saved_product_capability]['capability-type']) {
						case 'bbpress':
							wpsc_add_bb_capability($purchase_log['user_ID'], $saved_product_capability);
						break;
	
						case 'wordpress':
						default:
						
							if($is_permanent ){
								$members_lengths[$saved_product_capability] = 'never';
							}else{
								switch($membership_length['unit']) {
						          case 'd':
						          $future_time = mktime(date('h'),date('m'),date('s'),date('m'),(date('d')+$membership_length['length']),date('Y'));
						          break;
						          case 'm':
						          $future_time = mktime(date('h'),date('m'),date('s'),(date('m')+$membership_length['length']),date('d'),date('Y'));
						          break;
						          case 'Y':
						          $future_time = mktime(date('h'),date('m'),date('s'),date('m'),date('d'),(date('Y')+$membership_length['length']));
						          break;
						          case 'w':
						          $length = 7 * (int)$membership_length['length'];	
						          $future_time = mktime(date('h'),date('m'),date('s'),date('m'),(date('d')+$length),date('Y'));						          
						          break;
								  
					           }
								$members_lengths[$saved_product_capability]= $future_time;
								
							}
							$members_starts[$saved_product_capability]= time();
							$edit_user->add_cap($saved_product_capability, true);
							$subscription_lengths[$saved_product_capability] = $future_time - time();
						break;
					}
				}
			}
			
			update_user_meta($edit_user->ID, '_subscription_length',$subscription_lengths);
			update_user_meta($edit_user->ID,'_subscription_ends', $members_lengths);
			update_user_meta($edit_user->ID,'_subscription_starts', $members_starts);
			update_user_meta($edit_user->ID,'_has_current_subscription', 'true');
		}
	}
	
	
	
	function wpsc_post_required_capabilities() {
	  global $wpdb, $wpsc_product_capability_list, $post_ID;
	  $post_needed_capabilities = get_post_meta($post_ID, '_required_capabilities',true);
		?>
		<div id='postvisibility' class='postbox meta-box-sortables'>
			<h3><?php _e("Subscriptions needed to view this Post",'wpsc'); ?></h3>
		    <div class='inside'>
		    
				<input type="hidden" name="wpsc_post_capabilities_is_submitted"  value="true"/>
				<?php
					//echo "<pre>".print_r($post_needed_capabilities,true)."</pre>";
					foreach($wpsc_product_capability_list as $product_capability => $product_capability_data) {
					  if($product_capability_data['capability-type'] == 'wordpress') {
							$is_checked = '';
							if(array_search($product_capability, (array)$post_needed_capabilities) !== false) {
								$is_checked = "checked='checked'";
							}
							?>
							<div>
								<label>
								<input type="checkbox" <?php echo $is_checked; ?> name="wpsc_post_capabilities[]"  value="<?php echo $product_capability; ?>"/>
								<?php echo $product_capability_data['name']; ?>
								</label>
							</div>	
						<?php
					  }
					}
				?>
			</div>
			
		</div>
		<?php
	}


add_action( 'admin_footer', 'wpsc_meta_boxes' );
add_action('edit_form_advanced','wpsc_post_required_capabilities');
add_action('edit_page_form', 'wpsc_post_required_capabilities');
	
	
	function wpsc_save_post_required_capabilities($post_ID) {
	  global $wpdb;
	  if($_POST['wpsc_post_capabilities_is_submitted'] == 'true') {
			update_post_meta($post_ID, '_required_capabilities', (array)$_POST['wpsc_post_capabilities']);
	  }
	}
	
	add_action('wp_insert_post', 'wpsc_save_post_required_capabilities');
	
	function wpsc_restrict_post_access($post_array) {
	  global $wpdb,$gateway_checkout_form_fields, $userdata;
	  $siteurl = get_option('siteurl');
	 
	  if(!current_user_can('administrator')) {
			foreach((array)$post_array as $key=>$post) {
				if ($post->post_author == $userdata->ID)
					continue;
				$required_capabilities = (array)get_post_meta($post->ID,'_required_capabilities', true);
				if((count($required_capabilities) > 0) and ($required_capabilities[0] != '')) {
					$post_access = false;
					$members_length = (array)get_user_meta($userdata->ID,'_subscription_ends');
					if(is_array($members_length) && !empty($members_length[0])){
						$members_length = $members_length[0];
					}
					foreach($required_capabilities as $capability ) {
						if($capability != null){
							if ((int)mktime() < (int)$members_length[$capability] || $members_length[$capability] == 'never' ){
								$post_access = true;
								break;					
							}
						}
					}
					$subscription_canceled = get_user_meta( $user_id, '_subscription_canceled', true ); 

					if ($subscription_canceled)
					{
						$post_access = false;
					}
					if($post_access === false ) {
						$post_array[$key]->post_content = apply_filters( 'wpsc_post_insufficient_capabilities', $post_array[$key]->post_content, $post);
					}
				}
			}
		}
	  return $post_array;
	}
	
	add_action('the_posts','wpsc_restrict_post_access');
	
	
	function wpsc_insufficient_capability_messages($post_content, $post) {
	  global $wpdb,$gateway_checkout_form_fields, $user_ID, $userdata;
		
		$siteurl = get_option('siteurl');
	
		// 	$post_content .= "<pre>".print_r($user_ID,true)."</pre>";
		// 	$post_content .= "<pre>".print_r($userdata,true)."</pre>";
		$default_message = get_capability_error_message($post);
		
		
		$post_content = htmlentities(stripslashes(__($default_message)), ENT_QUOTES, 'UTF-8');
			//$post_content = wpsc_filter_preview_content($post_content);
		if (!($user_ID > 0)) {
			// mostly standard wp login form, redirects back to the current page, needs the siteurl because of permalinks
			//exit('<pre>'.print_r($post_content,true).'</pre>');
			//$post_content = wpsc_filter_preview_content($post_content);
			//$post_content .= apply_filters('the_content', $post_content);
			$post_content .= "<div>";
			//$post_content .= "This page is for subscribers only, if you have a subscription please login below <br />";
			$post_content .= " <form name='loginform' id='loginform' action='".$siteurl."/wp-login.php' method='post' style='text-align: left;'>";
			$post_content .= "   <label>Username: <input type='text' name='log' id='log' value='' size='20' tabindex='1' /></label><br />";
			$post_content .= "   <label>Password: <input type='password' name='pwd' id='pwd' value='' size='20' tabindex='2' /></label>";
			$post_content .= "   <input type='submit' name='submit' id='submit' value='Login &raquo;' tabindex='4' />";
			$post_content .= "   <input type='hidden' name='redirect_to' value='".$post->guid."' />";
			$post_content .= " </form>";
			$post_content .= "</div>";
		} else {
			echo $default_message;
		}
	  return $post_content;
	}
	
	add_filter('wpsc_post_insufficient_capabilities', 'wpsc_insufficient_capability_messages', 10, 2);
	


	
	
	function wpsc_add_bb_capability($user_id, $capability = '') {
		if($capability != '' ) {
			$bbpress_roles = (array)get_usermeta( $user_id, WPSC_BB_PREFIX . 'capabilities' );
			if(in_array($capability, $bbpress_roles)) {
				$bbpress_roles[] = $capability;
				update_usermeta( $user_id, WPSC_BB_PREFIX . 'capabilities', $bbpress_roles );
			}
		}
	}
	
	//require_once('purchase_capability_admin.php');
	add_action('wpsc_transaction_result_cart_item','wpsc_add_caps_to_user');

			
	function wpsc_capabilities_add_scripts(){
		wp_enqueue_script('wpsc_capabilities', WPSC_MEMBERS_URL.'/css-and-js/admin.js', array('jquery'), '1.0.3');
		wp_enqueue_style('wpsc_capabilities', WPSC_MEMBERS_URL.'/css-and-js/style.css');
	}
	

function wpsc_send_email(){
global $wpdb;
//select all product ID's that haave send email turned on
$product_ids = $wpdb->get_col('SELECT `product_id` FROM `wp_wpsc_productmeta` WHERE `meta_key` = "_wpsc_email_on_subscription_end" and `meta_value` = "on"'); 
		
foreach ($product_ids as $product_id){
//get all the purcahseIds for people who have bought this product
	$purchase_ids = $wpdb->get_col('SELECT `purchaseid` FROM `wp_wpsc_cart_contents` WHERE `prodid` ="'.$product_id.'"'); 
	}
	
foreach ($purchase_ids as $purchase_id){
	if (!empty($purchase_id))
	//get all the user ids from the purchase number
		$user_ids = $wpdb->get_col('SELECT `user_ID` FROM `wp_wpsc_purchase_logs` WHERE `id`  ="'.$purchase_id.'"'); 	
	}
				
	foreach ($user_ids as $user_id){
		$subscriptions = get_user_meta($user_id, '_subscription_ends');
		

		foreach ($subscriptions as $user_subscription){
			$subscription_names = array_keys($user_subscription);

			for ($x=0; $x<count($subscription_names); $x++){
				$current_time = time();
				$subscription_name = $subscription_names[$x];
				$end_time = $user_subscription[$subscription_name];
			
				if($current_time >= $end_time) {
					//send user email to go in here
					$user = new WP_User($user_id);
					$user_email = $user->user_email;
					$site_name = get_option( 'blogname' );
					$products_page = get_option( 'product_list_url' );
					$admin_email = get_option('admin_email');
					$headers = 'From: '.$site_name.' <'.$admin_email.'>';
					$subject = "Youre membership with". $site_name ."has expired";
					$message = "Hi there, your ".$subscription_name." subscription has ended you can renew your subscription here:". $products_page;
					
					wp_mail( $user_email, $subject, $message, $headers);
					
					//unset the meta for the expired subscription and resave
					$subscription_start = get_user_meta($user_id, '	_subscription_starts');
					foreach ($subscription_start as $start){
						unset($start[$subscription_name]);
						update_user_meta( $user_id, '_subscription_starts', $start);
					}
					
					$subscription_length = get_user_meta($user_id, '_subscription_length');
					foreach ($subscription_length as $length){
						unset($length[$subscription_name]);
						update_user_meta( $user_id, '_subscription_length', $length);
					}
					//remove subscription
					unset($user_subscription[$subscription_name]);
					//remove the capability
					$user->remove_cap($subscription_name);
					update_user_meta( $user_id, '_subscription_ends', $user_subscription);
					
				}
			}	
		}
	}
}

register_activation_hook(__FILE__, 'my_activation');
add_action('send_email_daily', 'wpsc_send_email');

function my_activation() {
	wp_schedule_event(time(), 'hourly', 'send_email_daily');
}

register_deactivation_hook(__FILE__, 'my_deactivation');


function my_deactivation() {
	wp_clear_scheduled_hook('send_email_daily');
}

		
/*
	function wpsc_manage_caps(){
	 global	$wpsc_product_capability_list;
	}
	
	if($_REQUEST['members_caps']=='true'){
		wpsc_manage_caps();
	}
*/
	
	function get_capability_error_message($post)
	{
		global $user_ID , $userdata;	
		
		$default_message = 'This page is for subscribers only, ';
		$error_message = '';
		
		$required_capabilities = (array)get_post_meta($post->ID,'_required_capabilities', true);
		$capabilities = get_option('wpsc_product_capability_list');
		
		
		if($required_capabilities != NULL) {
			foreach($required_capabilities as $capability_slug) 
			{
				$error_message .= $capabilities[$capability_slug]['message-details'].' ';
			}
			
			$default_message = $error_message;
		}
		
		return $default_message ;
	}
	
	

?>