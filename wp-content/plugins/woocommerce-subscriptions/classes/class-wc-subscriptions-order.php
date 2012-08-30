<?php
/**
 * Subscriptions Order Class
 * 
 * Mirrors and overloads a few functions in the WC_Order class to work for subscriptions. 
 *
 * @package		WooCommerce Subscriptions
 * @subpackage	WC_Subscriptions_Order
 * @category	Class
 * @author		Brent Shepherd
 */
class WC_Subscriptions_Order {

	/**
	 * Bootstraps the class and hooks required actions & filters.
	 * 
	 * @since 1.0
	 */
	public static function init() {
		add_filter( 'woocommerce_get_order_item_totals', __CLASS__ . '::get_order_item_totals', 10, 2 );
		add_filter( 'woocommerce_get_formatted_order_total', __CLASS__ . '::get_formatted_order_total', 10, 2 );
		add_filter( 'woocommerce_order_formatted_line_subtotal', __CLASS__ . '::get_formatted_line_total', 10, 3 );
		add_filter( 'woocommerce_order_subtotal_to_display', __CLASS__ . '::get_subtotal_to_display', 10, 3 );
		add_filter( 'woocommerce_order_cart_discount_to_display', __CLASS__ . '::get_cart_discount_to_display', 10, 3 );
		add_filter( 'woocommerce_order_discount_to_display', __CLASS__ . '::get_order_discount_to_display', 10, 3 );
		add_filter( 'woocommerce_order_shipping_to_display', __CLASS__ . '::get_shipping_to_display', 10, 2 );

		add_action( 'woocommerce_thankyou', __CLASS__ . '::subscription_thank_you' );
		add_action( 'woocommerce_subscriptions_renewal_order_created', __CLASS__ . '::maybe_send_customer_renewal_order_email', 10, 1 );

		add_action( 'manage_shop_order_posts_custom_column', __CLASS__ . '::add_contains_subscription_hidden_field', 10, 1 );
		add_action( 'woocommerce_admin_order_data_after_order_details', __CLASS__ . '::contains_subscription_hidden_field', 10, 1 );

		add_action( 'woocommerce_process_shop_order_meta', __CLASS__ . '::maybe_manually_change_subscriptions', 0, 2 ); // Need to fire before WooCommerce

		// Record initial payment against the subscription
		add_action( 'woocommerce_payment_complete', __CLASS__ . '::record_order_payment', 10, 1 );

		// After payment on an order completes, make sure a scheduled subscription payment is never fired repeatedly to safeguard against WP-Cron inifinite loop bugs
		add_action( 'woocommerce_payment_complete', __CLASS__ . '::safeguard_scheduled_payments', 10, 1 );
	}

	/*
	 * Helper functions for extracting the details of subscriptions in an order
	 */

	/**
	 * Checks an order to see if it contains a subscription.
	 *
	 * @since 1.0
	 */
	public static function order_contains_subscription( $order ) {

		if ( ! is_object( $order ) )
			$order = new WC_Order( $order );

		$contains_subscription = false;

		foreach ( $order->get_items() as $order_item ) {
			if ( WC_Subscriptions_Product::is_subscription( $order_item['id'] ) ) {
				$contains_subscription = true;
				break;
			}
		}

		return $contains_subscription;
	}

