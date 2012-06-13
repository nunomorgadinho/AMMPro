<?php
/*
Plugin Name: uBillboard 3
Plugin URI: http://code.udesignstudios.net/plugins/uBillboard
Description: uBillboard is Premium Slider Plugin for WordPress by <a href="http://codecanyon.net/user/uDesignStudios">uDesignStudios</a> that allows you to create complex and eye-catching presentations for your web.
Version: 3.1.0
Author: uDesignStudios
Author URI: http://udesignstudios.net
Tags: billboard, slider, jquery, javascript, effects, udesign
*/

////////////////////////////////////////////////////////////////////////////////
//
//	Constants
//
////////////////////////////////////////////////////////////////////////////////

// Version
define('UDS_BILLBOARD_VERSION', '3.1.0');

// Handle theme insertion
if(uds_billboard_is_plugin()) {
	define('UDS_BILLBOARD_URL', plugin_dir_url(__FILE__));
	define('UDS_BILLBOARD_PATH', plugin_dir_path(__FILE__));
	define('UDS_TIMTHUMB_URL',  UDS_BILLBOARD_URL . 'lib/timthumb.php');
} else {
	define('UDS_BILLBOARD_URL', trailingslashit(get_template_directory_uri() . '/uBillboard'));
	define('UDS_BILLBOARD_PATH', trailingslashit(get_template_directory() . '/uBillboard'));
}

if(!defined('UDS_CACHE_PATH')) {
	define('UDS_CACHE_PATH',  trailingslashit(UDS_BILLBOARD_PATH) . 'cache');
}

if(!defined('UDS_CACHE_URL')) {
	define('UDS_CACHE_URL',  trailingslashit(UDS_BILLBOARD_URL) . 'cache');
}

// User configurable options
define('UDS_BILLBOARD_OPTION', 'uds-billboard-3');
define('UDS_BILLBOARD_OPTION_GENERAL', 'uds-billboard-general-3');

// Localization textdomain
define('uds_billboard_textdomain', 'uBillboard');
load_plugin_textdomain(uds_billboard_textdomain, false, dirname( plugin_basename( __FILE__ ) ) . '/lang/');

require_once 'lib/compat.php';
require_once 'lib/embed.php';
require_once 'lib/importexport.php';
require_once 'lib/classTextile.php';
require_once 'lib/uBillboard.class.php';
require_once 'lib/uBillboardSlide.class.php';
require_once 'lib/tinymce/tinymce.php';
require_once 'lib/shortcodes.php';
require_once 'lib/widget.php';

// Error Handler
global $uds_billboard_errors;

////////////////////////////////////////////////////////////////////////////////
//
//	Helper Functions
//
////////////////////////////////////////////////////////////////////////////////

/**
 *	Function, detect if uBillboard is currently being used as a plugin or
 *	as a part of a theme.
 *
 *	@return bool
 */
function uds_billboard_is_plugin()
{
	$plugins = get_option('active_plugins', array());
	
	$dir = end(explode(DIRECTORY_SEPARATOR, dirname(__FILE__)));
	return in_array($dir . '/' . basename(__FILE__), $plugins);
}

/**
 *	Function, check if timthumb image cache is writable
 *
 *	@return bool
 */
function uds_billboard_cache_is_writable()
{
	return is_writable(UDS_CACHE_PATH);
}

/**
 *	Function, detect if uBillboard will be used on the current page.
 *	This is only possible when uBillboard is loaded exclusively using
 *	shortcodes. Fuction checks if the uBillboard shortcode is present
 *	on the current page
 *
 *	@return bool
 */
function uds_billboard_is_active()
{
	if(uds_billboard_use_shortcode_optimization() && !is_admin()) {
		if(function_exists('uds_active_shortcodes')) {
			$active_shortcodes = uds_active_shortcodes();
			if( ! in_array('uds-billboard', $active_shortcodes)) {
				return false;
			}
		}
	}
	
	return true;
}

/**
 *	Function, detect if uBillboard will be used on the current page.
 *	And if the current page is the Preview.
 *
 *	@return bool
 */
function uds_billboard_is_preview()
{
	if(	( !isset($_GET['page']) || $_GET['page'] != 'uds_billboard_edit' ) ||
		( !isset($_GET['action']) || $_GET['action'] != 'preview') ) {
		return false;
	}
	
	return true;
}

/**
 *	Function, gets the "use compression" option value
 *
 *	@return bool
 */
