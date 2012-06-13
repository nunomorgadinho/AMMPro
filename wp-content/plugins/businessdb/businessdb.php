<?php
/*
Plugin Name: Business Database
Plugin URI: http://www.artmarketmonitor.com/
Description: Business Database Plugin
Version: 0.1
Author: ArtMarketMonitor
Author URI: http://www.artmarketmonitor.com/
*/

error_reporting(1);

//Define plugin directories
define( 'WP_ADDCUSTOMTYPE_URL', WP_PLUGIN_URL.'/'.plugin_basename(dirname(__FILE__)) );

class BusinessDb {
	var $meta_fields = array("website", "email", "phone", "comment");
	
	function BusinessDb()
	{
		$labels = array(
						    'name' => _x( 'business_category', 'taxonomy general name' ),
						    'singular_name' => _x( 'Business Category', 'taxonomy singular name' ),
						    'search_items' =>  __( 'Search Business Directory' ),
						    'all_items' => __( 'All Business' ),
						    'parent_item' => __( 'Parent Business Category' ),
						    'parent_item_colon' => __( 'Parent Business Category:' ),
						    'edit_item' => __( 'Edit Business Category' ), 
						    'update_item' => __( 'Update Business Category' ),
						    'add_new_item' => __( 'Add New Business Category' ),
						    'new_item_name' => __( 'New Business Name' ),
						    'menu_name' => __( 'Business Categories' ),
						  ); 
						  
		register_taxonomy("business_category", array("business_category"), array("hierarchical" => true, "label" => __("Business Categories",'addcustomtype'), "labels" => $labels, "singular_label" => __("Business Category",'addcustomtype'), "rewrite" => true));
		
		// Register custom post types
		register_post_type('businessentry', array(
			'label' => __('Business Directory','addcustomtype'),
			'singular_label' => __('Business Database','addcustomtype'),
			'labels' => array('add_new' => __('New Business','addcustomtype'),
							  'add_new_item' => __('New Business','addcustomtype'),
							  'new_item' => __('New Business','addcustomtype')),
			'public' => true,
			'show_ui' => true, // UI in admin panel
			'_builtin' => false, // It's a custom post type, not built in
			'_edit_link' => 'post.php?post=%d&post_type=businessentry',
			'capability_type' => 'post',
			'rewrite' => false,
			'query_var' => "businessentry", // This goes to the WP_Query schema
			'hierarchical' => false,
			'taxonomies' => array("business_category"),
			'supports' => array('title', 
								'editor', 
								'thumbnail',
								'email',
								'phone',
								'comment',
								'comments'
								) 
		));
		
		// Admin interface init
		add_action("admin_init", array(&$this, "admin_init"));
	}
	
	
	function admin_init() 
	{
		global $blog_id;
		
		wp_enqueue_script('jquery');
		
		// Custom meta boxes for the edit Business screen
		add_meta_box("p30-meta", __('Business Details', 'addcustomtype'), array(&$this, "meta_options"), "businessentry", "normal", "low");
	}
	
	// Admin post meta contents
	function meta_options()
	{	
		global $post;
		$custom = get_post_custom($post->ID);
		
		//print_r($custom);
		
		$name = $post->title;
		$website = (isset($custom["website"][0])) ? $custom["website"][0] : '';
		$email = (isset($custom["email"][0])) ? $custom["email"][0] : '';
		$phone = (isset($custom["phone"][0])) ? $custom["phone"][0] : '';
		$description = $post->content;
		$comment = (isset($custom["comment"][0])) ? $custom["comment"][0] : '';


?>
	<div class="classform" id="formbox">
	<div id="err_msg" style="background: red"></div>
	<?php _e('Website','admanager'); ?><br/>
	<input type="text" name="website" size="52" maxlength="100" value="<?php if(isset($website)){echo $website;} ?>" > </input>
	<br/>
	<?php _e('E-Mail','admanager'); ?><br/>
	<input type="text" name="email" size="52" maxlength="100" value="<?php if(isset($email)){echo $email;} ?>" > </input>
	<br/>
	<?php _e('Phone','admanager'); ?><br/>
	<input type="text" name="phone" size="52" maxlength="100" value="<?php if(isset($phone)){echo $phone;} ?>" > </input>
	<br/>
	<label for="comment">Comment</label>
	<textarea id="comment" name="comment" style="width: 470px; height: 100px" rows="2"><?php if ($comment) { echo $comment; } ?></textarea>
	</div>
	
	
<?php

	 // Use nonce for verification
  	echo '<input type="hidden" name="addcustomtype_noncename" id="addcustomtype_noncename" value="' . 
    wp_create_nonce( plugin_basename(__FILE__) ) . '" />';

	}
}

// When a post is inserted or updated
/*
 * You may have to use error_log instead of echo in here
 * 
 */
function my_wp_insert_biz(/*$post_id, $post = null*/)
{	
	global $post;
	$meta_fields = array("website", "name", "email", "phone", "comment");
	$post_id = $post->ID;
		
	if (empty($_POST['addcustomtype_noncename'])) $_POST['addcustomtype_noncename'] = '';
  	if ( !wp_verify_nonce( $_POST['addcustomtype_noncename'], plugin_basename(__FILE__) )) {
    	return $post_id;
  	}

  	// verify if this is an auto save routine. If it is our form has not been submitted, so we dont want
  	// to do anything
  	if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) 
    	return $post_id;
	
	if ($post->post_type == "businessentry")
	{
		// Loop through the POST data
		foreach ($meta_fields as $key)
		{
			$value = @$_POST[$key];
			
			if (empty($value))
			{
				delete_post_meta($post_id, $key);
			}
			
			// If value is a string it should be unique
			if (!is_array($value))
			{
				// Update meta
				if (!update_post_meta($post_id, $key, $value))
				{
					// Or add the meta data
					add_post_meta($post_id, $key, $value, true);
				}
			}
			else
			{
				// If passed along is an array, we should remove all previous data
				delete_post_meta($post_id, $key);
				
				// Loop through the array adding new values to the post meta as different entries with the same name
				foreach ($value as $entry)
					add_post_meta($post_id, $key, $entry);
			}
		}
	}
}

// Initiate the plugin
add_action("init", "BusinessDbInit"); 
add_action('save_post', "my_wp_insert_biz");

function BusinessDbInit() { global $p30; $p30 = new BusinessDb(); }

/*
 * 
 * CUSTOM SEARCH
 * 
 */
function widget_bsearch_assign($args) {
    extract($args);
?>
        <?php echo $before_widget; ?>
        
          <?php echo $before_title
              . 'Search Business Directory'
              . $after_title; ?>
         
		
		<?php 
			if(function_exists('wp_custom_fields_search')) 
				wp_custom_fields_search('preset-1'); 
		?> 
		
        <?php echo $after_widget; ?>
<?php
}

wp_register_sidebar_widget('Search Business Directory', 'Search Business Directory', 'widget_bsearch_assign');
?>
