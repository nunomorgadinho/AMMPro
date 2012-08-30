<?php
/**
 * Plugin Name: WooCommerce Subscriptions
 * Plugin URI: http://www.woothemes.com/extension/subscriptions/
 * Description: Sell products and services with recurring payments in your WooCommerce Store.
 * Author: Brent Shepherd
 * Author URI: http://find.brentshepherd.com/
 * Version: 1.1.3
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 	
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package		WooCommerce Subscriptions
 * @author		Brent Shepherd
 * @since		1.0
 */

/**
 * Check if WooCommerce is active, and if it isn't, disable Subscriptions.
 * 
 * @since 1.0
 */
if ( ! function_exists( 'is_woocommerce_active' ) )
	require_once( 'woo-includes/woo-functions.php' );

if ( ! is_woocommerce_active() ) {
	require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	deactivate_plugins( basename( dirname( __FILE__ ) ) . '/' . basename( __FILE__ ) );
	wp_die(
		sprintf(
			__( 'The %sWooCommerce plugin%s must be active for the WooCommerce Subscriptions plugin to work. %sWooCommerce Subscriptions has been deactivated. %sInstall & activate WooCommerce%s', WC_Subscriptions::$text_domain ), 
			'<a href="http://woothemes.com/woocommerce">', '</a>', '</p><p>',
			'</p><p><a href="' . admin_url( 'plugins.php' ) . '">', '&nbsp;&raquo;</a>' 
		)
	);
}

/**
 * Plugin updates
 *
 * @since 1.0
 */
if ( is_admin() ) {
	$woo_plugin_updater_subscriptions = new WooThemes_Plugin_Updater( __FILE__ );
	$woo_plugin_updater_subscriptions->api_key = '62dc47679a7b266cbd091c8044fde3db';
	$woo_plugin_updater_subscriptions->init();
}

require_once( 'classes/class-wc-subscriptions-product.php' );

require_once( 'classes/class-wc-subscriptions-admin.php' );

require_once( 'classes/class-wc-subscriptions-manager.php' );

require_once( 'classes/class-wc-subscriptions-cart.php' );

require_once( 'classes/class-wc-subscriptions-order.php' );

require_once( 'classes/class-wc-subscriptions-checkout.php' );

require_once( 'classes/class-wc-subscriptions-email.php' );

require_once( 'classes/gateways/class-wc-subscriptions-payment-gateways.php' );

require_once( 'classes/gateways/gateway-paypal-standard-subscriptions.php' );

/**
 * The main subscriptions class. 
 *
 * @since 1.0
 */
class WC_Subscriptions {

	public static $name = 'subscription';

	public static $text_domain = 'woocommerce_subscription';

	public static $activation_transient = 'woocommerce_subscriptions_activated';

	public static $plugin_file = __FILE__;

	public static $version = '1.1.3';

	/**
	 * Set up the class, including it's hooks & filters, when the file is loaded.
	 *
	 * @since 1.0
	 **/
	public static function init() {

		add_action( 'admin_init', __CLASS__ . '::maybe_activate_woocommerce_subscriptions' );

		register_deactivation_hook( __FILE__, __CLASS__ . '::deactivate_woocommerce_subscriptions' );

		// Overide the WC default "Add to Cart" text to "Sign Up Now" (in various places/templates)
		add_filter( 'add_to_cart_text', __CLASS__ . '::add_to_cart_text' );
		add_filter( 'single_add_to_cart_text', __CLASS__ . '::add_to_cart_text' );
		add_filter( 'woocommerce_order_button_text', __CLASS__ . '::order_button_text' );
		add_action( 'woocommerce_' . self::$name . '_add_to_cart', __CLASS__ . '::subscription_add_to_cart', 30 );

		// Redirect the user immediately to the checkout page after clicking "Sign Up Now" buttons to encourage immediate checkout
		add_filter( 'add_to_cart_redirect', __CLASS__ . '::add_to_cart_redirect' );

		// Ensure a subscription is never in the cart with products
		add_filter( 'woocommerce_add_to_cart_validation', __CLASS__ . '::maybe_empty_cart', 10, 3 );

		// Mark subscriptions as individual items
		add_filter( 'woocommerce_is_sold_individually', __CLASS__ . '::is_sold_individually', 10, 2 );

		// Update Order totals via Ajax when a order form is updated
		add_action( 'wp_ajax_woocommerce_subscriptions_update_order_total', __CLASS__ . '::ajax_get_order_totals' );
		add_action( 'wp_ajax_nopriv_woocommerce_subscriptions_update_order_total', __CLASS__ . '::ajax_get_order_totals' );

		// Display Subscriptions on a User's account page
		add_action( 'woocommerce_before_my_account', __CLASS__ . '::get_my_subscriptions_template' );

		// Load translation files
		add_action( 'plugins_loaded', __CLASS__ . '::load_plugin_textdomain' );

	}

