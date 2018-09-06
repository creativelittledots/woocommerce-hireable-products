<?php
/**
 * Hireable products order filters and functions.
 *
 * @class 	WC_HP_Order
 * @version 1.0.0
 * @since   1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WC_HP_Order {

	public function __construct() {
		
		add_action( 'wp_loaded', array( $this, 'redirect_if_order_again' ), 9 );
		
		add_action( 'woocommerce_checkout_create_order_line_item', array($this, 'set_hireable_meta' ), 10, 4 );
		
		add_filter( 'woocommerce_order_again_cart_item_data', array($this, 'order_again_cart_item_data'), 10, 3 );
		
		add_action( 'woocommerce_add_to_cart', array($this, 'order_again' ), 10, 6 );
		
	}
	
	public function redirect_if_order_again( $order ) {
		
		if ( isset( $_GET['order_again'], $_GET['_wpnonce'] ) && is_user_logged_in() && wp_verify_nonce( wp_unslash( $_GET['_wpnonce'] ), 'woocommerce-order_again' ) ) {
			
			if( $order = wc_get_order( $_GET['order_again'] ) ) {
				
				if( $product_id = $order->get_meta( '_hireable' ) ) {
					
					$product = wc_get_product( $product_id );
					
					wp_redirect( $product ? get_permalink( $product->get_id() ) : home_url(), 302 );
					
					exit();
					
				} 
				
			}
			
		}
		
	}
		
	public function set_hireable_meta( $item, $cart_item_key, $values, $order ) {

		if( ! empty( $item->legacy_values['_hireable_data'] ) ) {
				
			$item->update_meta_data('_hireable', $item->legacy_values['hireable']);
			$order->update_meta_data('_hireable', $item->get_product_id());
			
		}

	}
	
	public function order_again_cart_item_data( $item_data, $item, $order ) {
		
		if( $item->get_meta('_hireable') ) {
			
			$item_data['_hireable_data'] = $item->get_meta('_hireable');
			
		}
		
		return $item_data;
		
	}
	
	public function order_again( $cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data ) {
		
		if( ! empty( $cart_item_data['_hireable_data'] ) ) {
			
			$variation = array_merge($variation, array_combine(array_map(function($label) {
				
				return implode(' ', array_map('ucfirst', explode('_', $label)));
				
			}, array_keys($cart_item_data['_hireable_data'])), array_map(function($time) {
			
				return date('jS M Y', $time);
			
			}, $cart_item_data['_hireable_data'])));
			
			wc()->cart->cart_contents[ $cart_item_key ]['variation'] = $variation;
			
		}
		
	}
	
}
