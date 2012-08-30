<?php
/**
 * Subscriptions Admin Class
 * 
 * Adds a Subscription setting tab and saves subscription settings. Adds a Subscriptions Management page. Adds
 * Welcome messages and pointers to streamline learning process for new users.
 * 
 * @package		WooCommerce Subscriptions
 * @subpackage	WC_Subscriptions_Admin
 * @category	Class
 * @author		Brent Shepherd
 * @since		1.0
 */
class WC_Subscriptions_Admin {

	/**
	 * The WooCommerce settings tab name
	 * 
	 * @since 1.0
	 */
	public static $tab_name = 'subscriptions';

	/**
	 * The prefix for subscription settings
	 * 
	 * @since 1.0
	 */
	public static $option_prefix = 'woocommerce_subscriptions';

	/**
	 * Bootstraps the class and hooks required actions & filters.
	 * 
	 * @since 1.0
	 */
	public static function init() {

		// Add subscriptions to the product select box
		add_filter( 'product_type_selector', __CLASS__ . '::add_subscription_to_select' );

		// Add subscription pricing fields on edit product page
		add_action( 'woocommerce_product_options_general_product_data', __CLASS__ . '::subscription_pricing_fields' );

		// Save subscription meta only when a subscription product is saved by hooking to the "'woocommerce_process_product_meta_' . $product_type" action
		add_action( 'save_post', __CLASS__ . '::save_subscription_meta', 11 );

		add_filter( 'woocommerce_settings_tabs_array', __CLASS__ . '::add_subscription_settings_tab' );

		add_action( 'woocommerce_settings_tabs_subscriptions', __CLASS__ . '::subscription_settings_page' );

		add_action( 'woocommerce_update_options_' . self::$tab_name, __CLASS__ . '::update_subscription_settings' );

		add_action( 'admin_menu', __CLASS__ . '::add_menu_pages' );

		add_filter( 'manage_users_columns', __CLASS__ . '::add_user_columns', 11, 1 );

		add_action( 'manage_users_custom_column', __CLASS__ . '::user_column_values', 10, 3 );

		add_action( 'admin_enqueue_scripts', __CLASS__ . '::enqueue_styles_scripts' );

		add_action( 'woocommerce_admin_field_informational', __CLASS__ . '::add_informational_admin_field' );
	}

	/**
	 * Add the 'subscriptions' product type to the WooCommerce product type select box.
	 * 
	 * @param array Array of Product types & their labels, excluding the Subscription product type.
	 * @return array Array of Product types & their labels, including the Subscription product type.
	 * @since 1.0
	 */
	public static function add_subscription_to_select( $product_types ){

		$product_types[WC_Subscriptions::$name] = ucfirst( WC_Subscriptions::$name );

		return $product_types;
	}

