<?php
/**
 * Subscriptions Management Class
 * 
 * An API of Subscription utility functions and Account Management functions.
 * 
 * Subscription activation and cancellation functions are hooked directly to order status changes
 * so your payment gateway only needs to work with WooCommerce APIs. You can however call other
 * management functions directly when necessary.
 * 
 * @package		WooCommerce Subscriptions
 * @subpackage	WC_Subscriptions_Manager
 * @category	Class
 * @author		Brent Shepherd
 * @since		1.0
 */
class WC_Subscriptions_Manager {

	/**
	 * The database key for user's subscriptions. 
	 * 
	 * @since 1.0
	 */
	public static $users_meta_key = 'woocommerce_subscriptions';

	/**
	 * A variable for storing any filters that are removed by @see self::safeguard_scheduled_payments()
	 * 
	 * @since 1.1.2
	 */
	private static $removed_filter_cache = array();

	/**
	 * Set up the class, including it's hooks & filters, when the file is loaded.
	 *
	 * @since 1.0
	 **/
	public static function init() {

		// When an order's status is changed, run the appropriate subscription function
		add_action( 'woocommerce_order_status_refunded', __CLASS__ . '::cancel_subscriptions_for_order' );
		add_action( 'woocommerce_order_status_cancelled', __CLASS__ . '::cancel_subscriptions_for_order' );
		add_action( 'woocommerce_order_status_failed', __CLASS__ . '::failed_subscription_sign_ups_for_order' );
		add_action( 'woocommerce_order_status_processing', __CLASS__ . '::activate_subscriptions_for_order' );
		add_action( 'woocommerce_order_status_completed', __CLASS__ . '::activate_subscriptions_for_order' );

		// Create a subscription entry when a new order is placed
		add_action( 'woocommerce_checkout_order_processed', __CLASS__ . '::process_subscriptions_on_checkout', 10, 2 );

		// Check if a user is requesting to cancel their subscription
		add_action( 'init', __CLASS__ . '::maybe_change_users_subscription', 100 );

		// Expire a user's subscription
		add_action( 'scheduled_subscription_expiration', __CLASS__ . '::expire_subscription', 10, 2 );

		// Subscription Trial End
		add_action( 'scheduled_subscription_trial_end', __CLASS__ . '::subscription_trial_end', 0, 2 );

		// Make sure a scheduled subscription payment is never fired repeatedly to safeguard against WP-Cron inifinite loop bugs
		add_action( 'scheduled_subscription_payment', __CLASS__ . '::safeguard_scheduled_payments', 0, 2 );

		// Reschedule payments after the most recent payment has been made (and should have been recorded against the subscription)
		add_action( 'scheduled_subscription_payment', __CLASS__ . '::reschedule_subscription_payment', 100, 2 );

		// Order is trashed, trash subscription
		add_action( 'wp_trash_post', __CLASS__ . '::maybe_trash_subscription', 10 );
	}

	/**
	 * Marks a single subscription as active on a users account.
	 *
	 * @param $user_id int The id of the user whose subscription is to be activated.
	 * @param $subscription_key string A subscription key of the form created by @see self::get_subscription_key()
	 * @since 1.0
	 */
	public static function activate_subscription( $user_id, $subscription_key ) {

		$subscription = self::get_users_subscription( $user_id, $subscription_key );

		if ( empty( $subscription ) || $subscription['status'] == 'active' )
			return false;

		$order = new WC_Order( $subscription['order_id'] );

		if ( $subscription['status'] != 'pending' && ! self::can_subscription_be_changed_to( 'active', $subscription_key, $user_id ) ) {

		 	$order->add_order_note( sprintf( __( 'Unable to activate subscription %s.', WC_Subscriptions::$text_domain ), $subscription_key ) );

			do_action( 'unable_to_activate_subscription', $user_id, $subscription_key );

		} else {

			// Mark subscription as active
			$users_subscriptions = self::update_users_subscriptions( $user_id, array( $subscription_key => array( 'status' => 'active', 'end_date' => 0 ) ) );

			// Make sure subscriber is marked as a "Paying Customer"
			self::mark_paying_customer( $subscription['order_id'] );

			// Assign default subscriber role to user
			self::update_users_role( $user_id, 'default_subscriber_role' );

			// Log activation on order
			$order->add_order_note( sprintf( __( 'Subscription %s activated.', WC_Subscriptions::$text_domain ), $subscription_key ) );

			// Schedule expiration & payment hooks
			$hook_args = array( 'user_id' => (int)$user_id, 'subscription_key' => $subscription_key );
			wp_schedule_single_event( strtotime( WC_Subscriptions_Order::get_next_payment_date( $order, $subscription['product_id'] ) ), 'scheduled_subscription_payment', $hook_args );

			if ( $subscription['expiry_date'] != 0 && strtotime( $subscription['expiry_date'] ) > time() )
				wp_schedule_single_event( strtotime( $subscription['expiry_date'] ), 'scheduled_subscription_expiration', $hook_args );

			if ( isset( $subscription['trial_expiry_date'] ) && $subscription['trial_expiry_date'] != 0 && strtotime( $subscription['trial_expiry_date'] ) > time() )
				wp_schedule_single_event( strtotime( $subscription['trial_expiry_date'] ), 'scheduled_subscription_trial_end', $hook_args );

			do_action( 'activated_subscription', $user_id, $subscription_key );

		}

	}

	/**
	 * Changes a single subscription from suspended to active on a users account.
	 *
	 * @param $user_id int The id of the user whose subscription is to be activated.
	 * @param $subscription_key string A subscription key of the form created by @see self::get_subscription_key()
	 * @since 1.0
	 */
	public static function reactivate_subscription( $user_id, $subscription_key ) {

		self::activate_subscription( $user_id, $subscription_key );

		do_action( 'reactivated_subscription', $user_id, $subscription_key );
	}

	/**
	 * Suspends a single subscription on a users account.
	 *
	 * @param $user_id int The id of the user whose subscription should be suspended.
	 * @param $subscription_key string A subscription key of the form created by @see self::get_subscription_key()
	 * @since 1.0
	 */
	public static function suspend_subscription( $user_id, $subscription_key ) {

		$subscription = self::get_users_subscription( $user_id, $subscription_key );

		if ( empty( $subscription ) || $subscription['status'] == 'suspended' )
			return false;

		$order = new WC_Order( $subscription['order_id'] );

		if ( ! self::can_subscription_be_changed_to( 'suspended', $subscription_key, $user_id ) ) {

		 	$order->add_order_note( sprintf( __( 'Unable to suspend subscription %s. Subscription status can not be changed to suspended.', WC_Subscriptions::$text_domain ), $subscription_key ) );

			do_action( 'unable_to_suspend_subscription', $user_id, $subscription_key );

		} else {

			// Mark subscription as suspended
			$users_subscriptions = self::update_users_subscriptions( $user_id, array( $subscription_key => array( 'status' => 'suspended' ) ) );

			// Clear hooks
			$hook_args = array( 'user_id' => (int)$user_id, 'subscription_key' => $subscription_key );
			wp_clear_scheduled_hook( 'scheduled_subscription_expiration', $hook_args );
			wp_clear_scheduled_hook( 'scheduled_subscription_payment', $hook_args );
			wp_clear_scheduled_hook( 'scheduled_subscription_trial_end', $hook_args );

			// Unset subscriber as a "Paying Customer"
			self::mark_not_paying_customer( $subscription['order_id'] );

			// Assign default cancelled subscriber role to user
			self::update_users_role( $user_id, 'default_cancelled_role' );

			// Log cancellation on order
			$order->add_order_note( sprintf( __( 'Subscription %s suspended.', WC_Subscriptions::$text_domain ), $subscription_key ) );

			do_action( 'suspended_subscription', $user_id, $subscription_key );
		}
	}

