<?php
/*
* Plugin Name: WooCommerce Hire Products
* Description: Allow customers to hire your products with WooCommerce
* Version: 1.0.0
* Author: Creative Little Dots
* Author URI: http://creativelittledots.co.uk
*
* Text Domain: woocommerce-hireable-products
* Domain Path: /languages/
*
*
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WC_Hireable_Products {
	
	public $version 	= '1.0.0';
	public $required 	= '2.1.0';

	public $admin;
	public $api;
	public $cart;
	public $order;
	public $display;
	
	public function __construct() {

		add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ) );
		add_action( 'init', array( $this, 'init' ) );
		add_action( 'admin_init', array( $this, 'activate' ) );
		register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );

	}
	
	public function plugin_url() {
		return plugins_url( basename( plugin_dir_path(__FILE__) ), basename( __FILE__ ) );
	}

	public function plugin_path() {
		return untrailingslashit( plugin_dir_path( __FILE__ ) );
	}

	public function plugins_loaded() {

		global $woocommerce;

		// WC 2 check
		if ( version_compare( $woocommerce->version, $this->required ) < 0 ) {
			add_action( 'admin_notices', array( $this, 'admin_notice' ) );
			return false;
		}

		// Functions for 2.X back-compat
		include_once( 'includes/wc-hp-functions.php' );


		// Admin functions and meta-boxes
		if ( is_admin() ) {
			
			$this->admin_includes();
			
		}

		// Cart-related functions and filters
		require_once( 'includes/class-wc-hp-cart.php' );
		$this->cart = new WC_HP_Cart();

		// Order-related functions and filters
		require_once( 'includes/class-wc-hp-order.php' );
		$this->order = new WC_HP_Order();

		// Front-end filters
		require_once( 'includes/class-wc-hp-display.php' );
		$this->display = new WC_HP_Display();

	}

	/**
	 * Loads the Admin filters / hooks.
	 *
	 * @return void
	 */
	private function admin_includes() {

		require_once( 'includes/admin/class-wc-hp-admin.php' );
		$this->admin = new WC_HP_Admin();
	}
	
	/**
	 * Load textdomain.
	 *
	 * @return void
	 */
	public function init() {

		load_plugin_textdomain( 'woocommerce-hireable-products', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
		
	}

	/**
	 * Displays a warning message if version check fails.
	 *
	 * @return string
	 */
	public function admin_notice() {

	    echo '<div class="error"><p>' . sprintf( __( 'WooCommerce Hire Products requires at least WooCommerce %s in order to function. Please upgrade WooCommerce.', 'woocommerce-hireable-products'), $this->required ) . '</p></div>';
	}

	/**
	 * Update or create 'Hire' product type on activation as required.
	 *
	 * @return void
	 */
	public function activate() {

		global $wpdb;

		$version = get_option( 'wc_hp_products_version', false );

		if ( $version == false ) {

			add_option( 'wc_hp_products_version', $this->version );

			// Update from previous versions

			// delete old option
			delete_option( 'woocommerce_hire_products_active' );

		} elseif ( version_compare( $version, $this->version, '<' ) ) {

			update_option( 'wc_hp_products_version', $this->version );
		}

	}

	/**
	 * Deactivate extension.
	 *
	 * @return void
	 */
	public function deactivate() {

		delete_option( 'wc_hp_products_version' );
	}
	
}

$GLOBALS[ 'wc_hireable_products' ] = new WC_Hireable_Products();