	/**
	 * Output the subscription specific pricing fields on the "Edit Product" admin page.
	 * 
	 * @since 1.0
	 */
	public static function subscription_pricing_fields() {
		global $post;

		// Set month as the default billing period
		if ( ! $subscription_period = get_post_meta( $post->ID, '_subscription_period', true ) )
		 	$subscription_period = 'month';

		echo '<div class="options_group show_if_subscription subscription_pricing">';

		// Subscription Price
		woocommerce_wp_text_input( array( 
			'id'          => '_subscription_price', 
			'class'       => 'wc_input_subscription_price', 
			'label'       => sprintf( __( 'Subscription Price (%s)', WC_Subscriptions::$text_domain ), get_woocommerce_currency_symbol() ),
			'placeholder' => __( 'e.g. 5.90', WC_Subscriptions::$text_domain ),
			)
		);

		// Subscription Period Interval
		woocommerce_wp_select( array( 
			'id'          => '_subscription_period_interval', 
			'class'       => 'wc_input_subscription_period_interval', 
			'label'       => __( 'Subscription Periods', WC_Subscriptions::$text_domain ),
			'options'     => WC_Subscriptions_Manager::get_subscription_period_interval_strings()
			)
		);

		// Billing Period
		woocommerce_wp_select( array( 
			'id'          => '_subscription_period', 
			'class'       => 'wc_input_subscription_period', 
			'label'       => __( 'Billing Period', WC_Subscriptions::$text_domain ), 
			'value'       => $subscription_period, 
			'description' => __( 'for', WC_Subscriptions::$text_domain ),
			'options'     => WC_Subscriptions_Manager::get_subscription_period_strings()
			)
		);

		// Subscription Length
		woocommerce_wp_select( array( 
			'id'          => '_subscription_length', 
			'class'       => 'wc_input_subscription_length', 
			'label'       => __( 'Subscription Length', WC_Subscriptions::$text_domain ),
			'options'     => WC_Subscriptions_Manager::get_subscription_ranges( $subscription_period ),
			'description' => sprintf( __( 'with a %s', WC_Subscriptions::$text_domain ), get_woocommerce_currency_symbol() )
			)
		);

		// Sign-up Fee
		woocommerce_wp_text_input( array( 
			'id'          => '_subscription_sign_up_fee', 
			'class'       => 'wc_input_subscription_intial_price', 
			'label'       => sprintf( __( 'Sign-up Fee (%s)', WC_Subscriptions::$text_domain ), get_woocommerce_currency_symbol() ),
			'placeholder' => __( 'e.g. 9.90', WC_Subscriptions::$text_domain ),
			'description' => __( 'sign-up fee and', WC_Subscriptions::$text_domain )
			)
		);

		// Trial Period
		woocommerce_wp_select( array( 
			'id'          => '_subscription_trial_length', 
			'class'       => 'wc_input_subscription_trial_length', 
			'label'       => __( 'Subscription Trial Period', WC_Subscriptions::$text_domain ),
			'options'     => WC_Subscriptions_Manager::get_subscription_trial_lengths( $subscription_period ),
			'description' => __( 'free trial', WC_Subscriptions::$text_domain )
			)
		);

		do_action( 'woocommerce_subscriptions_product_options_pricing' );

		echo '</div>';
	}

	/**
	 * Save subscription details when edit product page is submitted for a subscription product type
	 * or the bulk edit product is saved.
	 * 
	 * The $_REQUEST global is used to account for both $_GET submission from Bulk Edit page and 
	 * $_POST submission from Edit Product page.
	 * 
	 * @param array Array of Product types & their labels, excluding the Subscription product type.
	 * @return array Array of Product types & their labels, including the Subscription product type.
	 * @since 1.0
	 */
	public static function save_subscription_meta( $post_id ) {

		if ( ! WC_Subscriptions_Product::is_subscription( $post_id ) )
			return;

		if ( isset( $_REQUEST['_subscription_price'] ) ) {
			update_post_meta( $post_id, '_subscription_price', stripslashes( $_REQUEST['_subscription_price'] ) );
			update_post_meta( $post_id, '_price', stripslashes( $_REQUEST['_subscription_price'] ) );
		} else { // Handle bulk edit, where _subscription_price field is not available
			if( isset( $_REQUEST['_regular_price'] ) && ! empty( $_REQUEST['_regular_price'] ) ) {
				update_post_meta( $post_id, '_subscription_price', stripslashes( $_REQUEST['_regular_price'] ) );
				update_post_meta( $post_id, '_price', stripslashes( $_REQUEST['_regular_price'] ) );
			} elseif ( isset( $_REQUEST['_sale_price'] ) && ! empty( $_REQUEST['_sale_price'] ) ) {
				update_post_meta( $post_id, '_subscription_price', stripslashes( $_REQUEST['_sale_price'] ) );
				update_post_meta( $post_id, '_price', stripslashes( $_REQUEST['_sale_price'] ) );
			}
		}

		$subscription_fields = array( '_subscription_sign_up_fee', '_subscription_period', '_subscription_period_interval', '_subscription_length', '_subscription_trial_length' );

		foreach ( $subscription_fields as $field_name )
			if ( isset( $_REQUEST[$field_name] ) )
				update_post_meta( $post_id, $field_name, stripslashes( $_REQUEST[$field_name] ) );

	}

