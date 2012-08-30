<?php
/**
 * Subscriptions Checkout
 * 
 * Extends the WooCommerce checkout class to add subscription meta on checkout.
 *
 * @package		WooCommerce Subscriptions
 * @subpackage	WC_Subscriptions_Checkout
 * @category	Class
 * @author		Brent Shepherd
 */
class WC_Subscriptions_Checkout {

	private static $signup_option_changed = false;

	private static $guest_checkout_option_changed = false;

	/**
	 * Bootstraps the class and hooks required actions & filters.
	 * 
	 * @since 1.0
	 */
	public static function init(){
		add_action( 'woocommerce_checkout_update_order_meta', __CLASS__ . '::add_order_meta' );

		// Make sure users can register on checkout (before any other hooks before checkout)
		add_action( 'woocommerce_before_checkout_form', __CLASS__ . '::make_checkout_registration_possible', -1 );

		// Restore the settings after switching them for the checkout form
		add_action( 'woocommerce_after_checkout_form', __CLASS__ . '::restore_checkout_registration_settings', 100 );

		// Make sure guest checkout is not enabled in option param passed to WC JS 
		add_filter( 'woocommerce_params', __CLASS__ . '::filter_woocommerce_script_paramaters', 10, 1 );

		// Force checkout during checkout process
		add_action( 'woocommerce_before_checkout_process', __CLASS__ . '::force_registration_during_checkout', 10 );
	}

	/**
	 * When a new order is inserted, add the subscriptions period to the order. 
	 * 
	 * It's important that the period is tied to the order so that changing the products
	 * period does not change the past. 
	 *
	 * @since 1.0
	 */
	public static function add_order_meta( $order_id ) {
		global $woocommerce;

		if( WC_Subscriptions_Order::order_contains_subscription( $order_id ) ) {

			$order = new WC_Order( $order_id );

			$order_subscription_periods       = array();
			$order_subscription_intervals     = array();
			$order_subscription_lengths       = array();
			$order_subscription_trial_lengths = array();

			foreach ( $order->get_items() as $item ) {
				$period = WC_Subscriptions_Product::get_period( $item['id'] );
				if ( ! empty( $period ) )
					$order_subscription_periods[$item['id']] = $period;

				$interval = WC_Subscriptions_Product::get_interval( $item['id'] );
				if ( ! empty( $interval ) )
					$order_subscription_intervals[$item['id']] = $interval;

				$length = WC_Subscriptions_Product::get_length( $item['id'] );
				if ( ! empty( $length ) )
					$order_subscription_lengths[$item['id']] = $length;

				$trial_length = WC_Subscriptions_Product::get_trial_length( $item['id'] );
				if ( ! empty( $trial_length ) )
					$order_subscription_trial_lengths[$item['id']] = $trial_length;
			}

			update_post_meta( $order_id, '_order_subscription_periods', $order_subscription_periods );
			update_post_meta( $order_id, '_order_subscription_intervals', $order_subscription_intervals );
			update_post_meta( $order_id, '_order_subscription_lengths', $order_subscription_lengths );
			update_post_meta( $order_id, '_order_subscription_trial_lengths', $order_subscription_trial_lengths );

			// Store sign-up fee details
			foreach ( WC_Subscriptions_Cart::get_sign_up_fee_fields() as $field_name )
				update_post_meta( $order_id, "_$field_name", $woocommerce->cart->{$field_name} );

			// Prepare sign up fee taxes to store in same format as order taxes
			$sign_up_fee_taxes = array();

			foreach ( array_keys( $woocommerce->cart->sign_up_fee_taxes ) as $key ) {

				$is_compound = ( $woocommerce->cart->tax->is_compound( $key ) ) ? 1 : 0;

				$sign_up_fee_taxes[] = array(
					'label' => $woocommerce->cart->tax->get_rate_label( $key ),
					'compound' => $is_compound,
					'cart_tax' => number_format( $woocommerce->cart->sign_up_fee_taxes[$key], 2, '.', '' )
				);
			}

			update_post_meta( $order_id, '_sign_up_fee_taxes', $sign_up_fee_taxes );

		}
	}

	/**
	 * If shopping cart contains subscriptions, make sure a user can register on the checkout page
	 *
	 * @since 1.0
	 */
	public static function make_checkout_registration_possible() {

		if ( WC_Subscriptions_Cart::cart_contains_subscription() && ! is_user_logged_in() ) {

			// Make sure users can sign up
			if ( 'no' == get_option( 'woocommerce_enable_signup_and_login_from_checkout' ) ) {
				update_option( 'woocommerce_enable_signup_and_login_from_checkout', 'yes' );
				self::$signup_option_changed = true;
			}

			// Make sure users are required to register an account
			if ( 'yes' == get_option( 'woocommerce_enable_guest_checkout' ) ) {
				update_option( 'woocommerce_enable_guest_checkout', 'no' );
				self::$guest_checkout_option_changed = true;
			}

		}

	}

	/**
	 * After displaying the checkout form, restore the store's original registration settings.
	 *
	 * @since 1.1
	 */
	public static function restore_checkout_registration_settings() {

		if ( self::$signup_option_changed )
			update_option( 'woocommerce_enable_signup_and_login_from_checkout', 'no' );

		if ( self::$guest_checkout_option_changed )
			update_option( 'woocommerce_enable_guest_checkout', 'yes' );

	}

	/**
	 * Also make sure the guest checkout option value passed to the woocommerce.js forces registration.
	 * Otherwise the registration form is hidden by woocommerce.js.
	 *
	 * @since 1.1
	 */
	public static function filter_woocommerce_script_paramaters( $woocommerce_params ) {

		if ( WC_Subscriptions_Cart::cart_contains_subscription() && ! is_user_logged_in() && $woocommerce_params['option_guest_checkout'] == 'yes' )
			$woocommerce_params['option_guest_checkout'] = 'no';

		return $woocommerce_params;
	}

	/**
	 * During the checkout process, force registration when the cart contains a subscription.
	 *
	 * @since 1.1
	 */
	public static function force_registration_during_checkout( $woocommerce_params ) {

		if ( WC_Subscriptions_Cart::cart_contains_subscription() && ! is_user_logged_in() )
			$_POST['createaccount'] = 1;

	}


}

WC_Subscriptions_Checkout::init();