	/**
	 * Creates a string representation of the subscription period/term for each item in the cart
	 * 
	 * @param $price float (optional) The price to display in the subscription. Defaults to empty, which returns just the period & duration components of the string.
	 * @since 1.0
	 */
	public static function get_order_subscription_string( $order, $price = '', $sign_up_fee = '' ) {

		if ( count( $order->get_items() ) == 1 ) {

			$subscription_period       = self::get_subscription_period( $order );
			$subscription_length       = self::get_subscription_length( $order );
			$subscription_interval     = self::get_subscription_interval( $order );
			$subscription_trial_length = self::get_subscription_trial_length( $order );

			$subscription_string = sprintf( _n( ' %s / %s', ' %s every %s', $subscription_interval, WC_Subscriptions::$text_domain ), $price, WC_Subscriptions_Manager::get_subscription_period_strings( $subscription_interval, strtolower( $subscription_period ) ) );

			if ( $subscription_length ) {
				$ranges = WC_Subscriptions_Manager::get_subscription_ranges( $subscription_period );
				$subscription_string = sprintf( __( '%s for %s', WC_Subscriptions::$text_domain ), $subscription_string, $ranges[$subscription_length] );
			}

			if ( $subscription_trial_length > 0 ) {
				$trial_lengths = WC_Subscriptions_Manager::get_subscription_trial_lengths( $subscription_period );
				$subscription_string = sprintf( __( '%s with %s free trial', WC_Subscriptions::$text_domain ), $subscription_string, $trial_lengths[$subscription_trial_length] );
			}

			$sign_up_fee = ( ! empty( $sign_up_fee ) ) ? $sign_up_fee : self::get_meta( $order, '_sign_up_fee_total' );

			if ( $sign_up_fee > 0 ) {
				if ( self::is_renewal( $order ) )
					$subscription_string = sprintf( __( '%s with a %s initial payment', WC_Subscriptions::$text_domain ), $subscription_string, woocommerce_price( $sign_up_fee ) );
				else
					$subscription_string = sprintf( __( '%s with a %s sign-up fee', WC_Subscriptions::$text_domain ), $subscription_string, woocommerce_price( $sign_up_fee ) );
			}

		} else {

			$subscription_string = __( 'Multiple Subscriptions', WC_Subscriptions::$text_domain );

		}

		return $subscription_string;
	}

	/**
	 * Returns the total amount to be charged at the outset of the Subscription.
	 * 
	 * This may return 0 if there is a free trial period and no sign up fee, otherwise it will be the sum of the sign up 
	 * fee and price per period. 
	 * 
	 * @param $order mixed A WC_Order object or the ID of the order which the subscription was purchased in.
	 * @param $product_id int (optional) The post ID of the subscription WC_Product object purchased in the order. Defaults to the ID of the first product purchased in the order.
	 * @since 1.1
	 */
	public static function get_total_initial_payment( $order, $product_id = '' ) {

		if ( ! is_object( $order ) )
			$order = new WC_Order( $order );

		// Don't include the per-period payment as there is a free trial period
		if ( self::get_subscription_trial_length( $order, $product_id ) > 0 )
			$price_per_period = 0;
		else
			$price_per_period = self::get_price_per_period( $order, $product_id );

		return self::get_sign_up_fee( $order, $product_id ) + $price_per_period;
	}

	/**
	 * Returns the price per period for a subscription in an order.
	 * 
	 * There must be only one subscription in an order for this to be accurate. 
	 * 
	 * @param $order mixed A WC_Order object or the ID of the order which the subscription was purchased in.
	 * @param $product_id int (optional) The post ID of the subscription WC_Product object purchased in the order. Defaults to the ID of the first product purchased in the order.
	 * @since 1.0
	 */
	public static function get_price_per_period( $order, $product_id = '' ) {

		if ( ! is_object( $order ) )
			$order = new WC_Order( $order );

		return $order->get_order_total();
	}

	/**
	 * Returns the total sign-up fee for a subscription product in an order.
	 * 
	 * @param $order mixed A WC_Order object or the ID of the order which the subscription was purchased in.
	 * @param $product_id int (optional) The post ID of the subscription WC_Product object purchased in the order. Defaults to the ID of the first product purchased in the order.
	 * @since 1.0
	 */
	public static function get_sign_up_fee( $order, $product_id = '' ) {
		return self::get_meta( $order, '_sign_up_fee_total' );
	}

	/**
	 * Returns the period (e.g. month) for a each subscription product in an order.
	 * 
	 * @param $order mixed A WC_Order object or the ID of the order which the subscription was purchased in.
	 * @param $product_id int (optional) The post ID of the subscription WC_Product object purchased in the order. Defaults to the ID of the first product purchased in the order.
	 * @since 1.0
	 */
	public static function get_subscription_period( $order, $product_id = '' ) {

		$periods = self::get_meta( $order, '_order_subscription_periods', array() );

		return array_pop( $periods );
	}

	/**
	 * Returns the interval (e.g. 3 for a subscription charged every 3 months) for a each subscription product in an order.
	 * 
	 * @param $order mixed A WC_Order object or the ID of the order which the subscription was purchased in.
	 * @param $product_id int (optional) The post ID of the subscription WC_Product object purchased in the order. Defaults to the ID of the first product purchased in the order.
	 * @since 1.0
	 */
	public static function get_subscription_interval( $order, $product_id = '' ) {

		$intervals = self::get_meta( $order, '_order_subscription_intervals', array() );

		if ( isset( $intervals[$product_id] ) )
			$interval = $intervals[$product_id];
		elseif ( ! empty( $intervals ) )
			$interval = array_pop( $intervals );
		else
			$interval = 1;

		return $interval;
	}