	/**
	 * Adds all necessary admin styles.
	 * 
	 * @param array Array of Product types & their labels, excluding the Subscription product type.
	 * @return array Array of Product types & their labels, including the Subscription product type.
	 * @since 1.0
	 */
	public static function enqueue_styles_scripts() {
		global $woocommerce, $pagenow, $post;

		// Get admin screen id
	    $screen = get_current_screen();

		if ( in_array( $screen->id, array( 'product', 'edit-shop_order', 'shop_order' ) ) ) {

			$dependencies = array( 'jquery' );

			if( $screen->id == 'product' ) {
				$dependencies[] = 'woocommerce_writepanel';

				$script_params = array(
					'productType'         => WC_Subscriptions::$name,
					'trialLengths'        => WC_Subscriptions_Manager::get_subscription_trial_lengths(),
					'subscriptionLengths' => WC_Subscriptions_Manager::get_subscription_ranges()
				);
			} else if ( $screen->id == 'edit-shop_order' ) {
				$script_params = array(
					'bulkTrashWarning'    => __( "You are about to trash one or more orders which contain a subscription.\n\nTrashing the orders will also trash the subscriptions purchased with these orders.", WC_Subscriptions::$text_domain )
				);
			} else if ( $screen->id == 'shop_order' ) {
				$script_params = array(
					'bulkTrashWarning'    => __( 'Trashing this order will also trash the subscription purchased with the order.', WC_Subscriptions::$text_domain )
				);
			}

			wp_enqueue_script( 'woocommerce_subscriptions_admin', plugin_dir_url( WC_Subscriptions::$plugin_file ) . 'js/admin.js', $dependencies );

			wp_localize_script( 'woocommerce_subscriptions_admin', 'WCSubscriptions', apply_filters( 'woocommerce_subscriptions_admin_script_parameters', $script_params ) );

			// Maybe add the pointers for first timers
			if ( isset( $_GET['subscription_pointers'] ) && self::show_user_pointers() ) {

				$dependencies[] = 'wp-pointer';

				$pointer_script_params = array(
					'typePointerContent'  => sprintf( __( '%sChoose Subscription%sThe WooCommerce Subscriptions extension adds a new %sSubscription%s product type.%s', WC_Subscriptions::$text_domain ), '<h3>', '</h3><p>', '<em>', '</em>', '</p>' ),
					'pricePointerContent' => sprintf( __( '%sSet a Price%sSubscription prices are a little different to product prices. You also have to set a billing period and length for a subscription.%s', WC_Subscriptions::$text_domain ), '<h3>', '</h3><p>', '</p>' ),
				);

				wp_enqueue_script( 'woocommerce_subscriptions_admin_pointers', plugin_dir_url( WC_Subscriptions::$plugin_file ) . 'js/admin-pointers.js', $dependencies );

				wp_localize_script( 'woocommerce_subscriptions_admin_pointers', 'WCSPointers', apply_filters( 'woocommerce_subscriptions_admin_pointer_script_parameters', $pointer_script_params ) );

				wp_enqueue_style( 'wp-pointer' );
			}

		}

		// Maybe add the admin notice
		if ( get_transient( WC_Subscriptions::$activation_transient ) == true ) {

			wp_enqueue_style( 'woocommerce-activation', plugins_url(  '/assets/css/activation.css', self::get_woocommerce_plugin_dir_file() ) );

			add_action( 'admin_notices', __CLASS__ . '::admin_installed_notice' );

			delete_transient( WC_Subscriptions::$activation_transient );
		}
		
		wp_enqueue_style( 'woocommerce_admin_styles', $woocommerce->plugin_url() . '/assets/css/admin.css' );
		wp_enqueue_style( 'woocommerce_subscriptions_admin', plugin_dir_url( WC_Subscriptions::$plugin_file ) . 'css/admin.css' );

	}

	/**
	 * Add the "Active Subscriber?" column to the User's admin table
	 */
	public static function add_user_columns( $columns ) {

		if ( current_user_can( 'manage_woocommerce' ) ) {
			// Move Active Subscriber before Orders for aesthetics
			$last_column = array_slice( $columns, -1, 1, true );
			array_pop( $columns );
			$columns['woocommerce_active_subscriber'] = __( 'Active Subscriber?', WC_Subscriptions::$text_domain );
			$columns += $last_column;
		}

		return $columns;
	}

