<?php
/**
 * Subscriptions Cart Class
 * 
 * Mirrors a few functions in the WC_Cart class to work for subscriptions. 
 * 
 * @package		WooCommerce Subscriptions
 * @subpackage	WC_Subscriptions_Cart
 * @category	Class
 * @author		Brent Shepherd
 * @since		1.0
 */
class WC_Subscriptions_Cart {

	/**
	 * Bootstraps the class and hooks required actions & filters.
	 * 
	 * @since 1.0
	 */
	public static function init() {

		// Make sure the sign-up fee totals persist in the cart
		add_action( 'init', __CLASS__ . '::get_cart_from_session', 6 );

		// Set the sign-up fee fields when the cart is saved to a session
		add_action( 'woocommerce_cart_updated', __CLASS__ . '::set_session' );

		// Reset the sign-up fee fields when the cart is emptied
		add_action( 'woocommerce_cart_emptied', __CLASS__ . '::reset' );

		// Calculate sign-up fee totals whenever cart totals are calculated
		add_action( 'woocommerce_before_calculate_totals', __CLASS__ . '::calculate_sign_up_fee_totals' );

		// Override Product totals
		add_filter( 'woocommerce_cart_item_price_html', __CLASS__ . '::get_cart_item_price_html', 10, 2 );
		add_filter( 'woocommerce_cart_product_subtotal', __CLASS__ . '::get_product_subtotal', 10, 2 );

		// Override Discounts
		add_filter( 'woocommerce_cart_discounts_before_tax', __CLASS__ . '::get_discounts_before_tax', 10 );
		add_filter( 'woocommerce_cart_discounts_after_tax', __CLASS__ . '::get_discounts_after_tax', 10 );

		// Override Cart Tax
		add_filter( 'woocommerce_cart_formatted_taxes', __CLASS__ . '::get_formatted_taxes', 10, 2 );

		// Override Totals
		add_filter( 'woocommerce_cart_subtotal', __CLASS__ . '::get_cart_subtotal', 10, 2 );
		add_filter( 'woocommerce_cart_contents_total', __CLASS__ . '::get_cart_contents_total', 10, 2 );
		add_filter( 'woocommerce_cart_total', __CLASS__ . '::get_total', 10, 2 );
		add_filter( 'woocommerce_cart_total_ex_tax', __CLASS__ . '::get_total_ex_tax', 10, 2 );

	}

	/**
	 * Returns the formatted subscription price string for an item
	 *
	 * @since 1.0
	 */
	public static function get_cart_item_price_html( $price_string, $cart_item ) {
		global $woocommerce;

		if( WC_Subscriptions_Product::is_subscription( $cart_item['product_id'] ) ) {
			$exclude_tax = ( $woocommerce->cart->display_cart_ex_tax && $woocommerce->cart->prices_include_tax ) ? 'exclude_tax' : '';
			$price_string = '<span class="subscription-price">' . WC_Subscriptions_Product::get_price_string( $cart_item['product_id'], array( 'price' => $price_string, 'tax_calculation' => $exclude_tax ) ) . '</span>';
		}

		return $price_string;
	}

	/**
	 * Returns the subtotal for a cart item including the subscription period and duration details
	 *
	 * @since 1.0
	 */
	public static function get_product_subtotal( $product_subtotal, $product ){
		global $woocommerce;

		if( WC_Subscriptions_Product::is_subscription( $product ) ) {
			$exclude_tax = ( $woocommerce->cart->display_cart_ex_tax && $woocommerce->cart->prices_include_tax ) ? 'exclude_tax' : '';
			$product_subtotal = '<span class="subscription-price">' . WC_Subscriptions_Product::get_price_string( $product, array( 'price' => $product_subtotal, 'tax_calculation' => $exclude_tax ) ) . '</span>';
		}

		return $product_subtotal;
	}

	/**
	 * Includes the sign-up fee subtotal in the subtotal displayed in the cart.
	 *
	 * @since 1.0
	 */
	public static function get_cart_subtotal( $cart_subtotal, $compound ) {
		global $woocommerce;

		if ( self::cart_contains_subscription() ) {

			// If the cart has compound tax, we want to show the subtotal as cart + non-compound taxes (after discount)
			if ( $compound ) {

				$cart_subtotal = self::get_cart_subscription_string( $cart_subtotal, $woocommerce->cart->cart_contents_sign_up_fee_total + self::get_sign_up_taxes_total( false ) );

			// Otherwise we show cart items totals only (before discount)
			} else {

				// Display ex tax if the option is set, or prices exclude tax
				if ( $woocommerce->cart->display_totals_ex_tax || ! $woocommerce->cart->prices_include_tax ) {

					$cart_subtotal = self::get_cart_subscription_string( $cart_subtotal, $woocommerce->cart->sign_up_fee_subtotal_ex_tax );

					if ( $woocommerce->cart->tax_total > 0 && $woocommerce->cart->prices_include_tax )
						$cart_subtotal .= ' <small>' . $woocommerce->countries->ex_tax_or_vat() . '</small>';

				} else {

					$cart_subtotal = self::get_cart_subscription_string( $cart_subtotal, $woocommerce->cart->sign_up_fee_subtotal );

					if ( $woocommerce->cart->tax_total > 0 && ! $woocommerce->cart->prices_include_tax ) {
						$cart_subtotal .= ' <small>' . $woocommerce->countries->inc_tax_or_vat() . '</small>';
					}
				}
			}
		}

		return $cart_subtotal;
	}