	/**
	 * Returns the length for a subscription in an order.
	 * 
	 * There must be only one subscription in an order for this to be accurate. 
	 * 
	 * @param $order mixed A WC_Order object or the ID of the order which the subscription was purchased in.
	 * @param $product_id int (optional) The post ID of the subscription WC_Product object purchased in the order. Defaults to the ID of the first product purchased in the order.
	 * @since 1.0
	 */
	public static function get_subscription_length( $order, $product_id = '' ) {

		$lengths = self::get_meta( $order, '_order_subscription_lengths', array() );

		return array_pop( $lengths );
	}

	/**
	 * Returns the length for a subscription products trial period as set when added to an order.
	 * 
	 * For now, there must be only one subscription in an order for this to be accurate. 
	 * 
	 * @param $order mixed A WC_Order object or the ID of the order which the subscription was purchased in.
	 * @param $product_id int (optional) The post ID of the subscription WC_Product object purchased in the order. Defaults to the ID of the first product purchased in the order.
	 * @since 1.1
	 */
	public static function get_subscription_trial_length( $order, $product_id = '' ) {

		$trial_lengths = self::get_meta( $order, '_order_subscription_trial_lengths', array() );

		if ( isset( $trial_lengths[$product_id] ) )
			$trial_length = $trial_lengths[$product_id];
		elseif ( ! empty( $trial_lengths ) )
			$trial_length = array_pop( $trial_lengths );
		else
			$trial_length = 0;

		return $trial_length;
	}

	/**
	 * Takes a subscription product's ID and returns the date on which the next payment is due.
	 * 
	 * Calculation is based on the subscription's $from_date if specified, or the current date/time. 
	 * 
	 * The next payment date will occur after any free trial period and up to any expiration date.
	 * 
	 * @param $order mixed A WC_Order object or the ID of the order which the subscription was purchased in.
	 * @param $product_id int The product/post ID of the subscription
	 * @param $from_date mixed A MYSQL formatted date/time string from which to calculate the next payment date, or empty (default), which will use the last payment on the subscription, or today's date/time if no previous payments have been made.
	 * @since 1.0
	 */
	public static function get_next_payment_date( $order, $product_id, $from_date = '' ) {

		if ( ! is_object( $order ) )
			$order = new WC_Order( $order );

		$subscription              = WC_Subscriptions_Manager::get_users_subscription( $order->user_id, WC_Subscriptions_Manager::get_subscription_key( $order->id, $product_id ) );
		$subscription_period       = self::get_subscription_period( $order, $product_id );
		$subscription_interval     = self::get_subscription_interval( $order, $product_id );
		$subscription_trial_length = self::get_subscription_trial_length( $order, $product_id );

		$trial_start_date = ( empty ( $subscription['start_date'] ) ) ? date( 'Y-m-d H:i:s' ) : $subscription['start_date'];
		$trial_end_time   = strtotime( "$trial_start_date + $subscription_trial_length {$subscription_period}s" );

		// If the subscription has a free trial period, and we're still in the free trial period, the next payment is due at the end of the free trial
		if ( $subscription_trial_length > 0 && $trial_end_time > time() + 120 ) {

			$next_payment_date = date( 'Y-m-d H:i:s', $trial_end_time );

		} else { // Otherwise it's due at the next payment period

			if ( empty( $from_date ) ) {
				if ( ! empty( $subscription['completed_payments'] ) )
					$from_date = array_pop( $subscription['completed_payments'] );
				else if ( ! empty ( $subscription['start_date'] ) )
					$from_date = $subscription['start_date'];
				else
					$from_date = date( 'Y-m-d H:i:s' );
			}

			$next_payment_date = date( 'Y-m-d H:i:s', strtotime( "$from_date + {$subscription_interval} {$subscription_period}" ) );
		}

		return $next_payment_date;
	}