	/**
	 * Hooked to the users table to display a check mark if a given user has an active subscription.
	 * 
	 * @param string $value The string to output in the column specified with $column_name
	 * @param string $column_name The string key for the current column in an admin table
	 * @param int $user_id The ID of the user to which this row relates
	 * @return string $value A check mark if the column is the active_subscriber column and the user has an active subscription.
	 * @since 1.0
	 */
	public static function user_column_values( $value, $column_name, $user_id ) {
		global $woocommerce;

		if( $column_name == 'woocommerce_active_subscriber' ) {

			$users_subscriptions = WC_Subscriptions_Manager::get_users_subscriptions( $user_id );

			// Inactive until proven otherwise
			$value = '<img src="' . $woocommerce->plugin_url() . '/assets/images/success-off.png" alt="no" />';

			if ( ! empty( $users_subscriptions ) ) {
				foreach( $users_subscriptions as $subscription ) {
					if( $subscription['status'] == 'active' ) {
						$value = '<img src="' . $woocommerce->plugin_url() . '/assets/images/success.png" alt="yes" />';
						break;
					}
				}
			}

		}

		return $value;
	}

	/**
	 * Add a Subscriptions Management page under WooCommerce top level admin menu
	 * 
	 * @since 1.0
	 */
	public static function add_menu_pages() {
		add_submenu_page( 'woocommerce', __( 'Manage Subscriptions', WC_Subscriptions::$text_domain ),  __( 'Subscriptions', WC_Subscriptions::$text_domain ), 'manage_woocommerce', self::$tab_name, __CLASS__ . '::subscriptions_management_page' );
	}

	/**
	 * Outputs the Subscription Management admin page with a sortable @see WC_Subscriptions_List_Table used to
	 * display all the subscriptions that have been purchased.
	 * 
	 * @uses WC_Subscriptions_List_Table
	 * @since 1.0
	 */
	public static function subscriptions_management_page() {

		require_once( 'class-wc-subscriptions-list-table.php' );

		$subscriptions_table = new WC_Subscriptions_List_Table();
		$subscriptions_table->prepare_items(); ?>
<div class="wrap">
	<div id="icon-woocommerce" class="icon32-woocommerce-users icon32"><br/></div>
	<h2><?php _e( 'Manage Subscriptions', WC_Subscriptions::$text_domain ); ?></h2>
	<?php $subscriptions_table->messages(); ?>
	<?php $subscriptions_table->views(); ?>
	<form id="subscriptions-filter" method="get">
		<?php $subscriptions_table->display() ?>
	</form>
</div>
		<?php
	}

	/**
	 * Uses the WooCommerce options API to save settings via the @see woocommerce_update_options() function.
	 * 
	 * @uses woocommerce_update_options()
	 * @uses self::get_settings()
	 * @since 1.0
	 */
	public static function update_subscription_settings() {
		woocommerce_update_options( self::get_settings() );
	}

	/**
	 * Uses the WooCommerce admin fields API to output settings via the @see woocommerce_admin_fields() function.
	 * 
	 * @uses woocommerce_admin_fields()
	 * @uses self::get_settings()
	 * @since 1.0
	 */
	public static function subscription_settings_page() {
		woocommerce_admin_fields( self::get_settings() );
	}

	/**
	 * Add the Subscriptions settings tab to the WooCommerce settings tabs array.
	 * 
	 * @param array $settings_tabs Array of WooCommerce setting tabs & their labels, excluding the Subscription tab.
	 * @return array $settings_tabs Array of WooCommerce setting tabs & their labels, including the Subscription tab.
	 * @since 1.0
	 */
	public static function add_subscription_settings_tab( $settings_tabs ) {

		$settings_tabs[self::$tab_name] = __( 'Subscriptions', WC_Subscriptions::$text_domain );

		return $settings_tabs;
	}