	/**
	 * Loads the my-subscriptions.php template on the My Account page.
	 *
	 * @since 1.0
	 */
	public static function get_my_subscriptions_template() {
		require_once( plugin_dir_path( __FILE__ ) . 'templates/my-account/my-subscriptions.php' );
	}

	/**
	 * Output a redirect URL when an item is added to the cart when a subscription was already in the cart.
	 *
	 * @since 1.0
	 */
	public static function redirect_ajax_add_to_cart( $fragments ) {
		global $woocommerce;

		$data = array(
			'error' => true,
			'product_url' => $woocommerce->cart->get_cart_url()
		);

		return $data;
	}

	/**
	 * When a subscription is added to the cart, remove other products/subscriptions to
	 * work with PayPal Standard, which only accept one subscription per checkout.
	 *
	 * @since 1.0
	 */
	public static function maybe_empty_cart( $valid, $product_id, $quantity ) {
		global $woocommerce;

		if ( WC_Subscriptions_Product::is_subscription( $product_id ) ) {

			$woocommerce->cart->empty_cart();

		} elseif ( WC_Subscriptions_Cart::cart_contains_subscription() ) {

			self::remove_subscriptions_from_cart();

			$woocommerce->add_error( __( 'A subscription has been removed from your cart. Due to payment gateway restrictions, products and subscriptions can not be purchased at the same time.', self::$text_domain ) );
			$woocommerce->set_messages();

			// Redirect to cart page to remove subscription & notify shopper
			add_filter( 'add_to_cart_fragments', __CLASS__ . '::redirect_ajax_add_to_cart' );

		}

		return $valid;
	}

	/**
	 * Removes all subscription products from the shopping cart.
	 *
	 * @since 1.0
	 */
	public static function remove_subscriptions_from_cart() {
		global $woocommerce;

		foreach( $woocommerce->cart->cart_contents as $cart_item_key => $cart_item )
			if ( WC_Subscriptions_Product::is_subscription( $cart_item['product_id'] ) )
				$woocommerce->cart->set_quantity( $cart_item_key, 0 );
	}

	/**
	 * For a smoother sign up process, tell WooCommerce to redirect the shopper immediately to  
	 * the checkout page after she clicks the "Sign Up Now" button
	 * 
	 * @param $url string The cart redirect $url WooCommerce determined.
	 * @since 1.0
	 */
	public static function add_to_cart_redirect( $url ) {
		global $woocommerce;

		// If product is of the subscription type
		if ( is_numeric( $_REQUEST['add-to-cart'] ) && WC_Subscriptions_Product::is_subscription( (int) $_REQUEST['add-to-cart'] ) ) {
			// Remove default cart message
			$woocommerce->clear_messages();

			// Redirect to checkout
			$url = $woocommerce->cart->get_checkout_url();
		}

		return $url;
	}

	/**
	 * Subscriptions are individual items so override the WC_Product is_sold_individually function 
	 * to reflect this.
	 *
	 * @since 1.0
	 */
	public static function is_sold_individually( $is_individual, $product ) {

		// Sold individually if downloadable, virtual, and the option is enabled
		if ( WC_Subscriptions_Product::is_subscription( $product ) )
			$is_individual = true;

		return $is_individual;
	}

	/**
	 * Override the WooCommerce "Add to Cart" text with "Sign Up Now"
	 *
	 * @since 1.0
	 */
	public static function add_to_cart_text( $button_text ) {
		global $product;

		if ( WC_Subscriptions_Product::is_subscription( $product ) )
			$button_text = get_option( WC_Subscriptions_Admin::$option_prefix . '_add_to_cart_button_text', __( 'Sign Up Now', WC_Subscriptions::$text_domain ) );

		return $button_text;
	}

	/**
	 * Override the WooCommerce "Place Order" text with "Sign Up Now"
	 *
	 * @since 1.0
	 */
	public static function order_button_text( $button_text ) {
		global $product;

		if ( WC_Subscriptions_Cart::cart_contains_subscription() )
			$button_text = get_option( WC_Subscriptions_Admin::$option_prefix . '_order_button_text', __( 'Sign Up Now', WC_Subscriptions::$text_domain ) );

		return $button_text;
	}

	/**
	 * Load the subscription add_to_cart template.
	 * 
	 * Use the same cart template for subscription as that which is used for simple products. Reduce code duplication
	 * and is made possible by the friendly actions & filters found through WC.
	 * 
	 * Not using a custom template both prevents code duplication and helps future proof this extension from core changes. 
	 *
	 * @since 1.0
	 */
	public static function subscription_add_to_cart() {

		require_once( plugin_dir_path( __FILE__ ) . 'templates/single-product/add-to-cart/subscription.php' );
	}