	/**
	 * A unified API for accessing subscription order meta, especially for sign-up fee related order meta. 
	 * 
	 * @param $order WC_Order | int The WC_Order object or ID of the order for which the meta should be sought. 
	 * @param $meta_key string The key as stored in the post meta table for the meta item. 
	 * @param $default mixed (optional) The default value to return if the meta key does not exist. Default 0.
	 * @since 1.0
	 */
	public static function get_meta( $order, $meta_key, $default = 0 ) {

		if ( ! is_object( $order ) )
			$order = new WC_Order( $order );

		if ( isset( $order->order_custom_fields[$meta_key] ) ) {
			$meta_value = maybe_unserialize( $order->order_custom_fields[$meta_key][0] );
		} else {
			$meta_value = get_post_meta( $order->id, $meta_key, true );

			if ( empty( $meta_value ) )
				$meta_value = $default;
		}

		return $meta_value;
	}

	/* 
	 * Functions to customise the WooCommerce defaults.
	 */

	/**
	 * Appends the subscription period/duration string to order total
	 *
	 * @since 1.0
	 */
	public static function get_formatted_line_total( $formatted_total, $item, $order ) {

		if ( WC_Subscriptions_Product::is_subscription( $item['id'] ) ) {
			$include = ( self::is_renewal( $order ) ) ? array( 'sign_up_fee' => false ) : array();
			$include['price'] =  $formatted_total;
			$formatted_total = WC_Subscriptions_Product::get_price_string( $item['id'], $include );
		}

		return $formatted_total;
	}

	/**
	 * Appends the subscription period/duration string to order total
	 *
	 * @since 1.0
	 */
	public static function get_subtotal_to_display( $subtotal, $compound, $order ) {
		global $woocommerce;

		if( self::order_contains_subscription( $order ) ) {

			if ( ! $compound ) {
				if ( $order->display_cart_ex_tax )
					$sign_up_fee_subtotal = self::get_meta( $order, '_sign_up_fee_subtotal_ex_tax' );
				else
					$sign_up_fee_subtotal = self::get_meta( $order, '_sign_up_fee_subtotal' );

				if ( $order->display_cart_ex_tax && $order->prices_include_tax )
					$sign_up_fee_subtotal .= ' <small>'.$woocommerce->countries->ex_tax_or_vat().'</small>';

			} else {
		
				if ( $order->prices_include_tax )
					return;

				$sign_up_fee_subtotal = self::get_meta( $order, '_sign_up_fee_subtotal' );

				// Remove discounts
				$sign_up_fee_subtotal = $subtotal - self::get_meta( $order, '_sign_up_fee_discount_cart' );

			}

			$subtotal = self::get_order_subscription_string( $order, $subtotal, $sign_up_fee_subtotal );

			if ( $order->display_cart_ex_tax && $order->prices_include_tax )
				$subtotal .= ' <small>' . $woocommerce->countries->ex_tax_or_vat() . '</small>';

		}

		return $subtotal;
	}

	/**
	 * Appends the subscription period/duration string to order total
	 *
	 * @since 1.0
	 */
	public static function get_cart_discount_to_display( $discount, $order ) {

		if( self::order_contains_subscription( $order ) )
			$discount = sprintf( __( '%s discount', WC_Subscriptions::$text_domain ), self::get_order_subscription_string( $order, $discount, self::get_meta( $order, '_sign_up_fee_discount_cart' ) ) );

		return $discount;
	}

	/**
	 * Appends the subscription period/duration string to order total
	 *
	 * @since 1.0
	 */
	public static function get_order_discount_to_display( $discount, $order ) {

		if( self::order_contains_subscription( $order ) )
			$discount = sprintf( __( '%s discount', WC_Subscriptions::$text_domain ), self::get_order_subscription_string( $order, $discount, self::get_meta( $order, '_sign_up_fee_discount_total' ) ) );

		return $discount;
	}

	/**
	 * Appends the subscription period/duration string to order total
	 *
	 * @since 1.0
	 */
	public static function get_formatted_order_total( $formatted_total, $order ) {

		if( self::order_contains_subscription( $order ) )
			$formatted_total = self::get_order_subscription_string( $order, $formatted_total, self::get_meta( $order, '_sign_up_fee_total' ) );

		return $formatted_total;
	}

	/**
	 * Appends the subscription period/duration string to shipping fee
	 *
	 * @since 1.0
	 */
	public static function get_shipping_to_display( $shipping_to_display, $order ) {

		if( self::order_contains_subscription( $order ) && $order->order_shipping > 0 )
			$shipping_to_display = self::get_order_subscription_string( $order, $shipping_to_display, '0.00' );

		return $shipping_to_display;
	}

