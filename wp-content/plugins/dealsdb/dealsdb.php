<?php
/*
Plugin Name: Deals Database
Plugin URI: http://www.artmarketmonitor.com/
Description: Deals Database Plugin
Version: 0.1
Author: ArtMarketMonitor
Author URI: http://www.artmarketmonitor.com/
*/

//translation support
load_plugin_textdomain ( 'addcustomtype' , FALSE , '/addcustomtype/translations' );

error_reporting(1);

class DealsDb {
	var $meta_fields = array("price_sold", "comment", "buyer_name", "date_sold", "artist", "work_type", "work_year", "primary_or_secondary", "edition_min", "edition_max");
	
	function DealsDb()
	{
		//add_filter("manage_edit-deal_columns", array(&$this, "edit_columns"));
		//add_action("manage_posts_custom_column", array(&$this, "custom_columns"));
		
		// Register custom taxonomy
		 $labels = array(
						    'name' => _x( 'gallery', 'taxonomy general name' ),
						    'singular_name' => _x( 'Gallery', 'taxonomy singular name' ),
						    'search_items' =>  __( 'Search Galleries' ),
						    'all_items' => __( 'All Galleries' ),
						    'parent_item' => __( 'Parent Gallery' ),
						    'parent_item_colon' => __( 'Parent Gallery:' ),
						    'edit_item' => __( 'Edit Gallery' ), 
						    'update_item' => __( 'Update Gallery' ),
						    'add_new_item' => __( 'Add New Gallery' ),
						    'new_item_name' => __( 'New Gallery Name' ),
						    'menu_name' => __( 'Galleries' ),
						  ); 
						  
		 $labels2 = array(
						    'name' => _x( 'artfair', 'taxonomy general name' ),
						    'singular_name' => _x( 'Art Fair', 'taxonomy singular name' ),
						    'search_items' =>  __( 'Search Art Fairs' ),
						    'all_items' => __( 'All Art Fairs' ),
						    'parent_item' => __( 'Parent Art Fair' ),
						    'parent_item_colon' => __( 'Parent Art Fair:' ),
						    'edit_item' => __( 'Edit Art Fair' ), 
						    'update_item' => __( 'Update Art Fair' ),
						    'add_new_item' => __( 'Add New Art Fair' ),
						    'new_item_name' => __( 'New Art Fair Name' ),
						    'menu_name' => __( 'Art Fairs' ),
						  ); 
						  
		register_taxonomy("gallery", array("gallery"), array("hierarchical" => true, "label" => __("Galeries",'addcustomtype'), "labels" => $labels, "singular_label" => __("Gallery",'addcustomtype'), "rewrite" => true));
		register_taxonomy("artfair", array("artfair"), array("hierarchical" => true, "label" => __("Art Fairs",'addcustomtype'), "labels" => $labels2, "singular_label" => __("Art Fair",'addcustomtype'), "rewrite" => true));
					
		// Register custom post types
		register_post_type('dealentry', array(
			'label' => __('Deals','addcustomtype'),
			'singular_label' => __('Deal Database','addcustomtype'),
			'labels' => array('add_new' => __('New Deal','addcustomtype'),
							  'add_new_item' => __('New Deal','addcustomtype'),
							  'new_item' => __('New Deal','addcustomtype')),
			'public' => true,
			'show_ui' => true, // UI in admin panel
			'_builtin' => false, // It's a custom post type, not built in
			'_edit_link' => 'post.php?post=%d&post_type=dealentry',
			'capability_type' => 'post',
			'rewrite' => false,
			'query_var' => "dealentry", // This goes to the WP_Query schema
			'hierarchical' => false,
			'taxonomies' => array("gallery","artfair"),
			'supports' => array(//'title', 
								//'editor', 
								'thumbnail',
								'price_sold',
								'buyer_name',
								'date_sold',
								'work_title',
								'comment',
								'artist',
								'work_type',
								'edition_min', 'edition_max', 'primary_or_secondary', 
								'work_year',
								'images'
								) 
		));
		
		// Admin interface init
		add_action("admin_init", array(&$this, "admin_init"));		
		//add_action('save_post', array(&$this, "my_wp_insert_post"));
		//add_action('edit_post', array(&$this, "my_wp_insert_post"));
		
//		add_action("template_redirect", array(&$this, 'template_redirect'));
		
	}
	