	/**
	 * Cancels a single subscription on a users account.
	 *
	 * @param $user_id int The id of the user whose subscription should be cancelled.
	 * @param $subscription_key string A subscription key of the form created by @see self::get_subscription_key()
	 * @since 1.0
	 */
	public static function cancel_subscription( $user_id, $subscription_key ) {

		$subscription = self::get_users_subscription( $user_id, $subscription_key );

		if ( empty( $subscription ) || $subscription['status'] == 'cancelled' )
			return false;

		$order = new WC_Order( $subscription['order_id'] );

		if ( ! self::can_subscription_be_changed_to( 'cancelled', $subscription_key, $user_id ) ) {

		 	$order->add_order_note( sprintf( __( 'Unable to cancel subscription %s. Subscription status can not be changed to cancelled.', WC_Subscriptions::$text_domain ), $subscription_key ) );

			do_action( 'unable_to_cancel_subscription', $user_id, $subscription_key );

		} else {

			// Mark subscription as cancelled
			$users_subscriptions = self::update_users_subscriptions( $user_id, array( $subscription_key => array( 'status' => 'cancelled', 'end_date' => date( 'Y-m-d H:i:s' ) ) ) );

			// Clear scheduled expiration and payment hooks
			$hook_args = array( 'user_id' => (int)$user_id, 'subscription_key' => $subscription_key );
			wp_clear_scheduled_hook( 'scheduled_subscription_expiration', $hook_args );
			wp_clear_scheduled_hook( 'scheduled_subscription_payment', $hook_args );
			wp_clear_scheduled_hook( 'scheduled_subscription_trial_end', $hook_args );

			// Unset subscriber as a "Paying Customer"
			self::mark_not_paying_customer( $subscription['order_id'] );

			// Assign default cancelled subscriber role to user
			self::update_users_role( $user_id, 'default_cancelled_role' );

			// Log cancellation on order
			$order->add_order_note( sprintf( __( 'Subscription %s cancelled.', WC_Subscriptions::$text_domain ), $subscription_key ) );

			do_action( 'cancelled_subscription', $user_id, $subscription_key );

		}
	}

	/**
	 * Cancels a single subscription on a users account.
	 *
	 * @param $user_id int The id of the user whose subscription should be cancelled.
	 * @param $subscription_key string A subscription key of the form created by @see self::get_subscription_key()
	 * @since 1.0
	 */
	public static function failed_subscription_signup( $user_id, $subscription_key ) {

		$subscription = self::get_users_subscription( $user_id, $subscription_key );

		if ( empty( $subscription ) || $subscription['status'] == 'failed' )
			return false;

		// Run all cancellation related functions on the subscription
		if ( $subscription['status'] == 'active' )
			self::cancel_subscription( $user_id, $subscription_key );

		// Log failure on order
		$order = new WC_Order( $subscription['order_id'] );
		$order->add_order_note( sprintf( __( 'Subscription %s sign-up failed.', WC_Subscriptions::$text_domain ), $subscription_key ) );

		$users_subscriptions = self::update_users_subscriptions( $user_id, array( $subscription_key => array( 'status' => 'failed' ) ) );

		do_action( 'subscription_sign_up_failed', $user_id, $subscription_key );

	}

	/**
	 * Cancels a single subscription on a users account.
	 *
	 * @param $user_id int The id of the user whose subscription should be cancelled.
	 * @param $subscription_key string A subscription key of the form created by @see self::get_subscription_key()
	 * @since 1.0
	 */
	public static function trash_subscription( $user_id, $subscription_key ) {

		$subscription = self::get_users_subscription( $user_id, $subscription_key );

		if ( empty( $subscription ) || $subscription['status'] == 'trash' )
			return false;

		$order = new WC_Order( $subscription['order_id'] );

		if ( ! self::can_subscription_be_changed_to( 'trash', $subscription_key, $user_id ) ) {

		 	$order->add_order_note( sprintf( __( 'Unable to trash subscription %s. Subscription status can not be moved to the trash.', WC_Subscriptions::$text_domain ), $subscription_key ) );

			do_action( 'unable_to_trash_subscription', $user_id, $subscription_key );

		} else {

			// Run all cancellation related functions on the subscription
			if ( $subscription['status'] != 'cancelled' )
				self::cancel_subscription( $user_id, $subscription_key );

			// Log deletion on order
			$order->add_order_note( sprintf( __( 'Subscription %s sent to trash.', WC_Subscriptions::$text_domain ), $subscription_key ) );

			$users_subscriptions = self::update_users_subscriptions( $user_id, array( $subscription_key => array( 'status' => 'trash' ) ) );

			do_action( 'subscription_trashed', $user_id, $subscription_key );
		}
	}

	/**
	 * Expires a single subscription on a users account.
	 *
	 * @param $user_id int The id of the user who owns the expiring subscription. 
	 * @param $subscription_key string A subscription key of the form created by @see self::get_subscription_key()
	 * @since 1.0
	 */
	public static function expire_subscription( $user_id, $subscription_key ) {

		$subscription = self::get_users_subscription( $user_id, $subscription_key );

		// Don't expire an already expired, cancelled or trashed subscription
		if ( empty( $subscription ) || in_array( $subscription['status'], array( 'expired', 'cancelled', 'trash' ) ) )
			return;

		$users_subscriptions = self::update_users_subscriptions( $user_id, array( $subscription_key => array( 'status' => 'expired', 'end_date' => date( 'Y-m-d H:i:s' ) ) ) );

		// Unset subscriber as a "Paying Customer"
		self::mark_not_paying_customer( $subscription['order_id'] );

		// Assign default inactive subscriber role to user
		self::update_users_role( $user_id, 'default_cancelled_role' );

		// Clear any lingering expiration and payment hooks
		$hook_args = array( 'user_id' => (int)$user_id, 'subscription_key' => $subscription_key );
		wp_clear_scheduled_hook( 'scheduled_subscription_expiration', $hook_args );
		wp_clear_scheduled_hook( 'scheduled_subscription_payment', $hook_args );

		// Log expiration on order
		$order = new WC_Order( $subscription['order_id'] );
		$order->add_order_note( sprintf( __( 'Subscription %s expired.', WC_Subscriptions::$text_domain ), $subscription_key ) );

		do_action( 'subscription_expired', $user_id, $subscription_key );
	}

	/**
	 * Fires when the trial period for a subscription has completed.
	 *
	 * @param $user_id int The id of the user who owns the expiring subscription. 
	 * @param $subscription_key string A subscription key of the form created by @see self::get_subscription_key()
	 * @since 1.0
	 */
	public static function subscription_trial_end( $user_id, $subscription_key ) {
		do_action( 'subscription_trial_end', $user_id, $subscription_key );
	}

