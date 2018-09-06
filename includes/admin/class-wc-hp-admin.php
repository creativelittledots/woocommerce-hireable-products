<?php
/**
 * Admin filters and functions.
 *
 * @class 	WC_HP_Admin
 * @version 1.0.0
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class WC_HP_Admin {

	public function __construct() {
		
		add_action( 'woocommerce_process_product_meta', array($this, 'save_hire_product_field'), 10000, 2 );
		add_filter( 'product_type_options', array( $this, 'add_hire_options' ) );
		
		// Processes and saves the necessary post metas from the selections made above
		add_action( 'woocommerce_process_product_meta', array( $this, 'process_hireable_meta' ) );
		
		// Adds the base price options
		add_action( 'woocommerce_product_options_pricing', array( $this, 'show_pricing_option' ), 11 );
		
	}
	
	public function save_hire_product_field( $post_id, $post ) {
	    	
    	if( isset( $_POST['_hireable'] ) ) {
		
    		update_post_meta($post_id, '_hireable', 'yes');
    		
		} else {
    		
    		update_post_meta($post_id, '_hireable', 'no');
    		
		}
    	
	}
	
	public function add_hire_options( $options ) {
	
		$options[ 'hireable' ] = array(
			'id'            => '_hireable',
			'label'         => __( 'Hireable', 'wpkit' ),
			'description'   => __( 'When <strong>Hireable</strong> is checked, the product will show UI in the front end the customers to hire the product', 'woocommerce-hireable-products' ),
			'default'       => 'no'
		);

		return $options;
	}
	
	public function show_pricing_option() {
		
		echo '<div class="options_group show_if_hireable">';

		// Price
		woocommerce_wp_text_input( array( 
			'id' => '_hireable_price', 
			'class' => 'short', 
			'label' => __( 'Hire Price', 'woocommerce-hireable-products' ) . ' (' . get_woocommerce_currency_symbol().')', 
			'data_type' => 'price',
			'description'   => __( 'Price should be based on per day basis', 'woocommerce-hireable-products' )
		) );
		
		// Price Formula
		woocommerce_wp_text_input( array( 
			'id' => '_hireable_price_formula', 
			'class' => 'short', 
			'label' => __( 'Hire Price Formula', 'woocommerce-hireable-products' ), 
			'placeholder' => '{n}*{p}',
			'description'   => __( 'Use <strong>{n}</strong> in the formula as the number of days and <strong>{p}</strong> as the daily price. Leave blank to use default <strong>{n}*{p}</strong>.' )
		) );

		echo '</div>';
		
	}
	
	/**
	 * Process, verify and save hireable product data.
	 *
	 * @param  int 	$post_id
	 * @return void
	 */
	public function process_hireable_meta( $post_id ) {

		update_post_meta( $post_id, '_hireable_price', stripslashes( wc_format_decimal( $_POST[ '_hireable_price' ] ) ) );
		update_post_meta( $post_id, '_hireable_price_formula', stripslashes( $_POST[ '_hireable_price_formula' ] ) );

	}

}