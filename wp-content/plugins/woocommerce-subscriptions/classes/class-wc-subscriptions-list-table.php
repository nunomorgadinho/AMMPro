<?php
/**
 * Subscriptions List Table
 * 
 * Extends the WP_List_Table class to create a table for displaying sortable subscriptions.
 *
 * @package		WooCommerce Subscriptions
 * @subpackage	WC_Subscriptions_List_Table
 * @category	Class
 * @author		Brent Shepherd
 */

if ( ! class_exists( 'WP_List_Table' ) )
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );

class WC_Subscriptions_List_Table extends WP_List_Table {

	var $message_transient_prefix = '_subscriptions_messages_';

	/**
	 * Create and instance of this list table.
	 * 
	 * @since 1.0
	 */
	public function __construct(){
		parent::__construct( array(
			'singular'  => 'subscription',
			'plural'    => 'subscriptions',
			'ajax'      => false
		) );
	}

	/**
	 * Outputs the content for each column.
	 * 
	 * @param array $item A singular item (one full row's worth of data)
	 * @param array $column_name The name/slug of the column to be processed
	 * @return string Text or HTML to be placed inside the column <td>
	 * @since 1.0
	 */
	public function column_default( $item, $column_name ){
		global $woocommerce;

		switch( $column_name ){
			case 'status':
				$actions = array();

				$action_url = add_query_arg( 
					array( 
						'page'         => $_REQUEST['page'],
						'user'         => $item['user_id'],
						'subscription' => $item['subscription_key'],
						'_wpnonce'     => wp_create_nonce( $item['subscription_key'] )
					) 
				);

				if ( isset( $_REQUEST['status'] ) )
					$action_url = add_query_arg( array( 'status' => $_REQUEST['status'] ), $action_url );

				$order = new WC_Order( $item['order_id'] );

				$all_statuses = array(
					'active'    => __( 'Reactivate', WC_Subscriptions::$text_domain ),
					'suspended' => __( 'Suspend', WC_Subscriptions::$text_domain ),
					'cancelled' => __( 'Cancel', WC_Subscriptions::$text_domain ),
					'trash'     => __( 'Trash', WC_Subscriptions::$text_domain ),
				);

				foreach ( $all_statuses as $status => $label )
					if ( WC_Subscriptions_Manager::can_subscription_be_changed_to( $status, $item['subscription_key'], $item['user_id'] ) )
						$actions[$status] = sprintf( '<a href="%s">%s</a>', add_query_arg( 'new_status', $status, $action_url ), $label );

				if( $item['status'] == 'pending' )
					unset( $actions['active'] );

				$actions = apply_filters( 'woocommerce_subscriptions_list_table_actions', $actions, $item );

				$column_content = sprintf( '<mark class="%s">%s</mark> %s', sanitize_title( $item[$column_name] ), ucfirst( $item[$column_name] ), $this->row_actions( $actions ) );

				break;

			case 'title' :
				//Return the title contents
				$column_content = sprintf('<a href="%s">%s</a>', get_edit_post_link( $item['product_id'] ), get_the_title( $item['product_id'] ) );
				break;

			case 'order_id':
				$column_content = '<a href="'. get_edit_post_link( $item[$column_name] ) . '">' . sprintf( __( 'Order #%s', WC_Subscriptions::$text_domain ), $item[$column_name] ) . '</a>';
				break;

			case 'user':
				$user = get_user_by( 'id', $item['user_id'] );
				$column_content = sprintf( '<a href="%s">%s</a>', admin_url( 'user-edit.php?user_id=' . $user->ID ), ucfirst( $column_content = $user->display_name ) );
				break;

			case 'start_date':
			case 'expiry_date':
			case 'end_date':
				if ( $column_name == 'expiry_date' && $item[$column_name] == 0 ) {
					$column_content  = __( 'Never', WC_Subscriptions::$text_domain );
				} else if ( $column_name == 'end_date' && $item[$column_name] == 0 ) {
					$column_content = __( 'Not yet ended', WC_Subscriptions::$text_domain );
				} else {
					$column_content  = '<time title="' . esc_attr( strtotime( $item[$column_name] ) ) . '">';
					$column_content .= date_i18n( get_option( 'date_format' ), strtotime( $item[$column_name] ) );
					$column_content .= '</time>';
				}
				break;

			case 'last_payment_date':
				if ( empty( $item['completed_payments'] ) ) {
					$column_content  = '-';
				} else {
					$last_payment_date = array_pop( $item['completed_payments'] );
					$column_content  = '<time title="' . esc_attr( strtotime( $last_payment_date ) ) . '">';
					$column_content .= date_i18n( get_option( 'date_format' ), strtotime( $last_payment_date ) );
					$column_content .= '</time>';
				}
				break;

			case 'next_payment_date':
				if ( $item['status'] != 'active' ) {
					$column_content  = '-';
				} else {
					$next_payment_date = WC_Subscriptions_Order::get_next_payment_date( $item['order_id'], $item['product_id'] );

					$column_content  = '<time title="' . esc_attr( strtotime( $next_payment_date ) ) . '">';
					$column_content .= date_i18n( get_option( 'date_format' ), strtotime( $next_payment_date ) );
					$column_content .= '</time>';
				}
				break;

			default:
				$column_content = print_r( $item, true ); //Show the whole array for troubleshooting purposes
				break;
		}

		return $column_content;
	}