	/**
	 * Individual totals are taken care of by filters, but taxes are not, so we need to override them here.
	 * 
	 * @since 1.0
	 */
	public static function get_order_item_totals( $total_rows, $order ) {
		global $woocommerce;

		if ( self::order_contains_subscription( $order ) ) {

			$sign_up_fee_total = self::get_meta( $order, '_sign_up_fee_total' );
			$sign_up_fee_taxes = self::get_meta( $order, '_sign_up_fee_taxes', array() );
			$order_taxes       = $order->get_taxes();

			if ( count( $order_taxes ) > 0 ) {
				// Manually override the taxes
				foreach ( $order_taxes as $key => $tax ) {
					if ( $tax['compound'] ) {
						$has_compound_tax = true;
						continue;
					}

					if ( isset( $total_rows[$tax['label']] ) && $tax['cart_tax'] > 0 ) {
						$order_tax_total = $order_taxes[$key]['cart_tax'] + $order_taxes[$key]['shipping_tax'];
						$sign_up_fee_tax = ( $sign_up_fee_total > 0 && isset( $sign_up_fee_taxes[$key]['cart_tax'] ) ) ? $sign_up_fee_taxes[$key]['cart_tax'] : '';
						$total_rows[$tax['label']] = self::get_order_subscription_string( $order, woocommerce_price( $order_tax_total ), $sign_up_fee_tax );
					}
				}

				foreach ( $order_taxes as $key => $tax ) {
					if ( ! $tax['compound'] )
						continue;

					if ( isset( $total_rows[$tax['label']] ) && $tax['cart_tax'] > 0 ) {
						$order_tax_total = $order_taxes[$key]['cart_tax'] + $order_taxes[$key]['shipping_tax'];
						$sign_up_fee_tax = ( $sign_up_fee_total > 0 && isset( $sign_up_fee_taxes[$key]['cart_tax'] ) ) ? $sign_up_fee_taxes[$key]['cart_tax'] + $sign_up_fee_taxes[$key]['shipping_tax'] : '';
						$order_tax_total = $order_taxes[$key]['cart_tax'] + $order_taxes[$key]['shipping_tax'];
						$total_rows[$tax['label']] = self::get_order_subscription_string( $order, woocommerce_price( $order_tax_total ), $tax['cart_tax'] );
					}
				}

			} else {
				if ( isset( $total_rows[$woocommerce->countries->tax_or_vat()] ) )
					$total_rows[$woocommerce->countries->tax_or_vat()] = self::get_order_subscription_string( $order, woocommerce_price( $order->get_total_tax() ), self::get_meta( $order, '_sign_up_fee_tax_total' ) );
			}
		}

		return $total_rows;
	}

	/**
	 * Displays a few details about what happens to their subscription. Hooked
	 * to the thank you page. 
	 *
	 * @since 1.0
	 */
	public static function subscription_thank_you( $order_id ){

		if( self::order_contains_subscription( $order_id ) ) {
			echo '<p>' . __( 'Your subscription will be activated when payment clears.', WC_Subscriptions::$text_domain ) . '</p>';
			echo '<p>' . sprintf( __( 'View the status of your subscription in %syour account%s.', WC_Subscriptions::$text_domain ), '<a href="' . get_permalink( woocommerce_get_page_id( 'myaccount' ) ) . '">', '</a>' ) . '</p>';
		}
	}

	/**
	 * Returns the number of failed payments for a given subscription.
	 * 
	 * @param $order WC_Order The WC_Order object of the order for which you want to determine the number of failed payments.
	 * @param product_id int The ID of the subscription product.
	 * @return string The key representing the given subscription.
	 * @since 1.0
	 */
	public static function get_failed_payment_count( $order, $product_id ) {

		$failed_payment_count = WC_Subscriptions_Manager::get_subscriptions_failed_payment_count( $order->customer_user, WC_Subscriptions_Manager::get_subscription_key( $order->id, $product_id ) );

		return $failed_payment_count;
	}

	/**
	 * Returns the amount outstanding on a subscription product.
	 * 
	 * @param $order WC_Order The WC_Order object of the order for which you want to determine the number of failed payments.
	 * @param product_id int The ID of the subscription product.
	 * @return string The key representing the given subscription.
	 * @since 1.0
	 */
	public static function get_outstanding_balance( $order, $product_id ) {

		$failed_payment_count = self::get_failed_payment_count( $order, $product_id );

		$oustanding_balance = $failed_payment_count * self::get_price_per_period( $order, $product_id );

		return $oustanding_balance;
	}

