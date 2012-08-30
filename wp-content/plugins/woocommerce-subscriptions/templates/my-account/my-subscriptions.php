<?php
/**
 * My Subscriptoins
 */

global $woocommerce;

$payment_gateways = $woocommerce->payment_gateways->payment_gateways();

$subscriptions = WC_Subscriptions_Manager::get_users_subscriptions();

$user_id = get_current_user_id();

foreach ( $subscriptions as $subscription_key => $subscription_details )
	if ( $subscription_details['status'] == 'trash' )
		unset( $subscriptions[$subscription_key] );

?>

<h2><?php _e( 'My Subscriptions', WC_Subscriptions::$text_domain ); ?></h2>

<?php if ( ! empty( $subscriptions ) ) : ?>
<table class="shop_table my_account_subscriptions my_account_orders">

	<thead>
		<tr>
			<th class="subscription-order-number"><span class="nobr"><?php _e( 'Order', WC_Subscriptions::$text_domain ); ?></span></th>
			<th class="subscription-title"><span class="nobr"><?php _e( 'Subscription', WC_Subscriptions::$text_domain ); ?></span></th>
			<th class="subscription-status"><span class="nobr"><?php _e( 'Status', WC_Subscriptions::$text_domain ); ?></span></th>
			<th class="subscription-start-date"><span class="nobr"><?php _e( 'Start Date', WC_Subscriptions::$text_domain ); ?></span></th>
			<th class="subscription-expiry"><span class="nobr"><?php _e( 'Expiration', WC_Subscriptions::$text_domain ); ?></span></th>
			<th class="subscription-end-date"><span class="nobr"><?php _e( 'End Date', WC_Subscriptions::$text_domain ); ?></span></th>
			<th class="subscription-next-payment"><span class="nobr"><?php _e( 'Next Payment', WC_Subscriptions::$text_domain ); ?></span></th>
		</tr>
	</thead>

	<tbody>
	<?php foreach ( $subscriptions as $subscription_key => $subscription_details ) : ?>
		<?php $order = new WC_Order( $subscription_details['order_id'] ); ?>
		<?php $payment_gateway = isset( $payment_gateways[$order->payment_method] ) ? $payment_gateways[$order->payment_method] : ''; ?>
		<tr class="order">
			<td class="order-number" width="1%">
				<a href="<?php echo esc_url( add_query_arg( 'order', $subscription_details['order_id'], get_permalink( woocommerce_get_page_id( 'view_order' ) ) ) ); ?>"><?php echo $subscription_details['order_id'] ?></a>
			</td>
			<td class="subscription-title">
				<a href="<?php echo get_post_permalink( $subscription_details['product_id'] ); ?>">
					<?php echo get_the_title( $subscription_details['product_id'] ); ?>
				</a>
			</td>
			<td class="subscription-status" style="text-align:left; white-space:nowrap;">
				<?php echo ucfirst( $subscription_details['status'] ); ?>
				<?php if ( WC_Subscriptions_Manager::can_subscription_be_changed_to( 'suspended', $subscription_key, $user_id ) ) : ?>
				<a href="<?php echo esc_url( WC_Subscriptions_Manager::get_users_change_status_link( $subscription_key, 'suspended' ) ); ?>" class="suspend" title="<?php _e( 'Click to suspend this Subscription', WC_Subscriptions::$text_domain ); ?>">(<?php _e( 'Suspend', WC_Subscriptions::$text_domain ); ?>)</a>
				<?php elseif ( $subscription_details['status'] !== 'pending' && WC_Subscriptions_Manager::can_subscription_be_changed_to( 'active', $subscription_key, $user_id ) ) : ?>
				<a href="<?php echo esc_url( WC_Subscriptions_Manager::get_users_change_status_link( $subscription_key, 'active' ) ); ?>" class="activate" title="<?php _e( 'Click to reactivate this Subscription', WC_Subscriptions::$text_domain ); ?>">(<?php _e( 'Reactivate', WC_Subscriptions::$text_domain ); ?>)</a>
				<?php endif; ?>
				<?php if ( WC_Subscriptions_Manager::can_subscription_be_changed_to( 'cancelled', $subscription_key, $user_id ) ) : ?>
				<a href="<?php echo esc_url( WC_Subscriptions_Manager::get_users_change_status_link( $subscription_key, 'cancelled' ) ); ?>" class="cancel" title="<?php _e( 'Click to cancel this Subscription', WC_Subscriptions::$text_domain ); ?>">(<?php _e( 'Cancel', WC_Subscriptions::$text_domain ); ?>)</a>
				<?php endif; ?>
			</td>
			<td class="subscription-start-date">
				<time title="<?php echo esc_attr( strtotime( $subscription_details['start_date'] ) ); ?>">
					<?php echo date_i18n( get_option( 'date_format' ), strtotime( $subscription_details['start_date'] ) ); ?>
				</time>
			</td>
			<td class="subscription-expiry">
				<?php if ( $subscription_details['expiry_date'] == 0 ) : ?>
					<?php _e( 'Never', WC_Subscriptions::$text_domain ); ?>
				<?php else : ?>
					<time title="<?php echo esc_attr( strtotime( $subscription_details['expiry_date'] ) ); ?>">
						<?php echo date_i18n( get_option( 'date_format' ), strtotime( $subscription_details['expiry_date'] ) ); ?>
					</time>
				<?php endif; ?>
			</td>
			<td class="subscription-end-date">
				<?php if ( $subscription_details['end_date'] == 0 ) : ?>
					<?php _e( 'Not yet ended', WC_Subscriptions::$text_domain ); ?>
				<?php else : ?>
					<time title="<?php echo esc_attr( strtotime( $subscription_details['end_date'] ) ); ?>">
						<?php echo date_i18n( get_option( 'date_format' ), strtotime( $subscription_details['end_date'] ) ); ?>
					</time>
				<?php endif; ?>
			</td>
			<td class="subscription-next-payment">
				<?php if ( $subscription_details['status'] != 'active' ) : ?>
					-
				<?php else : ?>
				<?php $next_payment_date = WC_Subscriptions_Order::get_next_payment_date( $subscription_details['order_id'], $subscription_details['product_id'] ); ?>
				<time title="<?php echo esc_attr( strtotime( $next_payment_date ) ); ?>">
					<?php echo date_i18n( get_option( 'date_format' ), strtotime( $next_payment_date ) ); ?>
				</time>
				<?php endif; ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</tbody>

</table>

<?php else : ?>

	<p><?php _e( 'You have no active subscriptions.', WC_Subscriptions::$text_domain ); ?></p>

<?php endif;