function uds_billboard_use_compression()
{
	$option = get_option(UDS_BILLBOARD_OPTION_GENERAL, array('compression' => true));
	return $option['compression'];
}

/**
 *	Function, gets the "shortcode optimization" option value
 *
 *	@return bool
 */
function uds_billboard_use_shortcode_optimization()
{
	$option = get_option(UDS_BILLBOARD_OPTION_GENERAL, array('shortcode_optimization' => true));
	return $option['shortcode_optimization'];
}

if(!function_exists('is_ajax')) {
/**
 *	Is Ajax
 *	Simple tag that detects if the current request is an AJAX Call
 *
 *	@return bool True if current page has been requested via AJAX
 */
function is_ajax()
{
	return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest';
}
}

////////////////////////////////////////////////////////////////////////////////
//
//	WordPress Hooks
//
////////////////////////////////////////////////////////////////////////////////

add_action('admin_notices', 'uds_billboard_admin_notices');
/**
 *	Function, message handling, used with billboard edit message
 *	
 *	@return void
 */
function uds_billboard_admin_notices()
{
	if(!empty($_REQUEST['uds-message'])) {
		$message = $_REQUEST['uds-message'];
		$class = $_REQUEST['uds-class'];
		echo "<div id='message' class='$class'>$message</div>";
	}
}

if(uds_billboard_is_plugin()) {
	$plugin = plugin_basename( __FILE__ );
	//add_filter( 'plugin_action_links_' . $plugin, 'uds_billboard_plugin_action_links' );
	add_filter( 'plugin_row_meta', 'uds_billboard_plugin_row_meta', 10, 4);
}
/**
 *	Function, adds links to support to the uBillboard entry on the Plugins Installed
 *	page
 *	
 *	@param array $links
 *	@return array $links
 */
function uds_billboard_plugin_row_meta($plugin_meta, $plugin_file, $plugin_data, $status)
{
	if(uds_billboard_is_plugin()) {
		$plugin = plugin_basename( __FILE__ );
		if($plugin == $plugin_file) {
			$link = "<a href='http://codecanyon.net/user/uDesignStudios'>" .
					__('Get support', uds_billboard_textdomain) .
					'</a> <em>(' .
					__('Use the contact form towards the bottom of the page', uds_billboard_textdomain) .
					")</em>";

			$plugin_meta[] = $link;	
		}
	}
	return $plugin_meta;
}

// initialize billboard
add_action('admin_init', 'uds_billboard_admin_init');
/**
 *	Function, admin init hook
 *	
 *	@return void
 */
function uds_billboard_admin_init()
{
	global $uds_billboard_general_options, $uds_billboard_attributes;
	
	// Register settings
	register_setting('uds_billboard_general_options', UDS_BILLBOARD_OPTION_GENERAL, 'uds_billboard_general_validate');
	
	add_thickbox();
	
	// Basic init
	$dir = UDS_BILLBOARD_URL;
	
	$nonce = isset($_REQUEST['uds-billboard-update-nonce']) && wp_verify_nonce('uds-billboard-update-nonce', $_REQUEST['uds-billboard-update-nonce']);
	
	// process updates
	if(!empty($_POST['uds-billboard']) && !$nonce && !is_ajax()){
		die('Security check failed');
	} else {
		uds_billboard_process_updates();
	}
	
	// process deletes
	if(!empty($_REQUEST['uds-billboard-delete']) && wp_verify_nonce($_REQUEST['uds-billboard-delete-nonce'], 'uds-billboard-delete-nonce')) {
		uds_billboard_delete();
	}
	
	// process imports/exports
	if(isset($_GET['page']) && $_GET['page'] == 'uds_billboard_import_export') {
		if(isset($_GET['download_export']) && wp_verify_nonce($_GET['download_export'], 'uds-billboard-export')) {
			if(isset($_GET['uds-billboard-export'])) {
				uds_billboard_export($_GET['uds-billboard-export']);
			} else {
				uds_billboard_export();
			}
		}
		
		if(isset($_GET['uds-billboard-import-v2']) && wp_verify_nonce($_GET['uds-billboard-import-v2'], 'uds-billboard-import-v2')) {
			uds_billboard_import_v2();
		}
		
		if(isset($_FILES['uds-billboard-import']) && is_uploaded_file($_FILES['uds-billboard-import']['tmp_name'])) {
			uds_billboard_import($_FILES['uds-billboard-import']['tmp_name']);
		}
	}
	
	// Process Bulk Actions
	if(!empty($_REQUEST['uds-billboard-bulk-actions']) && wp_verify_nonce($_REQUEST['uds-billboard-bulk-actions'], 'uds-billboard-bulk-actions')) {
		if(isset($_POST['action']) && $_POST['action'] == 'export' && isset($_POST['billboards'])) {
			uds_billboard_export($_POST['billboards']);
		} elseif(isset($_POST['action']) && $_POST['action'] == 'delete' && isset($_POST['billboards'])) {
			uds_billboard_delete($_POST['billboards']);
		}
	}
	
	// Check cache
	if(!uds_billboard_cache_is_writable()) {
		add_action( 'admin_notices', create_function('', 'echo \'<div id="message" class="error"><p><strong>' . __("uBillboard Cache folder is not writable!", uds_billboard_textdomain) . '</strong></p></div>\';') );
	}
}

