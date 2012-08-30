<?php
/**
 * Individual Subscription Product API
 * 
 * An API for accessing details of a subscription product.
 * 
 * @package		WooCommerce Subscriptions
 * @subpackage	WC_Subscriptions_Product
 * @category	Class
 * @author		Brent Shepherd
 * @since		1.0
 */
class WC_Subscriptions_Product {

	/**
	 * Set up the class, including it's hooks & filters, when the file is loaded.
	 *
	 * @since 1.0
	 **/
	public static function init() {

		// Because the vanilla price meta field is empty, we need to output our custom subscription description
		add_filter( 'woocommerce_price_html', __CLASS__ . '::get_price_html', 10, 2 );

		// Gravity Forms Add-ons
		add_filter( 'woocommerce_gform_base_price', __CLASS__ . '::get_gravity_form_prices', 10, 2 );
		add_filter( 'woocommerce_gform_total_price', __CLASS__ . '::get_gravity_form_prices', 10, 2 );
		add_filter( 'woocommerce_gform_variation_total_price', __CLASS__ . '::get_gravity_form_prices', 10, 2 );
	}

	/**
	 * Checks a given product to determine if it is a subscription.
	 * 
	 * Can be passed either a product object or product ID.
	 *
	 * @since 1.0
	 */
	public static function is_subscription( $product ) {

		if ( ! is_object( $product ) )
			$product = new WC_Product( $product ); // Shouldn't matter if product is variation as all we need is the product_type

		return ( $product->is_type( WC_Subscriptions::$name ) ) ? true : false;
	}

	/**
	 * Output subscription string as the price html
	 *
	 * @since 1.0
	 */
	public static function get_price_html( $price, $product ) {

		if ( self::is_subscription( $product ) )
			$price = self::get_price_string( $product, array( 'price' => $price ) );

		return $price;
	}

	/**
	 * Output subscription string in Gravity Form fields.
	 *
	 * @since 1.1
	 */
	public static function get_gravity_form_prices( $price, $product ) {

		if ( self::is_subscription( $product ) )
			$price = self::get_price_string( $product, array( 'price' => $price, 'subscription_length' => false, 'sign_up_fee' => false, 'trial_length' => false ) );

		return $price;
	}

