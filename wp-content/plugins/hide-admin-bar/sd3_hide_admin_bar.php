<?php
/* 
Plugin Name: Hide Admin Bar
Description: Hides the Admin Bar in WordPress 3.1+, credits to <a href="http://yoast.com/disable-wp-admin-bar/">Yoast</a>, and <a href="http://developersmind.com/2011/02/23/disable-admin-bar-and-hide-preferences/">Pete Mall</a>.
Version: 0.2.3
Author: Shelby DeNike (sd3labs.com)
Author URI: http://www.sd3labs.com
*/ 
add_action( 'admin_print_scripts-profile.php', 'hide_admin_bar_prefs' );
function hide_admin_bar_prefs() { ?>
<style type="text/css">
	.show-admin-bar {display: none;}
</style>
<?php
}
add_filter( 'show_admin_bar', '__return_false' );
?>