	/**
	 * Returns a string with the cart discount and subscription period.
	 *
	 * @return mixed formatted price or false if there are none
	 * @since 1.0
	 */
	public static function get_discounts_before_tax( $discount ) {
		global $woocommerce;

		if ( $discount !== false && self::cart_contains_subscription() ) {

			$discount = self::get_cart_subscription_string( $discount );

			if ( $woocommerce->cart->sign_up_fee_discount_cart )
				$discount = sprintf( __( '%s with %s discount on the sign-up fee', WC_Subscriptions::$text_domain ), $discount, woocommerce_price( $woocommerce->cart->sign_up_fee_discount_cart ) );

		}

		return $discount;
	}

	/**
	 * Gets the order discount amount - these are applied after tax
	 *
	 * @return mixed formatted price or false if there are none
	 * @since 1.0
	 */
	public static function get_discounts_after_tax( $discount ) {
		global $woocommerce;

		if ( $discount !== false && self::cart_contains_subscription() ) {

			$discount = self::get_cart_subscription_string( $discount );

			if ( $woocommerce->cart->sign_up_fee_discount_total )
				$discount = sprintf( __( '%s with %s discount on the sign-up fee', WC_Subscriptions::$text_domain ), $discount, woocommerce_price( $woocommerce->cart->sign_up_fee_discount_total ) );

		}

		return $discount;
	}

	/**
	 * Displays each cart tax in a subscription string and calculates the sign-up fee taxes (if any)
	 * to display in the string.
	 *
	 * @since 1.0
	 */
	public static function get_formatted_taxes( $taxes ) {
		global $woocommerce;

		if ( self::cart_contains_subscription() ) {

			foreach ( $woocommerce->cart->get_cart() as $cart_item_key => $cart_item ) {

				$product = new WC_Product( $cart_item['product_id'] );

				if ( $product->is_taxable() ) {

					$base_tax_rates = $woocommerce->cart->tax->get_shop_base_rate( $product->tax_class );
					$tax_rates      = $woocommerce->cart->tax->get_rates( $product->get_tax_class() ); // This will get the base rate unless we're on the checkout page
					$signup_fee     = WC_Subscriptions_Product::get_sign_up_fee( $product );

					// Remove discounts from sign-up fee
					if ( $woocommerce->cart->sign_up_fee_discount_cart )
						$signup_fee -= $woocommerce->cart->sign_up_fee_discount_cart;

					if ( $woocommerce->cart->display_cart_ex_tax && $tax_rates == $base_tax_rates && $woocommerce->cart->prices_include_tax ) {

						if ( $signup_fee > 0 )
							$signup_taxes = $woocommerce->cart->tax->calc_tax( $signup_fee, $base_tax_rates, true );

					} elseif ( $woocommerce->cart->display_cart_ex_tax && $woocommerce->cart->prices_include_tax ) {

						if ( $signup_fee > 0 ) {
							$base_signup_taxes = $woocommerce->cart->tax->calc_tax( $signup_fee, $base_tax_rates, true, true );
							$signup_taxes      = $woocommerce->cart->tax->calc_tax( $signup_fee - array_sum( $base_signup_taxes ), $tax_rates, false );
						}

					} else {

						if ( $signup_fee > 0 )
							$signup_taxes = $woocommerce->cart->tax->calc_tax( $signup_fee, $tax_rates, false );

					}

				}

				foreach ( $taxes as $tax_id => $tax_amount ) {

					$subscription_interval = WC_Subscriptions_Product::get_interval( $product );

					$subscription_tax_string = sprintf( _n( ' %s / %s', ' %s every %s', $subscription_interval, WC_Subscriptions::$text_domain ), $tax_amount, WC_Subscriptions_Manager::get_subscription_period_strings( $subscription_interval, WC_Subscriptions_Product::get_period( $product ) ) );

					if ( WC_Subscriptions_Product::get_sign_up_fee( $product ) && isset( $signup_taxes[$tax_id] ) && $signup_taxes[$tax_id] > 0 )
						$subscription_tax_string = sprintf( __( '%s with %s tax on the sign-up fee', WC_Subscriptions::$text_domain ), $subscription_tax_string, woocommerce_price( $signup_taxes[$tax_id] ) );

					$taxes[$tax_id] = $subscription_tax_string;

				}
			}
		}

		return $taxes;
	}

	/**
	 * Appends the cart subscription string to a cart total using the @see self::get_cart_subscription_string and then returns it. 
	 *
	 * @return string Formatted subscription price string for the cart total.
	 * @since 1.0
	 */
	public static function get_total( $total ) {
		global $woocommerce;

		if ( self::cart_contains_subscription() )
			$total = self::get_cart_subscription_string( $total, $woocommerce->cart->sign_up_fee_total );

		return $total;
	}

	/**
	 * Appends the cart subscription string to a cart total using the @see self::get_cart_subscription_string and then returns it. 
	 *
	 * @return string Formatted subscription price string for the cart total.
	 * @since 1.0
	 */
	public static function get_total_ex_tax( $total_ex_tax ) {
		global $woocommerce;

		if ( self::cart_contains_subscription() ) {

			$sign_up_total_ex_tax = $woocommerce->cart->sign_up_fee_total - $woocommerce->cart->sign_up_fee_tax_total;

			if ( $sign_up_total_ex_tax < 0 )
				$sign_up_total_ex_tax = '';

			$total_ex_tax = self::get_cart_subscription_string( $total_ex_tax, $sign_up_total_ex_tax );
		}

		return $total_ex_tax;
	}

