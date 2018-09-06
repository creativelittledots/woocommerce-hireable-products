jQuery(document).ready(function($) {
	
	// wc_checkout_params is required to continue, ensure the object exists
	if ( typeof wc_hp_params === 'undefined' ) {
		return false;
	}
	
	$.blockUI.defaults.overlayCSS.cursor = 'default';
		
	var hireDates = {
		start_date: null,
		end_date: null
	}
	
	var price = wc_hp_params.price,
		formula = wc_hp_params.formula;
	
	Date.prototype.addDays = function(days) {
	    var date = new Date(this.valueOf());
	    date.setDate(date.getDate() + days);
	    return date;
	}
	
	Number.prototype.formatMoney = function(c, d, t) {

		var n = this, 
		    c = isNaN(c = Math.abs(c)) ? 2 : c, 
		    d = d == undefined ? "." : d, 
		    t = t == undefined ? "," : t, 
		    s = n < 0 ? "-" : "", 
		    i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "", 
		    j = (j = i.length) > 3 ? j % 3 : 0;
		    
		return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
		
 	}
 	
 	String.prototype.replaceAll = function(search, replacement) {
	    var target = this;
	    return target.split(search).join(replacement);
	}
	
	function getDates(startDate, stopDate) {
	    var dateArray = new Array();
	    var currentDate = startDate;
	    while (currentDate <= stopDate) {
	        dateArray.push(new Date (currentDate));
	        currentDate = currentDate.addDays(1);
	    }
	    return dateArray;
	}
	
	function setSelectedDates() {
		
		if(hireDates.start_date && hireDates.end_date) {
        	var dates = getDates(hireDates.start_date, hireDates.end_date),
        		days = 1 + ((parseInt(hireDates.end_date.getTime()) - parseInt(hireDates.start_date.getTime()))/(1000*60*60*24)),
	        	total = formula && formula.indexOf('{p}') > -1 ? eval(formula.replaceAll('{n}', days).replaceAll('{p}', price)) : days*price;
			$('.js-datepicker-container td.is-selected').removeClass('is-selected');
	        for(d in dates) {
		        var date = dates[d],
		        	y = date.getFullYear(),
		        	m = date.getMonth(),
		        	d = date.getDate();
		        $('.js-datepicker-container .pika-button.pika-day[data-pika-year="' + y + '"][data-pika-month="' + m + '"][data-pika-day="' + d + '"]').parent().addClass('is-selected');
	        }
			$('.js-hireable-price').text(total.formatMoney());
			$('.js-hireable-product-price-container').show();
			$('.js-hireable-product-add-to-cart-button').removeClass('disabled').removeAttr('disabled');
			$('.js-hireable-product-price-from-container').hide();
        } else {
	        $('.js-hireable-product-price-container').hide();
	        $('.js-hireable-product-add-to-cart-button').addClass('disabled').attr('disabled', 'disabled')
			$('.js-hireable-product-price-from-container').show();
        }
		
	}

    var picker = new Pikaday(
    {	
        field: document.querySelector('.js-datepicker'),
        firstDay: 1,
        minDate: new Date(),
        yearRange: [2000, 2020],
        bound: false,
        container: document.querySelector('.js-datepicker-container'),
        onDraw: setSelectedDates,
        onSelect: function(date) {
	        if(hireDates.start_date && date > hireDates.start_date && hireDates.start_date == hireDates.end_date) {
		        hireDates.end_date = date;
	        } else {
		        hireDates.start_date = date;
		        hireDates.end_date = date;
	        }
	        setSelectedDates();
		}
    });
    
    $('.js-hireable-product-add-to-cart-button').click(function(e) {
	   
		e.preventDefault();
	   
		if( $(this).attr('disabled') || ! hireDates.start_date || ! hireDates.end_date ) { return false; }

		var url = wc_add_to_cart_params.wc_ajax_url.toString().replace( '%%endpoint%%', 'add_to_cart' ),
			data = {
				'product_id': $(this).data('product_id'),
				'hireable_dates': hireDates
			},
			button = $(this).eq(0),
			container = $(this).closest('.js-hireable-product-container');
		
		container.block({
			message: null,
			overlayCSS: {
				opacity: 0.6
			}
		});
		
		// Trigger event
		$( document.body ).trigger( 'adding_to_cart', [ button, data ] );
		
		$.post(url, data, function(response) {
			
			if ( ! response ) {
				return;
			}

			var this_page = window.location.toString();

			this_page = this_page.replace( 'add-to-cart', 'added-to-cart' );

			if ( response.error && response.product_url ) {
				window.location = response.product_url;
				return;
			}

			// Redirect to cart option
			if ( wc_add_to_cart_params.cart_redirect_after_add === 'yes' ) {

				window.location = wc_add_to_cart_params.cart_url;
				return;

			} else {

				var fragments = response.fragments;
				var cart_hash = response.cart_hash;

				// Block fragments class
				if ( fragments ) {
					$.each( fragments, function( key ) {
						$( key ).addClass( 'updating' );
					});
				}

				// Replace fragments
				if ( fragments ) {
					$.each( fragments, function( key, value ) {
						$( key ).replaceWith( value );
					});
				}

			}
			
			// Trigger event so themes can refresh other areas
			$( document.body ).trigger( 'added_to_cart', [ fragments, cart_hash, button ] );
			
		}, 'json').always(function(response) {
			
			container.unblock();
			
		}); 
	    
    });

    
});