<?php
/**
 * My Sucscriptions widget class
 *
 * If a user is logged in it will display their current active sunscriptions
 *
 * @since 2.1
 */

class WP_Widget_My_Subscriptions extends WP_Widget {

	function WP_Widget_My_Subscriptions() {
		$widget_ops = array('classname' => 'widget_my_subscriptions', 'description' => __('A widget to display your current active subscriptions'));
		$control_ops = array('width' => 400, 'height' => 350);
		$this->WP_Widget('my_subscriptions', __('My Subscriptions'), $widget_ops, $control_ops);
	}

	function widget( $args, $instance ) {
		extract($args);
		$title = apply_filters( 'widget_title', empty($instance['title']) ? 'My Subscriptions' : $instance['title'], $instance, $this->id_base);
		
		//Checks if the user is logged in the displays if true
		if (is_user_logged_in()) {
			
			echo $before_widget;
			if ( !empty( $title ) ) { echo $before_title . $title . $after_title; } ?>
				<div class="widget_list">
				<?php echo wpsc_get_my_subscriptions();?>
				</div>
			<?php
			echo $after_widget;
		
		}
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		return $instance;
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '') );
		$title = strip_tags($instance['title']);
?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>

<?php
	} 
}
add_action( 'widgets_init', create_function( '', 'return register_widget("WP_Widget_My_Subscriptions");' ) );

//SR#START - display customer KOS my subscription page
function wpsc_get_my_subscriptions_kos() {
	global $userdata;
	
	$user = wp_get_current_user();
	
	$subscriptions_end = get_usermeta($user->id, '_subscription_ends', true);
	$subscriptions_length = get_usermeta($user->id,'_subscription_length', true);
	$subscriptions_starts = get_usermeta($user->id,'_subscription_starts', true);
	$date_format = get_option('date_format');
	$time_format = get_option('time_format');
	//echo '<pre> sub end'.print_r($subscriptions_end,1).'</pre>';
	//o exit('<pre> sub end'.print_r($subscriptions_end,1).'</pre>');
	$names = array_keys($subscriptions_end); 
	
	if($names != NULL){ 
		$ouput = '<tr class="kos_subscription_headers">';
		$ouput .= '<td>Subscription</td>';
		$ouput .= '<td>Start date</td>';
		$ouput .= '<td>Expiry date</td>';
		$ouput .= '</tr>';
		foreach($names as $name){
			$ouput .= '<tr>';
			$ouput .= '<td>'.$name.'</td>';
			// Uncomment the line below to display the start date.
			//$ouput .= '<li><div class="fl">'.date($date_format,$subscriptions_starts[$name]).'</div>';
			$ouput .= '<td>'.date($date_format,$subscriptions_starts[$name]).'</td>' ;
			$ouput .= '<td>'.date($date_format,$subscriptions_end[$name]).'</td>' ;
			//$ouput .= date($time_format,$subscriptions_length[$name]).'</div><br clear="all" /></li>' ;
			//$ouput .= wpsc_get_subscription_products($name).'</li>' ;
			$ouput .= '</tr>';
		}
		
		return $ouput;
	}
	else {
		$ouput = '<tr><td colspan="3">Currently, you have no active subscriptions</td></tr>';
		//return NULL;
		return $ouput;
		return NULL;
	}
}
//SR#END

function wpsc_get_my_subscriptions() {
	global $userdata;
	
	$user = wp_get_current_user();
	
	$subscriptions_end = get_usermeta($user->id, '_subscription_ends', true);
	$subscriptions_length = get_usermeta($user->id,'_subscription_length', true);
	$subscriptions_starts = get_usermeta($user->id,'_subscription_starts', true);
	$date_format = get_option('date_format');
	$time_format = get_option('time_format');
	//echo '<pre> sub end'.print_r($subscriptions_end,1).'</pre>';
	//o exit('<pre> sub end'.print_r($subscriptions_end,1).'</pre>');
	$names = array_keys($subscriptions_end); 
	
	if($names != NULL){ 
		$ouput = '<ul class="my-subscriptions-list">';
		$ouput .= '<li class="subscription-head"><div class="fl">Title</div><div class="fr">Expiry Date</div><br clear="all"></li>';
		foreach($names as $name){
			$ouput .= '<li><div class="fl">'.$name.'</div>';
			// Uncomment the line below to display the start date.
			//$ouput .= '<li><div class="fl">'.date($date_format,$subscriptions_starts[$name]).'</div>';
			$ouput .= '<div class="fr">'.date($date_format,$subscriptions_end[$name]).'</div><br clear="all" />' ;
			//$ouput .= date($time_format,$subscriptions_length[$name]).'</div><br clear="all" /></li>' ;
			$ouput .= wpsc_get_subscription_products($name).'</li>' ;
		}
		
		return $ouput;
	}
	else {
		return NULL;
	}
}

//This function will return the list of products that this subscription has been applied too.
function wpsc_get_subscription_products($subscription_id = false) {
	global $wpdb;
	if($subscription_id != false){
		$subs = $wpdb->get_results($wpdb->prepare("SELECT post_id,meta_value FROM ".$wpdb->postmeta." WHERE meta_key = '_required_capabilities'"));
		
		
		$id_array = '';
		foreach($subs as $sub){
			$sub->meta_value = maybe_unserialize($sub->meta_value);
			if( in_array($subscription_id,$sub->meta_value)) { $id_array[] =  $sub->post_id;}
		}
		
		$output = '<ul class="my-subscriptions-post-list">';
		foreach ($id_array as $id) {
			$output .= '<li><a href="'.get_permalink($id).'" title="'.get_the_title($id).'">'.get_the_title($id).'</a></li>';
		}
		$output .= '</ul>';
		return $output;

	}
	else {
		return false;
	}
	
}
function wpsc_my_subscription_css() { ?>
	<style media="screen">
		.widget_my_subscriptions ul li.subscription-head {
			font-weight:bold;
		}	
		.widget_my_subscriptions ul.my-subscriptions-list {
			margin-left:0px;
			padding-left:0px;
		}	
		.widget_my_subscriptions ul li .fl {
			float:left;
			width:59%;
		}
		.widget_my_subscriptions ul li .fr {
			float:right;
			width:40%;
		}
		.widget_my_subscriptions ul li {
			list-style-type:none;
		}
		.widget_my_subscriptions ul li ul li {
			list-style-type:disc;
		}		
	</style>
<?php }
add_action('wp_head','wpsc_my_subscription_css');
?>