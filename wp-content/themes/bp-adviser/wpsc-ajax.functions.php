<?php

/**
 * add_to_cart function, used through ajax and in normal page loading.
 * No parameters, returns nothing
 */
function wpsc_add_to_cart_2($product_id) {
	global $wpdb, $wpsc_cart;
	/// default values
	$default_parameters['variation_values'] = null;
	$default_parameters['quantity'] = 1;
	$default_parameters['provided_price'] = null;
	$default_parameters['comment'] = null;
	$default_parameters['time_requested'] = null;
	$default_parameters['custom_message'] = null;
	$default_parameters['file_data'] = null;
	$default_parameters['is_customisable'] = false;
	$default_parameters['meta'] = null;

	/* */
	$provided_parameters = array();
	
	/// sanitise submitted values
	$product_id = apply_filters( 'wpsc_add_to_cart_product_id', (int)$product_id );

	
	// compatibility with older themes
	if ( isset( $_POST['wpsc_quantity_update'] ) && is_array( $_POST['wpsc_quantity_update'] ) ) {
		$_POST['wpsc_quantity_update'] = $_POST['wpsc_quantity_update'][$product_id];
	}

	if(isset($_POST['variation'])){
		foreach ( (array)$_POST['variation'] as $key => $variation )
			$provided_parameters['variation_values'][(int)$key] = (int)$variation;

		if ( count( $provided_parameters['variation_values'] ) > 0 ) {
			$variation_product_id = wpsc_get_child_object_in_terms( $product_id, $provided_parameters['variation_values'], 'wpsc-variation' );
			if ( $variation_product_id > 0 )
				$product_id = $variation_product_id;
		}

	}
	

	if ((isset($_POST['quantity']) && $_POST['quantity'] > 0) && (!isset( $_POST['wpsc_quantity_update'] )) ) {
		$provided_parameters['quantity'] = (int)$_POST['quantity'];
	} else if ( isset( $_POST['wpsc_quantity_update'] ) ) {
		$wpsc_cart->remove_item( $_POST['key'] );
		$provided_parameters['quantity'] = (int)$_POST['wpsc_quantity_update'];
	}

	if (isset( $_POST['is_customisable']) &&  $_POST['is_customisable'] == 'true' ) {
		$provided_parameters['is_customisable'] = true;

		if ( isset( $_POST['custom_text'] ) ) {
			$provided_parameters['custom_message'] = $_POST['custom_text'];
		}
		if ( isset( $_FILES['custom_file'] ) ) {
			$provided_parameters['file_data'] = $_FILES['custom_file'];
		}
	}
	if ( isset($_POST['donation_price']) && ((float)$_POST['donation_price'] > 0 ) ) {
		$provided_parameters['provided_price'] = (float)$_POST['donation_price'];
	}
	$parameters = array_merge( $default_parameters, (array)$provided_parameters );
	/* */
	$state = $wpsc_cart->set_item( $product_id, $parameters );

	/* */
	$product = get_post( $product_id );

	if ( $state == true ) {
		$cart_messages[] = str_replace( "[product_name]", stripslashes( $product->post_title ), __( 'You just added "[product_name]" to your cart.', 'wpsc' ) );
	} else {
		if ( $parameters['quantity'] <= 0 ) {
			$cart_messages[] = __( 'Sorry, but you cannot add zero items to your cart', 'wpsc' );
		} else if ( $wpsc_cart->get_remaining_quantity( $product_id, $parameters['variation_values'], $parameters['quantity'] ) > 0 ) {
			$quantity = $wpsc_cart->get_remaining_quantity( $product_id, $parameters['variation_values'], $parameters['quantity'] );
			$cart_messages[] = sprintf( _n( 'Sorry, but there is only %s of this item in stock.', 'Sorry, but there are only %s of this item in stock.', $quantity, 'wpsc' ), $quantity );
		} else {
			$cart_messages[] = sprintf( __( 'Sorry, but the item "%s" is out of stock.', 'wpsc' ), $product->post_title	);
		}
	}

	if ( isset($_GET['ajax']) && $_GET['ajax'] == 'true' ) {
		if ( ($product_id != null) && (get_option( 'fancy_notifications' ) == 1) ) {
			echo "if(jQuery('#fancy_notification_content')) {\n\r";
			echo "   jQuery('#fancy_notification_content').html(\"" . str_replace( array( "\n", "\r" ), array( '\n', '\r' ), addslashes( fancy_notification_content( $cart_messages ) ) ) . "\");\n\r";
			echo "   jQuery('#loading_animation').css('display', 'none');\n\r";
			echo "   jQuery('#fancy_notification_content').css('display', 'block');\n\r";
			echo "}\n\r";
			$error_messages = array( );
		}

		ob_start();

		include_once( wpsc_get_template_file_path( 'wpsc-cart_widget.php' ) );

		$output = ob_get_contents();
		ob_end_clean();
		$output = str_replace( Array( "\n", "\r" ), Array( "\\n", "\\r" ), addslashes( $output ) );
		echo "jQuery('div.shopping-cart-wrapper').html('$output');\n";


		if ( get_option( 'show_sliding_cart' ) == 1 ) {
			if ( (wpsc_cart_item_count() > 0) || (count( $cart_messages ) > 0) ) {
				$_SESSION['slider_state'] = 1;
				echo "
               jQuery('#sliding_cart').slideDown('fast',function(){
                  jQuery('#fancy_collapser').attr('src', ('".WPSC_CORE_IMAGES_URL."/minus.png'));
               });
         ";
			} else {
				$_SESSION['slider_state'] = 0;
				echo "
               jQuery('#sliding_cart').slideUp('fast',function(){
                  jQuery('#fancy_collapser').attr('src', ('".WPSC_CORE_IMAGES_URL."/plus.png'));
               });
         ";
			}
		}

		echo "jQuery('.cart_message').delay(3000).slideUp(500);";

		do_action( 'wpsc_alternate_cart_html', $cart_messages );
		/* */
		exit();
	}
}

/**
 * empty cart function, used through ajax and in normal page loading.
 * No parameters, returns nothing
 */
function wpsc_empty_cart_2() {
	global $wpdb, $wpsc_cart;
	$wpsc_cart->empty_cart( false );

	if ( $_REQUEST['ajax'] == 'true' ) {
		ob_start();

		include_once( wpsc_get_template_file_path( 'wpsc-cart_widget.php' ) );
		$output = ob_get_contents();

		ob_end_clean();
		$output = str_replace( Array( "\n", "\r" ), Array( "\\n", "\\r" ), addslashes( $output ) );
		echo "jQuery('div.shopping-cart-wrapper').html('$output');";
		do_action( 'wpsc_alternate_cart_html' );

		if ( get_option( 'show_sliding_cart' ) == 1 ) {
			$_SESSION['slider_state'] = 0;
			echo "
            jQuery('#sliding_cart').slideUp('fast',function(){
               jQuery('#fancy_collapser').attr('src', (WPSC_CORE_IMAGES_URL+'/plus.png'));
            });
      ";
		}
		exit();
	}
}

?>