	/**
	 * Records a payment on a subscription.
	 *
	 * @param $user_id int The id of the user who owns the subscription. 
	 * @param $subscription_key string A subscription key of the form obtained by @see get_subscription_key( $order_id, $product_id )
	 * @since 1.0
	 */
	public static function process_subscription_payment( $user_id, $subscription_key ) {

		// Store a record of the subscription payment date
		$subscription = self::get_users_subscription( $user_id, $subscription_key );
		$subscription['completed_payments'][] = date( 'Y-m-d H:i:s' );
		$subscription['failed_payments'] = 0; // Reset failed payment count

		self::update_users_subscriptions( $user_id, array( $subscription_key => $subscription ) );

		// Make sure subscriber is marked as a "Paying Customer"
		self::mark_paying_customer( $subscription['order_id'] );

		// Make sure subscriber has default role
		self::update_users_role( $user_id, 'default_subscriber_role' );

		// Log payment on order
		$order = new WC_Order( $subscription['order_id'] );
		$order->add_order_note( sprintf( __( 'Payment received for subscription %s', WC_Subscriptions::$text_domain ), $subscription_key ) );

		do_action( 'processed_subscription_payment', $user_id, $subscription_key );
	}

	/**
	 * Expires a single subscription on a users account.
	 *
	 * @param $user_id int The id of the user who owns the expiring subscription. 
	 * @param $subscription_key string A subscription key of the form obtained by @see get_subscription_key( $order_id, $product_id )
	 * @since 1.0
	 */
	public static function process_subscription_payment_failure( $user_id, $subscription_key ) {

		// Store a record of the subscription payment date
		$subscription = self::get_users_subscription( $user_id, $subscription_key );

		$subscription['failed_payments'] = $subscription['failed_payments']++;

		self::update_users_subscriptions( $user_id, array( $subscription_key => $subscription ) );

		$order = new WC_Order( $subscription['order_id'] );

		// We've reached the maximum failed payments allowed on the subscription
		if ( $subscription['failed_payments'] >= get_option( WC_Subscriptions_Admin::$option_prefix . '_max_failed_payments' ) ) {

			self::cancel_subscription( $user_id, $subscription_key );

			$order->cancel_order( __( 'Maximum number of failed payments reached.', WC_Subscriptions::$text_domain ) );

			if ( 'yes' == get_option( WC_Subscriptions_Admin::$option_prefix . '_generate_renewal_order' ) )
				WC_Subscriptions_Order::generate_renewal_order( $subscription['order_id'], $subscription['product_id'] );

		} else {

			// Log payment failure on order
			$order->add_order_note( sprintf( __( 'Payment failed for subscription %s', WC_Subscriptions::$text_domain ), $subscription_key ) );

		}

		do_action( 'processed_subscription_payment_failure', $user_id, $subscription_key );
	}

	/**
	 * This function should be called whenever a subscription payment has been made.
	 * 
	 * This includes when the subscriber signs up and for each recurring payment. 
	 *
	 * @param $order WC_Order|int The order or ID of the order for which subscription payments should be marked against.
	 * @since 1.0
	 */
	public static function process_subscription_payments_on_order( $order, $product_id = '' ) {

		if ( ! is_object( $order ) )
			$order = new WC_Order( $order );

		if ( empty( $product_id ) ) {
			$order_items = $order->get_items();
			$product_id = $order_items[0]['id'];
		}

		if ( WC_Subscriptions_Order::order_contains_subscription( $order ) && WC_Subscriptions_Product::is_subscription( $product_id ) ) {

			self::process_subscription_payment( $order->customer_user, self::get_subscription_key( $order->id, $product_id ) );

			do_action( 'processed_subscription_payments_for_order', $order );
		}
	}

	/**
	 * This function should be called whenever a subscription payment has been made.
	 * 
	 * This includes when the subscriber signs up and for each recurring payment. 
	 *
	 * @param $order WC_Order|int The order or ID of the order for which subscription payments should be marked against.
	 * @since 1.0
	 */
	public static function process_subscription_payment_failure_on_order( $order, $product_id = '' ) {

		if ( ! is_object( $order ) )
			$order = new WC_Order( $order );

		if ( empty( $product_id ) ) {
			$order_items = $order->get_items();
			$product_id = $order_items[0]['id'];
		}

		if ( WC_Subscriptions_Order::order_contains_subscription( $order ) && WC_Subscriptions_Product::is_subscription( $product_id ) ) {

			self::process_subscription_payment_failure( $order->customer_user, self::get_subscription_key( $order->id, $product_id ) );

			do_action( 'processed_subscription_payments_for_order', $order );
		}
	}

	/**
	 * Activates all the subscription products in an order.
	 *
	 * @param $order WC_Order|int The order or ID of the order for which subscriptions should be marked as activated.
	 * @since 1.0
	 */
	public static function activate_subscriptions_for_order( $order ) {

		// Update subscription in User's account, calls self::activate_subscription
		self::update_users_subscriptions_for_order( $order, 'active' );

		do_action( 'subscriptions_activated_for_order', $order );
	}

	/**
	 * Mark all subscriptions in an order as cancelled on the user's account.
	 *
	 * @param $order WC_Order|int The order or ID of the order for which subscriptions should be marked as cancelled.
	 * @since 1.0
	 */
	public static function cancel_subscriptions_for_order( $order ) {

		// Update subscription in User's account, calls self::cancel_subscription for each subscription
		self::update_users_subscriptions_for_order( $order, 'cancelled' );

		do_action( 'subscriptions_cancelled_for_order', $order );
	}

	/**
	 * Marks all the subscriptions in an order as expired 
	 *
	 * @param $order WC_Order|int The order or ID of the order for which subscriptions should be marked as expired.
	 * @since 1.0
	 */
	public static function expire_subscriptions_for_order( $order ) {

		// Update subscription in User's account, calls self::expire_subscription
		self::update_users_subscriptions_for_order( $order, 'expired' );

		do_action( 'subscriptions_expired_for_order', $order );
	}

	/**
	 * Called when a sign up fails during the payment processing step.
	 *
	 * @param $order WC_Order | int The order or ID of the order for which subscriptions should be marked as failed.
	 * @since 1.0
	 */
	public static function failed_subscription_sign_ups_for_order( $order ) {

		if ( ! is_object( $order ) )
			$order = new WC_Order( $order );

		// Set subscription status to failed and log failure
		$order->update_status( 'failed', __( 'Subscription sign up failed.', WC_Subscriptions::$text_domain ) );

		self::mark_not_paying_customer( $order );

		// Update subscription in User's account
		self::update_users_subscriptions_for_order( $order, 'failed' );

		do_action( 'failed_subscription_sign_ups_for_order', $order );
	}