	/**
	 * Returns a string representing the details of the subscription. 
	 * 
	 * For example "$20 per Month for 3 Months with a $10 sign-up fee".
	 * 
	 * @param $product WC_Product | Int A WC_Product object or ID of a WC_Product.
	 * @param $inclusions array An associative array of flags to indicate how to calculate the price and what to include, values:
	 *			'tax_calculation'     => false to ignore tax, 'include_tax' or 'exclude_tax' To indicate that tax should be added or excluded respectively
	 *			'subscription_length' => true to include subscription's length (default) or false to exclude it
	 *			'sign_up_fee'         => true to include subscription's sign up fee (default) or false to exclude it
	 *			'price'               => string a price to short-circuit the price calculations and use in a string for the product
	 * @since 1.0
	 */
	public static function get_price_string( $product, $include = array() ) {

		if ( ! is_object( $product ) )
			$product = new WC_Product( $product ); // Shouldn't matter if product is variation as all we need is the product_type

		if ( ! self::is_subscription( $product ) )
			return;

		$include = wp_parse_args( $include, array(
				'tax_calculation'     => false,
				'subscription_length' => true,
				'sign_up_fee'         => true,
				'trial_length'        => true
			)
		);

		if ( $include['tax_calculation'] != false ) {

			if ( $include['tax_calculation'] == 'exclude_tax' ) { // Subtract Tax

				$tax_per_period = self::calculate_tax_for_subscription( $product->product_custom_fields['_subscription_price'][0], $product );

				if ( isset( $include['price'] ) )
					$price = $include['price'];
				else
					$price = woocommerce_price( $product->product_custom_fields['_subscription_price'][0] - $tax_per_period );

				if ( $product->product_custom_fields['_subscription_sign_up_fee'][0] > 0 ) {
					$sign_up_tax = self::calculate_tax_for_subscription( $product->product_custom_fields['_subscription_sign_up_fee'][0], $product );
					$sign_up_fee = $product->product_custom_fields['_subscription_sign_up_fee'][0] - $sign_up_tax;
				}

			} else { // Add Tax

				$tax_per_period = self::calculate_tax_for_subscription( $product->product_custom_fields['_subscription_price'][0], $product, true );

				if ( isset( $include['price'] ) )
					$price = $include['price'];
				else
					$price = woocommerce_price( $product->product_custom_fields['_subscription_price'][0] + $tax_per_period );

				if ( $product->product_custom_fields['_subscription_sign_up_fee'][0] > 0 ) {
					$sign_up_tax = self::calculate_tax_for_subscription( $product->product_custom_fields['_subscription_sign_up_fee'][0], $product, true );
					$sign_up_fee = $product->product_custom_fields['_subscription_sign_up_fee'][0] - $sign_up_tax;
				}

			}

		} else {

			if ( isset( $include['price'] ) )
				$price = $include['price'];
			else
				$price = woocommerce_price( $product->product_custom_fields['_subscription_price'][0] );

			if ( $product->product_custom_fields['_subscription_sign_up_fee'][0] > 0 )
				$sign_up_fee = $product->product_custom_fields['_subscription_sign_up_fee'][0];

		}

		$subscription_string = sprintf( _n( ' %s / %s', ' %s every %s', self::get_interval( $product ), WC_Subscriptions::$text_domain ), $price, WC_Subscriptions_Manager::get_subscription_period_strings( self::get_interval( $product ), self::get_period( $product ) ) );

		if ( $include['subscription_length'] && isset( $product->product_custom_fields['_subscription_length'][0] ) && $product->product_custom_fields['_subscription_length'][0] != 0 ) {
			$ranges = WC_Subscriptions_Manager::get_subscription_ranges( self::get_period( $product ) );
			$subscription_string = sprintf( __( '%s for %s', WC_Subscriptions::$text_domain ), $subscription_string, $ranges[$product->product_custom_fields['_subscription_length'][0]] );
		}

		if ( $include['trial_length'] && isset( $product->product_custom_fields['_subscription_trial_length'][0] ) && $product->product_custom_fields['_subscription_trial_length'][0] != 0 ) {
			$trial_lengths = WC_Subscriptions_Manager::get_subscription_trial_lengths( self::get_period( $product ) );
			$subscription_string = sprintf( __( '%s with %s free trial', WC_Subscriptions::$text_domain ), $subscription_string, $trial_lengths[$product->product_custom_fields['_subscription_trial_length'][0]] );
		}

		if ( $include['sign_up_fee'] && $product->product_custom_fields['_subscription_sign_up_fee'][0] > 0 )
			$subscription_string = sprintf( __( '%s and a %s sign-up fee', WC_Subscriptions::$text_domain ), $subscription_string, woocommerce_price( $sign_up_fee ) );

		return apply_filters( 'woocommerce_subscription_price_string', $subscription_string, $product );
	}

	/**
	 * Returns the price per period for a product if it is a subscription.
	 * 
	 * @param $product mixed A WC_Product object or product ID
	 * @return float The price charged per period for the subscription, or an empty string if the product is not a subscription.
	 * @since 1.0
	 */
	public static function get_price( $product ) {

		if ( ! is_object( $product ) )
			$product = new WC_Product( $product ); // Shouldn't matter if product is variation as all we need is the product_type

		if ( ! self::is_subscription( $product ) || empty( $product->product_custom_fields['_subscription_price'][0] ) )
			return '';

		return $product->product_custom_fields['_subscription_price'][0];
	}