	/**
	 * Returns either the total if prices include tax because this doesn't include tax, or the 
	 * subtotal if prices don't includes tax, because this doesn't include tax. 
	 *
	 * @return string formatted price
	 *
	 * @since 1.0
	 */
	public static function get_cart_contents_total( $cart_contents_total ) {
		global $woocommerce;

		if ( self::cart_contains_subscription() ) {

			if ( ! $woocommerce->cart->prices_include_tax )
				$cart_contents_total = self::get_cart_subscription_string( $cart_contents_total, $woocommerce->cart->cart_contents_sign_up_fee_total );
			else
				$cart_contents_total = self::get_cart_subscription_string( $cart_contents_total, $woocommerce->cart->cart_contents_sign_up_fee_total + $woocommerce->cart->sign_up_fee_tax_total );

		}

		return $cart_contents_total;
	}

	/*
	 * Helper functions for extracting the details of subscriptions in the cart
	 */

	/**
	 * Creates a string representation of the subscription period/term for each item in the cart
	 * 
	 * @param $price_string float (optional) The price to display in the subscription. Defaults to empty, which returns just the period & duration components of the string.
	 * @param $include array (optional) Array of flags to determine what is included in the price. Options:
	 * 			'length' Include the length of the subscription.
	 * 			'sign_up_fee' Include the sign-up fee for the subscription.
	 * 			'exclude_tax' Remove tax from the price (and other prices to include in the string, like sign-up fee)
	 * @since 1.0
	 */
	public static function get_cart_subscription_string( $subscription_price, $sign_up_fee = 0 ) {
		global $woocommerce;

		if ( strpos( $subscription_price, $woocommerce->countries->inc_tax_or_vat() ) !== false )
			$subscription_price = str_replace( $woocommerce->countries->inc_tax_or_vat(), '', $subscription_price );
		if ( strpos( $subscription_price, $woocommerce->countries->ex_tax_or_vat() ) !== false )
			$subscription_price = str_replace( $woocommerce->countries->ex_tax_or_vat(), '', $subscription_price );

		$subscription_interval = self::get_cart_subscription_interval();

		$subscription_string = sprintf( _n( ' %s / %s', ' %s every %s', $subscription_interval, WC_Subscriptions::$text_domain ), $subscription_price, WC_Subscriptions_Manager::get_subscription_period_strings( $subscription_interval, strtolower( self::get_cart_subscription_period() ) ) );

		if ( self::get_cart_subscription_length() ) {
			$ranges = WC_Subscriptions_Manager::get_subscription_ranges( self::get_cart_subscription_period() );
			$subscription_string = sprintf( __( '%s for %s', WC_Subscriptions::$text_domain ), $subscription_string, $ranges[self::get_cart_subscription_length()] );
		}

		if ( self::get_cart_subscription_trial_length() ) {
			$trial_lengths = WC_Subscriptions_Manager::get_subscription_trial_lengths( self::get_cart_subscription_period() );
			$subscription_string = sprintf( __( '%s with %s free trial', WC_Subscriptions::$text_domain ), $subscription_string, $trial_lengths[self::get_cart_subscription_trial_length()] );
		}

		if ( $sign_up_fee > 0 )
			$subscription_string = sprintf( __( '%s and a %s sign-up fee', WC_Subscriptions::$text_domain ), $subscription_string, woocommerce_price( $sign_up_fee ) );

		return $subscription_string;
	}

	/**
	 * Gets the subscription period from the cart and returns it as an array (eg. array( 'month', 'day' ) )
	 * 
	 * @since 1.0
	 */
	public static function get_cart_subscription_period() {
		global $woocommerce;

		foreach ( $woocommerce->cart->cart_contents as $cart_item ) {
			if ( WC_Subscriptions_Product::is_subscription( $cart_item['product_id'] ) ) {
				$period = WC_Subscriptions_Product::get_period( $cart_item['product_id'] );
				break;
			}
		}

		return $period;
	}

	/**
	 * Gets the subscription period from the cart and returns it as an array (eg. array( 'month', 'day' ) )
	 * 
	 * @since 1.0
	 */
	public static function get_cart_subscription_interval() {
		global $woocommerce;

		foreach ( $woocommerce->cart->cart_contents as $cart_item ) {
			if ( WC_Subscriptions_Product::is_subscription( $cart_item['product_id'] ) ) {
				$interval = WC_Subscriptions_Product::get_interval( $cart_item['product_id'] );
				break;
			}
		}

		return $interval;
	}

	/**
	 * Gets the subscription length from the cart and returns it as an array (eg. array( 'month', 'day' ) )
	 * 
	 * @since 1.0
	 */
	public static function get_cart_subscription_length() {
		global $woocommerce;

		$length = 0;

		foreach ( $woocommerce->cart->cart_contents as $cart_item ) {
			if ( WC_Subscriptions_Product::is_subscription( $cart_item['product_id'] ) ) {
				$length = WC_Subscriptions_Product::get_length( $cart_item['product_id'] );
				break;
			}
		}

		return $length;
	}

	/**
	 * Gets the subscription length from the cart and returns it as an array (eg. array( 'month', 'day' ) )
	 * 
	 * @since 1.1
	 */
	public static function get_cart_subscription_trial_length() {
		global $woocommerce;

		$trial_length = 0;

		foreach ( $woocommerce->cart->cart_contents as $cart_item ) {
			if ( WC_Subscriptions_Product::is_subscription( $cart_item['product_id'] ) ) {
				$trial_length = WC_Subscriptions_Product::get_trial_length( $cart_item['product_id'] );
				break;
			}
		}

		return $trial_length;
	}

