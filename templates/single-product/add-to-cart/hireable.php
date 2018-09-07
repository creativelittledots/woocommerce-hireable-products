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

<style>
	
	.datepicker__toggle, .datepicker__toggle:focus, .datepicker__toggle:hover {
		
		font-weight: 600;
		color: black;
		font-size: 0.75rem;
		
	}
	
	.datepicker__wrapper {
		
		display: none;
		width: 100%;
		
	}
	
	.datepicker__toggle:after {
		
		content: '>' !important;
		position: relative;
		float: right;
		color: #b5b5b5;
		font-weight: 600;
		font-size: 1rem;
		transform: scale(1.9, 1.3) rotate(90deg);
		transition: transform 300ms ease;
		
	}
	
	.datepicker__toggle.closed:after {
		
	    transform: scale(1.9, 1.3) rotate(-90deg);
	    
	}
	
</style>

<script type="text/javascript">

	jQuery(document).ready(function($) {
	
		$('.js-datepicker-toggle').click(function(e) {
			
			e.preventDefault();
			
			$('.js-datepicker-wrapper').slideToggle();
			$(this).toggleClass('closed');
			
		});
		
	});
	
	
</script>