	/**
	 * Takes a number and returns the number with its relevant suffix appended, eg. for 2, the function returns 2nd
	 *
	 * @since 1.0
	 */
	public static function append_numeral_suffix( $number ) {

		// If the tens digit of a number is 1, then write "th" after the number. For example: 13th, 19th, 112th, 9311th. http://en.wikipedia.org/wiki/English_numerals
		if ( strlen( $number ) > 1 && substr( $number, -2 ) ) {
			$number_string = sprintf( __( '%sth', self::$text_domain ), $number );
		} else { // Append relevant suffix
			switch( substr( $number, -1 ) ) {
				case 1:
					$number_string = sprintf( __( '%sst', self::$text_domain ), $number );
					break;
				case 2:
					$number_string = sprintf( __( '%snd', self::$text_domain ), $number );
					break;
				case 3:
					$number_string = sprintf( __( '%srd', self::$text_domain ), $number );
					break;
				default:
					$number_string = sprintf( __( '%sth', self::$text_domain ), $number );
					break;
			}
		}

		return apply_filters( 'woocommerce_numeral_suffix', $number_string, $number );
	}


	/*
	 * Plugin House Keeping
	 */

	public static function maybe_activate_woocommerce_subscriptions(){
		global $wpdb;

		$is_active      = get_option( WC_Subscriptions_Admin::$option_prefix . '_is_active', false );
		$active_version = get_option( WC_Subscriptions_Admin::$option_prefix . '_active_version', '0' );

		if ( $is_active == false || version_compare( $active_version, self::$version, '<' ) ) {

			// Update meta keys for 1.1 to 1.1.1 multisite changes
			if ( version_compare( $active_version, '1.1.1', '<' ) )
				$updated_count = $wpdb->update( $wpdb->usermeta, array( 'meta_key' => $wpdb->get_blog_prefix() . WC_Subscriptions_Manager::$users_meta_key ), array( 'meta_key' => WC_Subscriptions_Manager::$users_meta_key ) );

			// Fix any products that were incorrectly added as a subscription for a user in 1.1.2
			if ( version_compare( $active_version, '1.1.3', '<' ) ) {
				foreach ( get_users() as $user ) {
					$users_subscriptions = WC_Subscriptions_Manager::get_users_subscriptions( $user->ID );
					foreach ( $users_subscriptions as $subscription_key => $subscription_details )
						if ( ! isset ( $subscription_details['order_id'] ) )
							WC_Subscriptions_Manager::remove_users_subscription( $user->ID, $subscription_key );
				}
			}

			// Add the "Subscriptions" product type if it doesn't exist
			if ( ! get_term_by( 'slug', sanitize_title( self::$name ), 'product_type' ) )
				wp_insert_term( self::$name, 'product_type' );

			// If no Subscription settings exist, add defaults
			if ( get_option( WC_Subscriptions_Admin::$option_prefix . '_cancelled_role', false ) == false )
				WC_Subscriptions_Admin::add_default_settings();

			add_option( WC_Subscriptions_Admin::$option_prefix . '_is_active', true );

			update_option( WC_Subscriptions_Admin::$option_prefix . '_active_version', self::$version );

			set_transient( self::$activation_transient, true, 60 * 60 );

			do_action( 'woocommerce_subscriptions_activated' );
		}
	}

	/**
	 * Called when the plugin is deactivated. Deletes the subscription product type and fires an action.
	 *
	 * @since 1.0
	 */
	public static function deactivate_woocommerce_subscriptions() {

		wp_delete_term( self::$name, 'product_type' );

		delete_option( WC_Subscriptions_Admin::$option_prefix . '_is_active' );

		do_action( 'woocommerce_subscriptions_deactivated' );
	}

	/**
	 * Called on plugins_loaded to load any translation files.
	 *
	 * @since 1.1
	 */
	public static function load_plugin_textdomain(){

		$plugin_rel_path = apply_filters( 'woocommerce_subscriptions_translation_file_rel_path', dirname( plugin_basename( __FILE__ ) ) );

		load_plugin_textdomain( self::$text_domain, false, $plugin_rel_path ); 
	}


	/* Deprecated Functions */

	/**
	 * Was called when a plugin is activated using official register_activation_hook() API
	 * 
	 * However, apparently the official API is "lame" and it's far better to use an upgrade routine fired on admin_init: http://core.trac.wordpress.org/ticket/14170
	 * 
	 * Upgrade routine is now in @see maybe_activate_woocommerce_subscriptions()
	 * 
	 * @since 1.0
	 */
	public static function activate_woocommerce_subscriptions(){
		_deprecated_function( __CLASS__ . '::' . __FUNCTION__, '1.1', __CLASS__ . '::maybe_activate_woocommerce_subscriptions()' );
	}
}

WC_Subscriptions::init();
