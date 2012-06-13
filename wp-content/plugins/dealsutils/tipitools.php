<?php
/*
Plugin Name: Deals Database Utils
Plugin URI: http://www.artmarketmonitor.com/
Description: Utilities needed for the deals database plugin
Version: 0.1
Author: ArtMarketMonitor
Author URI: http://www.artmarketmonitor.com/
*/
/* 
	A plugin to include the google maps javascript
	and possibly the template code to include maps in the post content?
*/
//require(ABSPATH . 'wp-includes/constants.php');

//translation support
load_plugin_textdomain ( 'tipitools' , FALSE , '/tipitools/translations' );

define( 'WP_TIPITOOLS_URL', WP_PLUGIN_URL.'/'.plugin_basename(dirname(__FILE__)) );
define( 'WP_TIPITOOLS_DIR', WP_PLUGIN_DIR.'/'.plugin_basename(dirname(__FILE__)) );

/*
 * classi_lightbox - given an array of images creates the link to show a gallery of photos.
 * */

function classi_lightbox ($images) {
  $matches = $images;//explode(",", $images);
  if(!empty($matches))
  {
	foreach($matches as $var) {
		if ($var != "") {
			$upload_arr = wp_upload_dir();
			$image_folder_name = "deals_images";
			if(!isset($upload_arr['basedir'])) $upload_arr['basedir'] = '';
			$image_basedir = trailingslashit($upload_arr['basedir']) . $image_folder_name;
	
			$pattern = '/(\d+)/';
			preg_match($pattern, $image_basedir, $matches, PREG_OFFSET_CAPTURE, 4);
			
			//if(isset($matches[0][0]))
			//	$blog_id = $matches[0][0];
			global $blog_id;
			
			$thumb_var = substr($var,strripos($var,"/")+1);
			
			$single_thumb_img_url = WP_TIPITOOLS_URL."/includes/img_resize.php?width=100&amp;height=100&amp;url=".$thumb_var."&id=".$blog_id;
			
			echo "<a href=\"$var\" rel=\"prettyPhoto[gallery]\"><img src=\"$single_thumb_img_url\" class=\"size-thumbnail\" alt=\"".get_the_title()."\" title=\"".get_the_title()."\" /></a>"."\n";						
		
		} else {
			if (isset($matches[0]) && $matches[0] == "") {
				_e('There are no images','cp');
			}
		}
	}
  }
}

?>