	/**
	 * Uses the details of an order to create a pending subscription on the customers account
	 * for a subscription product, as specified with $product_id.
	 *
	 * @param $order mixed int | WC_Order The order ID or WC_Order object to create the subscription from.
	 * @param product_id int The ID of the subscription product on the order.
	 * @since 1.1
	 */
	public static function create_pending_subscription_for_order( $order, $product_id ) {

		if ( ! is_object( $order ) )
			$order = new WC_Order( $order );

		if ( ! WC_Subscriptions_Product::is_subscription( $product_id ) )
			return;

		$subscription_key = self::get_subscription_key( $order->id, $product_id );

		// In case the subscription exists already
		$subscription = self::get_users_subscription( $order->customer_user, $subscription_key );

		// Adding a new subscription so set the start date/time to now
		$start_date = ( isset( $subscription['start_date'] ) ) ? $subscription['start_date'] : date( 'Y-m-d H:i:s' );

		// Adding a new subscription so set the expiry date/time from the order date
		$expiration = ( isset( $subscription['expiry_date'] ) ) ? $subscription['expiry_date'] : WC_Subscriptions_Product::get_expiration_date( $product_id, $order->order_date );

		// Adding a new subscription so set the expiry date/time from the order date
		$trial_expiration = ( isset( $subscription['trial_expiry_date'] ) ) ? $subscription['trial_expiry_date'] : WC_Subscriptions_Product::get_trial_expiration_date( $product_id, $order->order_date );

		$failed_payments = ( isset( $subscription['failed_payments'] ) ) ? $subscription['failed_payments'] : 0;
		$completed_payments = ( isset( $subscription['completed_payments'] ) ) ? $subscription['completed_payments'] : array();

		$subscriptions[$subscription_key] = array(
			'product_id'         => $product_id,
			'order_key'          => $order->order_key,
			'order_id'           => $order->id,
			'start_date'         => $start_date,
			'expiry_date'        => $expiration,
			'end_date'           => 0,
			'status'             => 'pending',
			'trial_expiry_date'  => $trial_expiration,
			'failed_payments'    => $failed_payments,
			'completed_payments' => $completed_payments
		);

		self::update_users_subscriptions( $order->customer_user, $subscriptions );

		// Set subscription status to active and log activation
		$order->add_order_note( sprintf( __( 'Pending subscription created for product %s.', WC_Subscriptions::$text_domain ), $product_id ) );

		do_action( 'pending_subscription_created_for_order', $order, $product_id );

	}

	/**
	 * Creates subscriptions against a users account with a status of pending when a user creates
	 * an order containing subscriptions.
	 *
	 * @param $order_id int The ID of the order for which subscriptions should be created.
	 * @since 1.0
	 */
	public static function process_subscriptions_on_checkout( $order_id ) {

		$order = new WC_Order( $order_id );

		// Clear any subscriptions on this order to prevent duplicate "Pending" subscriptions on an order
		self::clear_users_subscriptions_from_order( $order );

		if ( WC_Subscriptions_Order::order_contains_subscription( $order ) ) {

			// Update subscription in User's account
			self::update_users_subscriptions_for_order( $order, 'pending' );

			$order->add_order_note( __( 'Pending subscriptions created at checkout.', WC_Subscriptions::$text_domain ) );

			do_action( 'subscriptions_created_for_order', $order );
		}
	}

	/**
	 * Updates a user's subscriptions for each subscription product in the order.
	 *
	 * @param $order WC_Order The order to get subscriptions and user details from.
	 * @param $status String (optional) A status to change the subscriptions in an order to. Default is 'active'.
	 * @since 1.0
	 */
	public static function update_users_subscriptions_for_order( $order, $status = 'pending' ) {

		if ( ! is_object( $order ) )
			$order = new WC_Order( $order );

		foreach( $order->get_items() as $order_item ) {

			if ( ! WC_Subscriptions_Product::is_subscription( $order_item['id'] ) )
				continue;

			$subscription_key = self::get_subscription_key( $order->id, $order_item['id'] );

			$subscription = self::get_users_subscription( $order->customer_user, $subscription_key );

			switch ( $status ) {
				case 'active' :
					self::activate_subscription( $order->user_id, $subscription_key );
					break;
				case 'cancelled' :
					self::cancel_subscription( $order->user_id, $subscription_key );
					break;
				case 'expired' :
					self::expire_subscription( $order->user_id, $subscription_key );
					break;
				case 'failed' :
					self::failed_subscription_signup( $order->user_id, $subscription_key );
					break;
				case 'pending' :
				default :
					self::create_pending_subscription_for_order( $order, $order_item['id'] );
					break;
			}
		}

		do_action( 'updated_users_subscriptions_for_order', $order, $status );
	}

	/**
	 * Takes a user ID and array of subscription details and updates the users subscription details accordingly. 
	 * 
	 * @uses wp_parse_args To allow only part of a subscription's details to be updated, like status.
	 * @param $user_id int The ID of the user for whom subscription details should be updated
	 * @param $new_subscription_details array An array of arrays with a subscription key and corresponding 'detail' => 'value' pair. Should take the form:
	 *        'product_id'          The Product/Post ID of the subscription
	 *        'order_key'           The hash key of the order in which the subscription was purchased
	 *        'order_id'            The id of the order in which the subscription was purchased
	 *        'start_date'          The date the subscription was activated
	 *        'expiry_date'         The date the subscription expires or expired, false if the subscription will never expire
	 *        'end_date'            The date the subscription ended, false if the subscription has not yet ended
	 *        'status'              Subscription status can be: cancelled, active, expired or failed
	 *        'completed_payments'  An array of MYSQL formatted dates for all payments that have been made on the subscription
	 * @since 1.0
	 */
	public static function update_users_subscriptions( $user_id, $new_subscription_details ) {

		$subscriptions = self::get_users_subscriptions( $user_id );

		$subscriptions = self::array_merge_recursive_for_real( $subscriptions, $new_subscription_details );

		update_user_option( $user_id, self::$users_meta_key, $subscriptions );

		do_action( 'updated_users_subscriptions', $user_id, $new_subscription_details );

		return $subscriptions;
	}

	/**
	 * Clear all subscriptions from a user's account for a given order.
	 *
	 * @param $order WC_Order The order for which subscriptions should be cleared.
	 * @since 1.0
	 */
	public static function clear_users_subscriptions_from_order( $order ) {

		if ( ! is_object( $order ) )
			$order = new WC_Order( $order );

		$subscriptions = self::get_users_subscriptions( $order->user_id );

		foreach ( $subscriptions as $subscription_key => $subscription_details ) {
			if ( $subscription_details['order_id'] == $order->id )
				unset( $subscriptions[$subscription_key] );
		}

		update_user_option( $order->user_id, self::$users_meta_key, $subscriptions );

		do_action( 'cleared_users_subscriptions_from_order', $order );
	}

	/**
	 * Clear all subscriptions from a user's account for a given order.
	 *
	 * @param $order WC_Order The order for which subscriptions should be cleared.
	 * @since 1.0
	 */
	public static function maybe_trash_subscription( $order ) {

		if ( ! is_object( $order ) )
			$order = new WC_Order( $order );

		if ( WC_Subscriptions_Order::order_contains_subscription( $order ) ) {
			foreach( $order->get_items() as $order_item ) {
				if ( WC_Subscriptions_Product::is_subscription( $order_item['id'] ) ) {
					self::trash_subscription( $order->customer_user, self::get_subscription_key( $order->id, $order_item['id'] ) );
				}
			}
		}

	}

