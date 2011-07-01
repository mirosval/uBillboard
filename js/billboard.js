(function($){
	function d(variable) {
		try {
			console.log(variable);
		} catch(e) {
			//
		}
	}
	
	/**
	 *
	 */
	var $bb;
	/**
	 *
	 */
	var $stage;
	/**
	 *
	 */
	var $next;
	/**
	 *
	 */
	var $nextInsides;
	/**
	 *
	 */
	var slides;
	/**
	 *
	 */
	var options;
	/**
	 *
	 */
	var timers;
	/**
	 *
	 */
	var currentSlideId;
	
	var _public = {
		/**
		 *
		 */
		'init': function(defaults, passedOptions){
			d('Init');
			$bb = $(this);
			
			// Bind All events defined below
			bindEvents();
			
			// Fix options
			options = $.extend(defaults, passedOptions);
			
			$bb.css({
				width: options.width,
				height: options.height
			})
			
			_private.initSlides();
			_private.initAnimationMarkup();
			// Runs preloader, and when it finishes, it triggers the udsBillboardLoadingComplete Event
			// to continue normal code flow
			_private.preloadImages();
			
			// load first slide
			var currentSlide = slides[0];
			$stage.css({
				backgroundImage: 'url('+currentSlide.bg+')'
			}).html(currentSlide.html);
			
			_public.start();
		},
		
		/**
		 *
		 */
		'next': function() {
			var nextSlideId = _private.getNextSlideId();
			_private.prepareForAnimation(nextSlideId);
			
			animations.fade.setup();
			
			animations.fade.perform();
			
			setTimeout(function(){
				animations.fade.cleanup();
				currentSlideId = nextSlideId;
			}, animations.fade.duration);
		},
		
		/**
		 *
		 */
		'prev': function() {

		},
		
		/**
		 *
		 */
		'start': function() {
			if(typeof currentSlideId !== 'number' || currentSlideId === null) {
				currentSlideId = 0;
			}
			
			timers = $.extend(timers, {
				nextSlideAnimation: setInterval(function(){
					_public.next();
				}, slides[currentSlideId].delay)
			});
		},
		
		/**
		 *
		 */
		'stop': function() {
			if(timers.nextSlideAnimation !== null) {
				clearInterval(timers.nextSlideAnimation);
			}
		}
	};
	
	var _private = {
		/**
		 *
		 */
		initSlides: function() {
			slides = [];
			$('.uds-bb-slide', $bb).each(function(i, el){
				var slide = {
					delay: 3000,
					bg: $('.uds-bb-bg-image', el).remove().attr('src'),
					html: $(el).remove().html()
				};
				slides.push(slide);
			});
		},
		
		/**
		 *
		 */
		preloadImages: function() {
			var progress = 0;
			var totalImages = slides.length;
			
			for(var i = 0; i < totalImages; i++) {
				// only preload slides that actually have images
				if(slides[i].bg === '') {
					++progress;
					continue;
				}
				
				$('<img>').load(function(){
					++progress;
					
					if(progress == totalImages) {
						$bb.trigger('udsBillboardLoadingComplete');
					} else {
						d('Progress: '+(progress/totalImages));
					}
					
				}).attr('src', slides[i].bg);
			}
		},
		
		/**
		 *
		 */
		initAnimationMarkup: function() {
			$('.uds-bb-slides', $bb)
				.append("<div class='uds-stage'>")
				.append("<div class='uds-next'>");
			$stage = $('.uds-stage', $bb);
			$next = $('.uds-next', $bb);
			
			$($stage).add($next).css({
				width: options.width,
				height: options.height
			});
			
			var width = parseInt(options.width, 10);
			var height = parseInt(options.height, 10);
			var squareSize = parseInt(options.squareSize, 10);
			
			var cols = Math.ceil(width/squareSize);
			var rows = Math.ceil(height/squareSize);
			
			for(var y = 0; y < rows; y++) {
				for(var x = 0; x < cols; x++) {
					$('<div>', {
						class: 'uds-square uds-column-'+x+' uds-row-'+y,
						id: 'uds-square-'+(x+(cols*y))
					}).append($('<div>',{
						class: 'uds-square-inside'
					})).appendTo($next);
				}
			}
			
			_private.resetAnimation();
			
			$nextInsides = $('.uds-square-inside');
		},
		
		/**
		 *
		 */
		resetAnimation: function() {
			var width = parseInt(options.width, 10);
			var height = parseInt(options.height, 10);
			var squareSize = parseInt(options.squareSize, 10);
			
			var cols = Math.ceil(width/squareSize);
			var rows = Math.ceil(height/squareSize);
			
			for(var y = 0; y < rows; y++) {
				for(var x = 0; x < cols; x++) {
					$('#uds-square-'+(x+(cols*y))).css({
						width: squareSize,
						height: squareSize,
						top: y*squareSize,
						left: x*squareSize
					}).find('.uds-square-inside').css({
						width: width,
						height: height,
						top: - (y*squareSize),
						left: - (x*squareSize)
					});
				}
			}
		},
		
		/**
		 *
		 */
		prepareForAnimation: function(slideId) {
			var currentSlide = slides[currentSlideId];
			var nextSlide = slides[slideId];
			
			// Sanity Checks
			if(typeof currentSlide === 'undefined' || currentSlide === null) {
				d('Slide ' + currentSlideId + ' does not exist! (Current Slide)');
				return;
			}
			
			if(typeof nextSlide === 'undefined' || nextSlide === null) {
				d('Slide ' + slideId + ' does not exist! (Next Slide)');
				return;
			}
			
			$stage.css({
				backgroundImage: 'url('+currentSlide.bg+')'
			}).html(currentSlide.html);
			
			_private.resetAnimation();
			
			$next.hide();
			
			$nextInsides.css({
				backgroundImage: 'url('+nextSlide.bg+')'
			}).html(nextSlide.html);
		},
		
		/**
		 *
		 */
		getNextSlideId: function() {
			var nextSlideCandidateId = currentSlideId + 1;
			if(typeof slides[nextSlideCandidateId] === 'undefined') {
				return 0;
			}
			return nextSlideCandidateId;
		},
		
		/**
		 *
		 */
		getPrevSlideId: function() {
			var prevSlideCandidateId = currentSlideId - 1;
			if(typeof slides[prevSlideCandidateId] === 'undefined') {
				return slides.length;
			}
			return prevSlideCandidateId;
		}
	};
	
	// events
	function bindEvents() {
		/**
		 *
		 */
		$bb.bind('udsBillboardLoadingComplete', function(){
			d('Loadin Complete!');
		});
	}
	
	var animations = {
		/**
		 *
		 */
		fade: {
			duration: 500,
			setup: function() {
				d('Setup');
				$next.show().css({
					opacity: 0
				});
			},
			perform: function() {
				d('Perform');
				$next.animate({
					opacity: 1
				}, {
					duration: 500
				});
			},
			cleanup: function() {
				d('Cleanup');
			}
		}
	};
	
	$.fn.uBillboard = function(options){
		
		var defaults = {
			width:		'960px',
			height:		'400px',
			squareSize:	'80px',
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