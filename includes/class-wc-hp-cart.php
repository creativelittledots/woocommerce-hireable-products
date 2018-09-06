<?php
/**
 * Hireable products cart filters and functions.
 * @class 	WC_HP_Cart
 * @version 1.0.0
 * @since  1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;

class WC_HP_Cart {

	public function __construct() {
		
		add_filter( 'woocommerce_add_cart_item', array($this, 'add_cart_item_filter'), 10, 2 );
		
		add_filter( 'woocommerce_add_cart_item_data', array($this, 'add_cart_item_data'), 10, 3 );
		
		add_filter( 'woocommerce_cart_id', array( $this, 'product_cart_id' ), 10, 5 );

		// Preserve data in cart
		add_filter( 'woocommerce_get_cart_item_from_session', array( $this, 'get_cart_data_from_session' ), 10, 3 );
		
		add_filter( 'woocommerce_get_item_data', array( $this, 'get_item_data' ), 10, 2 );
		
	}
	
	/**
	 * Modifies cart item data - important for the first calculation of totals only.
	 *
	 * @param  array $cart_item
	 * @return array
	 */
	public function add_cart_item_filter( $cart_item, $cart_item_key, $request = true ) {

		// Get product type
		$product = $cart_item['data'];

		if( $product->get_meta('_hireable') == 'yes' ) {
			
			$cart_item = $this->add_cart_item_data($cart_item, $product->get_id(), null, $request );
			
		}		

		return $cart_item;

	}
	
	public function add_cart_item_data($cart_item, $product_id, $variation_id = null, $request = true) {
		
		$product = wc_get_product($product_id);
		
		if( $product->get_meta('_hireable') == 'yes' ) {
			
			if( ! empty( $_REQUEST['hireable_dates'] ) ) {
			
				$cart_item['_hireable_data'] = ! empty( $cart_item['_hireable_data'] ) ? $cart_item['_hireable_data'] : [
					'start_date' => (DateTime::createFromFormat('D M d Y H:i:s e+', $_REQUEST['hireable_dates']['start_date']))->getTimestamp() + 3600,
					'end_date' => (DateTime::createFromFormat('D M d Y H:i:s e+', $_REQUEST['hireable_dates']['end_date']))->getTimestamp() + 3600
				];
				
				if( empty( $cart_item['variation']['Start Date'] ) ) {
	
					$cart_item['variation']['Start Date'] = date('jS M Y', $cart_item['_hireable_data']['start_date']);
					
				}
				
				if( empty( $cart_item['variation']['End Date'] ) ) {
	
					$cart_item['variation']['End Date'] = date('jS M Y', $cart_item['_hireable_data']['end_date']);
					
				}
			
			}
			
			if( ( $price = $product->get_meta('_hireable_price') ) && ! empty( $cart_item['_hireable_data'] ) ) {
			
				$days = 1 + round(($cart_item['_hireable_data']['end_date'] - $cart_item['_hireable_data']['start_date']) / (60 * 60 * 24));
				$formula = $product->get_meta('_hireable_price_formula');
				$total = $formula && strpos($formula, '{p}') !== false ? eval("return " . str_replace(array(
					'{n}',
					'{p}'
				), [$days, $price], $formula) . ';') : $days*$price;
				
				if( $cart_item['data'] instanceof WC_Product ) {
				
					$cart_item['data']->set_price($total);
					
				}
				
			}
			
		}
		
		return $cart_item;
		
	}
	
	/**
	 * Load all hireable-related data to cart variation data
	 *
	 * @param  array 	$item_data
	 * @param  array 	cart_item
	 * @return array	item_data
	 */
	public function get_item_data( $item_data, $cart_item ) {
		
		if ( $cart_item['data']->get_meta( '_hireable' ) == 'yes' && is_array( $cart_item['variation'] ) ) {
			foreach ( $cart_item['variation'] as $name => $value ) {
				$taxonomy = wc_attribute_taxonomy_name( str_replace( 'attribute_pa_', '', urldecode( $name ) ) );

				// If this is a term slug, get the term's nice name
				if ( taxonomy_exists( $taxonomy ) ) {
					$term = get_term_by( 'slug', $value, $taxonomy );
					if ( ! is_wp_error( $term ) && $term && $term->name ) {
						$value = $term->name;
					}
					$label = wc_attribute_label( $taxonomy );

				// If this is a custom option slug, get the options name.
				} else {
					$value = apply_filters( 'woocommerce_variation_option_name', $value );
					$label = wc_attribute_label( str_replace( 'attribute_', '', $name ), $cart_item['data'] );
				}

				// Check the nicename against the title.
				if ( '' === $value || wc_is_attribute_in_product_name( $value, $cart_item['data']->get_name() ) ) {
					continue;
				}

				$item_data[] = array(
					'key'   => $label,
					'value' => $value,
				);
			}
		}
		
		return $item_data;
		
	}

	/**
	 * Load all hireable-related session data.
	 *
	 * @param  array 	$cart_item
	 * @param  array 	$item_session_values
	 * @return array	$cart_item
	 */
	public function get_cart_data_from_session( $cart_item, $item_session_values, $key ) {
		
		$cart_item = $this->add_cart_item_filter( $cart_item, $key, false );

		return $cart_item;
	}
	
	public function product_cart_id( $key, $product_id, $variation_id, $variation, $cart_item_data ) {
		
		$product = wc_get_product($product_id);
		
		if( $product->get_meta('_hireable') == 'yes' && ! empty( $cart_item_data['_hireable_data'] ) ) {
			
			$key .= md5( http_build_query( $cart_item_data['_hireable_data'] ) );
			
		}
		
		return $key;
		
	}

}