	/**
	 * Check if a given subscription can be changed to a given a status. 
	 * 
	 * The function checks the subscription's current status and if the payment gateway used to purchase the
	 * subscription allows for the given status to be set via its API. 
	 * 
	 * @param $changed_to_status string The status you want to change th subscription to.
	 * @param $subscription_key string A subscription key of the form created by @see self::get_subscription_key()
	 * @param $user_id int The ID of the user who owns the subscriptions. Although this parameter is optional, if you have the User ID you should pass it to improve performance.
	 * @since 1.0
	 */
	public static function can_subscription_be_changed_to( $new_status, $subscription_key, $user_id = '' ) {
		global $woocommerce;

		$subscription = array();

		if ( ! empty( $user_id ) ) {
			$subscription = self::get_users_subscription( $user_id, $subscription_key );
		} else {
			$users_subscriptions = self::get_all_users_subscriptions();

			foreach( $users_subscriptions as $user_id => $users_subscription ) {
				if ( isset( $users_subscription[$subscription_key] ) ) {
					$subscription = $users_subscription[$subscription_key];
					break;
				}
			}
		}

		if ( empty( $subscription ) ) {
			$subscription_can_be_changed = false;
		} else {

			$order = new WC_Order( $subscription['order_id'] );

			$payment_gateways = $woocommerce->payment_gateways->payment_gateways();

			$payment_gateway  = isset( $payment_gateways[$order->payment_method] ) ? $payment_gateways[$order->payment_method] : '';

			switch( $new_status ) {
				case 'active' :
					if ( ( empty( $payment_gateway ) || $payment_gateway->supports( 'subscription_reactivation' ) ) && $subscription['status'] == 'suspended' && $subscription['status'] != 'active' )
						$subscription_can_be_changed = true;
					elseif ( $subscription['status'] == 'pending' )
						$subscription_can_be_changed = true;
					else
						$subscription_can_be_changed = false;
					break;
				case 'suspended' :
					if ( ( empty( $payment_gateway ) || $payment_gateway->supports( 'subscription_suspension' ) ) && $subscription['status'] == 'active' && $subscription['status'] != 'suspended' )
						$subscription_can_be_changed = true;
					else
						$subscription_can_be_changed = false;
					break;
				case 'cancelled' :
					if ( ( empty( $payment_gateway ) || $payment_gateway->supports( 'subscription_cancellation' ) ) && ! in_array( $subscription['status'], array( 'cancelled', 'expired', 'trash' ) ) )
						$subscription_can_be_changed = true;
					else
						$subscription_can_be_changed = false;
					break;
				case 'expired' :
					if ( ! in_array( $subscription['status'], array( 'cancelled', 'trash' ) ) )
						$subscription_can_be_changed = true;
					else
						$subscription_can_be_changed = false;
					break;
				case 'trash' :
					if ( in_array( $subscription['status'], array( 'cancelled', 'expired' ) ) || self::can_subscription_be_changed_to( 'cancelled', $subscription_key, $user_id ) )
						$subscription_can_be_changed = true;
					else
						$subscription_can_be_changed = false;
					break;
				case 'failed' :
					$subscription_can_be_changed = false;
					break;
				default :
					$subscription_can_be_changed = false;
					break;
			}
		}

		return apply_filters( 'woocommerce_subscription_can_be_changed_to_' . $new_status, $subscription_can_be_changed, $subscription, $order );
	}

	/*
	 * Subscription Property functions
	 */

	/**
	 * Return an associative array of a given subscriptions details (if it exists).
	 *
	 * @param $subscription_key string A subscription key of the form created by @see self::get_subscription_key()
	 * @param $user_id int The ID of the user who owns the subscriptions. Although this parameter is optional, if you have the User ID you should pass it to improve performance.
	 * @return array Subscription details
	 * @since 1.1
	 */
	public static function get_subscription( $subscription_key, $user_id = '' ) {

		$subscription = array();

		if ( ! empty( $user_id ) ) {
			$subscription = self::get_users_subscription( $user_id, $subscription_key );
		} else {
			$users_subscriptions = self::get_all_users_subscriptions();

			foreach( $users_subscriptions as $user_id => $users_subscription ) {
				if ( isset( $users_subscription[$subscription_key] ) ) {
					$subscription = $users_subscription[$subscription_key];
					continue;
				}
			}
		}

		return apply_filters( 'woocommerce_get_subscription', $subscription, $subscription_key, $user_id );
	}

	/**
	 * Return an i18n'ified associative array of all possible subscription periods.
	 *
	 * @since 1.1
	 */
	public static function get_subscription_period_strings( $number = 1, $period = '' ) {

		$translated_periods = apply_filters( 'woocommerce_subscription_periods',
			array(
				'day'   => sprintf( _n( 'day', '%s days', $number, WC_Subscriptions::$text_domain ), $number ),
				'week'  => sprintf( _n( 'week', '%s weeks', $number, WC_Subscriptions::$text_domain ), $number ),
				'month' => sprintf( _n( 'month', '%s months', $number, WC_Subscriptions::$text_domain ), $number ),
				'year'  => sprintf( _n( 'year', '%s years', $number, WC_Subscriptions::$text_domain ), $number )
			)
		);

		return ( ! empty( $period ) ) ? $translated_periods[$period] : $translated_periods;
	}

	/**
	 * Return an i18n'ified associative array of all possible subscription periods.
	 *
	 * @since 1.0
	 */
	public static function get_subscription_period_interval_strings( $interval = '' ) {

		$intervals = array( 1 => __( 'per', WC_Subscriptions::$text_domain ) );

		foreach ( range( 2, 6 ) as $i )
			$intervals[$i] = sprintf( __( 'every %s', WC_Subscriptions::$text_domain ), WC_Subscriptions::append_numeral_suffix( $i )  );

		$intervals = apply_filters( 'woocommerce_subscription_period_interval_strings', $intervals );

		if ( empty( $interval ) )
			return $intervals;
		else
			return $intervals[$interval];
	}

	/**
	 * Returns an array of subscription lengths. 
	 * 
	 * PayPal Standard Allowable Ranges
	 * D – for days; allowable range is 1 to 90
	 * W – for weeks; allowable range is 1 to 52
	 * M – for months; allowable range is 1 to 24
	 * Y – for years; allowable range is 1 to 5
	 * 
	 * @param subscription_period string (optional) One of day, week, month or year. If empty, all subscription ranges are returned.
	 * @since 1.0
	 */
	public static function get_subscription_ranges( $subscription_period = '' ) {

		$subscription_periods = self::get_subscription_period_strings();

		foreach ( array( 'day', 'week', 'month', 'year' ) as $period ) {

			$subscription_lengths = array( 
				__( 'all time', WC_Subscriptions::$text_domain ),
				sprintf( __( '%s %s', WC_Subscriptions::$text_domain ), 1, $subscription_periods[$period] ),
			);

			switch( $period ) {
				case 'day':
					$subscription_range = range( 2, 90 );
					break;
				case 'week':
					$subscription_range = range( 2, 52 );
					break;
				case 'month':
					$subscription_range = range( 2, 24 );
					break;
				case 'year':
					$subscription_range = range( 2, 5 );
					break;
			}

			foreach ( $subscription_range as $number )
				$subscription_range[$number] = self::get_subscription_period_strings( $number, $period );

			// Add the possible range to all time range
			$subscription_lengths += $subscription_range;

			$subscription_ranges[$period] = $subscription_lengths;
		}

		if ( ! empty( $subscription_period ) )
			return $subscription_ranges[$subscription_period];
		else
			return $subscription_ranges;
	}

