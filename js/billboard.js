(function($){
	function d(variable) {
		try {
			console.log(variable);
		} catch(e) {
			//
		}
	}
	
	var $bb;
	var slides;
	
	var _public = {
		'init': function(defaults, options){
			d('Init');
			$bb = $(this);
			
			_private.initSlides();
			_private.initAnimation();
			
		},
		
		'next': function() {

		},
		
		'prev': function() {

		}
	};
	
	var _private = {
		initSlides: function() {
			slides = [];
			$('.uds-bb-slide', $bb).each(function(i, el){
				var slide = {
					bg: $('.uds-bb-bg-image', el).remove().attr('src'),
					html: $(el).remove().html()
				};
				slides.push(slide);
			});
		},
		initAnimation: function() {
			
		}
	};
	
	var animations = {
		fade: {
			setup: function() {
			
			},
			perform: function() {
			
			},
			cleanup: function() {
			
			}
		}
	};
	
	$.fn.uBillboard = function(options){
		
		var defaults = {
			width:		'960px',
			height:		'400px',
			layout:		'vertical-tabs',
			transition:	'slide'
		};
		
		return this.each(function(){
			var $this = $(this);
			
			if(_public[options]) {
				return _public[options].apply( this, Array.prototype.slice.call( arguments, 1 ));
			} else if(typeof options === 'object' || !options) {
				return _public.init.apply(this, [defaults, options]);
			} else {
				$.error('Method ' + options + ' does not exist on uBillboard');
			}
		});
	};
})(jQuery);

jQuery(document).ready(function($){
	$('.uds-bb').uBillboard();
});