	/**
	 * Gets the subscription sign up fee for the cart and returns it
	 * 
	 * Currently short-circuits to return just the sign-up fee of the first subscription, because only
	 * one subscription can be purchased at a time. 
	 * 
	 * @since 1.0
	 */
	public static function get_cart_subscription_sign_up_fee() {
		global $woocommerce;

		foreach ( $woocommerce->cart->cart_contents as $cart_item ) {
			if ( WC_Subscriptions_Product::is_subscription( $cart_item['product_id'] ) ) {
				$sign_up_fee = WC_Subscriptions_Product::get_sign_up_fee( $cart_item['product_id'] );
				break;
			}
		}

		return $sign_up_fee;
	}

	/**
	 * Checks the cart to see if it contains a subscription product. 
	 * 
	 * @since 1.0
	 */
	public static function cart_contains_subscription() {
		global $woocommerce;

		$contains_subscription = false;

		if ( ! empty( $woocommerce->cart->cart_contents ) ) {
			foreach ( $woocommerce->cart->cart_contents as $cart_item ) {
				if ( WC_Subscriptions_Product::is_subscription( $cart_item['product_id'] ) ) {
					$contains_subscription = true;
					break;
				}
			}
		}

		return $contains_subscription;
	}

	/**
	 * Calculate totals for the sign-up fees in the cart, based on @see WC_Cart::calculate_totals()
	 * 
	 * @since 1.0
	 */
	public static function calculate_sign_up_fee_totals() {
		global $woocommerce;

		if ( ! self::cart_contains_subscription() )
			return;

		self::reset();

		// Get count of all items + weights + subtotal (we may need this for discounts)
		if ( count( $woocommerce->cart->cart_contents ) > 0 ) {

			foreach ( $woocommerce->cart->cart_contents as $cart_item_key => $values ) {

				$product = $values['data'];

				$woocommerce->cart->cart_contents_sign_up_fee_count  = $woocommerce->cart->cart_contents_sign_up_fee_count + $values['quantity'];

				// Base Price (inlusive of tax for now)
				$row_base_price = WC_Subscriptions_Product::get_sign_up_fee( $product ) * $values['quantity'];
				$base_tax_rates = $woocommerce->cart->tax->get_shop_base_rate( $product->tax_class );
				$tax_amount     = 0;

				// Row price
				if ( $woocommerce->cart->prices_include_tax ) {

					if ( $product->is_taxable() ) {

						$tax_rates = $woocommerce->cart->tax->get_rates( $product->get_tax_class() );

						// ADJUST BASE if tax rate is different (different region or modified tax class)
						if ( $tax_rates !== $base_tax_rates ) {
							$base_taxes     = $woocommerce->cart->tax->calc_tax( $row_base_price, $base_tax_rates, true );
							$modded_taxes   = $woocommerce->cart->tax->calc_tax( $row_base_price - array_sum( $base_taxes ), $tax_rates, false );
							$row_base_price = ( $row_base_price - array_sum( $base_taxes ) ) + array_sum( $modded_taxes );
						}

						$taxes      = $woocommerce->cart->tax->calc_tax( $row_base_price, $tax_rates, true );
						$tax_amount = $woocommerce->cart->tax->get_tax_total( $taxes );
					}

					// Sub total is based on base prices (without discounts or shipping)
					$woocommerce->cart->sign_up_fee_subtotal        = $woocommerce->cart->sign_up_fee_subtotal + $row_base_price;
					$woocommerce->cart->sign_up_fee_subtotal_ex_tax = $woocommerce->cart->sign_up_fee_subtotal_ex_tax + ( $row_base_price - $tax_amount);

				} else {

					if ( $product->is_taxable() ) {
						$tax_rates  = $woocommerce->cart->tax->get_rates( $product->get_tax_class() );
						$taxes      = $woocommerce->cart->tax->calc_tax( $row_base_price, $tax_rates, false );
						$tax_amount = $woocommerce->cart->tax->get_tax_total( $taxes );
					}

					// Sub total is based on base prices (without discounts)
					$woocommerce->cart->sign_up_fee_subtotal        = $woocommerce->cart->sign_up_fee_subtotal + $row_base_price + $tax_amount;
					$woocommerce->cart->sign_up_fee_subtotal_ex_tax = $woocommerce->cart->sign_up_fee_subtotal_ex_tax + $row_base_price;

				}

			}
		}

		// Now calc the main totals, including discounts
		if ( $woocommerce->cart->prices_include_tax ) {

			/** 
			 * Calculate totals for items
			 */
			if ( count( $woocommerce->cart->cart_contents ) > 0 ) { 

				foreach ($woocommerce->cart->cart_contents as $cart_item_key => $values ) {

					$product = $values['data'];

					// Base Price (inlusive of tax for now)
					$base_price = WC_Subscriptions_Product::get_sign_up_fee( $product );

					// Base Price Adjustment
					if ( $product->is_taxable() ) {

						// Get rates
						$tax_rates = $woocommerce->cart->tax->get_rates( $product->get_tax_class() );

						/**
						 * ADJUST TAX - Checkout calculations when customer is OUTSIDE the shop base country and prices INCLUDE tax
						 * 	OR
						 * ADJUST TAX - Checkout calculations when a tax class is modified
						 */
						if ( ( $woocommerce->customer->is_customer_outside_base() && ( defined( 'WOOCOMMERCE_CHECKOUT' ) || $woocommerce->customer->has_calculated_shipping() ) ) || ( $product->get_tax_class() !== $product->tax_class ) ) {

							// Get tax rate for the store base, ensuring we use the unmodified tax_class for the product
							$base_tax_rates    = $woocommerce->cart->tax->get_shop_base_rate( $product->tax_class );

							// Work out new price based on region
							$row_base_price    = $base_price * $values['quantity'];
							$base_taxes        = $woocommerce->cart->tax->calc_tax( $row_base_price, $base_tax_rates, true, true );
							$taxes             = $woocommerce->cart->tax->calc_tax( $row_base_price - array_sum( $base_taxes ), $tax_rates, false );

							// Tax amount
							$tax_amount        = array_sum( $taxes );

							// Line subtotal + tax
							$line_subtotal_tax = ( get_option( 'woocommerce_tax_round_at_subtotal' ) == 'no' ) ? round( $tax_amount, 2 ) : $tax_amount;
							$line_subtotal     = $row_base_price - $woocommerce->cart->tax->get_tax_total( $base_taxes );

							// Adjusted price
							$adjusted_price    = ( $row_base_price - array_sum( $base_taxes ) + array_sum( $taxes ) ) / $values['quantity'];

							// Apply discounts
							$discounted_price  = self::get_discounted_price( $values, $adjusted_price, true );

							$discounted_taxes      = $woocommerce->cart->tax->calc_tax( $discounted_price * $values['quantity'], $tax_rates, true );
							$discounted_tax_amount = array_sum( $discounted_taxes ); // Sum taxes

						/**
						 * Regular tax calculation (customer inside base and the tax class is unmodified
						 */
						} else {
							// Base tax for line before discount - we will store this in the order data
							$tax_amount            = array_sum( $woocommerce->cart->tax->calc_tax( $base_price * $values['quantity'], $tax_rates, true ) );

							// Line subtotal + tax
							$line_subtotal_tax     = ( get_option( 'woocommerce_tax_round_at_subtotal' ) == 'no' ) ? round( $tax_amount, 2 ) : $tax_amount;
							$line_subtotal         = ( $base_price * $values['quantity'] ) - round( $line_subtotal_tax, 2 );

							// Calc prices and tax (discounted)
							$discounted_price      = self::get_discounted_price( $values, $base_price, true );
							$discounted_taxes      = $woocommerce->cart->tax->calc_tax( $discounted_price * $values['quantity'], $tax_rates, true );
							$discounted_tax_amount = array_sum( $discounted_taxes ); // Sum taxes
						}

						// Tax rows - merge the totals we just got
						foreach ( array_keys( $woocommerce->cart->sign_up_fee_taxes + $discounted_taxes ) as $key )
							$woocommerce->cart->sign_up_fee_taxes[$key] = ( isset( $discounted_taxes[$key] ) ? $discounted_taxes[$key] : 0 ) + ( isset( $woocommerce->cart->sign_up_fee_taxes[$key] ) ? $woocommerce->cart->sign_up_fee_taxes[$key] : 0 );

					} else {

						// Discounted Price (price with any pre-tax discounts applied)
						$discounted_price      = self::get_discounted_price( $values, $base_price, true );
						$discounted_tax_amount = 0;
						$tax_amount            = 0;
						$line_subtotal_tax     = 0;
						$line_subtotal         = ( $base_price * $values['quantity'] );

					}

					// Line prices
					$line_tax   = ( get_option( 'woocommerce_tax_round_at_subtotal' ) == 'no' ) ? round( $discounted_tax_amount, 2 ) : $discounted_tax_amount;
					$line_total = ( $discounted_price * $values['quantity'] ) - round( $line_tax, 2 );

					// Add any product discounts (after tax)
					self::apply_product_discounts_after_tax( $values, $line_total + $discounted_tax_amount );

					// Cart contents total is based on discounted prices and is used for the final total calculation
					$woocommerce->cart->cart_contents_sign_up_fee_total = $woocommerce->cart->cart_contents_sign_up_fee_total + $line_total;

					// Store costs + taxes for lines
					$woocommerce->cart->cart_contents[$cart_item_key]['line_sign_up_fee_total']        = $line_total;
					$woocommerce->cart->cart_contents[$cart_item_key]['line_sign_up_fee_tax']          = $line_tax;
					$woocommerce->cart->cart_contents[$cart_item_key]['line_sign_up_fee_subtotal']     = $line_subtotal;
					$woocommerce->cart->cart_contents[$cart_item_key]['line_sign_up_fee_subtotal_tax'] = $line_subtotal_tax;

				}
			}

		} else {

			if ( count( $woocommerce->cart->cart_contents ) > 0 ) { 
				foreach ( $woocommerce->cart->cart_contents as $cart_item_key => $values ) {
					/** 
					 * Prices exclude tax
					 *
					 * This calculation is simpler - work with the base, untaxed price.
					 */
					$product = $values['data'];

					// Base Price (i.e. no tax, regardless of region)
					$base_price = WC_Subscriptions_Product::get_sign_up_fee( $product );

					// Discounted Price (base price with any pre-tax discounts applied
					$discounted_price = self::get_discounted_price( $values, $base_price, true );

					// Tax Amount (For the line, based on discounted, ex.tax price)
					if ( $product->is_taxable() ) {

						// Get tax rates
						$tax_rates             = $woocommerce->cart->tax->get_rates( $product->get_tax_class() );

						// Base tax for line before discount - we will store this in the order data
						$tax_amount            = array_sum( $woocommerce->cart->tax->calc_tax( $base_price * $values['quantity'], $tax_rates, false ) );

						// Now calc product rates
						$discounted_taxes      = $woocommerce->cart->tax->calc_tax( $discounted_price * $values['quantity'], $tax_rates, false );
						$discounted_tax_amount = array_sum( $discounted_taxes );

						// Tax rows - merge the totals we just got
						foreach ( array_keys( $woocommerce->cart->taxes + $discounted_taxes ) as $key )
						    $woocommerce->cart->sign_up_fee_taxes[$key] = ( isset( $discounted_taxes[$key] ) ? $discounted_taxes[$key] : 0 ) + ( isset( $woocommerce->cart->sign_up_fee_taxes[$key] ) ? $woocommerce->cart->sign_up_fee_taxes[$key] : 0 );

					} else {

						$discounted_tax_amount = 0;
						$tax_amount            = 0;

					}

					// Line prices
					$line_subtotal_tax = $tax_amount;
					$line_tax          = $discounted_tax_amount;
					$line_subtotal     = $base_price * $values['quantity'];	
					$line_total        = $discounted_price * $values['quantity'];	

					// Add any product discounts (after tax)
					self::apply_product_discounts_after_tax( $values, $line_total + $line_tax );

					// Cart contents total is based on discounted prices and is used for the final total calculation
					$woocommerce->cart->cart_contents_sign_up_fee_total = $woocommerce->cart->cart_contents_sign_up_fee_total + $line_total;

					// Store costs + taxes for lines
					$woocommerce->cart->cart_contents[$cart_item_key]['line_sign_up_fee_total']        = $line_total;
					$woocommerce->cart->cart_contents[$cart_item_key]['line_sign_up_fee_tax']          = $line_tax;
					$woocommerce->cart->cart_contents[$cart_item_key]['line_sign_up_fee_subtotal']     = $line_subtotal;
					$woocommerce->cart->cart_contents[$cart_item_key]['line_sign_up_fee_subtotal_tax'] = $line_subtotal_tax;

				} 
			}
		}

		// Set tax total to sum of all tax rows
		$woocommerce->cart->sign_up_fee_tax_total = $woocommerce->cart->tax->get_tax_total( $woocommerce->cart->sign_up_fee_taxes );

		// VAT exemption done at this point - so all totals are correct before exemption
		if ( $woocommerce->customer->is_vat_exempt() || ( is_cart() && get_option( 'woocommerce_display_cart_taxes' ) == 'no' ) ) {
			$woocommerce->cart->sign_up_fee_tax_total = 0;
			$woocommerce->cart->sign_up_fee_taxes = array();
		}

		// Cart Discounts (after tax)
		self::apply_cart_discounts_after_tax();

		// Only go beyond this point if on the cart/checkout
		if ( ! is_checkout() && ! is_cart() && ! defined( 'WOOCOMMERCE_CHECKOUT' ) && ! defined( 'WOOCOMMERCE_CART' ) )
			return;

		// Round cart/shipping tax rows
		$woocommerce->cart->sign_up_fee_taxes = array_map( array( &$woocommerce->cart->tax, 'round' ), $woocommerce->cart->sign_up_fee_taxes );

		/** 
		 * Grand Total
		 *
		 * Based on discounted product prices, discounted tax, shipping cost + tax, and any discounts to be added after tax (e.g. store credit)
		 */
		$woocommerce->cart->sign_up_fee_total = number_format( $woocommerce->cart->cart_contents_sign_up_fee_total + $woocommerce->cart->sign_up_fee_tax_total - $woocommerce->cart->sign_up_fee_discount_total, 2, '.', '' );

		if ( $woocommerce->cart->sign_up_fee_total < 0 )
			$woocommerce->cart->sign_up_fee_total = 0;

	}

