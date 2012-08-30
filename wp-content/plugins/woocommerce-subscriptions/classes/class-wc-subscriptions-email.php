<?php
/**
 * Subscriptions Email Class
 * 
 * Modifies the base WooCommerce email class and extends it to send subscription emails.
 *
 * @package		WooCommerce Subscriptions
 * @subpackage	WC_Subscriptions_Email
 * @category	Class
 * @author		Brent Shepherd
 */
class WC_Subscriptions_Email {

	private static $woocommerce_email;

	/**
	 * Bootstraps the class and hooks required actions & filters.
	 * 
	 * @since 1.0
	 */
	public static function init() {
		add_action( 'woocommerce_email', __CLASS__ . '::set_email', 10, 1 );

		// Order Status Actions
		add_action( 'woocommerce_order_status_pending_to_processing_notification', __CLASS__ . '::maybe_remove_customer_processing_order', 1, 1 );
		add_action( 'woocommerce_order_status_pending_to_on-hold_notification', __CLASS__ . '::maybe_remove_customer_processing_order', 1, 1 );
	}

	/**
	 * Sets up the internal $woocommerce_email property for this class.
	 * 
	 * @since 1.0
	 */
	public static function set_email( $wc_email ) {
		self::$woocommerce_email = $wc_email;
	}

	/**
	 * Removes a couple of notifications that are less relevant for Subscription orders.
	 * 
	 * @since 1.0
	 */
	public static function maybe_remove_customer_processing_order( $order_id ) {

		if( WC_Subscriptions_Order::order_contains_subscription( $order_id ) ) {
			remove_action( 'woocommerce_order_status_pending_to_processing_notification', array( self::$woocommerce_email, 'customer_processing_order' ) );
			remove_action( 'woocommerce_order_status_pending_to_on-hold_notification', array( self::$woocommerce_email, 'customer_processing_order' ) );
		}

	}

}

WC_Subscriptions_Email::init();
