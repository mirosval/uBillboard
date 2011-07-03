(function($){
	function d(variable) {
		try {
			console.log(variable);
		} catch(e) {
			//
		}
	}
	
	/**
	 *	$bb holds the jQuery object for this uBillboard
	 */
	var $bb;
	
	/**
	 *	$stage is a jQuery object that represents the current slide as it is being displayed
	 */
	var $stage;
	
	/**
	 *	$next is holder div for all the squares that animate to display the next slide
	 */
	var $next;
	
	/**
	 *	$nextInsides is jQuery object that references the animatable squares
	 */
	var $nextInsides;
	
	/**
	 *	Array of slides
	 */
	var slides;
	
	/**
	 *	Object, options for the current uBillboard
	 */
	var options;
	
	/**
	 *	Array of timers needed for animation
	 */
	var timers;
	
	/**
	 *	Int ID of the current slide in the slides array
	 */
	var currentSlideId;
	
	/**
	 *	Public methods callable from the outside. Call like this:
	 *	$('bb-id').uBillboard('next')
	 */
	var _public = {
		/**
		 *	Initializes all necessary data structures
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
			
			// Setup Click Event handling
			$('.uds-bb-slides').live('click', function(){
				var slide = slides[currentSlideId];
				if(typeof slide.link === 'string' && slide.link !== '' && slide.link !== '#') {
					window.location = slide.link;
				}
			});
			
			if(options.autoplay === true) {
				d('Autoplay Initiated');
				_public.play();
			}
		},
		
		/**
		 *	Main backbone animation function. Animates slideId according to its definition
		 */
		'animateSlide': function(slideId) {
			//d('Will Animate Slide: '+slideId);
			
			if(slides[slideId] === null) {
				$.error('Slide ID ' + slideId + ' does not exist');
				return;
			}
			
			var slide = slides[slideId];
			
			_private.prepareForAnimation(slideId);
			
			// Decide on transition
			var transition = 'fade';
			if(slide.transition !== null && typeof slide.transition === 'string') {
				transition = slide.transition;
			}

			if(animations[transition] === null || typeof animations[transition] !== 'object'){
				$.error('Transition "' + transition + '" is not defined');
				return;
			}
			
			// Run Transition Setup function
			if(animations[transition].setup !== null && typeof animations[transition].setup === 'function') {
				animations[transition].setup();
			}
			
			// Run Transition Perform function
			if(animations[transition].perform !== null && typeof animations[transition].perform === 'function') {
				animations[transition].perform();
			}
			
			// Decide on transition duration
			var duration = 1000;
			if(animations[transition].duration !== null && typeof animations[transition].duration === 'number') {
				duration = animations[transition].duration;
			}
			
			// update current slide ID
			currentSlideId = slideId;
			
			// Run Transition cleanup
			setTimeout(function(){
				if(animations[transition].cleanup !== null && typeof animations[transition].cleanup === 'function') {
					animations[transition].cleanup();
				}
			}, duration);
		},
		
		/**
		 *	Animates the next slide in
		 */
		'next': function() {
			var nextSlideId = _private.getNextSlideId();
			_public.animateSlide(nextSlideId);
		},
		
		/**
		 *	Animates the previous slide in
		 */
		'prev': function() {
			var prevSlideId = _private.getPrevSlideId();
			_public.animateSlide(prevSlideId);
		},
		
		/**
		 *	Animates a random slide
		 */
		'random': function() {
			var slideId = Math.floor(Math.random() * slides.length + 1);
			_public.animateSlide(slideId);
		},
		
		/**
		 *	Starts Playback
		 */
		'play': function() {
			if(typeof currentSlideId !== 'number' || currentSlideId === null) {
				currentSlideId = 0;
			}
			
			timers = $.extend(timers, {
				nextSlideAnimation: setTimeout(function(){
					_public.next();
					_public.play();
				}, slides[currentSlideId].delay)
			});
		},
		
		/**
		 *	Stops Playback
		 */
		'stop': function() {
			if(timers.nextSlideAnimation !== null) {
				clearTimeout(timers.nextSlideAnimation);
			}
		}
	};
	
	/**
	 *	Private Method to be called only from within uBillboard methods
	 */
	var _private = {
		/**
		 *	Initializes the slides array by parsing the HTML markup
		 *	Also removes the markup
		 */
		initSlides: function() {
			slides = [];
			$('.uds-bb-slide', $bb).each(function(i, el){
				var slide = {
					delay: parseInt($('.uds-delay', el).remove().text(), 10),
					transition: $('.uds-transition', el).remove().text(),
					bg: $('.uds-bb-bg-image', el).remove().attr('src'),
					link: $('.uds-bb-link', el).remove().attr('href'),
					html: $(el).remove().html()
				};
				slides.push(slide);
			});
		},
		
		/**
		 *	Runs the image preloader
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
		 *	Creates square markup, should be run only once, at init time
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
		 *	Resets all animation squares and divs to the original position and size
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
		 *	Prepares slide slideId for animation
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
		 *	Figures out the next slide ID
		 */
		getNextSlideId: function() {
			var nextSlideCandidateId = currentSlideId + 1;
			if(typeof slides[nextSlideCandidateId] === 'undefined') {
				return 0;
			}
			return nextSlideCandidateId;
		},
		
		/**
		 *	Figures out the previous slide ID
		 */
		getPrevSlideId: function() {
			var prevSlideCandidateId = currentSlideId - 1;
			if(typeof slides[prevSlideCandidateId] === 'undefined') {
				return slides.length;
			}
			return prevSlideCandidateId;
		}
	};
	
	/**
	 *	Binds Event handlers
	 */
	function bindEvents() {
		/**
		 *	udsBillboardLoadingComplete, run when the loading completes
		 */
		$bb.bind('udsBillboardLoadingComplete', function(){
			d('Loadin Complete!');
		});
	}
	
	/**
	 *	
	 */
	var animations = {
		/**
		 *
		 */
		'fade': {
			duration: 500,
			setup: function() {
				$next.show().css({
					opacity: 0
				});
			},
			perform: function() {
				$next.animate({
					opacity: 1
				}, {
					duration: 500
				});
			},
			cleanup: function() {
				$next.css('opacity', 1);
			}
		},
		
		/**
		 *
		 */
		'fadeSquaresRandom': {
			duration: 1100,
			setup: function() {
				$next.show();
				$nextInsides.css('opacity', 0);
			},
			perform: function() {
				$nextInsides.each(function(el, i){
					$(this).delay(Math.random() * 700).animate({
						opacity: 1
					}, {
						duration: 400,
						easing: 'easeInOutQuad'
					});
				});
			},
			cleanup: function() {
				$nextInsides.css('opacity', 1);
			}
		}
	};
	
	/**
	 *	Main jQuery plugin definition
	 */
	$.fn.uBillboard = function(options){
		
		var defaults = {
			width:		'960px',
			height:		'400px',
			squareSize:	'80px',
			autoplay:	true
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