	/**
	 * Make sure the subscription key and user id are included in checkbox column.
	 * 
	 * @see WP_List_Table::::single_row_columns()
	 * @param array $item A singular item (one full row's worth of data)
	 * @return string Markup to be placed inside the column <td>
	 * @since 1.0
	 */
	public function column_cb( $item ){
		return sprintf( '<input type="checkbox" name="%1$s[%2$s][]" value="%3$s" />', $this->_args['plural'], $item['user_id'], $item['subscription_key'] );
	}

	/**
	 * Add all the Subscription field columns to the table.
	 * 
	 * @see WP_List_Table::::single_row_columns()
	 * @return array An associative array containing column information: 'slugs'=>'Visible Titles'
	 * @since 1.0
	 */
	public function get_columns(){

		$columns = array(
			'cb'                => '<input type="checkbox" />',
			'status'            => __( 'Status', WC_Subscriptions::$text_domain ),
			'title'             => __( 'Subscription', WC_Subscriptions::$text_domain ),
			'user'              => __( 'User', WC_Subscriptions::$text_domain ),
			'order_id'          => __( 'Order', WC_Subscriptions::$text_domain ),
			'start_date'        => __( 'Start Date', WC_Subscriptions::$text_domain ),
			'expiry_date'       => __( 'Expiration', WC_Subscriptions::$text_domain ),
			'end_date'          => __( 'End Date', WC_Subscriptions::$text_domain ),
			'last_payment_date' => __( 'Last Payment', WC_Subscriptions::$text_domain ),
			'next_payment_date' => __( 'Next Payment', WC_Subscriptions::$text_domain )
		);

		return $columns;
	}

	/**
	 * Make the table sortable by all columns and set the default sort field to be start_date.
	 * 
	 * @return array An associative array containing all the columns that should be sortable: 'slugs' => array( 'data_values', bool )
	 * @since 1.0
	 */
	public function get_sortable_columns() {

		$sortable_columns = array(
			'status'            => array( 'status', false ),
			'order_id'          => array( 'order_id', false ),
			'user'              => array( 'user', false ),
			'title'             => array( 'product_name', false ),
			'start_date'        => array( 'start_date', true ),
			'expiry_date'       => array( 'expiry_date', false ),
			'end_date'          => array( 'end_date', false ),
			'last_payment_date' => array( 'last_payment_date', false ),
			'next_payment_date' => array( 'next_payment_date', false )
		);

		return $sortable_columns;
	}

	/**
	 * Make it quick an easy to cancel or activate more than one subscription
	 * 
	 * @return array An associative array containing all the bulk actions: 'slugs' => 'Visible Titles'
	 * @since 1.0
	 */
	public function get_bulk_actions() {

		$actions = array();

		if ( ! isset( $_REQUEST['status'] ) || $_REQUEST['status'] != 'trash' )
			$actions = array(
				'trash' => __( 'Move to Trash', WC_Subscriptions::$text_domain )
			);

		return $actions;
	}

	/**
	 * Get the current action selected from the bulk actions dropdown.
	 *
	 * @since 3.1.0
	 * @access public
	 *
	 * @return string|bool The action name or False if no action was selected
	 */
	function current_action() {
		if ( isset( $_REQUEST['new_status'] ) )
			return $_REQUEST['new_status'];

		if ( isset( $_REQUEST['action'] ) )
			return $_REQUEST['action'];

		return false;
	}

