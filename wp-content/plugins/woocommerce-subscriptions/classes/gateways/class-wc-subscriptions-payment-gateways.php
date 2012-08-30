<?php
/**
 * Subscriptions Payment Gateways
 * 
 * Hooks into the WooCommerce payment gateways class to add subscription specific functionality.
 *
 * @package		WooCommerce Subscriptions
 * @subpackage	WC_Subscriptions_Payment_Gateways
 * @category	Class
 * @author		Brent Shepherd
 * @since		1.0
 */
class WC_Subscriptions_Payment_Gateways {

	/**
	 * Bootstraps the class and hooks required actions & filters.
	 * 
	 * @since 1.0
	 */
	public static function init(){

		add_filter( 'woocommerce_available_payment_gateways', __CLASS__ . '::get_available_payment_gateways' );

		// Create a custom hook for gateways that need to manually charge recurring payments
		add_action( 'scheduled_subscription_payment', __CLASS__ . '::gateway_scheduled_subscription_payment', 10, 2 );

		// Create a gateway specific hooks for subscription events
		add_action( 'activated_subscription', __CLASS__ . '::trigger_gateway_activated_subscription_hook', 10, 2 );
		add_action( 'reactivated_subscription', __CLASS__ . '::trigger_gateway_reactivated_subscription_hook', 10, 2 );
		add_action( 'suspended_subscription', __CLASS__ . '::trigger_gateway_suspended_subscription_hook', 10, 2 );
		add_action( 'cancelled_subscription', __CLASS__ . '::trigger_gateway_cancelled_subscription_hook', 10, 2 );
		add_action( 'subscription_expired', __CLASS__ . '::trigger_gateway_subscription_expired_hook', 10, 2 );
	}

	/**
	 * Only displays the gateways which support subscriptions. 
	 * 
	 * @since 1.0
	 */
	public static function get_available_payment_gateways( $available_gateways ) {

		if ( WC_Subscriptions_Cart::cart_contains_subscription() || ( isset( $_GET['order_id'] ) && WC_Subscriptions_Order::order_contains_subscription( $_GET['order_id'] ) ) ) { // || WC_Subscriptions_Order::order_contains_subscription( $order_id )
			foreach ( $available_gateways as $gateway_id => $gateway ) {
				if ( ! method_exists( $gateway, 'supports' ) || $gateway->supports( 'subscriptions' ) !== true )
					unset( $available_gateways[$gateway_id] );
			}
		}

		return $available_gateways;
	}

	/**
	 * For versions of WooCommerce prior to the existence of the woocommerce_available_gateways, 
	 * hide available gateways with JavaScript.
	 * 
	 * @since 1.0
	 */
	public static function gateway_scheduled_subscription_payment( $user_id, $subscription_key ) {

		$subscription = WC_Subscriptions_Manager::get_users_subscription( $user_id, $subscription_key );

		$order = new WC_Order( $subscription['order_id'] );

		$amount_to_charge = WC_Subscriptions_Order::get_price_per_period( $order, $subscription['product_id'] );

		$outstanding_payments = WC_Subscriptions_Order::get_outstanding_balance( $order, $subscription['product_id'] );

		if ( $outstanding_payments > 0 )
			$amount_to_charge += $outstanding_payments;

		do_action( 'scheduled_subscription_payment_' . $order->payment_method, $amount_to_charge, $order, $subscription['product_id'] );
	}

	/**
	 * Fire a gateway specific hook for when a subscription is activated.
	 * 
	 * @since 1.0
	 */
	public static function trigger_gateway_activated_subscription_hook( $user_id, $subscription_key ) {

		$subscription = WC_Subscriptions_Manager::get_users_subscription( $user_id, $subscription_key );

		$order = new WC_Order( $subscription['order_id'] );

		do_action( 'activated_subscription_' . $order->payment_method, $order, $subscription['product_id'] );
	}

	/**
	 * Fire a gateway specific hook for when a subscription is activated.
	 * 
	 * @since 1.0
	 */
	public static function trigger_gateway_reactivated_subscription_hook( $user_id, $subscription_key ) {

		$subscription = WC_Subscriptions_Manager::get_users_subscription( $user_id, $subscription_key );

		$order = new WC_Order( $subscription['order_id'] );

		do_action( 'reactivated_subscription_' . $order->payment_method, $order, $subscription['product_id'] );
	}

	/**
	 * Fire a gateway specific hook for when a subscription is suspended.
	 * 
	 * @since 1.0
	 */
	public static function trigger_gateway_suspended_subscription_hook( $user_id, $subscription_key ) {

		$subscription = WC_Subscriptions_Manager::get_users_subscription( $user_id, $subscription_key );

		$order = new WC_Order( $subscription['order_id'] );

		do_action( 'suspended_subscription_' . $order->payment_method, $order, $subscription['product_id'] );
	}

	/**
	 * Fire a gateway specific when a subscription is cancelled.
	 * 
	 * @since 1.0
	 */
	public static function trigger_gateway_cancelled_subscription_hook( $user_id, $subscription_key ) {

		$subscription = WC_Subscriptions_Manager::get_users_subscription( $user_id, $subscription_key );

		$order = new WC_Order( $subscription['order_id'] );

		do_action( 'cancelled_subscription_' . $order->payment_method, $order, $subscription['product_id'] );
	}

	/**
	 * Fire a gateway specific hook when a subscription expires.
	 * 
	 * @since 1.0
	 */
	public static function trigger_gateway_subscription_expired_hook( $user_id, $subscription_key ) {

		$subscription = WC_Subscriptions_Manager::get_users_subscription( $user_id, $subscription_key );

		$order = new WC_Order( $subscription['order_id'] );

		do_action( 'subscription_expired_' . $order->payment_method, $order, $subscription['product_id'] );
	}
}

WC_Subscriptions_Payment_Gateways::init();