	/**
	 * Output a hidden element in the order status of the orders list table to provide information about whether
	 * the order displayed in that row contains a subscription or not.
	 * 
	 * @param $column String The string of the current column.
	 * @since 1.1
	 */
	public static function add_contains_subscription_hidden_field( $column ) {
		global $post;

		if ( $column == 'order_status' )
			self::contains_subscription_hidden_field( $post->ID );
	}

	/**
	 * Output a hidden element in the order status of the orders list table to provide information about whether
	 * the order displayed in that row contains a subscription or not.
	 * 
	 * @param $column String The string of the current column.
	 * @since 1.1
	 */
	public static function contains_subscription_hidden_field( $order_id ) {

		$has_subscription = WC_Subscriptions_Order::order_contains_subscription( $order_id ) ? 'true' : 'false';

		echo '<input type="hidden" name="contains_subscription" value="' . $has_subscription . '">';
	}

	/**
	 * Creates a new order for renewing a subscription product based on the details of a previous order. 
	 * 
	 * @param $order WC_Order | int The WC_Order object or ID of the order for which the a new order should be created.
	 * @param $meta_key string The ID of the subscription product in the order which needs to be added to the new order.
	 * @since 1.0
	 */
	public static function generate_renewal_order( $original_order, $product_id ) {
		global $wpdb;

		if ( ! is_object( $original_order ) )
			$original_order = new WC_Order( $original_order );

		// Create the new order
		$renewal_order_data = array(
			'post_type'     => 'shop_order',
			'post_title'    => 'Renewal Order &ndash; '.date( 'F j, Y @ h:i A' ),
			'post_status'   => 'publish',
			'ping_status'   => 'closed',
			'post_excerpt'  => $original_order->customer_note,
			'post_author'   => 1,
			'post_password' => uniqid( 'order_' )
		);

		$renewal_order_id = wp_insert_post( $renewal_order_data );

		// Set the order as pending
		wp_set_object_terms( $renewal_order_id, 'pending', 'shop_order_status' );

		// Carry all the post meta from the old order over to the new order
		$order_meta_items = $wpdb->get_results( "SELECT `meta_key`, `meta_value` FROM $wpdb->postmeta WHERE `post_id` = $original_order->id", 'ARRAY_A' );

		foreach( $order_meta_items as $order_meta_item )
			add_post_meta( $renewal_order_id, $order_meta_item['meta_key'], maybe_unserialize( $order_meta_item['meta_value'] ), true );

		$outstanding_balance = self::get_outstanding_balance( $original_order, $product_id );

		// If there are outstanding payment amounts, add them as a sign-up fee to the order, otherwise set the sign-up fee to 0
		if ( $outstanding_balance > 0 && 'yes' == get_option( WC_Subscriptions_Admin::$option_prefix . '_add_outstanding_balance' ) ) {

			$failed_payment_count = self::get_failed_payment_count( $original_order, $product_id );

			$sign_up_fee_subtotal = 0;
			$sign_up_fee_subtotal_ex_tax = 0;

			// Calculate 
			foreach( $original_order->get_items() as $item ) {

				if ( $item['id'] != $product_id )
					continue;

				$base_price = WC_Subscriptions_Product::get_price( $item['id'] ) * $item['qty'];

				// Row price
				if ( $original_order->prices_include_tax ) {

					// Sub total is based on base prices (without discounts or shipping)
					$sign_up_fee_subtotal        += $base_price * $failed_payment_count;
					$sign_up_fee_subtotal_ex_tax += ( $base_price - $item['line_tax'] ) * $failed_payment_count;

				} else {

					// Sub total is based on base prices (without discounts)
					$sign_up_fee_subtotal        += ( $base_price + $item['line_tax'] ) * $failed_payment_count;
					$sign_up_fee_subtotal_ex_tax += $base_price * $failed_payment_count;

				}
			}

			update_post_meta( $renewal_order_id, '_sign_up_fee_total', $outstanding_balance );
			update_post_meta( $renewal_order_id, '_sign_up_fee_subtotal', $sign_up_fee_subtotal );
			update_post_meta( $renewal_order_id, '_sign_up_fee_subtotal_ex_tax', $sign_up_fee_subtotal_ex_tax );

			$sign_up_fee_taxes = get_post_meta( $renewal_order_id, '_sign_up_fee_taxes', true );


			foreach( $original_order->get_taxes() as $key => $tax ) {
				$sign_up_fee_taxes[$key]['cart_tax'] = $tax['cart_tax'] * $failed_payment_count;
				$sign_up_fee_taxes[$key]['shipping_tax'] = $tax['shipping_tax'] * $failed_payment_count;
			}

			update_post_meta( $renewal_order_id, '_sign_up_fee_taxes', $sign_up_fee_taxes );
			update_post_meta( $renewal_order_id, '_sign_up_fee_tax_total', $original_order->get_total_tax() * $failed_payment_count );

			update_post_meta( $renewal_order_id, '_sign_up_fee_discount_cart', $original_order->cart_discount * $failed_payment_count );
			update_post_meta( $renewal_order_id, '_sign_up_fee_discount_total', $original_order->order_discount * $failed_payment_count );

		} else { // Remove all sign-up fees

			foreach( array( '_cart_contents_sign_up_fee_total', '_cart_contents_sign_up_fee_count', '_sign_up_fee_total', '_sign_up_fee_subtotal', '_sign_up_fee_subtotal_ex_tax', '_sign_up_fee_tax_total', '_sign_up_fee_discount_cart', '_sign_up_fee_discount_total' ) as $meta_key )
				update_post_meta( $renewal_order_id, $meta_key, 0 );

			update_post_meta( $renewal_order_id, '_sign_up_fee_taxes', array() );

		}

		// Keep a record of the original order's ID on the renewal order
		add_post_meta( $renewal_order_id, '_original_order', $original_order->id, true );

		// Make sure the original order is cancelled and keep a note of the renewal order
		if ( ! in_array( $original_order->status, array( 'cancelled', 'expired', 'failed' ) ) )
			$original_order->cancel_order();

		$original_order->add_order_note( sprintf( __( 'Order superseded by renewal order %s.', WC_Subscriptions::$text_domain ), $renewal_order_data ) );

		$renewal_order = new WC_Order( $renewal_order_id );

		WC_Subscriptions_Manager::process_subscriptions_on_checkout( $renewal_order_id );

		do_action( 'woocommerce_subscriptions_renewal_order_created', $renewal_order, $original_order, $product_id );
	}