	/**
	 * Handle activate & cancel actions for both individual items and bulk edit. 
	 *
	 * @since 1.0
	 */
	public function process_actions() {

		if( $this->current_action() === false )
			return;

		$messages       = array();
		$error_messages = array();

		// Single subscription action
		if( isset( $_GET['subscription'] ) ) {
			if ( ! wp_verify_nonce( $_REQUEST['_wpnonce'], $_GET['subscription'] ) ) 
				wp_die( __( 'Action failed. Invalid Nonce.', WC_Subscriptions::$text_domain ) ); 

			if ( ! WC_Subscriptions_Manager::can_subscription_be_changed_to( $_GET['new_status'], $_GET['subscription'], $_GET['user'] ) ) {
				$error_messages[] = sprintf( __( 'Error: Subscription status can not be changed to "%s".', WC_Subscriptions::$text_domain ), esc_html( $_GET['new_status'] ) );
			} else {
				switch ( $_GET['new_status'] ) {
					case 'active' :
						WC_Subscriptions_Manager::reactivate_subscription( $_GET['user'], $_GET['subscription'] );
						$messages[] = __( 'Subscription activated.', WC_Subscriptions::$text_domain );
						break;
					case 'suspended' :
						WC_Subscriptions_Manager::suspend_subscription( $_GET['user'], $_GET['subscription'] );
						$messages[] = __( 'Subscription suspended.', WC_Subscriptions::$text_domain );
						break;
					case 'cancelled' :
						WC_Subscriptions_Manager::cancel_subscription( $_GET['user'], $_GET['subscription'] );
						$messages[] = __( 'Subscription cancelled.', WC_Subscriptions::$text_domain );
						break;
					case 'trash' :
						WC_Subscriptions_Manager::trash_subscription( $_GET['user'], $_GET['subscription'] );
						$messages[] = __( 'Subscription trashed.', WC_Subscriptions::$text_domain );
						break;
					default :
						$error_messages[] = __( 'Error: Unknown subscription status.', WC_Subscriptions::$text_domain );
						break;
				}
			}

		} else if( isset( $_GET['subscriptions'] ) ) { // Bulk actions

			if ( ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'bulk-' . $this->_args['plural'] ) ) 
				wp_die( __( 'Bulk edit failed. Invalid Nonce.', WC_Subscriptions::$text_domain ) ); 

			$subscriptions = $_GET[$this->_args['plural']];

			$subscription_count = 0;
			$error_count = 0;

			if( 'trash' === $this->current_action() ) {

				foreach ( $subscriptions as $user_id => $subscription_keys ) {
					foreach ( $subscription_keys as $subscription_key ) {

						if ( ! WC_Subscriptions_Manager::can_subscription_be_changed_to( 'trash', $subscription_key, $user_id ) ) {
							$error_count++;
						} else {
							$subscription_count++;
							WC_Subscriptions_Manager::trash_subscription( $user_id, $subscription_key );
						}
					}
				}

				if ( $subscription_count > 0 )
					$messages[] = sprintf( _n( '%d subscription moved to trash.', '%s subscriptions moved to trash.', $subscription_count ), $subscription_count );

				if ( $error_count > 0 )
					$error_messages[] = sprintf( _n( '%d subscription could not be trashed - is it active or suspended? Try cancelling it before trashing it.', '%s subscriptions could not be trashed - are they active or suspended? Try cancelling them before trashing.', $error_count ), $error_count );

			}
		}

		$status = ( isset( $_GET['status'] ) ) ? $_GET['status'] : 'all';

		$message_nonce = wp_create_nonce( __FILE__ );

		set_transient( $this->message_transient_prefix . $message_nonce, array( 'messages' => $messages, 'error_messages' => $error_messages ), 60 * 60 );

		$redirect_to = add_query_arg( array( 'status' => $status, 'message' => $message_nonce ), admin_url( 'admin.php?page=subscriptions' ) );