	/**
	 * Sets default values for all the WooCommerce Subscription options. Called on plugin activation.
	 * 
	 * @see WC_Subscriptions::activate_woocommerce_subscriptions
	 * @since 1.0
	 */
	public static function add_default_settings() {
		foreach ( self::get_settings() as $setting )
			if ( isset( $setting['std'] ) )
				add_option( $setting['id'], $setting['std'] );

	}

	/**
	 * Get all the settings for the Subscriptions extension in the format required by the @see woocommerce_admin_fields() function.
	 * 
	 * @return array Array of settings in the format required by the @see woocommerce_admin_fields() function.
	 * @since 1.0
	 */
	public static function get_settings() {
		global $woocommerce;

		$roles = get_editable_roles();

		foreach ( $roles as $role => $details )
			$roles_options[$role] = translate_user_role( $details['name'] );

		$available_gateways = array();

		foreach ( $woocommerce->payment_gateways->get_available_payment_gateways() as $gateway )
			if ( $gateway->supports( 'subscriptions' ) )
				$available_gateways[] = $gateway->title;

		if ( count( $available_gateways ) == 0 )
			$available_gateways_description = sprintf( __( 'No payment gateways capable of accepting subscriptions are enabled. Please enable the %sPayPal Standard%s gateway.', WC_Subscriptions::$text_domain ), '<a href="' . admin_url( 'admin.php?page=woocommerce&tab=payment_gateways#gateway-paypal' ) . '">', '</a>' );
		elseif ( count( $available_gateways ) == 1 )
			$available_gateways_description = sprintf( __( 'The %s gateway is enabled and can accept subscriptions.', WC_Subscriptions::$text_domain ), $available_gateways[0] );
		elseif ( count( $available_gateways ) > 1 )
			$available_gateways_description = sprintf( __( 'The %s & %s gateways are enabled and can accept subscriptions.', WC_Subscriptions::$text_domain ), implode( ', ', array_slice( $available_gateways, 0, count( $available_gateways ) - 1 ) ), array_pop( $available_gateways ) );

		$max_failed_payment_options = range( 0 , 12 );

		$max_failed_payment_options[0] = __( 'No maximum', WC_Subscriptions::$text_domain );

		return apply_filters( 'woocommerce_subscription_settings', array(

			array(
				'name'     => __( 'Button Text', WC_Subscriptions::$text_domain ),
				'type'     => 'title',
				'desc'     => '', 
				'id'       => self::$option_prefix . '_button_text' 
			),

			array( 
				'name'     => __( 'Add to Cart Button Text', WC_Subscriptions::$text_domain ),
				'desc'     => __( 'A product displays a button with the text "Add to Cart". By default, a subscription changes this to "Sign Up Now". You can customise the button text for subscriptions here.', WC_Subscriptions::$text_domain ),
				'tip'      => '',
				'id'       => self::$option_prefix . '_add_to_cart_button_text',
				'css'      => 'min-width:150px;',
				'std'      => __( 'Sign Up Now', WC_Subscriptions::$text_domain ),
				'type'     => 'text',
				'desc_tip' => true,
			),

			array( 
				'name'     => __( 'Place Order Button Text', WC_Subscriptions::$text_domain ),
				'desc'     => __( 'Use this field to customise the text displayed on the checkout button when an order contains a subscription. Normally the checkout submission button displays "Place Order". When the cart contains a subscription, this is changed to "Sign Up Now".', WC_Subscriptions::$text_domain ),
				'tip'      => '',
				'id'       => self::$option_prefix . '_order_button_text',
				'css'      => 'min-width:150px;',
				'std'      => __( 'Sign Up Now', WC_Subscriptions::$text_domain ),
				'type'     => 'text',
				'desc_tip' => true,
			),

			array( 'type' => 'sectionend', 'id' => self::$option_prefix . '_button_text' ),

			array(
				'name'     => __( 'Roles', WC_Subscriptions::$text_domain ),
				'type'     => 'title',
				'desc'     => __( 'Choose the default roles to assign to active and inactive subscribers. For record keeping purposes, a user account must be created for subscribers. Users with the <em>administrator</em> role, such as yourself, will never be allocated these roles to prevent locking out administrators.', WC_Subscriptions::$text_domain ),
				'id'       => self::$option_prefix . '_role_options' 
			),

			array( 
				'name'     => __( 'Subscriber Default Role', WC_Subscriptions::$text_domain ),
				'desc'     => __( 'When a subscription is activated, either manually or after a successful purchase, new users will be assigned this role.', WC_Subscriptions::$text_domain ),
				'tip'      => '',
				'id'       => self::$option_prefix . '_subscriber_role',
				'css'      => 'min-width:150px;',
				'std'      => 'subscriber',
				'type'     => 'select',
				'options'  => $roles_options,
				'desc_tip' => true,
			),

			array(
				'name'     => __( 'Inactive Subscriber Role', WC_Subscriptions::$text_domain ),
				'desc'     => __( 'If a subscriber\'s subscription is manually cancelled or expires, she will be assigned this role.', WC_Subscriptions::$text_domain ),
				'tip'      => '',
				'id'       => self::$option_prefix . '_cancelled_role',
				'css'      => 'min-width:150px;',
				'std'      => 'customer',
				'type'     => 'select',
				'options'  => $roles_options,
				'desc_tip' => true,
			),

			array( 'type' => 'sectionend', 'id' => self::$option_prefix . '_role_options' ),

			array(
				'name'          => __( 'Failed Payments', WC_Subscriptions::$text_domain ),
				'type'          => 'title',
				'desc'          => '', 
				'id'            => self::$option_prefix . '_email_options' 
			),

			array(
				'name'          => __( 'Maximum Failed Payments', WC_Subscriptions::$text_domain ),
				'desc'          => __( 'After the number of maximum failed payments is exceeded, a subscription will be automatically cancelled.', WC_Subscriptions::$text_domain ),
				'tip'           => '',
				'id'            => self::$option_prefix . '_max_failed_payments',
				'css'           => 'min-width:150px;',
				'std'           => '3',
				'type'          => 'select',
				'options'       => $max_failed_payment_options,
				'desc_tip'      => true
			),

			array(
				'name'          => __( 'Generate Renewal Orders', WC_Subscriptions::$text_domain ),
				'desc'          => __( 'Automatically generate a new order for subscribers to reactivate a cancelled subscription.', WC_Subscriptions::$text_domain ),
				'id'            => self::$option_prefix . '_generate_renewal_order',
				'std'           => 'no',
				'type'          => 'checkbox',
				'desc_tip'      => __( 'If a subscription is cancelled after exceeding the maximum number of allowable failed payments, a new order can be automatically generated and used by the subscriber to reactivate the subscription with new payment details.', WC_Subscriptions::$text_domain )
			),

			array(
				'name'          => __( 'Email Renewal Order', WC_Subscriptions::$text_domain ),
				'id'            => self::$option_prefix . '_email_renewal_order',
				'std'           => 'no',
				'type'          => 'checkbox',
				'desc_tip'      => __( 'An automatically generated renewal order can be emailed to a subscriber once it has been generated.', WC_Subscriptions::$text_domain )
			),

			array(
				'name'          => __( 'Add Outstanding Balance', WC_Subscriptions::$text_domain ),
				'id'            => self::$option_prefix . '_add_outstanding_balance',
				'std'           => 'yes',
				'type'          => 'checkbox',
				'desc_tip'      => __( 'If a payment fails, some gateways can add the outstanding amount to the next bill.', WC_Subscriptions::$text_domain )
			),

			array( 'type' => 'sectionend', 'id' => self::$option_prefix . '_payment_gateway_options' ),

			array(
				'name'          => __( 'Payment Gateways', WC_Subscriptions::$text_domain ),
				'desc'          => $available_gateways_description,
				'id'            => self::$option_prefix . '_payment_gateways_available',
				'type'          => 'informational'
			),

			array(
				'desc'          => sprintf( __( 'Only payment gateways that register support for subscriptions are displayed on the checkout page.', WC_Subscriptions::$text_domain ), '<a href="' . admin_url( 'admin.php?page=woocommerce&tab=payment_gateways' ) . '">', '</a>' ),
				'id'            => self::$option_prefix . '_payment_gateway_explanation',
				'type'          => 'informational'
			),

			array(
				'desc'          => sprintf( __( 'Get additional payment gateways that accept subscriptions from the %sOfficial WooCommerce Extension marketplace%s.', WC_Subscriptions::$text_domain ), '<a href="http://zfer.us/lxmt7?d=' . esc_url( 'http://www.woothemes.com/extensions/woocommerce-payment-gateways/' ) . '">', '</a>' ),
				'id'            => self::$option_prefix . '_payment_gateways_additional',
				'type'          => 'informational'
			),

			array( 'type' => 'sectionend', 'id' => self::$option_prefix . '_payment_gateway_options' )

		));

	}