	/**
	 * Function to apply discounts to a product and get the discounted price (before tax is applied)
	 * 
	 * @param mixed $values
	 * @param mixed $price
	 * @param bool $add_totals (default: false)
	 * @return float price
	 * @since 1.0
	 */
	public static function get_discounted_price( $values, $price, $add_totals = false ){
		global $woocommerce;

		if ( ! $price )
			return $price;

		if ( ! empty( $woocommerce->cart->applied_coupons ) ) {
			foreach ( $woocommerce->cart->applied_coupons as $code ) {
				$coupon = new WC_Coupon( $code );

				if ( $coupon->apply_before_tax() && $coupon->is_valid() ) {

					switch ( $coupon->type ) {

						case "fixed_product" :
						case "percent_product" :

							$this_item_is_discounted = false;

							$product_cats = wp_get_post_terms( $values['product_id'], 'product_cat', array("fields" => "ids") );

							// Specific products get the discount
							if ( sizeof( $coupon->product_ids ) > 0 ) {

								if ( in_array( $values['product_id'], $coupon->product_ids ) || in_array( $values['variation_id'], $coupon->product_ids ) || in_array( $values['data']->get_parent(), $coupon->product_ids ) ) 
									$this_item_is_discounted = true;

							// Category discounts
							} elseif ( sizeof($coupon->product_categories ) > 0 ) {

								if ( sizeof( array_intersect( $product_cats, $coupon->product_categories ) ) > 0 ) 
									$this_item_is_discounted = true;

							} else {

								// No product ids - all items discounted
								$this_item_is_discounted = true;

							}

							// Specific product ID's excluded from the discount
							if ( sizeof( $coupon->exclude_product_ids ) > 0 ) 
								if ( in_array( $values['product_id'], $coupon->exclude_product_ids ) || in_array( $values['variation_id'], $coupon->exclude_product_ids ) || in_array( $values['data']->get_parent(), $coupon->exclude_product_ids ) )
									$this_item_is_discounted = false;

							// Specific categories excluded from the discount
							if ( sizeof( $coupon->exclude_product_categories ) > 0 ) 
								if ( sizeof( array_intersect( $product_cats, $coupon->exclude_product_categories ) ) > 0 ) 
									$this_item_is_discounted = false;

							// Apply filter
							$this_item_is_discounted = apply_filters( 'woocommerce_item_is_discounted', $this_item_is_discounted, $values, $before_tax = true );

							// Apply the discount
							if ( $this_item_is_discounted ) {
								if ( $coupon->type=='fixed_product' ) {

									if ( $price < $coupon->amount ) {
										$discount_amount = $price;
									} else {
										$discount_amount = $coupon->amount;
									}

									$price = $price - $coupon->amount;

									if ( $price < 0 )
										$price = 0;

									if ( $add_totals )
										$woocommerce->cart->sign_up_fee_discount_cart = $woocommerce->cart->sign_up_fee_discount_cart + ( $discount_amount * $values['quantity'] );

								} elseif ( $coupon->type == 'percent_product' ) {

									$percent_discount = ( WC_Subscriptions_Product::get_sign_up_fee_excluding_tax( $values['data'] ) / 100 ) * $coupon->amount;

									if ( $add_totals )
										$woocommerce->cart->sign_up_fee_discount_cart = $woocommerce->cart->sign_up_fee_discount_cart + ( $percent_discount * $values['quantity'] );

									$price = $price - $percent_discount;
								}
							}

						break;

						case "fixed_cart" :

							/** 
							 * This is the most complex discount - we need to divide the discount between rows based on their price in
							 * proportion to the subtotal. This is so rows with different tax rates get a fair discount, and so rows
							 * with no price (free) don't get discount too.
							 */

							// Get item discount by dividing item cost by subtotal to get a %
							if ( $woocommerce->cart->sign_up_fee_subtotal_ex_tax )
								$discount_percent = ( WC_Subscriptions_Product::get_sign_up_fee_excluding_tax( $values['data'] ) * $values['quantity'] ) / $woocommerce->cart->sign_up_fee_subtotal_ex_tax;
							else
								$discount_percent = 0;

							// Use pence to help prevent rounding errors
							$coupon_amount_pence = $coupon->amount * 100;

							// Work out the discount for the row
							$item_discount = $coupon_amount_pence * $discount_percent;

							// Work out discount per item
							$item_discount = $item_discount / $values['quantity'];

							// Pence
							$price = ( $price * 100 );

							// Check if discount is more than price
							if ( $price < $item_discount )
								$discount_amount = $price;
							else
								$discount_amount = $item_discount;

							// Take discount off of price (in pence)
							$price = $price - $discount_amount;

							// Back to pounds
							$price = $price / 100; 

							// Cannot be below 0
							if ( $price < 0 )
								$price = 0;

							// Add coupon to discount total (once, since this is a fixed cart discount and we don't want rounding issues)
							if ( $add_totals )
								$woocommerce->cart->sign_up_fee_discount_cart = $woocommerce->cart->sign_up_fee_discount_cart + ( ( $discount_amount * $values['quantity'] ) / 100 );

						break;

						case "percent" :
							
							$percent_discount = ( WC_Subscriptions_Product::get_sign_up_fee( $values['data']->id ) / 100 ) * $coupon->amount;

							if ( $add_totals )
								$woocommerce->cart->sign_up_fee_discount_cart = $woocommerce->cart->sign_up_fee_discount_cart + ( $percent_discount * $values['quantity'] );

							$price = $price - $percent_discount;

						break;

					}
				}
			}
		}

		return $price;
	}