add_action('wp_print_scripts', 'uds_billboard_scripts');
/**
 *	Function, frontend scripts hook
 *	
 *	@return void
 */
function uds_billboard_scripts()
{
	global $wp_version;
	if((!uds_billboard_is_active() || is_admin()) && !uds_billboard_is_preview()) return;
	
	$dir = UDS_BILLBOARD_URL;
	
	// We need to override jQuery on older WP
	if(version_compare($wp_version, '3.0.0', '<')) {
		wp_deregister_script('jquery');
		wp_register_script('jquery', 'http://ajax.googleapis.com/ajax/libs/jquery/1.6.3/jquery.min.js');
	}
	
	if(uds_billboard_use_compression()){
		wp_enqueue_script("uds-billboard", $dir."js/billboard.min.js", array('jquery'), UDS_BILLBOARD_VERSION, true);
	} else {
		wp_enqueue_script("uds-billboard", $dir."js/billboard.js", array('jquery'), UDS_BILLBOARD_VERSION, true);
	}
}

add_action('wp_print_styles', 'uds_billboard_styles');
/**
 *	Function, frontend styles hook
 *	
 *	@return void
 */
function uds_billboard_styles()
{
	if((!uds_billboard_is_active() || is_admin()) && !uds_billboard_is_preview()) return;
	
	$dir = UDS_BILLBOARD_URL;
	if(uds_billboard_use_compression()) {
		wp_enqueue_style('uds-billboard', $dir.'css/billboard.min.css', false, UDS_BILLBOARD_VERSION, 'screen');
	} else {
		wp_enqueue_style('uds-billboard', $dir.'css/billboard.css', false, UDS_BILLBOARD_VERSION, 'screen');
	}
}

////////////////////////////////////////////////////////////////////////////////
//
//	Activation hooks
//
////////////////////////////////////////////////////////////////////////////////

register_activation_hook(__FILE__, 'uds_billboard_activation_hook');
register_uninstall_hook(__FILE__, 'uds_billboard_uninstall_hook');

/**
 *	Function, run on plugin activation, sets up the options with default values
 *	
 *	@return void
 */
function uds_billboard_activation_hook()
{
	$option = get_option(UDS_BILLBOARD_OPTION);
	if(!$option) {
		add_option(UDS_BILLBOARD_OPTION, array());
	}
	
	$option = get_option(UDS_BILLBOARD_OPTION_GENERAL);
	if(!$option) {
		add_option(UDS_BILLBOARD_OPTION_GENERAL, array(
			'compression' => true,
			'shortcode_optimization' => false
		));
	}
}

/**
 *	Function, run on plugin uninstall, removes all uBillboard options
 *	
 *	@return void
 */
function uds_billboard_uninstall_hook()
{
	delete_option(UDS_BILLBOARD_OPTION);
	delete_option(UDS_BILLBOARD_OPTION_GENERAL);
}

////////////////////////////////////////////////////////////////////////////////
//
//	Admin menus
//
////////////////////////////////////////////////////////////////////////////////

add_action('admin_menu', 'uds_billboard_menu');
/**
 *	Function, set up admin menu
 *	
 *	@return void
 */