	/**
	 * Hooks to the renewal order created action to determine if the order should be emailed to the customer. 
	 *
	 * @param $order WC_Order | int The WC_Order object or ID of a WC_Order order.
	 * @since 1.0
	 */
	public static function maybe_send_customer_renewal_order_email( $order ) {
		if ( 'yes' == get_option( WC_Subscriptions_Admin::$option_prefix . '_email_renewal_order' ) )
			self::send_customer_renewal_order_email( $order );
	}

	/**
	 * Processing Order
	 * 
	 * @param $order WC_Order | int The WC_Order object or ID of a WC_Order order.
	 * @since 1.0
	 */
	public static function send_customer_renewal_order_email( $order ) {
		global $woocommerce;

		if ( ! is_object( $order ) )
			$order = new WC_Order( $order );

		$emailer = $woocommerce->mailer();

		$email_heading = __( 'Subscription Renewal Invoice', WC_Subscriptions::$text_domain );

		$blogname = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );

		$subject = apply_filters( 'woocommerce_email_subject_customer_renewal_order', sprintf( __( '[%s] Subscription Renewal Invoice', WC_Subscriptions::$text_domain ), $blogname ), $order );

		// Buffer
		ob_start();

		// Get mail template
		woocommerce_get_template( 
			'emails/customer-renewal-order.php', 
			array(
				'order'         => $order,
				'email_heading' => $email_heading
			),
			'',
			plugin_dir_path( WC_Subscriptions::$plugin_file ) . 'templates/'
		);

		// Get contents
		$message = ob_get_clean();

		//	CC, BCC, additional headers
		$headers = apply_filters( 'woocommerce_email_headers', '', 'customer_renewal_order' );

		// Attachments
		$attachments = apply_filters( 'woocommerce_email_attachments', '', 'customer_renewal_order' );