	/**
	 * Function to apply product discounts after tax
	 * 
	 * @param mixed $values
	 * @param mixed $price
	 * @since 1.0
	 */
	public static function apply_product_discounts_after_tax( $values, $price ){
		global $woocommerce;

		if ( ! empty( $woocommerce->cart->applied_coupons) ) {
			foreach ( $woocommerce->cart->applied_coupons as $code ) {
				$coupon = new WC_Coupon( $code );

				do_action( 'woocommerce_product_discount_after_tax_' . $coupon->type, $coupon );

				if ( $coupon->type != 'fixed_product' && $coupon->type != 'percent_product' )
					continue;

				if ( ! $coupon->apply_before_tax() && $coupon->is_valid() ) {

					$product_cats = wp_get_post_terms( $values['product_id'], 'product_cat', array( "fields" => "ids" ) );

					$this_item_is_discounted = false;

					// Specific products get the discount
					if ( count( $coupon->product_ids ) > 0 ) {
						if ( in_array($values['product_id'], $coupon->product_ids ) || in_array( $values['variation_id'], $coupon->product_ids ) || in_array( $values['data']->get_parent(), $coupon->product_ids ) )
							$this_item_is_discounted = true;
					// Category discounts
					} elseif ( count( $coupon->product_categories ) > 0 ) {
						if ( sizeof( array_intersect( $product_cats, $coupon->product_categories ) ) > 0 ) 
							$this_item_is_discounted = true;
					} else { // No product ids - all items discounted
						$this_item_is_discounted = true;
					}

					// Specific product ID's excluded from the discount
					if ( count( $coupon->exclude_product_ids ) > 0 ) 
						if ( in_array( $values['product_id'], $coupon->exclude_product_ids ) || in_array( $values['variation_id'], $coupon->exclude_product_ids ) || in_array( $values['data']->get_parent(), $coupon->exclude_product_ids ) )
							$this_item_is_discounted = false;

					// Specific categories excluded from the discount
					if ( count( $coupon->exclude_product_categories ) > 0 ) 
						if ( count( array_intersect( $product_cats, $coupon->exclude_product_categories ) ) > 0 ) 
							$this_item_is_discounted = false;

					// Apply filter
					$this_item_is_discounted = apply_filters( 'woocommerce_item_is_discounted', $this_item_is_discounted, $values, $before_tax = false );

					// Apply the discount
					if ( $this_item_is_discounted ) {
						if ( $coupon->type == 'fixed_product' ) {

							if ( $price < $coupon->amount )
								$discount_amount = $price;
							else
								$discount_amount = $coupon->amount;

							$woocommerce->cart->sign_up_fee_discount_total = $woocommerce->cart->sign_up_fee_discount_total + ( $discount_amount * $values['quantity'] );

						} elseif ( $coupon->type == 'percent_product' ) {
							$woocommerce->cart->sign_up_fee_discount_total = $woocommerce->cart->sign_up_fee_discount_total + ( $price / 100 ) * $coupon->amount;
						}
					}
				}
			}
		}
	}

