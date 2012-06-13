<?php
/*
Plugin Name: Print Friendly and PDF
Plugin URI: http://www.printfriendly.com
Description: PrintFriendly & PDF optimizes your pages for print. Help your readers save paper and ink, plus enjoy your content in printed form. Website
Name and URL are included to ensure repeat visitors and new visitors when printed versions are shared.  
Version: 3.0.8
Author: Print Friendly
Author URI: http://www.PrintFriendly.com

Changelog :
3.0.8 - Reordered PrintFriendly & PDF buttons. CSS stylesheet option is now checked by default.
3.0.7 - Added additional images for print button.
3.0.6 - Fix bug that would display button on category pages when not wanted.
3.0.5 - Include button on category pages if user has selected "All pages".
3.0.4 - Align-right and align-center support for themes that remove WordPress core css.
3.0.3 - Support for bad themes that alter template tags and prevent JavaScript from loading in footer.
3.0.2 - Fixed JS bug with Google Chrome not submitting and fixed input validation issues.
3.0.1 - Fixed minor JS bug.
3.0 - Complete overhaul of the plugin by Joost de Valk.
2.1.8 - The Print Button was showing up on printed, or PDF, pages. Junk! Print or PDF button no longer displayed on printed out page or PDF. 
2.1.7 - Changed button from span to div to support floating.
2.1.6 - Added rel="nofollow" to links. Changed button from <a> to <span> to fix target_new or target_blank issues.
2.1.5 - Fix conflict with link tracking plugins. Custom image support for hosted wordpress sites.
2.1.4 - wp head fix.
2.1.3 - Manual option for button placement. Security updates for multi-author sites.
2.1.2 - Improvements to Setting page layout and PrintFriendly button launching from post pages.
2.1.1 - Fixed admin settings bug.
2.1 - Update for mult-author websites. Improvements to Settings page.
2.0 - Customize the style, placement, and pages your printfriendly button appears.
1.5 - Added developer ability to disable hook and use the pf_show_link() function to better be used in a custom theme & Uninstall cleanup.
1.4 - Changed Name.
1.3 - Added new buttons, removed redundant code.
1.2 - User can choose to show or not show buttons on the listing page.
*/
  
/**
 * PrintFriendly WordPress plugin. Allows easy embedding of printfriendly.com buttons.
 * @package PrintFriendly_WordPress
 * @author Joost de Valk <joost@yoast.com>
 * @copyright Copyright (C) 2011, PrintFriendly
 */