function uds_billboard_menu()
{
	global $menu;
	$position = null;
	
	$icon = UDS_BILLBOARD_URL . 'images/menu-icon.png';
	$ubillboard = add_menu_page("uBillboard", "uBillboard", 'edit_pages', 'uds_billboard_admin', 'uds_billboard_admin', $icon, $position);
	
	$add_title = __("Add Billboard", uds_billboard_textdomain);
	$general_title = __("General Options", uds_billboard_textdomain);
	$import_title = __("Import/Export", uds_billboard_textdomain);
	
	$ubillboard_add = add_submenu_page('uds_billboard_admin', $add_title, $add_title, 'edit_pages', 'uds_billboard_edit', 'uds_billboard_edit');
	$ubillboard_general = add_submenu_page('uds_billboard_admin', $general_title, $general_title, 'manage_options', 'uds_billboard_general', 'uds_billboard_general');
	$ubillboard_importexport = add_submenu_page('uds_billboard_admin', $import_title, $import_title, 'import', 'uds_billboard_import_export', 'uds_billboard_import_export');
	
	add_action("admin_print_styles-$ubillboard", 'uds_billboard_enqueue_admin_styles');
	add_action("admin_print_styles-$ubillboard_add", 'uds_billboard_enqueue_admin_styles');
	add_action("admin_print_styles-$ubillboard_importexport", 'uds_billboard_enqueue_admin_styles');
	
	add_action("admin_print_scripts-$ubillboard", 'uds_billboard_enqueue_admin_scripts');
	add_action("admin_print_scripts-$ubillboard_add", 'uds_billboard_enqueue_admin_scripts');
	add_action("admin_print_scripts-$ubillboard_importexport", 'uds_billboard_enqueue_admin_scripts');
		
	// Contextual help
	if(class_exists('WP_Screen')) {
		WP_Screen::get($ubillboard)->add_help_tab(array(
			'title' => __('uBillboard Help'),
			'id' => 'uds-billboard-help',
			'content' => @file_get_contents(UDS_BILLBOARD_PATH . '/help/contextual-billboards.html'),
			'callback' => false
		));
		
		WP_Screen::get($ubillboard_add)->add_help_tab(array(
			'title' => __('uBillboard Editing Help'),
			'id' => 'uds-billboard-help',
			'content' => @file_get_contents(UDS_BILLBOARD_PATH . '/help/contextual-edit.html'),
			'callback' => false
		));
		
		WP_Screen::get($ubillboard_general)->add_help_tab(array(
			'title' => __('uBillboard General Options Help'),
			'id' => 'uds-billboard-help',
			'content' => @file_get_contents(UDS_BILLBOARD_PATH . '/help/contextual-general.html'),
			'callback' => false
		));
		
		WP_Screen::get($ubillboard_importexport)->add_help_tab(array(
			'title' => __('uBillboard Import/Export Help'),
			'id' => 'uds-billboard-help',
			'content' => @file_get_contents(UDS_BILLBOARD_PATH . '/help/contextual-import.html'),
			'callback' => false
		));
	} else {
		add_contextual_help($ubillboard, @file_get_contents(UDS_BILLBOARD_PATH . '/help/contextual-billboards.html'));
		add_contextual_help($ubillboard_add, @file_get_contents(UDS_BILLBOARD_PATH . '/help/contextual-edit.html'));
		add_contextual_help($ubillboard_general, @file_get_contents(UDS_BILLBOARD_PATH . '/help/contextual-general.html'));
		add_contextual_help($ubillboard_importexport, @file_get_contents(UDS_BILLBOARD_PATH . '/help/contextual-import.html'));
	}
}

/**
 *	Function, admin menu entry handler
 *	
 *	@return void
 */
function uds_billboard_admin()
{
	if(!current_user_can('edit_pages')) {
		wp_die(__('You do not have sufficient permissions to access this page', uds_billboard_textdomain));
	}
	
	include 'admin/billboard-list.php';
}

/**
 *	Function, admin menu entry handler
 *	
 *	@return void
 */
function uds_billboard_edit()
{
	if(!current_user_can('edit_pages')) {
		wp_die(__('You do not have sufficient permissions to access this page', uds_billboard_textdomain));
	}

	if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'preview') {
		include 'admin/billboard-preview.php';
	} else {
		include 'admin/billboard-edit.php';
	}
}

/**
 *	Function, admin menu entry handler
 *	
 *	@return void
 */
function uds_billboard_general()
{
	if(!current_user_can('manage_options')) {
		wp_die(__('You do not have sufficient permissions to access this page', uds_billboard_textdomain));
	}

	include 'admin/billboard-general.php';
}

/**
 *	Function, admin menu entry handler
 *	
 *	@return void
 */
function uds_billboard_import_export()
{
	global $uds_billboard_errors;
	
	if(!current_user_can('import')) {
		wp_die(__('You do not have sufficient permissions to access this page', uds_billboard_textdomain));
	}
	
	include 'admin/billboard-import-export.php';
}

