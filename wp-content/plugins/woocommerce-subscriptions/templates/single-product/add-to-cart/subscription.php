<?php
/**
 * Subscription Product Add to Cart
 */
global $woocommerce, $product;

// Availability
$availability = $product->get_availability();

if ($availability['availability']) :
	echo apply_filters( 'woocommerce_stock_html', '<p class="stock '.$availability['class'].'">'.$availability['availability'].'</p>', $availability['availability'] );
   endif;

if ( ! $product->is_in_stock() ) : ?>
	<link itemprop="availability" href="http://schema.org/OutOfStock">
<?php else : ?>

	<link itemprop="availability" href="http://schema.org/InStock">

	<?php do_action( 'woocommerce_before_add_to_cart_form' ); ?>

	<form action="<?php echo esc_url( $product->add_to_cart_url() ); ?>" class="cart" method="post" enctype='multipart/form-data'>

	 	<?php do_action( 'woocommerce_before_add_to_cart_button' ); ?>

	 	<button type="submit" class="button alt"><?php echo apply_filters( 'single_add_to_cart_text', __( 'Sign Up', 'woocommerce' ), $product->product_type ); ?></button>

	 	<?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>

	</form>

	<?php do_action( 'woocommerce_after_add_to_cart_form' ); ?>

<?php endif; ?>