	/**
	 * Function to apply cart discounts after tax
	 * 
	 * @since 1.0
	 */
	public static function apply_cart_discounts_after_tax(){
		global $woocommerce;

		if ( $woocommerce->cart->applied_coupons ) {

			foreach ( $woocommerce->cart->applied_coupons as $code ) {

				$coupon = new WC_Coupon( $code );

				do_action( 'woocommerce_cart_discount_after_tax_' . $coupon->type, $coupon );

				if ( ! $coupon->apply_before_tax() && $coupon->is_valid() ) {

					switch ( $coupon->type ) {
						case "fixed_cart" :
							$woocommerce->cart->sign_up_fee_discount_total = $woocommerce->cart->sign_up_fee_discount_total + $coupon->amount;
							break;
						case "percent" :
							$percent_discount = ( $woocommerce->cart->cart_contents_sign_up_fee_total + $woocommerce->cart->sign_up_fee_tax_total / 100 ) * $coupon->amount;
							$woocommerce->cart->sign_up_fee_discount_total = $woocommerce->cart->sign_up_fee_discount_total + $percent_discount;
							break;
					}
				}
			}
		}
	}

	/**
	 * Get the sign-up fee cart values from the session
	 * 
	 * @since 1.0
	 */
	public static function get_cart_from_session(){
		global $woocommerce;

		foreach ( self::get_sign_up_fee_fields() as $field )
			$woocommerce->cart->{$field} = isset( $_SESSION[$field] ) ? $_SESSION[$field] : 0;

		if ( $woocommerce->cart->sign_up_fee_taxes == 0 ) // There's always one exception
			$woocommerce->cart->sign_up_fee_taxes = array();
	}