	function edit_columns($columns)
	{
		$columns = array(
			"cb" => "<input type=\"checkbox\" />",
			"title" => "Deal Title",
			"description" => "Description",
			"length" => "Length",
			"sale" => "Sale",
			'price_sold' => "price_sold",
			'date_sold' => "date_sold",
			'work_title' => "Title of Work",
			'comment' => "Comment",
			'artist' => "Artist",
			'buyer_name' => "Buyer's Name",
			'work_type' => "Type of Work",
			'work_year' => "Year",
			'images' => "Images"
		);
		
		return $columns;
	}
	
	function custom_columns($column)
	{
		global $post;
		switch ($column)
		{
			case "description":
				the_excerpt();
				break;
			case "length":
				$custom = get_post_custom();
				echo $custom["length"][0];
				break;
			case "sale":
				$speakers = get_the_terms(0, "sale");
				$speakers_html = array();
				foreach ($speakers as $speaker)
					array_push($speakers_html, '<a href="' . get_term_link($speaker->slug, "sale") . '">' . $speaker->name . '</a>');
				
				echo implode($speakers_html, ", ");
				break;
			case "price_sold":
				$custom = get_post_custom();
				echo $custom["price_sold"][0];
				break;
			case "date_sold":
				$custom = get_post_custom();
				echo $custom["date_sold"][0];
				break;
			case "buyer_name":
				$custom = get_post_custom();
				echo $custom["buyer_name"][0];
				break;
			case "work_title":
				$custom = get_post_custom();
				echo $custom["work_title"][0];
				break;
			case "comment":
				$custom = get_post_custom();
				echo $custom["comment"][0];
				break;
				
			case "artist":
				$custom = get_post_custom();
				echo $custom["artist"][0];
				break;
			case "work_type":
				$custom = get_post_custom();
				echo $custom["work_type"][0];
				break;
			case "work_year":
				$custom = get_post_custom();
				echo $custom["work_year"][0];
				break;
		}
	}
	
	// Template selection
	function template_redirect()
	{
		global $wp;
		if ($wp->query_vars["post_type"] == "dealentry")
		{
			include(TEMPLATEPATH . "/deal.php");
			die();
		}
	}
	
	
	function admin_init() 
	{
		global $blog_id;
		
		wp_enqueue_script('jquery');
		
		
		// Custom meta boxes for the edit deal screen
		add_meta_box("p30-meta", __('Deal Details', 'addcustomtype'), array(&$this, "meta_options"), "dealentry", "normal", "low");
	}
	