	/**
	 * Displays instructional information for a WooCommerce setting.
	 * 
	 * @since 1.0
	 */
	public static function add_informational_admin_field( $field_details ) {

		if ( isset( $field_details['name'] ) && $field_details['name'] )
			echo '<h3>' . $field_details['name'] . '</h3>'; 

		if ( isset( $field_details['desc'] ) && $field_details['desc'] )
			echo wpautop( wptexturize( $field_details['desc'] ) );
	}

	/**
	 * Outputs a welcome message. Called when the Subscriptions extension is activated.
	 * 
	 * @since 1.0
	 */
	public static function admin_installed_notice() { ?>
<div id="message" class="updated woocommerce-message wc-connect">
	<div class="squeezer">
		<h4><?php printf( __( '%sWooCommerce Subscriptions Installed%s &#8211; %sYou\'re ready to start selling subscriptions!%s', WC_Subscriptions::$text_domain ), '<strong>', '</strong>', '<em>', '</em>' ); ?></h4>

		<p class="submit">
			<a href="<?php echo self::add_subscription_url(); ?>" class="button-primary"><?php _e( 'Add a Subscription &raquo;', WC_Subscriptions::$text_domain ); ?></a>
			<a href="<?php echo admin_url( 'admin.php?page=woocommerce&tab=subscriptions' ); ?>" class="docs button-primary"><?php _e( 'Settings', WC_Subscriptions::$text_domain ); ?></a>
			<a href="https://twitter.com/share" class="twitter-share-button" data-url="http://www.woothemes.com/extension/subscriptions/" data-text="Woot! I can now sell subscriptions with #WooCommerce &amp; #WordPress" data-via="WooThemes" data-size="large">Tweet</a>
			<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
		</p>
	</div>
</div>
		<?php
	}