	/**
	 * Store the sign-up fee cart values in the session
	 * 
	 * @since 1.0
	 */
	public static function set_session(){
		global $woocommerce;

		foreach ( self::get_sign_up_fee_fields() as $field )
			$_SESSION[$field] = isset( $woocommerce->cart->{$field} ) ? $woocommerce->cart->{$field} : 0;

		if ( isset( $woocommerce->cart->sign_up_fee_taxes ) && $woocommerce->cart->sign_up_fee_taxes == 0 ) // There's always one exception
			$woocommerce->cart->sign_up_fee_taxes = array();

	}

	/**
	 * Reset the sign-up fee fields in the current session
	 * 
	 * @since 1.0
	 */
	public static function reset(){
		global $woocommerce;

		foreach ( self::get_sign_up_fee_fields() as $field ) {
			$woocommerce->cart->{$field} = 0;
			unset( $_SESSION[$field] );
		}

		if ( $woocommerce->cart->sign_up_fee_taxes == 0 ) // There's always one exception
			$woocommerce->cart->sign_up_fee_taxes = array();

	}

	/**
	 * Get tax row amounts with or without compound taxes includes
	 *
	 * @return float price
	 */
	public static function get_sign_up_taxes_total( $compound = true ) {
		global $woocommerce;

		$sign_up_taxes_total = 0;

		foreach ( $woocommerce->cart->sign_up_fee_taxes as $key => $tax ) {

			if ( ! $compound && $woocommerce->cart->tax->is_compound( $key ) )
				continue;

			$sign_up_taxes_total += $tax;
		}

		return $sign_up_taxes_total;
	}

	public static function get_sign_up_fee_fields(){
		return array(
			'cart_contents_sign_up_fee_total',
			'cart_contents_sign_up_fee_count',
			'sign_up_fee_total',
			'sign_up_fee_subtotal',
			'sign_up_fee_subtotal_ex_tax',
			'sign_up_fee_tax_total',
			'sign_up_fee_taxes',
			'sign_up_fee_discount_cart',
			'sign_up_fee_discount_total'
		);
	}
}

WC_Subscriptions_Cart::init();