	/**
	 * Returns an array of allowable trial periods. 
	 * 
	 * @see self::get_subscription_ranges()
	 * @param subscription_period string (optional) One of day, week, month or year. If empty, all subscription ranges are returned.
	 * @since 1.1
	 */
	public static function get_subscription_trial_lengths( $subscription_period = '' ) {

		$all_trial_periods = self::get_subscription_ranges();

		foreach ( $all_trial_periods as $period => $trial_periods )
			$all_trial_periods[$period][0] = __( 'no', WC_Subscriptions::$text_domain ); // "No Trial Period"

		if ( ! empty( $subscription_period ) )
			return $all_trial_periods[$subscription_period];
		else
			return $all_trial_periods;
	}

	/**
	 * Returns the string key for a subscription purchased in an order specified by $order_id
	 * 
	 * @param order_id int The ID of the order in which the subscription was purchased. 
	 * @param product_id int The ID of the subscription product.
	 * @return string The key representing the given subscription.
	 * @since 1.0
	 */
	public static function get_subscription_key( $order_id, $product_id = '' ) {

		if ( empty( $product_id ) ) {
			$order       = new WC_Order( $order_id );
			$order_items = $order->get_items();
			$product_id  = $order_items[0]['id'];
		}

		$subscription_key = $order_id . '_' . $product_id;

		return apply_filters( 'woocommerce_subscription_key', $subscription_key, $order_id, $product_id );
	}

	/**
	 * Returns the number of failed payments for a given subscription.
	 * 
	 * @param $subscription_key string A subscription key of the form created by @see self::get_subscription_key()
	 * @param $user_id int The ID of the user who owns the subscriptions. Although this parameter is optional, if you have the User ID you should pass it to improve performance.
	 * @return int The number of outstanding failed payments on the subscription, if any.
	 * @since 1.0
	 */
	public static function get_subscriptions_failed_payment_count( $subscription_key, $user_id = '' ) {

		$subscription = self::get_users_subscription( $user_id, $subscription_key );

		if ( ! isset( $subscription['failed_payments'] ) )
			$subscription['failed_payments'] = 0;

		return apply_filters( 'woocommerce_subscription_failed_payment_count', $subscription['failed_payments'], $user_id, $subscription_key );
	}

	/**
	 * Takes a subscription key and returns the date on which the subscription is scheduled to expire 
	 * or 0 if it is cancelled, expired, or never going to expire.
	 * 
	 * @param $subscription_key string A subscription key of the form created by @see self::get_subscription_key()
	 * @param $user_id int The ID of the user who owns the subscriptions. Although this parameter is optional, if you have the User ID you should pass it to improve performance.
	 * @since 1.1
	 */
	public static function get_subscription_expiration_date( $subscription_key, $user_id = '' ) {

		$subscription = self::get_subscription( $subscription_key, $user_id );

		if ( empty( $subscription ) ) {

			$expiration_date = 0;

		} else {

			$order = new WC_Order( $subscription['order_id'] );

			$subscription_period = WC_Subscriptions_Order::get_subscription_period( $order, $subscription['product_id'] );
			$subscription_length = WC_Subscriptions_Order::get_subscription_length( $order, $subscription['product_id'] );

			if ( $subscription_length > 0 ){

				$order_date = ( empty( $order->order_date ) ) ? date( 'Y-m-d H:i:s' ) : $order->order_date;

				$expiration_date = date( 'Y-m-d H:i:s', strtotime( "$order_date + $subscription_length {$subscription_period}s" ) );

			} else {

				$expiration_date = 0;

			}
		}

		return apply_filters( 'woocommerce_subscription_expiration_date' , $expiration_date, $subscription_key, $user_id );
	}

	/*
	 * User API Functions
	 */

	/**
	 * Check if a user has a subscription, optionally specified with $product_id.
	 * 
	 * @param $user_id int (optional) The id of the user whose subscriptions you want. Defaults to the currently logged in user.
	 * @param product_id int The ID of a subscription product.
	 * @return bool True if the user has the subscription (or any subscription if no subscription specified), otherwise false.
	 */
	public static function user_has_subscription( $user_id = '', $product_id = '' ) {
		$subscriptions = self::get_users_subscriptions( $user_id );

		$has_subscription = false;

		if ( empty( $product_id ) ) { // Any subscription

			if ( ! empty( $subscriptions ) )
				$has_subscription = true;

		} else {

			foreach ( $subscriptions as $subscription ) {
				if ( $subscription['product_id'] == $product_id ) {
					$has_subscription = true;
					continue;
				}
			}

		}

		return apply_filters( 'woocommerce_user_has_subscription', $has_subscription, $user_id, $product_id );
	}

	/**
	 * Gets all the active and inactive subscriptions for all users.
	 * 
	 * @return array An associative array containing all users with subscriptions and the details of their subscriptions: 'user_id' => $subscriptions
	 * @since 1.0
	 */
	public static function get_all_users_subscriptions() {
		global $wpdb;

		$users_and_subscriptions = array();

		$users_with_subscriptions = get_users( array( 'fields' => 'id', 'meta_key' => $wpdb->get_blog_prefix() . self::$users_meta_key ) );

		foreach( $users_with_subscriptions as $user_id )
			$users_and_subscriptions[$user_id] = self::get_users_subscriptions( $user_id );

		return apply_filters( 'woocommerce_all_users_subscriptions', $users_and_subscriptions );
	}

	/**
	 * Gets all the active and inactive subscriptions for a user, as specified by $user_id
	 *
	 * @param $user_id int (optional) The id of the user whose subscriptions you want. Defaults to the currently logged in user.
	 * @since 1.0
	 */
	public static function get_users_subscriptions( $user_id = 0 ) {
		global $wpdb;

		$subscriptions = get_user_option( $wpdb->get_blog_prefix() . self::$users_meta_key, $user_id ); // Prepending $wpdb->get_blog_prefix() to self::$users_meta_key circumvents the site agnostic fallback in WP

		if( empty( $subscriptions ) )
			$subscriptions = array();

		return apply_filters( 'woocommerce_users_subscriptions', $subscriptions, $user_id );
	}

	/**
	 * Gets a specific subscription for a user, as specified by $subscription_key
	 *
	 * @param $user_id int (optional) The id of the user whose subscriptions you want. Defaults to the currently logged in user.
	 * @param $subscription_key string A subscription key of the form created by @see self::get_subscription_key()
	 * @since 1.0
	 */
	public static function get_users_subscription( $user_id = '', $subscription_key ) {

		$subscriptions = self::get_users_subscriptions( $user_id );

		if( isset( $subscriptions[$subscription_key] ) )
			$subscription = $subscriptions[$subscription_key];
		else
			$subscription = array();

		return apply_filters( 'woocommerce_users_subscription', $subscription, $user_id, $subscription_key );
	}

