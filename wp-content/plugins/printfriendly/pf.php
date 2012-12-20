<?php
/*
Plugin Name: Print Friendly and PDF
Plugin URI: http://www.printfriendly.com
Description: PrintFriendly & PDF button for your website. Optimizes your pages and brand for print, pdf, and email.
Name and URL are included to ensure repeat visitors and new visitors when printed versions are shared.
Version: 3.1.4
Author: Print Friendly
Author URI: http://www.PrintFriendly.com

Changelog :
3.1.4 - Changed https url. Don't hide text change box when disabling css.
3.1.3 - Fixed bug with disable css option
3.1.2 - Added disable css option to admin settings.
3.1.1 - Fixed admin js caching.
3.1.0 - Fixed admin css caching.
3.0.9 - New features: Custom header, disable click-to-delete, https support (beta), PrintFriendly Pro (ad-free).
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
 * @author PrintFriendly <support@printfriendly.com>
 * @copyright Copyright (C) 2012, PrintFriendly
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
    var $hook = 'printfriendly';

    /**
     * The option name, used throughout to refer to the plugins option and option group.
     * @var string
     */
    var $option_name = 'printfriendly_option';

    /**
     * The plugins options, loaded on init containing all the plugins settings.
     * @var array
     */
    var $options = array();

    /**
     * Database version, used to allow for easy upgrades to / additions in plugin options between plugin versions.
     * @var int
     */
    var $db_version = 2;

    /**
     * Settings page, used within the plugin to reliably load the plugins admin JS and CSS files only on the admin page.
     * @var string
     */
    var $settings_page = '';

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

      add_action( 'wp_head', array( &$this, 'front_head' ) );
      // automaticaly add the link
      if( 'manual' != $this->options['show_list'] ) {
        add_filter( 'the_content', array( &$this, 'show_link' ) );
        add_filter( 'the_excerpt', array( &$this, 'show_link' ) );
      }

      if ( !is_admin() )
        return;

      // Hook into init for registration of the option and the language files
      add_action( 'admin_init', array( &$this, 'init' ) );

      // Register the settings page
      add_action( 'admin_menu', array( &$this, 'add_config_page' ) );

      // Register the contextual help
      add_filter( 'contextual_help', array( &$this, 'contextual_help' ), 10, 2 );

      // Enqueue the needed scripts and styles
      add_action( 'admin_enqueue_scripts',array( &$this, 'admin_enqueue_scripts' ) );

      // Register a link to the settings page on the plugins overview page
      add_filter( 'plugin_action_links', array( &$this, 'filter_plugin_actions' ), 10, 2 );
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
     * Prints the PrintFriendly button CSS, in the header.
     *
     * @since 3.0
     */
    function front_head() {

      if ( isset( $this->options['enable_css'] ) && $this->options['enable_css'] != 'on' )
        return;

?>
        <style type="text/css" media="screen">
          div.printfriendly {
            margin: <?php echo $this->options['margin_top'].'px '.$this->options['margin_right'].'px '.$this->options['margin_bottom'].'px '.$this->options['margin_left'].'px'; ?>;
          }
          .printfriendly a {
            text-decoration: none;
            font-size: <?php echo $this->options['text_size']; ?>px;
            color: <?php echo $this->options['text_color']; ?>;
            vertical-align: bottom;
          }
          
          .printfriendly a:hover {
            cursor: pointer;
          }

          .printfriendly a img  {
            border: none;
            padding:0;
            margin-right: 6px;
          } 
          .printfriendly a span{
            vertical-align: bottom;
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
      if (isset($this->options['javascript']) && $this->options['javascript'] == 'no')
        return;

      else {
        $tagline = $this->options['tagline'];
        $image_url = $this->options['image_url'];
        if( $this->options['logo'] == 'favicon' ) {
          $tagline = '';
          $image_url = '';
        }

        // Currently we use v3 for both: normal and password protected sites
        $pf_src = '//cdn.printfriendly.com/printfriendly.js';
        if($this->options['website_protocol'] == 'https')
          $pf_src = 'https://pf-cdn.printfriendly.com/ssl/main.js';


?>
        <script type="text/javascript">
          var pfHeaderImgUrl = "<?php echo $image_url ?>";
          var pfHeaderTagline = "<?php echo $tagline ?>";
          var pfdisableClickToDel = "<?php echo $this->options['click_to_delete'] ?>";

          // PrintFriendly
          var e = document.createElement('script'); e.type="text/javascript";
          e.async = true;
          e.src = '<?php echo $pf_src ?>';
          document.getElementsByTagName('head')[0].appendChild(e);
      </script>
<?php
      }
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

      $onclick = 'onclick="window.print(); return false;"';
      $href = 'http://www.printfriendly.com/print/v2?url='.get_permalink();

      if ( isset( $this->options['javascript'] ) && $this->options['javascript'] == 'no' )
        $onclick = 'target="_blank"';

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
        add_action( 'wp_footer', array( &$this, 'print_script_footer' ) );
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

      if ( !in_array( $input['button_type'], array( 'pf-button.gif', 'button-print-grnw20.png',  'button-print-blu20.png',  'button-print-gry20.png',  'button-print-whgn20.png',  'pf_button_sq_gry_m.png',  'pf_button_sq_gry_l.png',  'pf_button_sq_grn_m.png',  'pf_button_sq_grn_l.png', 'pf-button-big.gif', 'pf-icon-small.gif', 'pf-icon.gif', 'pf-button-both.gif', 'pf-icon-both.gif', 'text-only', 'custom-image') ) )
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
        add_settings_error( $this->option_name, 'invalid_color', __( 'The text size you entered is invalid, please stay between 9px and 25px', $this->hook ) );
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
        $ver = '3.1.4';
        wp_register_script( 'pf-color-picker', plugins_url( 'colorpicker.js', __FILE__ ), array( 'jquery', 'media-upload' ), $ver );
        wp_register_script( 'pf-admin-js', plugins_url( 'admin.js', __FILE__ ), array( 'jquery', 'media-upload' ), $ver );

        wp_enqueue_script( 'pf-color-picker' );
        wp_enqueue_script( 'pf-admin-js' );

        wp_enqueue_style( 'printfriendly-admin-css', plugins_url( 'admin.css', __FILE__ ), array(), $ver);
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
        'button_type' => 'pf-button.gif',
        'content_position' => 'left',
        'content_placement' => 'after',
        'custom_image' => 'http://cdn.printfriendly.com/pf-icon.gif',
        'custom_text' => 'Print Friendly',
        'enable_css' => 'on',
        'margin_top' => '12',
        'margin_right' => '12',
        'margin_bottom' => '12',
        'margin_left' => '12',
        'show_list' => 'single',
        'text_color' => '#6D9F00',
        'text_size' => 14,
        'logo' => 'favicon',
        'image_url' => '',
        'tagline' => '',
        'click_to_delete' => '0', // 0 - allow, 1 - do not allow
        'website_protocol' => 'http',
        'password_protected' => 'no',
        'javascript' => 'yes'
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

      // update options to version 2
      if($this->options['db_version'] < 2) {

        $additional_options = array(
          'enable_css' => 'on',
          'logo' => 'favicon',
          'image_url' => '',
          'tagline' => '',
          'click_to_delete' => '0',
          'website_protocol' => 'http',
          'password_protected' => 'no',
          'javascript' => 'yes'
        );

        // use old javascript_include value to initialize javascript
        if(!isset($this->options['javascript_include']))
          $additional_options['javascript'] = 'no';

        unset($this->options['javascript_include']);
        unset($this->options['javascript_fallback']);

        // correcting badly named option
        if(isset($this->options['disable_css'])) {
          $additional_options['enable_css'] = $this->options['disable_css'];
          unset($this->options['disable_css']);
        }

        // check whether image we do not list any more was used
        if(in_array($this->options['button_type'], array('button-print-whgn20.png',  'pf_button_sq_qry_m.png',  'pf_button_sq_qry_l.png',  'pf_button_sq_grn_m.png',  'pf_button_sq_grn_l.png'))) {
          // previous version had a bug with button name
          if(in_array($this->options['button_type'], array('pf_button_sq_qry_m.png',  'pf_button_sq_qry_l.png'))) 
            $this->options['button_type'] = str_replace('qry', 'gry', $this->options['button_type']);

          $image_address = '//cdn.printfriendly.com/'.$this->options['button_type'];
          $this->options['button_type'] = 'custom-image';
          $this->options['custom_text'] = '';
          $this->options['custom_image'] = $image_address;
        }

        $this->options = array_merge($this->options, $additional_options);
      }

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
    function radio($name, $br = false){
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
      $img_path = 'http://cdn.printfriendly.com/';
      if($this->options['website_protocol'] == 'https')
        $img_path = 'https://pf-cdn.printfriendly.com/images/';

      switch($name){
      case "custom-image":
        if( '' == $this->options['custom_image'] )
          $return = '';
        else
          $return = '<img src="'.$this->options['custom_image'].'" alt="Print Friendly" />';

        $return .= $this->options['custom_text'];

        return $return;
        break;
      case "text-only":
        return '<span class="printfriendly-text2">'.$text.'</span>';
        break;

      case "pf-icon-both.gif":
        return '<span class="printfriendly-text2 printandpdf"><img style="border:none;margin-right:6px;" src="'.$img_path.'pf-print-icon.gif" width="16" height="15" alt="Print Friendly Version of this page" />Print <img style="border:none;margin:0 6px" src="'.$img_path.'pf-pdf-icon.gif" width="12" height="12" alt="Get a PDF version of this webpage" />PDF</span>';
        break;

      case "pf-icon-small.gif":
        return '<img style="border:none;margin-right:4px;" src="'.$img_path.'pf-icon-small.gif" alt="PrintFriendly and PDF" width="18" height="18"><span class="printfriendly-text2">'.$text.'</span>';
        break;
      case "pf-icon.gif":
        return '<img style="border:none;margin-right:6px;" src="'.$img_path.'pf-icon.gif" width="23" height="25" alt="PrintFriendly and PDF"><span class="printfriendly-text2">'.$text.'</span>';
        break;

      default:
        return '<img src="'.$img_path.$name.'" alt="Print Friendly" />';
        break;
      }
    }


    /**
     * Convenience function to output a value custom button preview elements
     *
     * @since 3.0.9
     */
    function custom_button_preview() {
      if( '' == trim($this->options['custom_image']) )
        $button_preview = '<span id="pf-custom-button-preview"></span>';
      else
        $button_preview = '<span id="pf-custom-button-preview"><img src="'.$this->options['custom_image'].'" alt="Print Friendly" /></span>';

      $button_preview .= '<span class="printfriendly-text2">'.$this->options['custom_text'].'</span>';

      echo $button_preview;
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
     * Like the WordPress selected() function but it doesn't throw notices when the array key isn't set and uses the plugins options array.
     *
     * @since 3.0.9
     * @param mixed $val value to check.
     * @param mixed $check_against value to check against.
     * @return string checked, when true, empty, when false.
     */
    function selected( $val, $check_against = true) {
      if ( !isset( $this->options[$val] ) )
        return;

      return selected ($this->options[$val], $check_against);
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
        
          <h3><?php _e( "Pick Your Button Style", $this->hook ); ?></h3>
        
          <fieldset id="button-style">
            <div id="buttongroup1">
              <?php $this->radio('pf-button.gif'); ?>
              <?php $this->radio('pf-button-both.gif'); ?>
              <?php $this->radio('pf-button-big.gif'); ?>
            </div>
            <div id="buttongroup2">
              <?php $this->radio('button-print-grnw20.png'); ?>
              <?php $this->radio('button-print-blu20.png'); ?>
              <?php $this->radio('button-print-gry20.png'); ?>
            </div>
            <div id="buttongroup3">
              <?php $this->radio('pf-icon-small.gif'); ?>
              <?php $this->radio('pf-icon-both.gif'); ?>
              <?php $this->radio('pf-icon.gif'); ?>
              <?php $this->radio('text-only'); ?>
            </div>

            <div id="custom">
              <label for="custom-image">
                <?php echo '<input id="custom-image" class="radio" name="'.$this->option_name.'[button_type]" type="radio" value="custom-image" '.$this->checked( 'button_type', 'custom-image', false ).'/>'; ?>
                <?php _e( "Custom Button", $this->hook ); ?>
              </label>
              <div id="custom-img">
                <?php _e( "Enter Image URL", $this->hook ); ?><br>
                <input id="custom_image" type="text" class="clear regular-text" size="30" name="<?php echo $this->option_name; ?>[custom_image]" value="<?php $this->val( 'custom_image' ); ?>" />
                <div class="description"><?php _e( "Ex: http://www.example.com/<br>Ex: /wp/wp-content/uploads/example.png)", $this->hook ); ?>
                </div>
              </div>
              <div id="pf-custom-button-error"></div>
              <div id="custom-txt" >
                <div id="txt-enter">
                  <?php _e( "Text", $this->hook ); ?><br>
                  <input type="text" size="10" name="<?php echo $this->option_name; ?>[custom_text]" id="custom_text" value="<?php $this->val( 'custom_text' ); ?>">
                </div>
                <div id="txt-color">
                  <?php _e( "Text Color", $this->hook ); ?>
                  <input type="hidden" name="<?php echo $this->option_name; ?>[text_color]" id="text_color" value="<?php $this->val( 'text_color' ); ?>"/><br>
                  <div id="colorSelector">
                    <div style="background-color: <?php echo $this->options['text_color']; ?>;"></div>
                  </div>
                </div>
                <div id="txt-size">
                  <?php _e( "Text Size", $this->hook ); ?><br>
                  <input type="number" id="text_size" min="9" max="25" class="small-text" name="<?php echo $this->option_name; ?>[text_size]" value="<?php $this->val( 'text_size' ); ?>"/>
                </div>
              </div>
            <div id="custom-button-preview">
              <?php $this->custom_button_preview(); ?>
            </div>
          </fieldset>
          <br class="clear">
    
    <!--Section 2 Button Placement-->
          <div id="button-placement">
            <h3><?php _e( "Button Placement", $this->hook ); ?>
      <span id="css"><input type="checkbox" name="<?php echo $this->option_name; ?>[enable_css]" value="<?php $this->val('enable_css');?>" <?php $this->checked('enable_css', 'off'); ?> />Do not use CSS for button styles</span>
            </h3>
            <div id="button-placement-options">
              <div id="alignment">
                <label>
                  <select id="pf_content_position" name="<?php echo $this->option_name; ?>[content_position]" >
                    <option value="left" <?php selected( $this->options['content_position'], 'left' ); ?>><?php _e( "Left Align", $this->hook ); ?></option>
                    <option value="right" <?php selected( $this->options['content_position'], 'right' ); ?>><?php _e( "Right Align", $this->hook ); ?></option>
                    <option value="center" <?php selected( $this->options['content_position'], 'center' ); ?>><?php _e( "Center", $this->hook ); ?></option>
                    <option value="none" <?php selected( $this->options['content_position'], 'none' ); ?>><?php _e( "None", $this->hook ); ?></option>
                  </select>
                </label>
              </div>
              <div class="content_placement">
                <label>
                  <select id="pf_content_placement" name="<?php echo $this->option_name; ?>[content_placement]" >
                    <option value="before" <?php selected( $this->options['content_placement'], 'before' ); ?>><?php _e( "Above Content", $this->hook ); ?></option>
                    <option value="after" <?php selected( $this->options['content_placement'], 'after' ); ?>><?php _e( "Below Content", $this->hook ); ?></option>
                  </select>
                </label>
              </div>
              <div id="margin">
                <label>
                  <input type="number" name="<?php echo $this->option_name; ?>[margin_left]" value="<?php $this->val( 'margin_left' ); ?>" maxlength="3"/>
                  <?php _e( "Margin Left", $this->hook ); ?>
                </label>
                <label>
                  <input type="number" name="<?php echo $this->option_name; ?>[margin_right]" value="<?php $this->val( 'margin_right' ); ?>"/> <?php _e( "Margin Right", $this->hook ); ?>
                </label>
                <label>
                  <input type="number" name="<?php echo $this->option_name; ?>[margin_top]"  value="<?php $this->val( 'margin_top' ); ?>" maxlength="3"/> <?php _e( "Margin Top", $this->hook ); ?>
                </label>
                <label>
                  <input type="number" name="<?php echo $this->option_name; ?>[margin_bottom]" value="<?php $this->val( 'margin_bottom' ); ?>" maxlength="3"/> <?php _e( "Margin Bottom", $this->hook ); ?>
                </label>
              </div>
            </div>
            <div id="pages">
              <label><input type="radio" class="show_list" name="<?php echo $this->option_name; ?>[show_list]" value="all" <?php $this->checked( 'show_list', 'all'); ?>/> <?php _e( "Homepage, Posts, and Pages", $this->hook ); ?></label>
              <label><input type="radio" class="show_list" name="<?php echo $this->option_name; ?>[show_list]" value="single" <?php $this->checked( 'show_list', 'single'); ?>/> <?php _e( "Posts and Pages", $this->hook ); ?></label>
              <label><input type="radio" class="show_list" name="<?php echo $this->option_name; ?>[show_list]" value="posts" <?php $this->checked( 'show_list', 'posts'); ?>/> <?php _e( "Posts", $this->hook ); ?></label>
              <label><input type="radio" class="show_list" name="<?php echo $this->option_name; ?>[show_list]" value="manual" <?php $this->checked( 'show_list', 'manual'); ?> /> <?php _e( "Use shortcode in your template", $this->hook ); ?>
              </label>
              <textarea  id="pf-shortcode" class="code" rows="2" cols="40">&lt;?php if(function_exists('pf_show_link')){echo pf_show_link();} ?&gt;</textarea>
            </div>
          </div>
          
    <!--Section 3 Button Print Options-->        
          <div id="print-options">
            <h3><?php _e( "Print PDF Options", $this->hook ); ?></h3>
            <label id="pf-favicon" for="favicon">
              <?php _e( "Page header", $this->hook ); ?>
              <select id="pf-logo" name="<?php echo $this->option_name; ?>[logo]" >
                <option value="favicon" <?php selected( $this->options['logo'], 'favicon' ); ?>><?php _e( "My Website Icon", $this->hook ); ?></option>
                <option value="upload-an-image" <?php selected( $this->options['logo'], 'upload-an-image' ); ?>><?php _e( "Upload an Image", $this->hook ); ?></option>
              </select>
            </label>
            <div class="custom-logo"><label for="Enter_URL">Enter url</label><input id="upload-an-image" type="text" class="regular-text" name="<?php echo $this->option_name; ?>[image_url]" value="<?php $this->val( 'image_url' ); ?>" /><label for="Text__optional_">Text (optional)</label><input id="image-tagline" type="text" class="regular-text" name="<?php echo $this->option_name; ?>[tagline]" value="<?php $this->val( 'tagline' ); ?>" /></div>
            <div id="pf-image-error"></div>
            <div id="pf-image-preview"></div>
            <label for="click_to_delete">
              <?php _e( "Click-to-delete", $this->hook ); ?>
              <select name="<?php echo $this->option_name; ?>[click_to_delete]" id="click-to-delete">
                <option value="0" <?php selected( $this->options['click_to_delete'], '0' ); ?>><?php _e( "Allow", $this->hook ); ?></option>
                <option value="1" <?php selected( $this->options['click_to_delete'], '1' ); ?>><?php _e( "Not Allow", $this->hook ); ?></option>
              </select>
            </label>
          </div>
   
   <!--Section 4 WebMaster-->         
        <h3><?php _e( "Webmaster Settings", $this->hook ); ?></h3>
        
        <label for="protocol">Website Protocol<br>
          <select id="website_protocol" name="<?php echo $this->option_name; ?>[website_protocol]" >
            <option value="http" <?php selected( $this->options['website_protocol'], 'http' ); ?>><?php _e( "http (common)", $this->hook ); ?></option>
            <option value="https" <?php selected( $this->options['website_protocol'], 'https' ); ?>><?php _e( "https (secure)", $this->hook ); ?></option>
          </select>
          <span id="https-beta-registration" class="description">HTTPS is in Beta. Please <a href="#" onclick="window.open('http://www.printfriendly.com/https-registration.html', 'newwindow', 'width=600, height=550'; return false;">Register for updates</a>.
          </span>
        </label>
        <label for="password-site">Password Protected Content
          <select id="password_protected" name="<?php echo $this->option_name; ?>[password_protected]">
            <option value="no" <?php selected( $this->options['password_protected'], 'no' ); ?>><?php _e( "No", $this->hook ); ?></option>
            <option value="yes" <?php selected( $this->options['password_protected'], 'yes' ); ?>><?php _e( "Yes", $this->hook ); ?></option>
          </select>
        </label>
        <label id="pf-javascript-container">Use JavaScript<br>
          <select id="javascript" name="<?php echo $this->option_name; ?>[javascript]>">
            <option value="yes" <?php $this->selected( 'javascript', 'yes' ); ?>> <?php _e( "Yes", $this->hook ); ?></option>
            <option value="no" <?php $this->selected( 'javascript', 'no' ); ?>> <?php _e( "No", $this->hook ); ?></option>
          </select>
          <span class="description javascript">
            <?php _e( "Display print preview on-page using a JavaScript Lightbox (user never leaves the site/page).", $this->hook ); ?>
          </span>
          <span class="description no-javascript">
            <?php _e( "Display print preview on PrintFriendly.com (No JavaScript)", $this->hook ); ?>
          </span>
        </label>
        
        <p class="submit">
          <input type="submit" class="button-primary" value="<?php esc_attr_e( "Save Options", $this->hook ); ?>"/>
          <input type="reset" class="button-secondary" value="<?php esc_attr_e( "Cancel", $this->hook ); ?>"/>
        </p>
        <div id="after-submit">
          <p>Need professional options for your corporate, education, or agency developed website? Check out <a href="http://www.printfriendly.com/pro">PrintFriendly Pro</a>.</p>
          <p>
            <?php _e( "Like PrintFriendly?", $this->hook ); ?> <a href="http://wordpress.org/extend/plugins/printfriendly/"><?php _e( "Give us a rating", $this->hook ); ?></a>. <?php _e( "Need help or have suggestions?", $this->hook ); ?> <a href="mailto:support@printfriendly.com?subject=Support%20for%20PrintFriendly%20WordPress%20plugin">support@PrintFriendly.com</a>.</p>
          </div>
        
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