	/**
	 * Checks whether a user should be shown pointers or not, based on whether a user has previously dismissed pointers.
	 * 
	 * @since 1.0
	 */
	public static function show_user_pointers(){
		// Get dismissed pointers
		$dismissed = explode( ',', (string) get_user_meta( get_current_user_id(), 'dismissed_wp_pointers', true ) );

		// Pointer has been dismissed
		if ( in_array( 'wcs_pointer', $dismissed ) )
			return false;
		else
			return true;
	}

	/**
	 * Returns a URL for adding/editing a subscription, which special parameters to define whether pointers should be shown.
	 * 
	 * The 'select_subscription' flag is picked up by JavaScript to set the value of the product type to "Subscription".
	 * 
	 * @since 1.0
	 */
	public static function add_subscription_url( $show_pointers = true ) {
		$add_subscription_url = admin_url( 'post-new.php?post_type=product&select_subscription=true' );

		if ( $show_pointers == true )
			$add_subscription_url = add_query_arg( 'subscription_pointers', 'true', $add_subscription_url );

		return $add_subscription_url;
	}

	/**
	 * Searches through the list of active plugins to find WooCommerce. Just in case
	 * WooCommerce resides in a folder other than /woocommerce/
	 * 
	 * @since 1.0
	 */
	public static function get_woocommerce_plugin_dir_file() {
		foreach ( get_option( 'active_plugins', array() ) as $plugin ) {
			if ( substr( $plugin, strlen( '/woocommerce.php' ) * -1 ) === '/woocommerce.php' ) {
				$woocommerce_plugin_file = $plugin;
				break;
			}
		}

		return $woocommerce_plugin_file;
	}
}

WC_Subscriptions_Admin::init();