	/**
	 * Gets all the subscriptions for a user that have been trashed, as specified by $user_id
	 *
	 * @param $user_id int (optional) The id of the user whose subscriptions you want. Defaults to the currently logged in user.
	 * @since 1.0
	 */
	public static function get_users_trashed_subscriptions( $user_id = '' ) {

		$subscriptions = self::get_users_subscriptions( $user_id );

		foreach ( $subscriptions as $key => $subscription )
			if ( $subscription['status'] != 'trash' )
				unset( $subscriptions[$key] );

		return apply_filters( 'woocommerce_users_trashed_subscriptions', $subscriptions, $user_id );
	}

	/**
	 * Removes a specific subscription for a user, as specified by $subscription_key
	 *
	 * @param $user_id int (optional) The id of the user whose subscriptions you want. Defaults to the currently logged in user.
	 * @param $subscription_key string A subscription key of the form created by @see self::get_subscription_key()
	 * @since 1.0
	 */
	public static function remove_users_subscription( $user_id, $subscription_key ) {

		$subscriptions = self::get_users_subscriptions( $user_id );

		$removed_subscription = array();

		if( isset( $subscriptions[$subscription_key] ) ) {
			$removed_subscription = $subscriptions[$subscription_key];
			unset( $subscriptions[$subscription_key] );
			update_user_option( $user_id, self::$users_meta_key, $subscriptions );
		}

		return apply_filters( 'woocommerce_removed_users_subscription', $removed_subscription, $user_id, $subscription_key );
	}

	/**
	 * A convenience wrapper for changing a users role. 
	 * 
	 * @param $user_id int The id of the user whose role should be changed
	 * @param $role_name string Either a WordPress role or one of the WCS keys: 'default_subscriber_role' or 'default_cancelled_role'
	 * @since 1.0
	 */
	public static function update_users_role( $user_id, $role_name ) {
		$user = new WP_User( $user_id );

		// Never change an admin's role to avoid locking out admins testing the plugin
		if ( ! empty( $user->roles ) && in_array( 'administrator', $user->roles ) )
			return;

		if ( $role_name == 'default_subscriber_role' )
			$role_name = get_option( WC_Subscriptions_Admin::$option_prefix . '_subscriber_role' );
		else if ( $role_name == 'default_cancelled_role' )
			$role_name = get_option( WC_Subscriptions_Admin::$option_prefix . '_cancelled_role' );

		$user->set_role( $role_name );

		do_action( 'woocommerce_subscriptions_updated_users_role', $role_name, $user );
	}

	/**
	 * Marks a customer as a paying customer when their subscription is activated.
	 * 
	 * A wrapper for the @see woocommerce_paying_customer() function.
	 * 
	 * @param $order_id int The id of the order for which customers should be pulled from and marked as paying. 
	 * @since 1.0
	 */
	public static function mark_paying_customer( $order_id ) {

		if ( is_object( $order_id ) )
			$order_id = $order_id->id;

		woocommerce_paying_customer( $order_id );
	}

	/**
	 * Unlike someone making a once-off payment, a subscriber can cease to be a paying customer. This function 
	 * changes a user's status to non-paying. 
	 * 
	 * @param $order object The order for which a customer ID should be pulled from and marked as paying.
	 * @since 1.0
	 */
	public static function mark_not_paying_customer( $order ) {

		if ( ! is_object( $order ) )
			$order = new WC_Order( $order );

		if ( $order->user_id > 0 )
			update_user_meta( $order->user_id, 'paying_customer', 0 );
	}

	/**
	 * Return a link for subscribers to change the status of their subscription, as specified with $status parameter
	 * 
	 * @param $subscription_key string A subscription key of the form created by @see self::get_subscription_key()
	 * @since 1.0
	 */
	public static function get_users_change_status_link( $subscription_key, $status ) {

		$action_link = add_query_arg( array( 'subscription_key' => $subscription_key, 'change_subscription_to' => $status ) );
		$action_link = wp_nonce_url( $action_link, $subscription_key );

		return apply_filters( 'woocommerce_subscriptions_users_action_link', $action_link, $subscription_key, $status );
	}

	/**
	 * Checks if the current request is by a user to change the status of their subscription, and if it is
	 * validate the subscription cancellation request and maybe processes the cancellation. 
	 * 
	 * @since 1.0
	 */
	public static function maybe_change_users_subscription() {
		global $woocommerce;

		if ( isset( $_GET['change_subscription_to'] ) && isset( $_GET['subscription_key'] ) && isset( $_GET['_wpnonce'] )  ) {

			$user_id = get_current_user_id();
			$subscription = self::get_users_subscription( $user_id, $_GET['subscription_key'] );

			if ( wp_verify_nonce( $_GET['_wpnonce'], $_GET['subscription_key'] ) === false ) {

				$woocommerce->add_error( __( 'There was an error with your cancellation request. Please try again.', WC_Subscriptions::$text_domain ) );

			} elseif ( empty( $subscription ) ) {

				$woocommerce->add_error( __( 'That doesn\'t appear to be one of your subscriptions.', WC_Subscriptions::$text_domain ) );

			} elseif ( ! WC_Subscriptions_Manager::can_subscription_be_changed_to( $_GET['change_subscription_to'], $_GET['subscription_key'], $user_id ) ) {

				$woocommerce->add_error( sprintf( __( 'That subscription can not be changed to %s. Please contact us if you need assistance.', WC_Subscriptions::$text_domain ), $_GET['change_subscription_to'] ) );

			} elseif ( ! in_array( $_GET['change_subscription_to'], array( 'active', 'suspended', 'cancelled' ) ) ) {

				$woocommerce->add_error( sprintf( __( 'Unknown subscription status: "%s". Please contact us if you need assistance.', WC_Subscriptions::$text_domain ), $_GET['change_subscription_to'] ) );

			} else {

				switch ( $_GET['change_subscription_to'] ) {
					case 'active' :
						self::reactivate_subscription( $user_id, $_GET['subscription_key'] );
						break;
					case 'suspended' :
						self::suspend_subscription( $user_id, $_GET['subscription_key'] );
						break;
					case 'cancelled' :
						self::cancel_subscription( $user_id, $_GET['subscription_key'] );
						break;
				}

				$order = new WC_Order( $subscription['order_id'] );

				$order->add_order_note( sprintf( __( 'The status of subscription %s was changed to %s by the subscriber from their account page.', WC_Subscriptions::$text_domain ), $_GET['subscription_key'], $_GET['change_subscription_to'] ) );

				$status_message = ( $_GET['change_subscription_to'] == 'active' ) ? __( 'activated', WC_Subscriptions::$text_domain ) : $_GET['change_subscription_to'];

				$woocommerce->add_message( sprintf( __( 'Your subscription has been %s.', WC_Subscriptions::$text_domain ), $status_message ) );

			}

			wp_safe_redirect( get_permalink( woocommerce_get_page_id( 'myaccount' ) ) );
			exit;
		}
	}

	/*
	 * Helper Functions
	 */