if ( ! class_exists( 'PrintFriendly_WordPress' ) ) {

	/**
	 * Class containing all the plugins functionality.
	 * @package PrintFriendly_WordPress
	 */
	class PrintFriendly_WordPress {
			/**
			 * The hook, used for text domain as well as hooks on pages and in get requests for admin.
			 * @var string
			 */
			var $hook 			= 'printfriendly';

			/**
			 * The option name, used throughout to refer to the plugins option and option group.
			 * @var string
			 */
			var $option_name	= 'printfriendly_option';
			
			/**
			 * The plugins options, loaded on init containing all the plugins settings.
			 * @var array
			 */
			var $options		= array();
			
			/**
			 * Database version, used to allow for easy upgrades to / additions in plugin options between plugin versions.
			 * @var int
			 */			
			var $db_version		= 1;
			
			/**
			 * Settings page, used within the plugin to reliably load the plugins admin JS and CSS files only on the admin page.
			 * @var string
			 */			
			var $settings_page	= '';
			
			/**
			 * Constructor
			 *
			 * @since 3.0
			 */
			function __construct() {
				// delete_option( $this->option_name );

				// Retrieve the plugin options
				$this->options = get_option( $this->option_name );
				
				// If the options array is empty, set defaults
				if ( ! is_array( $this->options ) )
					$this->set_defaults();
					
				// If the version number doesn't match, upgrade
				if ( $this->db_version > $this->options['db_version'] )
					$this->upgrade();

				// automaticaly add the link
				if( 'manual' != $this->options['show_list'] ) {
					add_action( 'wp_head',		 	array( &$this, 'front_head' ) );
					add_filter( 'the_content', 		array( &$this, 'show_link' ) );
					add_filter( 'the_excerpt', 		array( &$this, 'show_link' ) );
				}
				
				if ( !is_admin() )
					return;

				// Hook into init for registration of the option and the language files
				add_action(	'admin_init', 			array( &$this, 'init' ) );

				// Register the settings page	
				add_action( 'admin_menu', 			array( &$this, 'add_config_page' ) );

				// Register the contextual help
				add_filter( 'contextual_help', 		array( &$this, 'contextual_help' ), 10, 2 );
				
				// Enqueue the needed scripts and styles
				add_action( 'admin_enqueue_scripts',array( &$this, 'admin_enqueue_scripts' ) );

				// Register a link to the settings page on the plugins overview page
				add_filter( 'plugin_action_links', 	array( &$this, 'filter_plugin_actions' ), 10, 2 );
			}
			
			/**
			 * PHP 4 Compatible Constructor
			 *
			 * @since 3.0
			 */
			function PrintFriendly_WordPress() {
				$this->__construct();
			}
			
			/**
			 * Prints the PrintFriendly button CSS, in the header. Possible to disable this in the plugin settings.
			 *
			 * @since 3.0
			 */
			function front_head() {
				
				if ( !isset( $this->options['disable_css'] ) || $this->options['disable_css'] != 'on' )
					return;
					
				?>
				<style type="text/css" media="screen">
					.printfriendly {
						margin: <?php echo $this->options['margin_top'].'px '.$this->options['margin_right'].'px '.$this->options['margin_bottom'].'px '.$this->options['margin_left'].'px'; ?>;
					}
					.printfriendly a {
						text-decoration: none;
					}
					.printfriendly a:hover {
						cursor: pointer;
					}
					.printfriendly .printfriendly-text {
						margin-left: 3px;
						color: <?php echo $this->options['text_color']; ?>;
					}
					.printfriendly a img {
						border:none; 
						padding:0;
					}
					.alignleft {
					    float:left;
					    margin: 5px 20px 20px 0;
					}
					.alignright {
					    float:right;
					    margin: 5px 0 20px 20px;
					}
					.aligncenter {
						text-align: center;
						margin: 5px auto 5px auto;
					}
				</style>
				<style type="text/css" media="print">
					.printfriendly {
						display: none;
					}
				</style>
				<?php
			}
			
			/**
			 * Prints the PrintFriendly JavaScript, in the footer, and loads it asynchronously.
			 *
			 * @since 3.0
			 */
			function print_script_footer() {
				if ( !isset( $this->options['javascript_include'] ) || $this->options['javascript_include'] != 'on' )
					return;
					
				else
				?>
	<script type="text/javascript">
		// PrintFriendly
		var e = document.createElement('script'); e.type="text/javascript"; e.async = true; 
		e.src = '//cdn.printfriendly.com/printfriendly.js';
		document.getElementsByTagName('head')[0].appendChild(e);
	</script>
				<?php 
				
			}
			
			/**
			 * Primary frontend function, used either as a filter for the_content, or directly using pf_show_link
			 *
			 * @since 3.0
			 * @param string $content the content of the post, when the function is used as a filter
			 * @return string $button or $content with the button added to the content when appropriate, just the content when button shouldn't be added or just button when called manually.
			 */
			function show_link( $content = false ) {
				if( !$content && 'manual' != $this->options['show_list'] )
					return "";

				$href 		= '#';
				$onclick 	= 'onclick="window.print(); return false;"';
				
				if ( !isset( $this->options['javascript_include'] ) || $this->options['javascript_include'] != 'on' || !isset( $this->options['javascript_fallback'] ) || $this->options['javascript_fallback'] )
					$href = 'http://www.printfriendly.com/print/v2?url='.get_permalink();

				if ( !isset( $this->options['javascript_include'] ) || $this->options['javascript_include'] != 'on' )
					$onclick = '';

				if ( !is_singular() && '' != $onclick )  {
					$onclick = '';
					$href = get_permalink().'?pfstyle=wp';
				}
			
				$align = '';
				if ( 'none' != $this->options['content_position'] )
					$align = ' align'.$this->options['content_position'];
				
				$button = apply_filters( 'printfriendly_button', '<div class="printfriendly'.$align.'"><a href="'.$href.'" rel="nofollow" '.$onclick.'>'.$this->button().'</a></div>' );
				
				if (is_singular())
				{
					// Hook the script call now, so it only get's loaded when needed, and need is determined by the user calling pf_button
					add_action( 'wp_footer', 	array( &$this, 'print_script_footer' ) );
				}
				
				if ( 'manual' == $this->options['show_list'] )
				{
					return $button;
				}				
				
				else 
				{
					if (is_single() || ( is_page() && 'posts' != $this->options['show_list'] ) || ((is_home() || is_category())  && 'all' == $this->options['show_list'] ))
					{
					
						if ( $this->options['content_placement'] == 'before' )
							return $button.$content;
						else 
							return $content.$button;
					}
				
					else 
					{
						return $content;
					}
					
				}
				
			}
			
			/**
			 * Register the textdomain and the options array along with the validation function
			 *
			 * @since 3.0
			 */
			function init() {
				// Allow for localization
				load_plugin_textdomain( $this->hook, false, basename( dirname( __FILE__ ) ) . '/languages' );

				// Register our option array
				register_setting( $this->option_name, $this->option_name, array( &$this, 'options_validate' ) );
			}
			
			/**
			 * Validate the saved options.
			 *
			 * @since 3.0
			 * @param array $input with unvalidated options.
			 * @return array $valid_input with validated options.
			 */
			function options_validate( $input ) {
				$valid_input = $input;

				// echo '<pre>'.print_r($input,1).'</pre>';
				// die;
				
				if ( !in_array( $input['button_type'], array( 'pf-button.gif', 'button-print-grnw20.png',  'button-print-blu20.png',  'button-print-gry20.png',  'button-print-whgn20.png',  'pf_button_sq_qry_m.png',  'pf_button_sq_qry_l.png',  'pf_button_sq_grn_m.png',  'pf_button_sq_grn_l.png', 'pf-button-big.gif', 'pf-icon-small.gif', 'pf-icon.gif', 'pf-button-both.gif', 'pf-icon-both.gif', 'text-only', 'custom-image') ) )
					$valid_input['button_type'] = 'pf-button.gif';

				if ( !isset( $input['custom_image'] ) )
					$valid_input['custom_image'] = '';

				if ( !in_array( $input['show_list'], array( 'all', 'single', 'posts', 'manual') ) )
					$valid_input['show_list'] = 'all';

				if ( !in_array( $input['content_position'], array( 'none', 'left', 'center', 'right' ) ) )
					$valid_input['content_position'] = 'left';

				if ( !in_array( $input['content_placement'], array( 'before', 'after' ) ) )
					$valid_input['content_placement'] = 'after';

				foreach ( array( 'margin_top', 'margin_right', 'margin_bottom', 'margin_left' ) as $opt )
					$valid_input[$opt] = (int) $input[$opt];
				
				$valid_input['text_size'] = (int) $input['text_size'];
				
				if ( !isset($valid_input['text_size']) || 0 == $valid_input['text_size'] ) {
					$valid_input['text_size'] = 14;
				} else if ( 25 < $valid_input['text_size'] || 9 > $valid_input['text_size'] ) {
					$valid_input['text_size'] = 14;
					add_settings_error( $this->option_name, 'invalid_color', __( 'The text size you entered is too high, please stay below 25px.', $this->hook ) );
				}
				
				if ( !isset( $input['text_color'] )) {
					$valid_input['text_color'] = $this->options['text_color'];
				} else if ( ! preg_match('/^#[a-f0-9]{3,6}$/i', $input['text_color'] ) ) {
					// Revert to previous setting and throw error.
					$valid_input['text_color'] = $this->options['text_color'];
					add_settings_error( $this->option_name, 'invalid_color', __( 'The color you entered is not valid, it must be a valid hexadecimal RGB font color.', $this->hook ) );
				}
				
				$valid_input['db_version'] = $this->db_version;
				
				return $valid_input;
			}
			
			/**
			 * Register the config page for all users that have the manage_options capability
			 *
			 * @since 3.0
			 */
			function add_config_page() {
				$this->settings_page = add_options_page( __( 'PrintFriendly Options', $this->hook ), __( 'Print Friendly & PDF', $this->hook ), 'manage_options', $this->hook, array( &$this, 'config_page' ) );
			}
			
			/**
			 * Shows help on the plugin page when clicking on the Help button, top right.
			 *
			 * @since 3.0
			 */
			function contextual_help( $contextual_help, $screen_id ) {
				if ( $this->settings_page == $screen_id ) {
					$contextual_help = '<strong>'.__( "Need Help?", $this->hook ).'</strong><br/>'
										.sprintf( __( "Be sure to check out the %s!"), '<a href="http://wordpress.org/extend/plugins/printfriendly/faq/">'.__( "Frequently Asked Questions", $this->hook ).'</a>' );
				}
				return $contextual_help;
			}
			
			/**
			 * Enqueue the scripts for the admin settings page
			 *
			 * @since 3.0
			 * @param string $hook_suffix hook to check against whether the current page is the PrintFriendly settings page.
			 */
			function admin_enqueue_scripts( $screen_id ) {
				if ( $this->settings_page == $screen_id ) {
					wp_register_script( 'pf-color-picker', plugins_url( 'colorpicker.js', __FILE__ ), array( 'jquery', 'media-upload' ) );
					wp_register_script( 'pf-admin-js', plugins_url( 'admin.js', __FILE__ ), array( 'jquery', 'media-upload' ) );

					wp_enqueue_script( 'pf-color-picker' );
					wp_enqueue_script( 'pf-admin-js' );
					
					wp_enqueue_style( 'printfriendly-admin-css', plugins_url( 'admin.css', __FILE__ ) );
				}
			}
			
			/**
			 * Register the settings link for the plugins page
			 *
			 * @since 3.0
			 * @param array $links the links for the plugins.
			 * @param string $file filename to check against plugins filename.
			 * @return array $links the links with the settings link added to it if appropriate.
			 */
			function filter_plugin_actions( $links, $file ){
				// Static so we don't call plugin_basename on every plugin row.
				static $this_plugin;
				if ( ! $this_plugin ) $this_plugin = plugin_basename( __FILE__ );

				if ( $file == $this_plugin ){
					$settings_link = '<a href="options-general.php?page='.$this->hook.'">' . __( 'Settings', $this->hook ) . '</a>';
					array_unshift( $links, $settings_link ); // before other links
				}
				return $links;
			}
			
			/**
			 * Set default values for the plugin. If old, as in pre 1.0, settings are there, use them and then delete them.
			 *
			 * @since 3.0
			 */
			function set_defaults() {
				// Set some defaults
				$this->options = array(
					'button_type'			=> 'pf-button.gif',
					'content_position'		=> 'left',
					'content_placement'		=> 'after',
					'custom_image'			=> '',
					'custom_text'			=> 'Print Friendly',
					'disable_css'			=> 'on',
					'javascript_include'	=> 'on',
					'javascript_fallback'	=> 'on',
					'margin_top' 			=> 0,
					'margin_right'			=> 0,
					'margin_bottom' 		=> 0,
					'margin_left'			=> 0,
					'show_list'				=> 'all',
					'text_color'			=> '#55750C',
					'text_size'				=> 14,
				);

				// Check whether the old badly named singular options are there, if so, use the data and delete them.
				foreach ( array_keys( $this->options ) as $opt ) {
					$old_opt = get_option( 'pf_'.$opt );
					if ( $old_opt !== false ) {
						$this->options[$opt] = $old_opt;
						delete_option( 'pf_'.$opt );
					}
				}

				// This should always be set to the latest immediately when defaults are pushed in.
				$this->options['db_version'] = $this->db_version;
				
				update_option( $this->option_name, $this->options );
			}
			
			/**
			 * Upgrades the stored options, used to add new defaults if needed etc.
			 *
			 * @since 3.0
			 */
			function upgrade() {
				// Do stuff
				
				$this->options['db_version'] = $this->db_version;
				update_option( $this->option_name, $this->options );
			}
			
			/**
			 * Displays radio button in the admin area
			 *
			 * @since 3.0
			 * @param string $name the name of the radio button to generate.
			 * @param boolean $br whether or not to add an HTML <br> tag, defaults to true.
			 */
			function radio($name, $br = true){
				$var = '<input id="'.$name.'" class="radio" name="'.$this->option_name.'[button_type]" type="radio" value="'.$name.'" '.$this->checked( 'button_type', $name, false ).'/>';
				$button = $this->button( $name );
				if ( '' != $button )
					echo '<label for="'.$name.'">' . $var . $button . '</label>';
				else
					echo $var;
					
				if ( $br )
					echo '<br>';
			}
			
			/**
			 * Displays button image in the admin area
			 *
			 * @since 3.0
			 * @param string $name the name of the button to generate.
			 */
			function button( $name = false ){
				if( !$name )
					$name = $this->options['button_type'];

				$text = $this->options['custom_text'];

				switch($name){		
					case "custom-image":
						if( '' == $this->options['custom_image'] )
							return '';

						$return = '<img src="'.$this->options['custom_image'].'" alt="Print Friendly" />';

						if( $this->options['custom_text'] != '' )
							$return .= '<span class="printfriendly-text">'.$this->options['custom_text'].'</span>';

						return $return;
					break;

					case "text-only":
						return '<span class="printfriendly-text">'.$text.'</span>';
					break;

					case "pf-icon-both.gif":
						return '<img src="//cdn.printfriendly.com/pf-print-icon.gif" alt="Print Friendly"/><span class="printandpdf printfriendly-text"> Print <img src="//cdn.printfriendly.com/pf-pdf-icon.gif" alt="Get a PDF version of this webpage" /> PDF </span>';
					break;

					case "pf-icon-small.gif":
					case "pf-icon.gif":
						return '<img src="//cdn.printfriendly.com/'.$name.'" alt="Print Friendly"/><span class="printfriendly-text">'.$text.'</span>';
					break;

					default:
						return '<img src="//cdn.printfriendly.com/'.$name.'" alt="Print Friendly" />';
					break;
				}
			}
			
			/**
			 * Convenience function to output a value for an input
			 *
			 * @since 3.0
			 * @param string $val value to check.
			 */
			function val( $val ) {
				if ( isset( $this->options[$val] ) )
					echo esc_attr( $this->options[$val] );
			}
			
			/**
			 * Like the WordPress checked() function but it doesn't throw notices when the array key isn't set and uses the plugins options array.
			 *
			 * @since 3.0
			 * @param mixed $val value to check.
			 * @param mixed $check_against value to check against.
			 * @param boolean $echo whether or not to echo the output.
			 * @return string checked, when true, empty, when false.
			 */
			function checked( $val, $check_against = true, $echo = true ) {
				if ( !isset( $this->options[$val] ) )
					return;
				
				if ( $this->options[$val] == $check_against ) {
					if ( $echo )
						echo ' checked="checked" ';
					else
						return ' checked="checked" ';
				}
			}
			
			/**
			 * Output the config page
			 *
			 * @since 3.0
			 */
			function config_page() {

				// Since WP 3.2 outputs these errors by default, only display them when we're on versions older than 3.2 that do support the settings errors.
				global $wp_version;
				if ( version_compare( $wp_version, '3.2', '<' ) )
					settings_errors();

				// Show the content of the options array when debug is enabled
				if ( WP_DEBUG )
					echo '<pre>Options:<br><br>' . print_r( $this->options, 1 ) . '</pre>';
			?>
				<div id="pf_settings" class="wrap">
					<div class="icon32" id="printfriendly"></div>
				    <h2><?php _e( 'Print Friendly & PDF Settings', $this->hook ); ?></h2>

					<form action="options.php" method="post">
						<?php settings_fields( $this->option_name ); ?>

						<table class="form-table">
						<tr>
							<th colspan="2">
								<h3><?php _e( "Pick a Button and/or Text", $this->hook ); ?></h3>
							</th>
						</tr>
						<tr>
							<th scope="row"><?php _e( "Pick Your Button Style", $this->hook ); ?></th>
							<td class="defaultavatarpicker">
								<fieldset>
					            	<?php $this->radio('pf-button.gif'); ?>
                                    <?php $this->radio('pf-button-both.gif'); ?>
					                <?php $this->radio('pf-button-big.gif'); ?>
                                    <?php $this->radio('button-print-grnw20.png'); ?>
					            	<?php $this->radio('button-print-blu20.png'); ?>
					            	<?php $this->radio('button-print-gry20.png'); ?>
					            	<?php $this->radio('button-print-whgn20.png'); ?>
					            	<?php $this->radio('pf_button_sq_gry_m.png'); ?>
					            	<?php $this->radio('pf_button_sq_gry_l.png'); ?>
					            	<?php $this->radio('pf_button_sq_grn_m.png'); ?>
					            	<?php $this->radio('pf_button_sq_grn_l.png'); ?>
									<span class="button_preview">
										<?php $this->radio('pf-icon-small.gif'); ?>
										<?php $this->radio('pf-icon-both.gif'); ?>
										<?php $this->radio('pf-icon.gif'); ?>
										<?php $this->radio('text-only'); ?>
										<?php $this->radio('custom-image', false); ?>
									</span>
									<label for="custom-image"><?php _e( "Custom Image URL", $this->hook ); ?></label>
			                    	<input id="custom_image" type="text" class="regular-text" size="40" name="<?php echo $this->option_name; ?>[custom_image]" value="<?php $this->val( 'custom_image' ); ?>" /><br>
									<span class="description"><?php _e( ".JPG .GIF or .PNG Absolute (http://www.example.com/...), or Relative (/wp/wp-content/uploads/example.png)", $this->hook ); ?></span>
									<br>
								</fieldset>
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="custom_text"><?php _e( "Button Text", $this->hook ); ?></label></th>
							<td><input type="text" class="text" name="<?php echo $this->option_name; ?>[custom_text]" id="custom_text" value="<?php $this->val( 'custom_text' ); ?>"></td>
						</tr>
						<tr class="css">
							<th colspan="2">
								<h3><?php _e( "Style the Button and Text", $this->hook ); ?></h3>
							</th>
						</tr>
						<tr class="css">
							<th scope="row"><?php _e( "Text Color", $this->hook ); ?></th>
							<td>
								<div id="colorSelector"><div style="background-color: <?php echo $this->options['text_color']; ?>;"></div></div>
								<input type="hidden" name="<?php echo $this->option_name; ?>[text_color]" id="text_color" value="<?php $this->val( 'text_color' ); ?>"/><br>
								<span class="description"><?php _e( "Use the color picker, or enter a valid hex color in the color picker input box.", $this->hook ); ?></span>
							</td>
						</tr>
						<tr class="css">
							<th scope="row"<label for="text_size"><?php _e( "Text Size", $this->hook ); ?></label></th>
							<td>
								<input type="number" id="text_size" min="9" max="25" class="small-text" name="<?php echo $this->option_name; ?>[text_size]" value="<?php $this->val( 'text_size' ); ?>"/>
								<span class="description"><?php _e( "In pixels (px)", $this->hook ); ?></span>
							</td>
						</tr>
						<tr class="css">
							<th scope="row"><?php _e( "Margin", $this->hook ); ?></th>
							<td>
								<label><input class="small-text" type="number" name="<?php echo $this->option_name; ?>[margin_left]" value="<?php $this->val( 'margin_left' ); ?>" maxlength="3"/> <?php _e( "Left", $this->hook ); ?></label> &nbsp;&nbsp;&nbsp; <span class="description"><?php _e( "In pixels (px)", $this->hook ); ?></span><br>
								<label><input class="small-text" type="number" name="<?php echo $this->option_name; ?>[margin_right]" value="<?php $this->val( 'margin_right' ); ?>"/> <?php _e( "Right", $this->hook ); ?></label><br>
								<label><input class="small-text" type="number" name="<?php echo $this->option_name; ?>[margin_top]"  value="<?php $this->val( 'margin_top' ); ?>" maxlength="3"/> <?php _e( "Top", $this->hook ); ?></label><br>
								<label><input class="small-text" type="number" name="<?php echo $this->option_name; ?>[margin_bottom]" value="<?php $this->val( 'margin_bottom' ); ?>" maxlength="3"/> <?php _e( "Bottom", $this->hook ); ?></label><br>
							</td>
						</tr>
						<tr>
							<th colspan="2">
								<h3><?php _e( "Button Placement", $this->hook ); ?></h3>
							</th>
						</tr>
						<tr>
							<th scope="row"><?php _e( "Horizontal Alignment", $this->hook ); ?></th>
							<td>
								<label><input type="radio" name="<?php echo $this->option_name; ?>[content_position]" value="none" <?php $this->checked( 'content_position', 'none'); ?>/> <?php _e( "None", $this->hook ); ?></label><br>
								<label><input type="radio" name="<?php echo $this->option_name; ?>[content_position]" value="left" <?php $this->checked( 'content_position', 'left'); ?>/> <?php _e( "Left", $this->hook ); ?></label><br>
								<label><input type="radio" name="<?php echo $this->option_name; ?>[content_position]" value="right" <?php $this->checked( 'content_position', 'right') ?>/> <?php _e( "Right", $this->hook ); ?></label><br>
								<label><input type="radio" name="<?php echo $this->option_name; ?>[content_position]" value="center" <?php $this->checked( 'content_position', 'center') ?>/> <?php _e( "Center", $this->hook ); ?></label><br>
							</td>
						</tr>
						<tr class="content_placement">
							<th scope="row"><?php _e( "Vertical Position", $this->hook ); ?></th>
							<td>
								<label><input type="radio" name="<?php echo $this->option_name; ?>[content_placement]" value="before" <?php $this->checked( 'content_placement', 'before'); ?> <?php if( $this->options['show_list']=='manual'){ echo 'disabled="disabled"'; } ?>/> <?php _e( "Before Content", $this->hook ); ?></label><br>
								<label><input type="radio" name="<?php echo $this->option_name; ?>[content_placement]" value="after" <?php $this->checked( 'content_placement', 'after'); ?> <?php if( $this->options['show_list']=='manual'){ echo 'disabled="disabled"'; } ?>/> <?php _e( "After Content", $this->hook ); ?></label><br>
							</td>
						</tr>
						<tr>
							<th scope="row"><?php _e( "Add PrintFriendly To", $this->hook ); ?></th>
							<td>
								<label><input type="radio" class="show_list" name="<?php echo $this->option_name; ?>[show_list]" value="all" <?php $this->checked( 'show_list', 'all'); ?>/> <?php _e( "Homepage, Archives, Posts, and Pages", $this->hook ); ?></label><br>
				    			<label><input type="radio" class="show_list" name="<?php echo $this->option_name; ?>[show_list]" value="single" <?php $this->checked( 'show_list', 'single'); ?>/> <?php _e( "Posts and Pages", $this->hook ); ?></label><br>
				    			<label><input type="radio" class="show_list" name="<?php echo $this->option_name; ?>[show_list]" value="posts" <?php $this->checked( 'show_list', 'posts'); ?>/> <?php _e( "Posts", $this->hook ); ?></label><br>
			    				<label><input type="radio" class="show_list" name="<?php echo $this->option_name; ?>[show_list]" value="manual" <?php $this->checked( 'show_list', 'manual'); ?> /> <?php _e( "Manual", $this->hook ); ?></label><br>
			      				<div id="addmanual-help">
			      					<p><?php _e( "Copy and paste the code below anywhere in your template pages.", $this->hook ); ?></p>
									<code style="display:block;">&lt;?php if(function_exists('pf_show_link')){echo pf_show_link();} ?&gt;</code>			
				  				</div>
							</td>
						</tr>
						<tr>
							<th colspan="2">
								<h3><?php _e( "Other Settings", $this->hook ); ?></h3>
							</th>
						</tr>
						<tr>
							<th scope="row"><?php _e( "CSS Stylesheet", $this->hook ); ?></th>
							<td>
								<label><input type="checkbox" id="disable_css" name="<?php echo $this->option_name; ?>[disable_css]" <?php $this->checked( 'disable_css', 'on' ); ?>/> <?php _e( "Add CSS to pages", $this->hook ); ?></label><br/>
								<p class="description">
									<?php _e( "If you uncheck this box you have to add the styling for the PrintFriendly button to your theme's stylesheet.", $this->hook ); ?>
								</p>
							</td>
						</tr>
						<tr>
							<th scope="row"><?php _e( "JavaScript", $this->hook ); ?></th>
							<td>
								<label><input type="checkbox" name="<?php echo $this->option_name; ?>[javascript_fallback]" <?php $this->checked( 'javascript_fallback', 'on' ); ?>/> <?php _e( "JavaScript Fallback", $this->hook ); ?></label><br/>
								<p class="description">
									<?php _e( "If you uncheck this box users without JavaScript will be unable to use the PrintFriendly service.", $this->hook ); ?>
								</p>
								<label><input type="checkbox" name="<?php echo $this->option_name; ?>[javascript_include]" <?php $this->checked( 'javascript_include', 'on' ); ?>/> <?php _e( "Include JavaScript", $this->hook ); ?></label><br/>
								<p class="description">
									<?php _e( "If you uncheck this box, all buttons will become links to the PrintFriendly webservice and the PrintFriendly JavaScript will not be loaded on your site.", $this->hook ); ?>
								</p>
							</td>
						</tr>
						</table>

						<p class="submit">
							<input type="submit" class="button-primary" value="<?php esc_attr_e( "Save Options", $this->hook ); ?>"/>
							<input type="reset" class="button-secondary" value="<?php esc_attr_e( "Cancel", $this->hook ); ?>"/>
						</p>
				        <p>
							<?php _e( "Like PrintFriendly?", $this->hook ); ?> <a href="http://wordpress.org/extend/plugins/printfriendly/"><?php _e( "Give us a rating", $this->hook ); ?></a>. <?php _e( "Need help or have suggestions?", $this->hook ); ?> <a href="mailto:support@printfriendly.com?subject=Support%20for%20PrintFriendly%20WordPress%20plugin">support@PrintFriendly.com</a>.
						</p>
					</form>
				</div>
			<?php
			}
	}
	$printfriendly = new PrintFriendly_WordPress();
}

/**
 * Convenience function for use in templates.
 *
 * @since 3.0
 * @return string returns a button to be printed.
 */
function pf_show_link() {
	global $printfriendly;
	return $printfriendly->show_link();
}