	/**
	 * Returns the subscription period for a product, if it's a subscription.
	 * 
	 * @param $product mixed A WC_Product object or product ID
	 * @return string A string representation of the period, either Day, Week, Month or Year, or an empty string if product is not a subscription.
	 * @since 1.0
	 */
	public static function get_period( $product ) {

		if ( ! is_object( $product ) )
			$product = new WC_Product( $product ); // Shouldn't matter if product is variation as all we need is the product_type

		if ( ! self::is_subscription( $product ) || empty( $product->product_custom_fields['_subscription_period'][0] ) )
			return '';

		return $product->product_custom_fields['_subscription_period'][0];
	}

	/**
	 * Returns the subscription interval for a product, if it's a subscription.
	 * 
	 * @param $product mixed A WC_Product object or product ID
	 * @return string A string representation of the period, either Day, Week, Month or Year, or an empty string if product is not a subscription.
	 * @since 1.0
	 */
	public static function get_interval( $product ) {

		if ( ! is_object( $product ) )
			$product = new WC_Product( $product ); // Shouldn't matter if product is variation as all we need is the product_type

		if ( ! self::is_subscription( $product ) || empty( $product->product_custom_fields['_subscription_period_interval'][0] ) )
			return 1;

		return $product->product_custom_fields['_subscription_period_interval'][0];
	}

	/**
	 * Returns the length of a subscription product, if it is a subscription.
	 * 
	 * @param $product mixed A WC_Product object or product ID
	 * @return int An integer representing the length of the subscription, or 0 if the product is not a subscription or the subscription continues for perpetuity
	 * @since 1.0
	 */
	public static function get_length( $product ) {

		if ( ! is_object( $product ) )
			$product = new WC_Product( $product ); // Shouldn't matter if product is variation as all we need is the product_type

		if ( ! self::is_subscription( $product ) || $product->product_custom_fields['_subscription_length'][0] == 0 )
			return 0;

		return $product->product_custom_fields['_subscription_length'][0];
	}

	/**
	 * Returns the trial length of a subscription product, if it is a subscription.
	 * 
	 * @param $product mixed A WC_Product object or product ID
	 * @return int An integer representing the length of the subscription, or 0 if the product is not a subscription or the subscription continues for perpetuity
	 * @since 1.0
	 */
	public static function get_trial_length( $product ) {

		if ( ! is_object( $product ) )
			$product = new WC_Product( $product ); // Shouldn't matter if product is variation as all we need is the product_type

		if ( ! self::is_subscription( $product ) || ! isset( $product->product_custom_fields['_subscription_trial_length'][0] ) )
			return 0;

		return $product->product_custom_fields['_subscription_trial_length'][0];
	}

	/**
	 * Returns the sign-up fee for a subscription, if it is a subscription.
	 * 
	 * @param $product mixed A WC_Product object or product ID
	 * @return float The value of the sign-up fee, or 0 if the product is not a subscription or the subscription has no sign-up fee
	 * @since 1.0
	 */
	public static function get_sign_up_fee( $product ) {

		if ( ! is_object( $product ) )
			$product = new WC_Product( $product ); // Shouldn't matter if product is variation as all we need is the product_type

		if ( ! self::is_subscription( $product ) || empty( $product->product_custom_fields['_subscription_sign_up_fee'][0] ) )
			return 0;

		return $product->product_custom_fields['_subscription_sign_up_fee'][0];
	}

	/**
	 * Takes a subscription product's ID and returns the date on which the subscription product will expire, 
	 * based on the subscription's length and calculated from either the $order_date if specified, or the current date/time.
	 * 
	 * @param $product_id int The product/post ID of the subscription
	 * @param $order_date mixed A MYSQL formatted date/time string from which to calculate the expiration date, or empty (default), which will use today's date/time.
	 * @since 1.0
	 */
	public static function get_expiration_date( $product_id, $order_date = '' ) {

		$subscription_length = self::get_length( $product_id );

		if( $subscription_length > 0 ){

			$subscription_period = self::get_period( $product_id );
			$trial_length        = self::get_trial_length( $product_id );

			if( empty( $order_date ) )
				$order_date = date( 'Y-m-d H:i:s' );

			$expiration_date = date( 'Y-m-d H:i:s', strtotime( "$order_date + $trial_length {$subscription_period}s + $subscription_length {$subscription_period}s" ) );

		} else {

			$expiration_date = 0;

		}

		return $expiration_date;
	}