	/**
	 * WP-Cron occasionally gets itself into an infinite loop on scheduled events, this function is 
	 * designed to create a non-cron related safeguard against payments getting caught up in such a loop.
	 * 
	 * When the scheduled subscription payment hook is fired by WP-Cron, this function is attached before 
	 * any other to make sure the hook hasn't already fired for this period.
	 * 
	 * A transient is used to keep a record of any payment for each period. The transient expiration is 
	 * set to one billing period in the future, minus 1 hour. The transient key uses both the user ID 
	 * and subscription key to ensure it is unique per subscription (even on multisite). 
	 * 
	 * @param $user_id int The id of the user who purchased the subscription
	 * @param $subscription_key string A subscription key of the form created by @see self::get_subscription_key()
	 * @since 1.1.2
	 */
	public static function safeguard_scheduled_payments( $user_id, $subscription_key ) {
		global $wp_filter;

		$transient_key = 'block_scheduled_subscription_payments_' . $user_id . '_' . $subscription_key;

		if ( get_transient( $transient_key ) == 'true' ) {

			// Clear the schedule for this hook
			wp_clear_scheduled_hook( 'scheduled_subscription_payment', array( 'user_id' => (int)$user_id, 'subscription_key' => $subscription_key ) );

			// But now that we've cleared the schedule, make sure the next subscription payment is correctly scheduled
			self::reschedule_subscription_payment( $user_id, $subscription_key );

			// Make sure nothing else fires for this duplicated hook, except for this function: we can't use remove_all_actions here because we need to keep a record of the action which were removed in case we need to add them for another hook in the same request
			foreach ( $wp_filter['scheduled_subscription_payment'] as $priority => $filters ) {
				foreach( $filters as $filter_id => $filter_details ) {
					if ( __CLASS__ . '::' . __FUNCTION__ == $filter_details['function'] )
						continue;

					self::$removed_filter_cache[] = array(
						'filter_id'     => $filter_id,
						'function'      => $filter_details['function'],
						'priority'      => $priority,
						'accepted_args' => $filter_details['accepted_args']
					);

					remove_action( 'scheduled_subscription_payment', $filter_details['function'], $priority, $filter_details['accepted_args'] );
				}
			}

		} else {

			$subscription = self::get_users_subscription( $user_id, $subscription_key );

			$next_billing_timestamp = strtotime( WC_Subscriptions_Order::get_next_payment_date( $subscription['order_id'], $subscription['product_id'], date( 'Y-m-d H:i:s' ) ) );

			$next_billing_transient_timeout = $next_billing_timestamp - 60 * 60 - time();

			set_transient( $transient_key, 'true', $next_billing_transient_timeout );

			// If the payment hook is fired for more than one subscription in the same request, and the actions associated with the hook were removed because a prevous instance was a duplicate, re-add the actions for this instance of the hook
			if ( ! empty( self::$removed_filter_cache ) ) {
				foreach ( self::$removed_filter_cache as $key => $filter ) {
					add_action( 'scheduled_subscription_payment', $filter['function'], $filter['priority'], $filter['accepted_args'] );
					unset( self::$removed_filter_cache[$key] );
				}
			}
		}
	}

	/**
	 * When a subscription payment hook is fired, reschedule the hook to run again on the
	 * time/date of the next payment (if any).
	 * 
	 * WP-Cron's built in wp_schedule_event() function can not be used because the recurrence
	 * must be a timestamp, which creates inaccurate schedules for month and year billing periods.
	 * 
	 * @since 1.0
	 */
	public static function reschedule_subscription_payment( $user_id, $subscription_key ) {

		$subscription = self::get_users_subscription( $user_id, $subscription_key );

		// Don't reschedule for cancelled or expired subscriptions
		if ( ! in_array( $subscription['status'], array( 'expired', 'cancelled', 'failed', 'suspended' ) ) ) {

			$next_billing_timestamp = strtotime( WC_Subscriptions_Order::get_next_payment_date( $subscription['order_id'], $subscription['product_id'] ) );

			// If the next billing date is before the expiration date, reschedule the 'scheduled_subscription_payment' hook
			if ( $subscription['expiry_date'] == 0 || $next_billing_timestamp < strtotime( $subscription['expiry_date'] ) ) {

				wp_schedule_single_event( $next_billing_timestamp, 'scheduled_subscription_payment', array( 'user_id' => (int)$user_id, 'subscription_key' => $subscription_key ) );

				do_action( 'rescheduled_subscription_payment', $user_id, $subscription_key );
			}
		}
	}

	/**
	 * Because neither PHP nor WP include a real array merge function that works recursively.
	 *
	 * @since 1.0
	 */
	public static function array_merge_recursive_for_real( $first_array, $second_array ) {

		$merged = $first_array;

		if ( is_array( $second_array ) ) {
			foreach ( $second_array as $key => $val ) {
				if ( is_array( $second_array[$key] ) ) {
					$merged[$key] = ( isset( $merged[$key] ) && is_array( $merged[$key] ) ) ? self::array_merge_recursive_for_real( $merged[$key], $second_array[$key] ) : $second_array[$key];
				} else {
					$merged[$key] = $val;
				}
			}
		}

		return $merged;
	}


	/* Deprecated Functions */

	/**
	 * @deprecated 1.1
	 * @param $subscription_key string A subscription key of the form created by @see self::get_subscription_key()
	 * @since 1.0
	 */
	public static function can_subscription_be_cancelled( $subscription_key, $user_id = '' ) {
		_deprecated_function( __CLASS__ . '::' . __FUNCTION__, '1.1', __CLASS__ . '::can_subscription_be_changed_to( "cancelled", $subscription_key, $user_id )' );
		$subscription_can_be_cancelled = self::can_subscription_be_changed_to( 'cancelled', $subscription_key, $user_id );

		return apply_filters( 'woocommerce_subscription_can_be_cancelled', $subscription_can_be_cancelled, $subscription, $order );
	}

	/**
	 * @deprecated 1.1
	 * @param $subscription_key string A subscription key of the form created by @see self::get_subscription_key()
	 * @since 1.0
	 */
	public static function get_users_cancellation_link( $subscription_key ) {
		_deprecated_function( __CLASS__ . '::' . __FUNCTION__, '1.1', __CLASS__ . '::get_users_action_link( $subscription_key, "cancel" )' );
		return apply_filters( 'woocommerce_subscriptions_users_cancellation_link', self::get_users_action_link( $subscription_key, 'cancel' ), $subscription_key );
	}

	/**
	 * @deprecated 1.1
	 * @since 1.0
	 */
	public static function maybe_cancel_users_subscription() {
		_deprecated_function( __CLASS__ . '::' . __FUNCTION__, '1.1', __CLASS__ . '::maybe_change_users_subscription()' );
		self::maybe_change_users_subscription();
	}

	/**
	 * @deprecated 1.1
	 * @param $user_id int The ID of the user who owns the subscriptions.
	 * @param $subscription_key string A subscription key of the form created by @see self::get_subscription_key()
	 * @since 1.0
	 */
	public static function get_failed_payment_count( $user_id, $subscription_key ) {
		_deprecated_function( __CLASS__ . '::' . __FUNCTION__, '1.1', __CLASS__ . '::get_subscriptions_failed_payment_count( $subscription_key, $user_id )' );
		return self::get_subscriptions_failed_payment_count( $subscription_key, $user_id );
	}

}

WC_Subscriptions_Manager::init();