		// Redirect to avoid performning actions on a page refresh
		wp_safe_redirect( $redirect_to );
	}

	/**
	 * Get an associative array ( id => link ) with the list
	 * of views available on this table.
	 *
	 * @since 1.0
	 * @return array
	 */
	function get_views() {
		$views = array();

		foreach ( $this->statuses as $status => $count ) {

			if ( ( isset( $_GET['status'] ) && $_GET['status'] == $status ) || ( ! isset( $_GET['status'] ) && $status == 'active' ) )
				$class = ' class="current"';
			else
				$class = '';

			$views[$status] = sprintf( '<a href="%s"%s>%s (%s)</a>', add_query_arg( 'status', $status, admin_url( 'admin.php?page=subscriptions' ) ), $class, ucfirst( $status ), $count );
		}

		return $views;
	}

	/**
	 * Output any messages set on the class
	 *
	 * @since 1.0
	 */
	public function messages() {

		if ( isset( $_GET['message'] ) ) {

			$all_messages = get_transient( $this->message_transient_prefix . $_GET['message'] );

			if ( ! empty( $all_messages ) ) {

				delete_transient( $this->message_transient_prefix . $_GET['message'] );

				if ( ! empty( $all_messages['messages'] ) )
					echo '<div id="moderated" class="updated"><p>' . implode( "<br/>\n", $all_messages['messages'] ) . '</p></div>';

				if ( ! empty( $all_messages['error_messages'] ) )
					echo '<div id="moderated" class="error"><p>' . implode( "<br/>\n", $all_messages['error_messages'] ) . '</p></div>';
			}
		}
	}

	/**
	 * Get, sort and filter subscriptions for display.
	 *
	 * @uses $this->_column_headers
	 * @uses $this->items
	 * @uses $this->get_columns()
	 * @uses $this->get_sortable_columns()
	 * @uses $this->get_pagenum()
	 * @uses $this->set_pagination_args()
	 * @since 1.0
	 */
	function prepare_items() {

		$per_page = 10;

		$this->_column_headers = array( 
			$this->get_columns(),         // columns
			array(),                      // hidden
			$this->get_sortable_columns() // sortable
		);

		$this->process_actions();

		$subscriptions_grouped_by_user = WC_Subscriptions_Manager::get_all_users_subscriptions();

		$status_to_show = ( isset( $_GET['status'] ) ) ? $_GET['status'] : 'all';

		// Reformat the subscriptions grouped by user to be usable by each row
		$subscriptions  = array();
		$this->statuses = array();

		foreach ( $subscriptions_grouped_by_user as $user_id => $users_subscriptions ) {
			foreach ( $users_subscriptions as $subscription_key => $subscription ) {
				$this->statuses[$subscription['status']] = ( isset( $this->statuses[$subscription['status']] ) ) ? $this->statuses[$subscription['status']] + 1 : 1;

				$all_subscriptions[$subscription_key] = $subscription + array( 
					'user_id'          => $user_id,
					'subscription_key' => $subscription_key
				);

				if ( $status_to_show == $subscription['status'] || ( $status_to_show == 'all' && $subscription['status'] != 'trash' ) ) {
					$subscriptions[$subscription_key] = $subscription + array( 
						'user_id'          => $user_id,
						'subscription_key' => $subscription_key
					);
				}
			}
		}

		// If we have a request for a status that does not exist, default to all subscriptions
		if ( ! isset( $this->statuses[$status_to_show] ) ) {
			if ( $status_to_show != 'all' ) {
				$status_to_show = $_GET['status'] = 'all';
				foreach ( $all_subscriptions as $subscription_key => $subscription )
					if ( $all_subscriptions[$subscription_key]['status'] != 'trash' )
						$subscriptions = $subscriptions + array( $subscription_key => $subscription );
			} else {
				$_GET['status'] = 'all';
			}
		}

		ksort( $this->statuses );

		$this->statuses = array( 'all' => array_sum( $this->statuses ) ) + $this->statuses;

		if ( isset( $this->statuses['trash'] ) )
			$this->statuses['all'] = $this->statuses['all'] - $this->statuses['trash'];

		usort( $subscriptions, array( &$this, 'sort_subscriptions' )  );

		// Add sorted & sliced data to the items property to be used by the rest of the class
		$this->items = array_slice( $subscriptions, ( ( $this->get_pagenum() - 1 ) * $per_page ), $per_page );

		$total_items = count( $subscriptions );

		$this->set_pagination_args( 
			array(
				'total_items' => $total_items,
				'per_page'    => $per_page,
				'total_pages' => ceil( $total_items / $per_page )
			) 
		);
	}

	/**
	 * Extra controls to be displayed between bulk actions and pagination
	 *
	 * @since 3.1.0
	 * @access protected
	 */
	function display_tablenav( $which ) {
		if ( 'top' == $which ) { ?>
		<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
		<?php if ( isset( $_REQUEST['status'] ) ) : ?>
		<input type="hidden" name="status" value="<?php echo $_REQUEST['status'] ?>" />
		<?php endif;
		}
		parent::display_tablenav( $which );
	}

	/**
	 * The text to display before any sign-ups. 
	 *
	 * @since 1.0
	 */
	public function no_items() {
		echo '<p>';
		_e( 'Subscriptions will appear here for you to view and manage once purchased by a customer.', WC_Subscriptions::$text_domain );
		echo '</p>';
		echo '<p>';
		printf( __( '%sMore about managing subscriptions%s', WC_Subscriptions::$text_domain ), '<a href="http://wcdocs.woothemes.com/user-guide/extensions/subscriptions/store-manager-guide/#section-3" target="_blank">', ' &raquo;</a>' );
		echo '</p>';
		echo '<p>';
		printf( __( '%sAdd a subscription product%s', WC_Subscriptions::$text_domain ), '<a href="' . WC_Subscriptions_Admin::add_subscription_url() . '">', ' &raquo;</a>' );
		echo '</p>';
	}

	/**
	 * If no sort order set, default to title. If no sort order, default to descending.
	 *
	 * @since 1.0
	 */
	function sort_subscriptions( $a, $b ){

		$order_by = ( ! empty( $_REQUEST['orderby'] ) ) ? $_REQUEST['orderby'] : 'start_date'; 

		$order = ( ! empty( $_REQUEST['order'] ) ) ? $_REQUEST['order'] : 'desc'; 

		switch ( $order_by ) {
			case 'product_name' :
				$product_name_a = get_the_title( $a['product_id'] );
				$product_name_b = get_the_title( $b['product_id'] );
				$result = strcasecmp( $product_name_a, $product_name_b );
				break;
			case 'user' : 
				$user_a = get_user_by( 'id', $a['user_id'] );
				$user_b = get_user_by( 'id', $b['user_id'] );
				$result = strcasecmp( $user_a->display_name, $user_b->display_name );
				break;
			case 'expiry_date' :
				if ( $order == 'asc' )
					$result = self::sort_with_zero_at_end( $a[$order_by], $b[$order_by] ); // Display subscriptions that have not ended at the end of the list
				else
					$result = self::sort_with_zero_at_beginning( $a[$order_by], $b[$order_by] );
				break;
			case 'end_date' :
				$result = self::sort_with_zero_at_end( $a[$order_by], $b[$order_by] ); // Display subscriptions that have not ended at the end of the list
				break;
			case 'next_payment_date' :
				$next_payment_a = ( $a['status'] != 'active' ) ? 0 : strtotime( WC_Subscriptions_Order::get_next_payment_date( $a['order_id'], $a['product_id'] ) );
				$next_payment_b = ( $b['status'] != 'active' ) ? 0 : strtotime( WC_Subscriptions_Order::get_next_payment_date( $b['order_id'], $b['product_id'] ) );
				$result = self::sort_with_zero_at_end( $next_payment_a, $next_payment_b ); // Display subscriptions with no future payments at the end
				break;
			case 'last_payment_date' :
				$last_payment_a = ( empty( $a['completed_payments'] ) ) ? 0 : strtotime( array_pop( $a['completed_payments'] ) );
				$last_payment_b = ( empty( $b['completed_payments'] ) ) ? 0 : strtotime( array_pop( $b['completed_payments'] ) );
				$result = self::sort_with_zero_at_end( $last_payment_a, $last_payment_b ); // Display subscriptions with no compelted payments at the end
				break;
			case 'order_id' :
				$result = strnatcmp( $a[$order_by], $b[$order_by] );
				break;
			default :
				$result = strcmp( $a[$order_by], $b[$order_by] );
				break;
		}

		return ( $order == 'asc' ) ? $result : -$result; // Send final sort direction to usort
	}

	/**
	 * A special sorting function to always push a 0 or empty value to the end of the sorted list
	 *
	 * @since 1.2
	 */
	function sort_with_zero_at_end( $a, $b ){

		$order = ( ! empty( $_REQUEST['order'] ) ) ? $_REQUEST['order'] : 'desc'; 

		if ( ( $a == 0 || $b == 0 ) && $a != $b ) {
			if ( $order == 'desc' ) // Set 0 to be < anything other than itself & anything other than 0 to be greater than 0
				$result = ( $a == 0 ) ? -1 : 1;
			elseif ( $order == 'asc' ) // Set 0 to be > anything other than itself & anything other than 0 to be less than 0
				$result = ( $a == 0 ) ? 1 : -1;
		} else {
			$result = strcmp( $a, $b );
		}

		return $result;
	}

	/**
	 * A special sorting function to always push a 0 value to the beginning of a sorted list
	 *
	 * @since 1.2
	 */
	function sort_with_zero_at_beginning( $a, $b ){

		$order = ( ! empty( $_REQUEST['order'] ) ) ? $_REQUEST['order'] : 'desc'; 

		if ( ( $a == 0 || $b == 0 ) && $a != $b ) {
			if ( $order == 'desc' ) // Set 0 to be > anything other than itself & anything other than 0 to be less than 0
				$result = ( $a == 0 ) ? 1 : -1;
			elseif ( $order == 'asc' ) // Set 0 to be < anything other than itself & anything other than 0 to be greater than 0
				$result = ( $a == 0 ) ? -1 : 1;
		} else {
			$result = strcmp( $a, $b );
		}

		return $result;
	}
}
