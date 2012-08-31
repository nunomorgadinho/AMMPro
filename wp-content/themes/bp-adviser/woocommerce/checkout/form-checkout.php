<?php
/**
 * Checkout Form
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

global $woocommerce; $woocommerce_checkout = $woocommerce->checkout();
?>

<?php $woocommerce->show_messages();  ?>

<?php do_action('woocommerce_before_checkout_form');?>



<?php
// filter hook for include new pages inside the payment method
$get_checkout_url = apply_filters( 'woocommerce_get_checkout_url', $woocommerce->cart->get_checkout_url() ); ?>

<form name="checkout" method="post" class="checkout" action="<?php echo get_home_url();//echo esc_url( $get_checkout_url ); ?>">

	<?php if ( sizeof( $woocommerce_checkout->checkout_fields ) > 0 ) : ?>

		<?php do_action( 'woocommerce_checkout_before_customer_details'); ?>

		<div class="col2-set" id="customer_details">

			<div class="">

				<?php do_action('woocommerce_checkout_billing'); ?>

			</div>

		</div>

		<?php do_action( 'woocommerce_checkout_after_customer_details'); ?>

		<!--  <h3 id="order_review_heading"><?php _e('Your order', 'woocommerce'); ?></h3>-->

	<?php endif; ?>

	<?php  do_action('woocommerce_checkout_order_review'); ?>

</form>

<?php do_action('woocommerce_after_checkout_form'); ?>