/**
 *	Function, enqueues admin styles
 *	
 *	@return void
 */
function uds_billboard_enqueue_admin_styles()
{
	$dir = UDS_BILLBOARD_URL;
	wp_enqueue_style('jquery-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');
	wp_enqueue_style('uds-billboard', $dir.'css/billboard-admin.css', false, false, 'screen');
}

/**
 *	Function, enqueues admin scripts
 *	
 *	@return void
 */
function uds_billboard_enqueue_admin_scripts()
{
	$dir = UDS_BILLBOARD_URL;
	
	wp_enqueue_script("jquery-ui-core");
	wp_enqueue_script("jquery-ui-tabs");
	wp_enqueue_script("jquery-ui-dialog");
	wp_enqueue_script("jquery-ui-sortable");
	wp_enqueue_script("jquery-ui-resizable");
	wp_enqueue_script("jquery-ui-draggable");
	wp_enqueue_script("uds-colorpicker", $dir."js/colorpicker/jscolor.js", UDS_BILLBOARD_VERSION, true);
	wp_enqueue_script('uds-billboard', $dir."js/billboard-admin.js", array('jquery', 'jquery-ui-tabs'), UDS_BILLBOARD_VERSION, true);
	
	wp_localize_script('uds-billboard', 'udsAdminL10n', array(
		'bulkActionsDelete' => __('Really delete all selected sliders? This is not undoable', uds_billboard_textdomain),
		'billboardDeleteConfirmation' => __('Really delete? This is not undoable', uds_billboard_textdomain),
		'slideDeleteConfirmation' => __('Really delete slide?', uds_billboard_textdomain),
		'addAnImage' => __('Add an Image', uds_billboard_textdomain),
		'slideN' => __('Slide %s', uds_billboard_textdomain),
		'billboardPreview' => __('uBillboard Preview', uds_billboard_textdomain),
		'pageLeaveConfirmation' => __('uBillboard has unsaved changes, do you really want to leave?', uds_billboard_textdomain),
		'saveEmptyBillboard' => __('You are trying to save an empty uBillboard, please add background Image or Content', uds_billboard_textdomain)
	));
}

////////////////////////////////////////////////////////////////////////////////
//
//	Slide Add/Update logic
//
////////////////////////////////////////////////////////////////////////////////

add_action('wp_ajax_uds_billboard_update', 'uds_billboard_process_updates');
/**
 *	Function, check for POST data and update billboard accordingly
 *	will redirect if successful
 *	
 *	@return void
 */
function uds_billboard_process_updates()
{
	global $uds_billboard_attributes, $uds_billboard_general_options;

	$post = isset($_POST['uds_billboard']) ? $_POST['uds_billboard'] : array();

	if(empty($post) || !is_admin()) return;
	
	$billboard = new uBillboard();
	$billboard->update($post);

	if($billboard->isValid()){
		$message = '';
		if((int)$post['regenerate-thumbs'] != 0) {
			if(!$billboard->createThumbs()) {
				$message = 'uds-message='.urlencode(__('Failed to generate thumbnails', uds_billboard_textdomain)).'&uds-class='.urlencode('warning');
			}
		}
		
		$billboards = maybe_unserialize(get_option(UDS_BILLBOARD_OPTION, array()));	
		$billboards[$billboard->name] = $billboard;
	
		update_option(UDS_BILLBOARD_OPTION, maybe_serialize($billboards));
		if(empty($message)) {
			$message = 'uds-message='.urlencode(__('Billboard updated successfully', uds_billboard_textdomain)).'&uds-class='.urlencode('updated');
		}
		
		if(is_ajax()) {
			die('OK');
		}
	} else {
		$message = 'uds-message='.urlencode(__('Failed to update uBillboard', uds_billboard_textdomain)).'&uds-class='.urlencode('error');
	}
	
	if(is_ajax()) {
		die('ERROR');
	}
	
	wp_safe_redirect(admin_url('admin.php?page=uds_billboard_edit&uds-billboard-edit='.urlencode($billboard->name).'&'.$message));
	exit();
}

add_action('wp_ajax_uds_billboard_content_editor_help', 'uds_billboard_content_editor_help');
/**
 *	Function, check for POST data and update billboard accordingly
 *	will redirect if successful
 *	
 *	@return void
 */
function uds_billboard_content_editor_help()
{
	die(@file_get_contents(UDS_BILLBOARD_PATH . '/help/contextual-content-editor.html'));
}