	/**
	 * Takes a subscription product's ID and returns the date on which the subscription product will expire, 
	 * based on the subscription's length and calculated from either the $order_date if specified, or the current date/time.
	 * 
	 * @param $product_id int The product/post ID of the subscription
	 * @param $order_date mixed A MYSQL formatted date/time string from which to calculate the expiration date, or empty (default), which will use today's date/time.
	 * @since 1.0
	 */
	public static function get_trial_expiration_date( $product_id, $order_date = '' ) {

		$trial_period = self::get_period( $product_id );
		$trial_length = self::get_trial_length( $product_id );

		if( $trial_length > 0 ){

			if( empty( $order_date ) )
				$order_date = date( 'Y-m-d H:i:s' );

			$trial_expiration_date = date( 'Y-m-d H:i:s', strtotime( "$order_date + $trial_length {$trial_period}s" ) );

		} else {

			$trial_expiration_date = 0;

		}

		return $trial_expiration_date;
	}

	/**
	 * Returns the sign-up fee for a subscription excluding tax - ignores tax_class filters since the price may *include* tax and thus needs subtracting
	 * 
	 * @param $product mixed A WC_Product object or product ID
	 * @return float The value of the sign-up fee, or 0 if the product is not a subscription or the subscription has no sign-up fee
	 * @since 1.0
	 */
	public static function get_sign_up_fee_excluding_tax( $product ) {

		$price = self::get_sign_up_fee( $product );

		if ( $product->is_taxable() && get_option( 'woocommerce_prices_include_tax' ) == 'yes' ) :

			$_tax = new WC_Tax();

			$tax_rates  = $_tax->get_shop_base_rate( $product->tax_class );
			$taxes      = $_tax->calc_tax( $price, $tax_rates, true );
			$tax_amount = $_tax->get_tax_total( $taxes );
			$price      = round( $price - $tax_amount, 2);

		endif;

		return $price;
	}

	/**
	 * Calculates a price (could be per period price or sign-up fee) for a subscription less tax
	 * if the subscription is taxable and the prices in the store include tax.
	 * 
	 * Based on the WC_Product::get_price_excluding_tax() function.
	 *
	 * @param $price float The price to adjust based on taxes
	 * @param $product WC_Product The product the price belongs too (needed to determine tax class)
	 * @since 1.0
	 */
	public static function calculate_tax_for_subscription( $price, $product, $deduct_base_taxes = false ) {

		if ( $product->is_taxable() ) {

			$tax = new WC_Tax();

			$base_tax_rates = $tax->get_shop_base_rate( $product->tax_class );
			$tax_rates      = $tax->get_rates( $product->get_tax_class() ); // This will get the base rate unless we're on the checkout page

			if ( $deduct_base_taxes && get_option( 'woocommerce_prices_include_tax' ) == 'yes' ) {

				$base_taxes = $tax->calc_tax( $price, $base_tax_rates, true );
				$taxes      = $tax->calc_tax( $price - array_sum( $base_taxes ), $tax_rates, false );

			} elseif ( get_option( 'woocommerce_prices_include_tax' ) == 'yes' ) {

				$taxes = $tax->calc_tax( $price, $base_tax_rates, true );

			} else {

				$taxes = $tax->calc_tax( $price, $base_tax_rates, false );

			}

			$tax_amount = $tax->get_tax_total( $taxes );

		} else {

			$tax_amount = 0;

		}

		return $tax_amount;
	}
}

WC_Subscriptions_Product::init();
