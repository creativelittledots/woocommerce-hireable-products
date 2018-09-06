<?php
/**
 * Hireable products front-end filters and functions.
 *
 * @class 	WC_HP_Display
 * @version 1.0.0
 * @since   1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;

class WC_HP_Display {
	
	public function __construct() {
		
		add_action( 'woocommerce_hireable_products_display_add_to_cart', array( $this, 'display_add_to_cart' ) );
		
		// Front end scripts
		add_action( 'wp_enqueue_scripts', array( $this, 'frontend_scripts' ) );
		
		// Single product template
		add_action( 'woocommerce_single_product_summary', array( $this, 'enqueue_scripts' ), 30 );
		
		// Single product add-to-cart UI template for hireable products
		add_action( 'woocommerce_after_add_to_cart_form', array( $this, 'output_add_to_cart' ) );
		
	}
	
	public function output_add_to_cart() {
		
		global $product;
		
		$this->display_add_to_cart( $product );
		
	}
	
	public function display_add_to_cart( WC_Product $product ) {
		
		global $wc_hireable_products;
		
		if( $product->get_meta('_hireable') == 'yes' ) {
			
			wc_get_template( 'single-product/add-to-cart/hireable.php', compact('product'), false, $wc_hireable_products->plugin_path() . '/templates/' );
			
		}
		
	}
	
	public function frontend_scripts() {
		
		global $wc_hireable_products;

		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		
		$dependencies = array( 'jquery', 'jquery-blockui' );

		// Add any custom script dependencies here
		// Examples: custom product type scripts and component layered filter scripts
		$dependencies = apply_filters( 'woocommerce_hireable_products_script_dependencies', $dependencies );
		
		wp_register_script( 'wc-add-to-cart-hireable', $wc_hireable_products->plugin_url() . '/assets/js/frontend/add-to-cart-hireable' . $suffix . '.js', $dependencies, $wc_hireable_products->version );

		wp_register_style( 'wc-hireable-single-css', $wc_hireable_products->plugin_url() . '/assets/css/frontend/wc-hireable-single.css', false, $wc_hireable_products->version, 'all' );
		
		
	}
	
	public function enqueue_scripts() {
		
		global $product;
		
		if( $product->get_meta( '_hireable' ) !== 'yes' || ! $product->get_meta( '_hireable_price' ) ) {
			return;
		}
		
		$params = array(
			'script_debug'	=> defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? 'yes' : 'no',
			'price' => $product->get_meta('_hireable_price'),
			'formula' => $product->get_meta('_hireable_price_formula'),
		);
		
		wp_localize_script( 'wc-add-to-cart-hireable', 'wc_hp_params', $params );
		
		wp_enqueue_script( 'wc-add-to-cart-hireable' );
		wp_enqueue_style( 'wc-hireable-single-css' );
		
	}
	
}