	// Admin post meta contents
	function meta_options()
	{	
		global $post;
		$custom = get_post_custom($post->ID); query_posts();
		
		//print_r($custom);
		$comment = $post->post_content;
	
		$length = (isset($custom["length"][0])) ? $custom["length"][0] : '';
		$price_sold = (isset($custom["price_sold"][0])) ? $custom["price_sold"][0] : '';
		$date_sold = (isset($custom["date_sold"][0])) ? $custom["date_sold"][0] : '';
		$work_title = $post->post_title;
		$artist = (isset($custom["artist"][0])) ? $custom["artist"][0] : '';
		$work_type = (isset($custom["work_type"][0])) ? $custom["work_type"][0] : '';
		$work_year = (isset($custom["work_year"][0])) ? $custom["work_year"][0] : '';
		$buyer_name = (isset($custom["buyer_name"][0])) ? $custom["buyer_name"][0] : '';
		$edition_min = (isset($custom["edition_min"][0])) ? $custom["edition_min"][0] : '';
		$edition_max = (isset($custom["edition_max"][0])) ? $custom["edition_max"][0] : '';
		$primary_or_secondary = (isset($custom["primary_or_secondary"][0])) ? $custom["primary_or_secondary"][0] : '';		
		$estimated_price_range = (isset($custom["estimated_price_range"][0])) ? $custom["estimated_price_range"][0] : '';								

		echo "<script type='text/javascript'>
                  jQuery(document).ready(function(){
                      jQuery('#date_sold').datepicker();
                  });
              </script>";
?>
	<div class="classform" id="formbox">
	<div id="err_msg" style="background: red"></div>
	
	<b><label for="artist"><?php _e('Artist*:','addcustomtype'); ?> </label></b>
	<input type="text" id="artist" class="adfields" name="artist" size="52" maxlength="100" value="<?php if(isset($artist)){echo $artist;} ?>" />		
	<br/>
	<b><?php _e('Title*:','admanager'); ?></b><br/>
	<input type="text" name="post_title" class="adfields" size="52" maxlength="100" value="<?php if(isset($work_title)){echo $work_title;} ?>" > </input>
	<br/>
	
	<b><label for="work_year"><?php _e('Year:','addcustomtype'); ?> </label></b>
	<input type="text" id="work_year" class="adfields" name="work_year" maxlength="4" value="<?php if(isset($work_year)){echo $work_year;} ?>" />	
	<br/>
	<b><label for="work_type"><?php _e('Type:','addcustomtype'); ?> </label></b>
	
	<?php 
	
	// check if type is active for the given post
	function type_selected($type, $post)
	{
		$custom = get_post_custom($post->ID);
		$types = (isset($custom["work_type"])) ? $custom["work_type"] : '' ;
		
		for ($i=0; $i < count($types); $i++)
		{
			$val = $types[$i];
			if ($val == $type)
				return true;
		}
		
		return false;		
	}
	
	$types_of_work = array("Painting", "Watercolor", "Sculpture", "Work on Paper", "Drawing", "Work of Art", "Photograph", "Ceramic", "Print", "Bronze");
	
	$checked = '';
	foreach ($types_of_work as $type) {
		if (type_selected($type, $post)) {
			$checked = 'checked';
		}
		
		echo '<input type="checkbox" name="work_type[]" '.$checked.' value="'.$type.'">'.$type.'</input><br/>';
		$checked = '';
	}
	
	?>
	
	<br/>
	<b>Edition:</b>
	<input type="text" class="adfields" name="edition_min" value="<?php echo $edition_min; ?>" size="2"></input> of <input type="text" class="adfields" name="edition_max" value="<?php echo $edition_max; ?>" size="3"></input> 
	
	<br/><br/>
	<b>Sale:</b><br/>
	<input type="radio" name="primary_or_secondary" value="primary" <?php if ($primary_or_secondary == 'primary') echo 'checked="checked"'; ?>></input> Primary Sale
	<br/>
	<input type="radio" name="primary_or_secondary" value="secondary" <?php if ($primary_or_secondary == 'secondary') echo 'checked="checked"'; ?>></input> Secondary Sale
	
	<br/>
	<br/>
	<b>Price:</b><br/>
	
	
	<label for="price_sold"><?php _e('Price Sold in USD Dollars','admanager'); ?> </label>
	<input type="text" class="adfields" name="price_sold" value="<?php if (isset($price_sold)) echo $price_sold; ?>"></input> 
	<br/>Estimated range:<br/>
	<select name="estimated_price_range" style="width: 150px">
		<option <?php if($estimated_price_range == "Emerging") echo 'selected=\"selected\"' ?> value="Emerging">Emerging ($0-$25,000)</option>
		<option <?php if($estimated_price_range == "Established") echo 'selected=\"selected\"' ?>  value="Established">Established ($25-75,000)</option>
		<option <?php if($estimated_price_range == "Significant") echo 'selected=\"selected\"' ?>  value="Significant">Significant ($75,000 - $250,000)</option>
		<option <?php if($estimated_price_range == "Conviction Buy") echo 'selected=\"selected\"' ?>  value="Conviction Buy">Conviction Buy ($250,000 - $750,000)</option>
		<option <?php if($estimated_price_range == "Investment Grade") echo 'selected=\"selected\"' ?>  value="Investment Grade">Investment Grade ($750k and above)</option>
	</select> 

	<br/>
	
	<b>Date Sold</b> <br/><input type="text" class="adfields" id="date_sold" name="date_sold" value="<?php if(isset($date_sold)){echo $date_sold;} ?>" ></input>
	<br/>

	<br/><br/>
	<b>Buyer</b><br/><br/>
	Buyer Name <small>(if public)</small><br/> <input type="text" class="adfields" id="buyer_name" size="52" maxlength="100" name="buyer_name" value="<?php if(isset($buyer_name)){echo $buyer_name;} ?>"></input>
	<br/>

	<b>Comment</b><br/>
	<textarea id="comment" name="comment" style="width: 470px; height: 100px" rows="2"><?php if(isset($comment)){echo $comment; } ?></textarea>
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
function my_wp_insert_post(/*$post_id, $post = null*/)
{	

			
	global $post;
	$meta_fields = array("price_sold", "comment","estimated_price_range", "buyer_name", "date_sold", "artist", "work_type", "work_year", "images", "edition_min", "edition_max", "primary_or_secondary");
	$post_id = $post->ID;
		
	// verify this came from the our screen and with proper authorization,
 	// because save_post can be triggered at other times

	
	if (empty($_POST['addcustomtype_noncename'])) $_POST['addcustomtype_noncename'] = '';
  	if ( !wp_verify_nonce( $_POST['addcustomtype_noncename'], plugin_basename(__FILE__) )) {
    	return $post_id;
  	}

  
  	// verify if this is an auto save routine. If it is our form has not been submitted, so we dont want
  	// to do anything
  	if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) 
    	return $post_id;
	
	if ($post->post_type == "dealentry")
	{	
		// Loop through the POST data
		foreach ($meta_fields as $key)
		{
			$value = @$_POST[$key];
			
			if (empty($value))
			{
				if ($key == "images") {
					if ($_POST['delete_images']) {
						error_log("DELETE ALL");
						delete_post_meta($post_id, $key);
					}
				} else {
					error_log( "WOULD DELETE HERE " . $key);
					delete_post_meta($post_id, $key);
				}
				continue;
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
				//error_log( "WOULD DELETE HERE INSTEAD" );
				delete_post_meta($post_id, $key);
				
				// Loop through the array adding new values to the post meta as different entries with the same name
				foreach ($value as $entry)
					add_post_meta($post_id, $key, $entry);
			}
		}
	}
}

//Define plugin directories
define( 'WP_ADDCUSTOMTYPE_URL', get_bloginfo('stylesheet_directory').'/../../plugins/'.plugin_basename(dirname(__FILE__)) );

function addcustomtype_styles() 
{	
	//wp_enqueue_style('my-style', WP_ADDCUSTOMTYPE_URL . '/css/smoothness/jquery-ui-1.7.3.custom.css?ver=1');
	//wp_register_script('mydatepicker', WP_ADDCUSTOMTYPE_URL . '/js/ui.datepicker.js?ver=3.3.2');
	
	wp_enqueue_script('jquery');
	wp_enqueue_script('jquery-ui-core');
	wp_enqueue_script('jquery-ui-dialog');
	//wp_enqueue_script('mydatepicker', WP_ADDCUSTOMTYPE_URL . '/js/ui.datepicker.js?ver=3.3.2', array(), '3.3.2');
}

function addcustomtype_admin_styles() {
	
	
	/*
	 * It will be called only on your plugin admin page, enqueue our script here
     */
		echo "
		<style type='text/css' media='all'>
	    	@import '".WP_ADDCUSTOMTYPE_URL."/css/styles/blue.css';
			@import '".WP_ADDCUSTOMTYPE_URL."/css/style.css';
			@import '".WP_ADDCUSTOMTYPE_URL."/css/ie.css';
			@import '".WP_ADDCUSTOMTYPE_URL."/css/iconified.css';
			@import '".WP_ADDCUSTOMTYPE_URL."/includes/js/fancybox/jquery.fancybox.css';
		</style>
		\n";
		
	wp_enqueue_style('my-style', WP_ADDCUSTOMTYPE_URL . '/css/smoothness/jquery-ui-1.7.3.custom.css');
}

function addcustomtype_admin_scripts()
{
	wp_register_script('textcounter', WP_ADDCUSTOMTYPE_URL . '/includes/js/textcounter.js?v=1');
	wp_register_script('global',    WP_ADDCUSTOMTYPE_URL . '/includes/js/global.js?v=1');
	wp_register_script('iconified', WP_ADDCUSTOMTYPE_URL . '/includes/js/iconified.js?v=1');
	wp_register_script('fancybox',  WP_ADDCUSTOMTYPE_URL . '/includes/js/fancybox/jquery.easing.1.3.js?v=1');
	wp_register_script('fancybox2', WP_ADDCUSTOMTYPE_URL . '/includes/js/fancybox/jquery.fancybox-1.2.1.pack.js?v=1');
	//wp_register_script('mydatepicker', WP_ADDCUSTOMTYPE_URL . '/js/ui.datepicker.js?ver=3.3.2');
	
	wp_enqueue_script('textcounter');
	wp_enqueue_script('global');
	wp_enqueue_script('iconified');
	wp_enqueue_script('fancybox');
	wp_enqueue_script('fancybox2');	
	wp_enqueue_script('jquery');
	wp_enqueue_script('jquery-ui-core');
	wp_enqueue_script('jquery-ui-dialog');
	//wp_enqueue_script('mydatepicker', WP_ADDCUSTOMTYPE_URL . '/js/ui.datepicker.js?ver=3.3.2', array(), '3.3.2');
}

// Initiate the plugin
add_action("init", "DealsDbInit");
add_action('admin_print_styles' , 'addcustomtype_admin_styles'); 
add_action('admin_print_scripts' , 'addcustomtype_admin_scripts'); 
add_action('wp_print_styles' , 'addcustomtype_styles'); 
add_action('save_post', "my_wp_insert_post");

function DealsDbInit() { global $p30; $p30 = new DealsDb(); }


/*
 * WIDGET
 */

function widget_assign($args) {
    extract($args);
?>
        <?php echo $before_widget; ?>
        
        <div class="dealsdb">
		<h2>Deals Database</h2>
		<br/>
		<h3>Most Recent</h3>
		
		<?php get_most_recent_deals(); ?>
		
		<br/>
		<h3>Biggest Deal</h3>
		
		<?php get_biggest_deal(); ?>
				
		<br/>
		
		<a href="<?php echo get_site_url(); ?>/add-deal/">Add New</a>
		</div>
		
        <?php echo $after_widget; ?>
<?php
}

function widget_assign_control() {

}

wp_register_sidebar_widget('Deals Database', 'Deals Database', 'widget_assign');
wp_register_widget_control('Deals Database', 'Deals Database', 'widget_assign_control');


function get_most_recent_deals()
{
	$args = array( 'post_type' => 'dealentry', 'posts_per_page' => 3 );
	$loop = new WP_Query( $args );
	while ( $loop->have_posts() ) : $loop->the_post();

	echo '<a href="'.get_permalink().'">'; the_title(); echo '</a>';
	echo '<br/>';
	endwhile;
}

function get_biggest_deal()
{
	global $wpdb;

	$querystr = "
    SELECT wposts.ID, wpostmeta.* 
    FROM $wpdb->posts wposts, $wpdb->postmeta wpostmeta
    WHERE wposts.ID = wpostmeta.post_id 
    AND wpostmeta.meta_key = 'price_sold' 
    AND wposts.post_status = 'publish' 
    AND wposts.post_type = 'dealentry' 
    ORDER BY wposts.post_date DESC
    LIMIT 1
 ";

	 $pageposts = $wpdb->get_results($querystr, OBJECT);
	
	 //print_r($pageposts);
	 
	 
	 if ($pageposts) {
		 echo '<a href="'.get_permalink($pageposts[0]->ID).'">'.get_the_title($pageposts[0]->ID); echo '</a>';
		echo '<br/>';
	}
}


/*
 * 
 * CUSTOM SEARCH
 * 
 */
function widget_search_assign($args) {
    extract($args);
?>
        <?php echo $before_widget; ?>
        
          <?php echo $before_title
              . 'Search For Deals'
              . $after_title; ?>
         
		
		<?php if(function_exists('wp_custom_fields_search')) 
		wp_custom_fields_search(); ?> 
		
        <?php echo $after_widget; ?>
<?php
}

function widget_search_assign_control() {

}

wp_register_sidebar_widget('Search Deals Database', 'Search Deals Database', 'widget_search_assign');
wp_register_widget_control('Search Deals Database', 'Search Deals Database', 'widget_search_assign_control');

?>
