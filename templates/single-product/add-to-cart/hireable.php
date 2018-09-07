<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
		
<div class="js-hireable-product-container">

	<h5>Hire Product</h5>
	
	<?php do_action( 'woocommerce_hireable_products_before_add_to_cart', $product ); ?>
	
	<?php if( $price = $product->get_meta( '_hireable_price') ) : ?>
	
		<div class="total">
			
			<div class="js-hireable-product-price-from-container">
		
				<p class="price">
					
					<span class="from">From: </span>
					
					<?= wc_price( $price ); ?>
					
					<small class="woocommerce-price-suffix">per day</small>
					
				</p>
				
			</div>
			
			<div class="js-hireable-product-price-container hide">
				
				<p class="price">
					
					<span class="woocommerce-Price-amount amount">
					
						<span class="woocommerce-Price-currencySymbol">£</span>
						
						<span class="js-hireable-price"></span>
						
					</span>
					
					<small class="woocommerce-price-suffix">Excl. Tax</small>
					
				</p>
				
			</div>
			
		</div>
		
		<label class="datepicker__label">
			
			<a class="datepicker__toggle closed js-datepicker-toggle" href="#">
				
				<span>Click here to choose a date range:</span>
				
			</a>
		
		</label>
		
		<div class="datepicker__wrapper js-datepicker-wrapper">
			
			<input type="hidden" name="hire_date_range" id="hire_date_range" class="js-datepicker" />
		
			<div class="js-datepicker-container datepicker__container"></div>
			
			<button type="submit" class="button buy radius small-12 disabled js-hireable-product-add-to-cart-button" data-product_id="<?= get_the_ID(); ?>" disabled>Hire product</button>
			
		</div>
		
	<?php endif; ?>
	
</div>