		// Send the mail
		$emailer->send( $order->billing_email, $subject, $message, $headers, $attachments );
	}

	/**
	 * Check if a given order is a subscription renewal order
	 * 
	 * @param $order WC_Order | int The WC_Order object or ID of a WC_Order order.
	 * @since 1.0
	 */
	public static function is_renewal( $order ) {

		if ( ! is_object( $order ) )
			$order = new WC_Order( $order );

		if ( isset( $order->order_custom_fields['_original_order'] ) && ! empty( $order->order_custom_fields['_original_order'][0] ) )
			$is_renewal = true;
		else
			$is_renewal = false;

		return apply_filters( 'woocommerce_subscriptions_is_renewal_order', $is_renewal, $order );
	}

	/**
	 * When an order is added or updated from the admin interface, check if a new subscription product
	 * has been manually added to the order, and if one has, create a new subscription. 
	 * 
	 * @param $post_id int The ID of the post which is the WC_Order object.
	 * @param $post Object The post object of the order.
	 * @since 1.1
	 */
	public static function maybe_manually_change_subscriptions( $post_id, $post ) {

		$order = new WC_Order( $post_id );

		// Check if all the subscription products on the order have associated subscriptions on the user's account, and if not, add a new one
		foreach ( $_POST['item_id'] as $item_id ) {

			if ( ! WC_Subscriptions_Product::is_subscription( $item_id ) )
				continue;

			$subscription_key = WC_Subscriptions_Manager::get_subscription_key( $post_id, $item_id );

			$subscription = array();

			// If order customer changed, move the subscription from the old customer's account to the new customer
			if ( ! empty( $order->customer_user ) && $order->customer_user != (int)$_POST['customer_user'] ) {

				$subscription = WC_Subscriptions_Manager::remove_users_subscription( $order->customer_user, $subscription_key );

				$subscriptions = WC_Subscriptions_Manager::get_users_subscriptions( (int)$_POST['customer_user'] );

				if ( ! empty( $subscription ) ) {
					$subscriptions[$subscription_key] = $subscription;
					WC_Subscriptions_Manager::update_users_subscriptions( (int)$_POST['customer_user'], $subscriptions );
				}
			}

			// In case it's a new order or the customer has changed
			$order->customer_user = $order->user_id = (int)$_POST['customer_user'];

			$subscription = WC_Subscriptions_Manager::get_users_subscription( $order->customer_user, $subscription_key );

			if ( empty( $subscription ) ) { // Add a new subscription

				// The order doesn't may not exist yet, so we need to set a few things ourselves
				$order->order_key = uniqid( 'order_' );
				add_post_meta( $post_id, '_order_key', $order->order_key, true );

				WC_Subscriptions_Manager::create_pending_subscription_for_order( $order, $item_id );

				// Add the subscription meta for this item to the order
				$functions_and_meta = array( 'get_period' => '_order_subscription_periods', 'get_interval' => '_order_subscription_intervals', 'get_length' => '_order_subscription_lengths' );

				foreach ( $functions_and_meta as $function_name => $meta_key ) {
					$subscription_meta = self::get_meta( $order, $meta_key, array() );
					$subscription_meta[$item_id] = WC_Subscriptions_Product::$function_name( $item_id );
					update_post_meta( $order->id, $meta_key, $subscription_meta );
				}

				// Set the subscription's status if it should be something other than pending
				switch( $order->status ) {
					case 'completed' :
					case 'processing' :
						WC_Subscriptions_Manager::activate_subscription( $order->customer_user, $subscription_key );
						break;
					case 'refunded' :
					case 'cancelled' :
						WC_Subscriptions_Manager::cancel_subscription( $order->customer_user, $subscription_key );
						break;
					case 'failed' :
						WC_Subscriptions_Manager::failed_subscription_signup( $order->customer_user, $subscription_key );
						break;
				}
			}
		}

	}

	/**
	 * Once payment is completed on an order, set a lock on payments until the next subscription payment period.
	 * 
	 * @param $user_id int The id of the user who purchased the subscription
	 * @param $subscription_key string A subscription key of the form created by @see self::get_subscription_key()
	 * @since 1.1.2
	 */
	public static function safeguard_scheduled_payments( $order_id ) {

		$order = new WC_Order( $order_id );

		$subscription_key = WC_Subscriptions_Manager::get_subscription_key( $order_id );

		WC_Subscriptions_Manager::safeguard_scheduled_payments( $order->customer_user, $subscription_key );

	}

	/**
	 * Once payment is completed on an order, record the payment against the subscription automatically so that
	 * payment gateway extension developers don't have to do this.
	 * 
	 * @param $order_id int The id of the order to record payment against
	 * @since 1.1.2
	 */
	public static function record_order_payment( $order_id ) {
		WC_Subscriptions_Manager::process_subscription_payments_on_order( $order_id );
	}
}

WC_Subscriptions_Order::init();