/**
 *	Function, handle uBillboard deletion and redirect
 *	
 *	@return void
 */
function uds_billboard_delete($billboards_to_delete = false)
{
	if($billboards_to_delete === false) {
		$billboards_to_delete = array($_REQUEST['uds-billboard-delete']);
	}
	
	$billboards = maybe_unserialize(get_option(UDS_BILLBOARD_OPTION, array()));
	
	$message = 'uds-message=';
	$has_error = false;
	if(is_array($billboards_to_delete) && !empty($billboards_to_delete)) {
		foreach($billboards_to_delete as $billboard) {
			if(!isset($billboards[$billboard])) {
				$has_error = true;
				$message .= urlencode('<p>' . sprintf(__('Billboard %s does not exist', uds_billboard_textdomain), esc_html($billboard)) . '</p>');
			} else {
				unset($billboards[$billboard]);
				update_option(UDS_BILLBOARD_OPTION, maybe_serialize($billboards));
				$message .= urlencode('<p>' . sprintf(__('Billboard &quot;%s&quot; has been successfully deleted', uds_billboard_textdomain), esc_html($billboard)) . '</p>');
			}
		}
	}
	
	if($has_error) {
		$message .= '&uds-class='.urlencode('error');
	} else {
		$message .= '&uds-class='.urlencode('updated');
	}
	
	wp_safe_redirect(admin_url('admin.php?page=uds_billboard_admin&'.$message));
	exit();
}

add_action('wp_ajax_uds_billboard_list', 'uds_billboard_list');
/**
 *	Function, AJAX list billboards, used for shortcode uBillboard entry
 *	
 *	@return void
 */
function uds_billboard_list()
{
	$billboards = maybe_unserialize(get_option(UDS_BILLBOARD_OPTION));
	
	foreach($billboards as $name => $billboard) {
		if($name == '_uds_temp_billboard') continue;
		
		echo '<option name="'.$name.'">'.$name.'</option>';
	}
	
	die();
}

/**
 *	Validation function for General Options, plugs in the settings api
 *	
 *	@return
 */
function uds_billboard_general_validate($input)
{
	$input['compression'] = isset($input['compression']) && in_array($input['compression'], array('', 'on')) ? true : false;
	$input['shortcode_optimization'] = isset($input['shortcode_optimization']) && in_array($input['shortcode_optimization'], array('', 'on')) ? true : false;

	return $input;
}

////////////////////////////////////////////////////////////////////////////////
//
//	Frontend rendering functions
//
////////////////////////////////////////////////////////////////////////////////

add_action('wp_footer', 'uds_billboard_footer_scripts');
/**
 *	Function, renders footer scripts, creates uBillboards
 *	
 *	@return void
 */
function uds_billboard_footer_scripts()
{
	global $uds_billboard_footer_scripts;
	
	if(empty($uds_billboard_footer_scripts)) return;
	
	echo "
	<script type='text/javascript'>
		//<![CDATA[
		jQuery(document).ready(function($){
			$uds_billboard_footer_scripts
		});
		//]]>
	</script>";
}

/**
 *	Function, create uBillboard markup
 *
 *	@param (optional) string $name of the uBillboard to redner, defaults to 'billboard'
 *	@param (optional) array $options, currently unused
 *	
 *	@return string rendered billboard
 */
function get_uds_billboard($name = 'billboard', $options = array())
{
	global $uds_billboard_footer_scripts;
	static $id = 0;
	
	$bbs = maybe_unserialize(get_option(UDS_BILLBOARD_OPTION, array()));
	
	if(!isset($bbs[$name])) {
		return sprintf(__("Billboard named &quot;%s&quot; does not exist", uds_billboard_textdomain), $name);
	}
	
	$bb = $bbs[$name];
	
	if(!$bb->isValid()) {
		return __("Billboard is invalid", uds_billboard_textdomain);
	}
	
	$bb = apply_filters('uds_billboard_render_before', $bb);
	
	if(!$bb->isValid()) {
		return __("Billboard is invalid", uds_billboard_textdomain);
	}
	
	$out = $bb->render($id);
	
	$uds_billboard_footer_scripts .= $bb->renderJS($id);
	
	$id++;
	
	return $out;
}

/**
 *	Function, proxy to get_uds_billboard(), echoes the output
 *	
 *	@return void
 */
function the_uds_billboard($name = 'billboard')
{
	echo get_uds_billboard($name);
}

?>