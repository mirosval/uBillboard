/**
 *	@license
 *	uBillboard - Premium Slide for WordPress
 *
 *	Version: 3.5.0
 *
 *	Copyright: uDesignStudios (Miroslav Zoricak, Jan Keselak) 2011
 *	
 */
;(function($) {
	"use strict";
	
	function d(variable) {
		try {
			console.log(variable);
		} catch(e) {
			//
		}
	}
	
	$.extend(jQuery.easing, {
		easeInOutQuad: function (x, t, b, c, d) {
			if ((t/=d/2) < 1){ return c/2*t*t + b; }
			return -c/2 * ((--t)*(t-2) - 1) + b;
		},
		easeInOutCirc: function (x, t, b, c, d) {
			if ((t/=d/2) < 1) return -c/2 * (Math.sqrt(1 - t*t) - 1) + b;
			return c/2 * (Math.sqrt(1 - (t-=2)*t) + 1) + b;
		}
	});
	
	jQuery.fn.fastShow = function() {
		this.each(function(){
			this.style.display = "block";
		});
		return this;
	};
	
	jQuery.fn.fastHide = function() {
		this.each(function(){
			this.style.display = "none";
		});
		return this;
	};
	
	/**
	 *	Main jQuery plugin definition
	 */
	$.fn.uBillboard = function(options){
	
		/**
		 *	$bb holds the jQuery object for this uBillboard
		 */
		var $bb,
		
		/**
		 *	$slides is a jQuery object that holds $stage and $next
		 */
		$slides,
		
		/**
		 *	$stage is a jQuery object that represents the current slide as it is being displayed
		 */
		$stage,
		
		/**
		 *	$next is holder div for all the squares that animate to display the next slide
		 */
		$next,
		
		/**
		 *	$squares is jQuery object that references the animatable squares
		 */
		$squares,
		
		/**
		 *	$nextInsides is jQuery object that references the animatable square inside
		 */
		$nextInsides,
		
		/**
		 *	$controls is jQuery object that references all playback controls/pagination/slide countdown/etc
		 */
		$controls,
		
		/**
		 *	$countdown is jQuery referecne to the countdown canvas holder
		 */
		$countdown,
		
		/**
		 *	$preloader is jQuery referecne to the preloader
		 */
		$preloader,
		
		/**
		 *	Array of slides
		 */
		slides,
		
		/**
		 *	Array of timers needed for animation
		 */
		timers,
		
		/**
		 *	Int ID of the current slide in the slides array
		 */
		currentSlideId,
		
		/**
		 *	Bool, true if playing false if not
		 */
		playing,
		
		/**
		 *	Bool, true if a transition is in progress
		 */
		transitionInProgress,
		
		computedWidth,
		computedHeight,
		touches,
		
		/**
		 *	Public methods callable from the outside. Call like this:
		 *	$('bb-id').uBillboard('next')
		 */
		_public = {
			/**
			 *	Initializes all necessary data structures
			 */
			'init': function(defaults, passedOptions){
				//d('Init');
				$bb = $(this);
				
				$slides = $('.uds-bb-slides', $bb);
								
				// Fix options
				options = $.extend(defaults, passedOptions);
				
				$bb.css('overflow', 'visible');
				
				$bb.add($slides).css({
					maxWidth: options.width,
					maxHeight: options.height
				});
				
				// initialize timers
				timers = {};
				
				// initialize playing var
				playing = false;
				
				// initialize transitioning var
				transitionInProgress = false;
				
				// check if we don't have the images already in the cache
				var preloadRequired = _private.preloadRequired();
				
				_private.initSlides();
				_private.initAnimationMarkup();
				
				var willPreloadImages = preloadRequired;
				if(preloadRequired) {
					_private.initPreloader();
					// Runs preloader, and when it finishes, it triggers the udsBillboardLoadingDidComplete Event
					// to continue normal code flow
					willPreloadImages = _private.preloadImages();
				}
				
				// Init pagination and playback controls
				_private.initControls();
				
				// Setup Click Event handling
				$('.uds-bb-slides', $bb).live('click', function(){
					var slide = slides[currentSlideId];
					if(typeof slide.link === 'string' && slide.link !== '' && slide.link !== '#') {
						if(slide.linkTarget === '_blank') {
							window.open(slide.link, '_blank');
						} else {
							window.location = slide.link;
						}
					}
				});
				
				// Init touch support
				_private.initTouchSupport();
				
				// this call from the preloadImages() function would be too soon
				if(willPreloadImages === false) {
					$bb.trigger('udsBillboardLoadingDidComplete');
					_private.loadingCompleted();
				}
				
				$bb.bind('udsBillboardTransitionDidComplete', function(){
					transitionInProgress = false;
				});
				
				var ratio = (parseInt(options.height, 10) / parseInt(options.width, 10));
				$(window).resize(function(){
					$bb.add($nextInsides).add($stage).add($next).css({
						width: '100%',
						height: '100%'
					});
					
					computedWidth = $bb.width();
					computedHeight = computedWidth * ratio;
					$bb.add($nextInsides).add($stage).add($next).css({
						width: computedWidth + 'px',
						height: computedHeight + 'px'
					});
					
					// Center controls
					$('.uds-center', $bb).each(function() {
						var widthAdjustment = $(this).outerWidth() / 2;
						var heightAdjustment = $(this).outerHeight() / 2;
						
						$(this).css({
							top: computedHeight / 2 - heightAdjustment,
							left: computedWidth / 2 - widthAdjustment
						});
					});
					
					$('.uds-center-vertical', $bb).each(function() {
						var heightAdjustment = $(this).outerHeight() / 2;
						
						$(this).css({
							top: computedHeight / 2 - heightAdjustment
						});
					});
					
					$('.uds-center-horizontal', $bb).each(function() {
						var widthAdjustment = $(this).outerWidth() / 2;
						
						$(this).css({
							left: computedWidth / 2 - widthAdjustment
						});
					});
					
					// Center video content
					for(var i = 0; i < slides.length; i++) {
						if(!slides[i].hasVideo) {
							continue;
						}
						
						var $elements = $('.uds-bb-slide-' + i + '>*', $bb);
						
						for(var j = 0; j < $elements.length; j++) {							
							var el = $elements.eq(j);
							var videoRatio = $(el).attr('width') / $(el).attr('height');
							
							var width = $(el).attr('width'), 
								height = $(el).attr('height');
								
							if($(el).attr('height') > computedHeight) {
								height = computedHeight;
								width = height * videoRatio;
							} else if($(el).attr('width') > computedWidth) {
								width = computedWidth;
								height = width / videoRatio;
							}
							
							el.css({
								position: 'absolute',
								top: computedHeight / 2 - height / 2,
								left: computedWidth / 2 - width / 2,
								width: width,
								height: height,
								margin: 'auto'
							});
						}
					}
					
					// Resize Ken Burns Canvas
					$('.uds-bb-slide>canvas').each(function(){
						var $this = $(this);
						$this.css({
							width: computedWidth,
							height: computedHeight
						}).attr({
							width: computedWidth,
							height: computedHeight
						});
					});
					
					// Resize Description Boxes
					$('.uds-bb-slide>.uds-bb-description').each(function(){
						var $this = $(this),
							posList = ['top','bottom','height','right','left','width'],
							elList = 'h1,h2,h3,h4,h5,h6,p,div,span,ul,ol,li,a',
							attList = ['font-size', 'margin-left', 'margin-top', 'margin-right', 'margin-bottom'],
							css = {},
							heightRatio = computedHeight / parseInt(options.height, 10),
							widthRatio = computedWidth / parseInt(options.width, 10);
							
						if(typeof $this.data('original-position') === 'undefined') {
							$this.data('original-position', true);
							$.each(posList, function(i, el){
								$this.data(el, parseInt($this.css(el), 10));
							});
							
							// Font Size
							$(elList, $this).each(function(){
								var $t = $(this);
								$.each(attList, function(i,el){
									if(i == 0) { d($t.css(el)); }
									$t.data(el, parseInt($t.css(el), 10));
								});
							});
						}
						
						$.each(posList, function(i, el){
							var ratio = i < 3 ? heightRatio : widthRatio,
								value = $this.data(el);
							css[el] = isNaN(value) ? 'auto' : value * ratio;
						});
						
						$(elList, $this).each(function(){
							var $t = $(this);
							
							$.each(attList, function(i,el){
								$t.css(el, $t.data(el) * widthRatio);
							});
						});
						
						$this.css(css);
					});
					
				}).resize();
			},
			
			/**
			 *	Main backbone animation function. Animates slideId according to its definition
			 */
			'animateSlide': function(slideId, animate) {
				// No need to animate
				if(slideId === currentSlideId) {
					return;
				}
				
				if(typeof animate !== "boolean") {
					animate = true;
				}
				
				if(slides[slideId] === null) {
					$.error('Slide ID ' + slideId + ' does not exist');
					return;
				}
				
				$bb.trigger('udsBillboardSlideWillChange', currentSlideId);
				
				var slide = slides[slideId];
				
				// Pause video if this is a video slide
				if(slides[currentSlideId].hasVideo) {
					_private.pauseVideo(slides[currentSlideId]);
				}
				
				_private.prepareForAnimation(slideId);
				
				// Handle Embedded content
				_private.handleEmbeddedContent(slide);
				
				var duration = 0;
				
				if(animate) {
					// Decide on transition
					var transition = 'fade';
					if(slide.transition !== null && typeof slide.transition === 'string') {
						transition = slide.transition;
					}
		
					if(animations[transition] === null || typeof animations[transition] !== 'object'){
						d('Transition "' + transition + '" is not defined');
						transition = 'fade';
					}
					
					// Assign Direction
					var defaultDirection = animations[transition].direction;
					if(directions[slide.direction] === null || typeof directions[slide.direction] !== 'object') {
						if(directions[defaultDirection] === null || typeof directions[defaultDirection] !== 'object') {
							animations[transition].direction = 'right';
						} else {
							animations[transition].direction = defaultDirection;
						}
					} else {
						animations[transition].direction = slide.direction;
					}
					
					$next.fastShow().css('opacity', 1);
					
					transitionInProgress = true;
					
					// Run Transition Setup function
					if(animations[transition].setup !== null && typeof animations[transition].setup === 'function') {
						animations[transition].setup(currentSlideId, slideId);
					}
					
					// Run Transition Perform function
					if(animations[transition].perform !== null && typeof animations[transition].perform === 'function') {
						animations[transition].perform(currentSlideId, slideId);
					}
					
					// Decide on transition duration
					duration = 1000;
					if(animations[transition].duration !== null && typeof animations[transition].duration === 'number') {
						duration = animations[transition].duration;
					}
				}

				clearTimeout(timers.transitionComplete);
				timers.transitionComplete = setTimeout(function(){
					// Change cursor to pointer if there is a link present
					var cursor = 'default';
					if(typeof slide.link === 'string' && slide.link !== '' && slide.link !== '#') {
						cursor = 'pointer';
					}
					
					$stage
						.stop()
						.css({
							"-webkit-transition": "-webkit-transform 0s",
							"-webkit-transform": "translate3d(0px,0px,0px)",
							top: '0px',
							left: '0px',
							opacity: 1,
							cursor: cursor
						});
					$next.css({
						"-webkit-transition": "all 0s",
						"-webkit-transform": "translate3d(0px,0px,0px)"
					});
					$('.uds-bb-slide', $stage).fastHide();
					$('.uds-bb-slide-'+slide.id, $stage).fastShow();
					$('.uds-bb-description', $stage).fastShow();
					$next.stop().fastHide();
					
					if(slide.autoplayVideo && options.pauseOnVideo) {
						_private.playVideo(slide);
					}
					
					// Do Ken Burns
					if(slide.kenBurns) {
						_private.kenBurns(slide);
					}
					
					$bb.trigger('udsBillboardTransitionDidComplete', slideId);
				}, duration);
				
				// update current slide ID
				currentSlideId = slideId;
				
				$bb.trigger('udsBillboardSlideDidChange', currentSlideId);
				
				if(options.pauseOnVideo && slides[currentSlideId].hasVideo) {
					if($countdown !== null && typeof $countdown !== "undefined") {
						$countdown.fastHide();
					}
					clearTimeout(timers.nextSlideAnimation);
					return;
				}
				
				if(slide.stop) {
					playing = false;
				}
				
				// Run Countdown Animation
				if(playing) {
					_private.animateCountdown(slides[currentSlideId].delay);
				} else {
					if($countdown !== null && typeof $countdown !== "undefined") {
						$countdown.fastHide();
					}
				}
				
				// continue playing
				if(options.autoplay && playing && !slide.stop) {
					//clearTimeout(timers.nextSlideAnimation);
					_public.play();
				}
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
				
				if(timers !== null && timers.nextSlideAnimation !== null) {
					clearTimeout(timers.nextSlideAnimation);
				}
				
				if(typeof $countdown !== 'undefined' && $countdown !== null) {
					$countdown.fastShow();
				}
				
				if(!playing) {
					// Run Countdown Animation
					_private.animateCountdown(slides[currentSlideId].delay);
				}
				
				clearTimeout(timers.nextSlideAnimation);
				timers.nextSlideAnimation = setTimeout(function(){
					_public.next();
				}, slides[currentSlideId].delay);
				
				playing = true;
				
				$bb.trigger('didChangePlayingState', {playing: playing});
			},
			
			/**
			 *	Pauses Playback
			 */
			'pause': function() {
				playing = false;
				$bb.trigger('didChangePlayingState', {playing: playing});
				
				// clear timeouts
				if(timers.nextSlideAnimation !== null) {
					clearTimeout(timers.nextSlideAnimation);
				}
				
				if(typeof $countdown !== 'undefined' && $countdown !== null) {
					$countdown.fastHide();
				}
			},
			
			'playpause': function() {
				if(playing) {
					_public.pause();
				} else {
					_public.play();
				}
			}
		},
		
		/**
		 *	Private Method to be called only from within uBillboard methods
		 */
		_private = {
			/**
			 *	Initializes the slides array by parsing the HTML markup
			 *	Also removes the markup
			 */
			initSlides: function() {
				var defaultSlideOptions = {
					delay: 5000,
					hasVideo: false,
					linkTarget: '',
					transition: 'fade',
					direction: '',
					bg: '',
					bgColor: 'transparent',
					repeat: 'no-repeat',
					link: '',
					stop: false,
					autoplayVideo: true,
					kenBurns: false,
					kenBurnsSpeed: 4000,
					html: ''
				};
				
				slides = [];
				$('.uds-bb-slide', $bb).each(function(i, el){
					//d(options.slides[i]);
					var img_src = _private.maybeUseRetina($('.uds-bb-bg-image', el).remove().attr('src'), options.slides[i]);
					
					var slide = $.extend(
						{},
						defaultSlideOptions, // Default slide options
						options.slides[i], // Options passed in via the JS call
						{ // Options parsed in from the markup
							id: i,
							bg: img_src,
							link: $('.uds-bb-link', el).remove().attr('href'),
							html: $(el).remove().html()
						}
					);
					
					var $slide = $("<div class='uds-bb-slide-" + i + " uds-bb-slide'>");
					$slide
						.css({
							display: 'none',
							maxWidth: options.width,
							maxHeight: options.height,
							width: '100%',
							height: '100%'
						})
						.css(_private.getSlideBackgroundCSS(slide))
						.html(slide.html);
					
					slide.cache = $slide;
					
					slides.push(slide);
				});
			},
			
			preloadRequired: function() {
				var complete = true;
				$('.uds-bb-bg-image', $bb).each(function(){
					var el = $(this).get(0);
					complete = complete && el.complete;
				});
				
				return !complete;
			},
			
			initPreloader: function() {
				$bb.append('<div class="uds-bb-preloader-wrapper"><div class="uds-bb-preloader"><div class="uds-bb-preloader-indicator"></div></div></div>');
				$preloader = $('.uds-bb-preloader-wrapper', $bb);
				
				var $indicator = $('.uds-bb-preloader-indicator', $preloader);
				
				$('.uds-bb-preloader', $bb).css({
					top: parseInt(options.height, 10) / 2 - $indicator.height() / 2 + 'px',
					left: parseInt(options.width, 10) / 2 - $indicator.width() / 2 + 'px'
				});
			},
			
			updatePreloader: function(progress) {
				var $indicator = $('.uds-bb-preloader-indicator', $preloader), css;

				$indicator.stop().animate({
					left: '-' + Math.round((1 - progress) * $indicator.width()) + 'px'
				}, 200);
				
				if(progress >= 1) {
					$indicator.fadeOut('200', function(){
						$preloader.remove();
					});
				}
			},
			
			/**
			 *	Runs the image preloader
			 */
			preloadImages: function() {
				var progress = 0;
				var totalImages = slides.length;
				
				// handle sliders with no images whatsoever
				var hasAnyImages = false;
				for(var i = 0; i < totalImages; i++) {
					if(slides[i].bg !== '') {
						hasAnyImages = true;
					}
				}
				
				if(!hasAnyImages) {
					_private.updatePreloader(1);
					$bb.trigger('udsBillboardLoadingDidComplete');
					_private.loadingCompleted();
					return false;
				}
				
				// handle image preload
				for(i = 0; i < totalImages; i++) {
					// only preload slides that actually have images
					if(slides[i].bg === '') {
						++progress;
						continue;
					}
					
					$('<img>')
					.data('slideID', i)
					.load(function(){
						++progress;
						
						_private.updatePreloader(progress/totalImages);
						
						if(progress === totalImages) {
							$bb.trigger('udsBillboardLoadingDidComplete');
							_private.loadingCompleted();
						}
						
					}).error(function() {
						var slideID = $(this).data('slideID');
						d('Failed to load image: ' + slides[slideID].bg);
						
						++progress;
						
						_private.updatePreloader(progress/totalImages);
						
						if(progress === totalImages) {
							$bb.trigger('udsBillboardLoadingDidComplete');
							_private.loadingCompleted();
						}
						
						if(options.removeSlidesWithBrokenImages === true) {
							// remove slide
							slides.splice(slideID, 1);
						}
					}).attr('src', slides[i].bg);
				}
				
				return true;
			},
			
			/**
			 *	Run after all images have been safely loaded and playback should start
			 */
			loadingCompleted: function() {
				// load first slide
				currentSlideId = 0;
				var currentSlide = slides[currentSlideId],
					css = _private.getSlideBackgroundCSS(currentSlide);

				//$stage.css(css).html(currentSlide.html).fadeTo(300, 1);
				$('.uds-bb-slide-0', $stage).fastShow().fadeTo(300, 1);
				$('.uds-bb-description', $stage).fastShow();
				
				_private.handleEmbeddedContent(currentSlide);
				
				$controls.delay(300).fadeTo(300, 1);
				
				if(currentSlide.kenBurns) {
					_private.prepareKenBurns(currentSlide);
					_private.kenBurns(currentSlide);
				}
				
				// should we pause on this slide
				var pauseForVideo = options.pauseOnVideo && currentSlide.hasVideo;

				if(options.autoplay === true && !pauseForVideo) {
					//d('Autoplay Initiated');
					
					// Run Countdown Animation
					_private.animateCountdown(slides[currentSlideId].delay);
					
					_public.play();
				} else {
					if(typeof $countdown !== 'undefined' && $countdown !== null) {
						$countdown.fastHide();
					}
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
	
				$next.fastHide();
				
				$($stage).add($next).css({
					maxWidth: options.width,
					maxHeight: options.height,
					width: '100%',
					height: '100%',
					opacity: 0
				});
				
				var width = parseInt(options.width, 10);
				var height = parseInt(options.height, 10);
				var squareSize = parseInt(options.squareSize, 10);
				
				var cols = Math.ceil(width/squareSize);
				var rows = Math.ceil(height/squareSize);
				
				for(var y = 0; y < rows; y++) {
					for(var x = 0; x < cols; x++) {
						$('<div>', {
							'class': 'uds-square uds-column-'+x+' uds-row-'+y+' uds-square-'+(x+(cols*y))
						}).data('position', {x:x,y:y}).append($('<div>',{
							'class': 'uds-square-inside'
						})).appendTo($next);
					}
				}
				
				_private.resetAnimation();
				
				$squares = $('.uds-square', $bb);
				$nextInsides = $('.uds-square-inside', $bb);
				$controls = $('.uds-bb-controls', $bb);
				
				for(var i = 0; i < slides.length; i++) {
					var slide = slides[i];
					$stage.append(slide.cache.clone());
					
					if(!slide.hasVideo) {
						$nextInsides.append(slide.cache.clone());
					}
				}
				
				$('>div', $stage).add('>div', $nextInsides).fastHide();
				
				// initialize countdown
				_private.createCountdown();
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
						$('.uds-square-'+(x+(cols*y)), $bb).css({
							width: squareSize,
							height: squareSize,
							top: y*squareSize,
							left: x*squareSize,
							opacity: 1
						}).stop(true, true).find('.uds-square-inside').css({
							width: width,
							height: height,
							top: - (y*squareSize),
							left: - (x*squareSize),
							opacity: 1
						}).stop(true, true);
					}
				}
				
				$stage.css({
					opacity: 1
				}).add($next).css({
					//"-webkit-transform": "translate(0px, 0px)",
					top: '0px',
					left: '0px'
				});
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
				
				$('.uds-bb-slide', $stage).fastHide();
				$('.uds-bb-slide-'+currentSlide.id, $stage).fastShow();
				$('.uds-bb-description', $stage).fastShow();
				
				_private.resetAnimation();
				
				// Prepare for Ken Burns
				clearInterval(timers.kenBurnsCanvasTimer);
				if(nextSlide.kenBurns) {
					_private.prepareKenBurns(nextSlide);
				}
				
				if(nextSlide.hasVideo || _private.isSlowBrowser()) {
					if($('.uds-bb-square', $next).length > 0) {
						$squares.detach();
					}
					$(">.uds-bb-slide", $next).remove();
					$next.append($('>.uds-bb-slide-' + nextSlide.id, $stage).clone().css('display', 'block'));
				} else { // Do not create a million copies of embedded content ;)
					if($('.uds-bb-square', $next).length === 0) {
						$(">.uds-bb-slide", $next).remove();
						$next.append($squares);
					}
					$('.uds-bb-slide', $nextInsides).fastHide();
					$('.uds-bb-slide-'+nextSlide.id, $nextInsides).fastShow();
					$('.uds-bb-description', $nextInsides).fastShow();
					
					// Remember responsive!
					$('.uds-bb-slide-'+nextSlide.id, $nextInsides).css({
						width: computedWidth + 'px',
						height: computedHeight + 'px'
					});
				}
			},
			
			initControls: function() {
				$controls = $('.uds-bb-controls', $bb);
	
				// Setup CSS
				$controls.fastShow().css({
					maxWidth: options.width,
					maxHeight: options.height,
					width: '100%',
					height: '100%',
					opacity: 0
				});
				
				// fix for IE7 controls not displaying
				if($.browser.msie && $.browser.version < 8) {
					$('.uds-bb-paginator').css('position', 'static');
				}
				
				// setup variables for shorter code
				var $playpause = $('.uds-bb-playpause', $bb),
					$buttonNext = $('.uds-bb-next', $bb),
					$buttonPrev = $('.uds-bb-prev', $bb),
					$paginator = $('.uds-bb-paginator', $bb),
					$bullets = $('.uds-bb-position-indicator-bullets', $bb),
					$thumbs = $('.uds-bb-thumbnails', $bb),
					$thumb = $('.uds-bb-thumb', $thumbs),
					$container = $('.uds-bb-thumbnail-container', $thumbs);
				
				// Bind next/prev/playpause handlers
				$playpause.click(_public.playpause);
				$buttonNext.click(_public.next);
				$buttonPrev.click(_public.prev);
				
				// Change playing button class based on the active playing state
				$playpause.addClass('play');
				$bb.bind('didChangePlayingState', function(event, data){
					$playpause.removeClass('play pause');
					
					if(data.playing) {
						$playpause.addClass('pause');
					} else {
						$playpause.addClass('play');
					}
				});
				
				// Position Indicator 1/6
				$('.uds-bb-position-indicator', $bb).text(1 + "/" + slides.length);
				
				// Bullets
				var bullets = "";
				for(var i = 0; i < slides.length; i++) {
					bullets += "<div class='uds-bb-bullet'></div>";
				}
				
				$bullets.append(bullets).find('div:first').addClass('active');
				
				$('div', $bullets).click(function(){
					_public.animateSlide($(this).index());
				});
				
				$bb.bind('udsBillboardSlideDidChange', function() {
					// Position indicator
					$('.uds-bb-position-indicator').text((currentSlideId + 1) + "/" + slides.length);
					
					// Bullets
					$('div', $bullets).removeClass('active').eq(currentSlideId).addClass('active');
					
					// Thumbs
					$thumb
						.css('background-color', '')
						.removeClass('active')
						.eq(currentSlideId)
						.addClass('active')
						.css('background-color', options.thumbnailHoverColor);
				});
				
				// Thumbnails
				
				// Active class assign
				$thumb.removeClass('active').eq(0).addClass('active');
				
				// Thumbnail Click Handler
				$thumb.click(function(){
					_public.animateSlide($(this).index());
				});
				
				if($thumbs.is('.top,.bottom')) {
					$container.css('width', '10000px');
				}
				
				
				// Precompute thumbnail dimensions
				$thumb.each(function(i){
					var $img = $('img', this);
					$img.css({
						width: $img.attr('width') + 'px',
						height: $img.attr('height') + 'px'
					});
					
					$(this).css({
						width: $img.attr('width') + 'px',
						height: $img.attr('height') + 'px'
					});
					
					// Thumbs Retina Support
					if(_private.shouldUseRetina()) {
						$img.attr('src', $img.attr('src').replace('-thumb.', '-thumb-retina.'));
					}
					
					// TODO: Transform this condition so it checks if the image is actually present
					if(slides[i].bg === '' && !slides[i].hasVideo) {
						$img.replaceWith('<div>');
						$('div', this).css({
							width: $img.attr('width') + 'px',
							height: $img.attr('height') + 'px',
							backgroundColor: slides[i].bgColor
						});
					}
					
					var originalBgColorString = $(this).css('background-color');
					var originalBgColor = "";
					
					if(originalBgColorString.indexOf('rgb(') > -1) {
						var matches = originalBgColorString.match(/rgb\(([0-9]{1,3}), ([0-9]{1,3}), ([0-9]{1,3})/);
						originalBgColor = {
							r: parseInt(matches[1], 10),
							g: parseInt(matches[2], 10),
							b: parseInt(matches[3], 10)
						};
					} else {
						originalBgColor = {
							r: parseInt(originalBgColorString.substr(1, 2), 16),
							g: parseInt(originalBgColorString.substr(3, 2), 16),
							b: parseInt(originalBgColorString.substr(5, 2), 16)
						};
					}
					
					if($(this).is('.active')) {
						$(this).css('background-color', options.thumbnailHoverColor);
					}
					
					var	hoverColor = {
							r: parseInt(options.thumbnailHoverColor.substr(1, 2), 16),
							g: parseInt(options.thumbnailHoverColor.substr(3, 2), 16),
							b: parseInt(options.thumbnailHoverColor.substr(5, 2), 16)
						};
					
					// thumbnail hovering
					$(this).hover(function(){
						if($(this).is('.active')) {
							$(this).css('background-color', options.thumbnailHoverColor);
							return;
						}
						
						$(this)
							.css('background-color', originalBgColorString)
							.stop()
							.animate({
								opacity: 1
							}, {
								duration: 200,
								step: function(now, fx) {
									var progress = (new Date().getTime() - fx.startTime) / fx.options.duration;
									
									var r = Math.round(originalBgColor.r * (1 - progress) + hoverColor.r * progress),
										g = Math.round(originalBgColor.g * (1 - progress) + hoverColor.g * progress),
										b = Math.round(originalBgColor.b * (1 - progress) + hoverColor.b * progress);
										
									fx.elem.style.backgroundColor = 'rgb('+r+','+g+','+b+')';
								}
							});
					}, function() {
						if($(this).is('.active')) {
							$(this).css('background-color', options.thumbnailHoverColor);
							return;
						}
						
						$(this)
							.css('background-color', options.thumbnailHoverColor)
							.stop()
							.animate({
								opacity: 1
							}, {
								duration: 200,
								step: function(now, fx) {
									var progress = (new Date().getTime() - fx.startTime) / fx.options.duration;
										
									var r = Math.round(originalBgColor.r * progress + hoverColor.r * (1 - progress)),
										g = Math.round(originalBgColor.g * progress + hoverColor.g * (1 - progress)),
										b = Math.round(originalBgColor.b * progress + hoverColor.b * (1 - progress));
									
									fx.elem.style.backgroundColor = 'rgb('+r+','+g+','+b+')';
								}
							});
					});
				});
				
				// uBillboard margin to accommodate thumbs
				$bb.has('.uds-bb-thumbnails.top:not(.inside)').css('margin-top', $thumbs.outerHeight());
				$bb.has('.uds-bb-thumbnails.bottom:not(.inside)').css('margin-bottom', $thumbs.outerHeight());
				$bb.has('.uds-bb-thumbnails.left:not(.inside)').css('margin-left', $thumbs.outerWidth());
				$bb.has('.uds-bb-thumbnails.right:not(.inside)').css('margin-right', $thumbs.outerWidth());
				
				// Thumbs Container Shadow
				$("<div class='uds-thumbnails-shadow-left'>").appendTo($thumbs);
				$("<div class='uds-thumbnails-shadow-right'>").appendTo($thumbs);
				
				// Thumbnails scrolling
				var windowDim,
					containerDim,
					scrollProperty,
					position = 0,
					orientation = $thumbs.is('.right,.left') ? 'vertical' : 'horizontal',
					margin;
				
				// Calculate variables
				if(orientation === 'vertical') {
					windowDim = $thumbs.height();
					margin = ($thumb.outerHeight(true) - $thumb.outerHeight()) / 2;
					containerDim = $thumb.length * $thumb.outerHeight(true) - ($thumb.length - 1) * margin;
					scrollProperty = 'top';
					$container.css('height', containerDim + 'px');
				} else {
					windowDim = $thumbs.width();
					var containerWidth = $thumb.outerWidth(true);
					// Fix FF image loading
					if(containerWidth === 0) {
						containerWidth = parseInt($('img', $thumb).attr('width'), 10) + 18;
					}
					containerDim = $thumb.length * containerWidth;
					scrollProperty = 'left';
					$container.css('width', containerDim + 'px');
				}
	
				if(windowDim > containerDim) {
					position = windowDim / 2 - containerDim / 2;
					$container.css(scrollProperty, position + 'px');
				}
				
				var recalculateContainerPosition = function(e){
					// Calculate variables
					if(orientation === 'vertical') {
						windowDim = $thumbs.height();
					} else {
						windowDim = $thumbs.width();
					}
					
					if(windowDim > containerDim) {
						position = windowDim / 2 - containerDim / 2;
					}
					
					// Normalize coordinates
					var offset = 0, speed = 0;
					if(e !== null && typeof e !== 'undefined') {
						if(orientation === 'vertical') {
							offset = e.pageY - $thumbs.offset().top;
						} else {
							offset = e.pageX - $thumbs.offset().left;
						}
					}
					
					// speed is the distance from the center
					speed = offset - windowDim / 2;
					
					// normalize it to 0..1
					speed = (speed / (windowDim / 2)) * 5;

					if((speed < 0 && position > 0) || (speed > 0 && position < (containerDim - windowDim))) {
						position += speed;
					}
					
					if((e === null || typeof e === 'undefined') && windowDim > containerDim) {
						position = - (windowDim / 2 - containerDim / 2);
					} else if (windowDim > containerDim) {
						return;
					}
					
					$container.css(scrollProperty, - position + 'px');
				};
				
				if(_private.isMobile()) {
					$thumbs.css('overflow', 'scroll');
				} else {
					$thumbs.bind({
						'mouseenter mousemove': function(e){
							clearInterval(timers.thumbMove);
	
							timers.thumbMove = setInterval(function() {
								recalculateContainerPosition(e);
							}, 10);
						}, 
						'mouseleave': function(){
							clearInterval(timers.thumbMove);
						}
					});
				}
				
				$(window).resize(function(e){
					if($thumbs.length === 0) {
						return;
					}
					
					clearInterval(timers.thumbMove);
					recalculateContainerPosition();
				});
				
				
				// Comply with options (hide/hover etc)
				var $controlsToHover = $('');
				if(options.showControls === 'hover') {
					$controlsToHover = $controlsToHover.add($buttonNext).add($buttonPrev);
				}
	
				if(options.showPause === 'hover') {
					$controlsToHover = $controlsToHover.add($playpause);
				}
				
				if(options.showPaginator === 'hover') {
					$controlsToHover = $controlsToHover.add($bullets).add($('.uds-bb-position-indicator', $bb));
				}
				
				if(options.showThumbnails === 'hover') {
					$controlsToHover = $controlsToHover.add($thumbs);
				}
				
				// handle paginator background hiding
				if(options.showPaginator !== true && options.showPause !== true && options.showControls !== true) {
					$controlsToHover = $controlsToHover.add($paginator);
				}
				
				// Handle bullets container for Silver Skin
				var $bulletsContainer = $('.uds-bb-position-indicator-bullets-container', $bb);
				if($bulletsContainer.length > 0 && (options.showPause === 'hover' || options.showPaginator === 'hover')) {
					$controlsToHover = $controlsToHover.add($bulletsContainer);
				} 
				
				$controlsToHover.fadeTo(0, 0);
				$bb.hover(function(){
					$controlsToHover.stop().fadeTo(300, 1);
				}, function(){
					$controlsToHover.stop().fadeTo(300, 0);
				});
				
				// Hide controls based on the options
				if(options.showControls === false) {
					$buttonNext.fastHide();
					$buttonPrev.fastHide();
				}
	
				if(options.showPause === false) {
					$playpause.fastHide();
				}
				
				if(options.showPaginator === false) {
					$bullets.fastHide();
					$('.uds-bb-position-indicator', $bb).fastHide();
				}
				
				if(options.showThumbnails === false) {
					$thumbs.fastHide();
				}
				
				// Bullets contianer for the Silver Skin
				if($('>*', $bulletsContainer).not(':hidden').length === 0) {
					$bulletsContainer.fastHide();
				}
			},
			
			/**
			 *	Hooks up touch events handling
			 */
			initTouchSupport: function() {
				$('.uds-bb-slides', $bb).on("touchstart touchmove touchend", function(e){
					var event = e.originalEvent;
					
					if(event.type === "touchstart") {
						$bb.css('-webkit-user-select', 'none');
						
						touches = {
							startX: event.touches[0].clientX,
							time: new Date().getTime(),
							absoluteStartTime: new Date().getTime()
						};
						
						$('.uds-bb-slides', $bb).css('overflow', 'hidden');
						clearTimeout(timers.nextSlideAnimation);
						
						if(typeof $countdown === "object") {
							$countdown.fastHide();
						}
					}
					
					if(event.type === "touchmove") {
						var offset = event.touches[0].clientX - touches.startX;
						var deltaOffset = event.touches[0].clientX - touches.currentX;
						touches.currentX = event.touches[0].clientX;
						var slideId;
						var now = new Date().getTime();
						var timeDelta = now - touches.time;
						touches.time = now;
						touches.speed = deltaOffset / (timeDelta / 1000);
						var clicked = (new Date().getTime() - touches.absoluteStartTime) < 150;
						
						if(!clicked) {
							e.preventDefault();
						}

						if(offset > 0) {
							touches.direction = -1;
							slideId = _private.getPrevSlideId();
						} else {
							touches.direction = 1;
							slideId = _private.getNextSlideId();
						}
						
						touches.slideId = slideId;
						
						// Pause video if this is a video slide
						if(slides[currentSlideId].hasVideo) {
							_private.pauseVideo(slides[currentSlideId]);
						}
						
						_private.prepareForAnimation(slideId);
						
						// Handle Embedded content
						_private.handleEmbeddedContent(slides[currentSlideId]);
						
						//$stage.css('left', offset + "px");
						$stage.css('-webkit-transform', 'translate3d(' + offset + "px,0px,0px)");
						if(offset > 0) {
							touches.left = - computedWidth + offset;
						} else {
							touches.left = computedWidth + offset;
						}
						
						$next.fastShow().css({
							//left: touches.left + "px",
							'-webkit-transform': 'translate3d(' + touches.left + 'px,0px,0px)',
							opacity: 1
						});
					}
					
					if(event.type === "touchend") {
						var draggedAfterHalfWidth = Math.abs(touches.left) < (computedWidth / 2),
							swiped = Math.abs(touches.speed) > 100,
							clicked = (new Date().getTime() - touches.absoluteStartTime) < 150;
						
						//$('.uds-bb-slides', $bb).css('overflow', 'visible');
						
						$stage.css({
							left: -touches.direction * computedWidth + touches.left,
							'-webkit-transform': 'translate3d(0px,0px,0px)'
						});
						
						$next.css({
							left: touches.left,
							'-webkit-transform': 'translate3d(0px,0px,0px)'
						})
						
						if((draggedAfterHalfWidth || swiped) && !clicked) {
							e.preventDefault();
							
							$stage.animate({
								left: - touches.direction * computedWidth
							}, {
								duration: 500
							});
							
							$next.animate({
								left: 0
							}, {
								duration: 500,
								complete: function() {
									_public.animateSlide(touches.slideId, false);
								}
							});
						} else {
							if(!clicked) {
								e.preventDefault();
							}
							
							$stage.animate({
								left: 0
							}, 500);
							
							$next.animate({
								left: touches.direction * computedWidth
							}, 500);
						}
						
						$bb.css('-webkit-user-select', 'auto');
					}
				});
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
					return slides.length - 1;
				}
				return prevSlideCandidateId;
			},
			
			createCountdown: function() {
				if(options.showTimer === false) {
					return;
				}
				
				var dimension = 100,
					cssDimension = dimension,
					canvas = null,
					ctx = null;
				
				// Retina support
				if(window.devicePixelRatio) {
					dimension = cssDimension * window.devicePixelRatio;
				}
				
				$countdown = $('<div class="uds-bb-countdown"></div>').appendTo($controls);
				canvas = $countdown.append('<canvas width="'+dimension+'" height="'+dimension+'">').find('canvas').get(0);
				
				$('canvas', $countdown).css({
					'width': cssDimension + 'px',
					'height': cssDimension + 'px'
				});
				
				if(canvas && typeof canvas.getContext === 'function') {
					ctx = canvas.getContext('2d');
					
					// setup style					
					ctx.lineWidth = 3;
					ctx.strokeStyle = 'white';
					ctx.shadowOffsetX = 0;
					ctx.shadowOffsetY = 0;
					ctx.shadowBlur = 2;
					ctx.shadowColor = 'black';
				} else {
					$countdown.remove();
					$countdown = null;
					return;
				}
				
				$countdown.data('context', ctx);
			},
			
			animateCountdown: function(duration) {
				if(	$countdown === null || 
					typeof $countdown === 'undefined' || 
					options.showTimer === false ||
					_private.isSlowBrowser()) {
					return;
				}
				
				var ctx = $countdown.data('context'),
					progress = 0,
					cssDimension = 100,
					dimension = cssDimension;
				
				if(typeof ctx === 'undefined') {
					clearInterval(timers.countDown);
					return;
				}
				
				if(duration !== false) {
					var start = new Date().getTime();
					$countdown.data('start', start);
					$countdown.data('duration', duration);
					
					clearInterval(timers.countDown);
					timers.countDown = setInterval(function() {
						_private.animateCountdown(false);
					}, 30);
				} else {
					progress = new Date().getTime() - $countdown.data('start');
					duration = $countdown.data('duration');
				}
				
				if(progress / duration >= 1) {
					clearInterval(timers.countDown);
				}
				
				// Retina support
				if(window.devicePixelRatio) {
					dimension = cssDimension * window.devicePixelRatio;
				}
				
				ctx.clearRect(0,0,dimension,dimension);
				ctx.beginPath();
				ctx.arc(
					dimension / 2, 
					dimension / 2, 
					dimension / 5, 
					- Math.PI / 2, 
					- Math.PI / 2 + (2*Math.PI) * (progress/duration), 
					false
				);
				ctx.lineWidth = 3 * dimension / cssDimension;
				ctx.stroke();
			},
			
			getSlideBackgroundCSS: function(slide) {
				var css;
				//d(slide);
				if(slide.bg !== '') {
					css = {
						backgroundColor: slide.bgColor,
						backgroundImage: 'url('+slide.bg+')',
						backgroundRepeat: slide.repeat
					};
				} else {
					css = {
						backgroundColor: slide.bgColor,
						backgroundImage: '',
						backgroundRepeat: slide.repeat
					};
				}
				
				return css;
			},
			
			handleEmbeddedContent: function(slide) {
				if(slide.hasVideo && slide.embeddecContentHandled !== true) {					
					// center content
					var $element = $('>.uds-bb-slide-'+slide.id+'>*', $stage);
					
					if($element.is('object')) {
						$element.prepend("<param name='wmode' value='opaque' />");
						$('embed', $element).attr('wmode', 'opaque');
					}
					
					$element.css({
						position: 'absolute',
						top: parseInt(options.height, 10) / 2 - $element.attr('height') / 2,
						left: parseInt(options.width, 10) / 2 - $element.attr('width') / 2,
						margin: 'auto'
					});
					
					slide.embeddecContentHandled = true;
				}
			},
			
			playVideo: function(slide) {
				// Silently fail for IE < 8
				if(typeof window.postMessage !== 'function' || typeof JSON === 'undefined') {
					return;
				}
				
				var $slide, $iframe;
				
				$slide = $('.uds-stage .uds-bb-slide-'+slide.id, $bb);
				$iframe = $('iframe', $slide);
		
				for(var i = 0; i < $('iframe').length; i++) {
					if($('iframe').eq(i).get(0) === $iframe.get(0)) {						
						// YouTube
						window.frames[i].postMessage(JSON.stringify({
							"event": "command",
							"func": 'playVideo',
							"args": null,
							"id": $iframe.attr('id')
						}), "*");

						// Vimeo
						window.frames[i].postMessage(JSON.stringify({
							method: "play"
						}), "*");
						
						$(window).on("message", function(event) {
							var e = event.originalEvent;
							if(e.origin === "http://www.youtube.com") {
								var data = JSON.parse(e.data);
								if(data.event === "onStateChange" && data.info.playerState === 0) {
									_public.play();
								}
							}
						});
					}	
				}
			},
			
			pauseVideo: function(slide) {
				// Silently fail for IE < 8
				if(typeof window.postMessage !== 'function' || typeof JSON === 'undefined') {
					return;
				}
				
				var $slide, $iframe;
				
				$slide = $('.uds-stage .uds-bb-slide-'+slide.id, $bb);
				$iframe = $('iframe', $slide);
		
				for(var i = 0; i < $('iframe').length; i++) {
					if($('iframe').eq(i).get(0) === $iframe.get(0)) {
						// YouTube
						window.frames[i].postMessage(JSON.stringify({
				            "event": "command",
				            "func": 'pauseVideo',
				            "args": null,
				            "id": $iframe.attr('id')
				        }), "*");
				        
				        // Vimeo
						window.frames[i].postMessage(JSON.stringify({
							"method": "pause"
						}), "*");
					}	
				}
			},
			
			prepareKenBurns: function(slide) {
				if(slide.hasVideo) {
					return;
				}
				
				if(typeof slide.kenBurnsImageCache === 'undefined') {
					var $slide = $(".uds-bb-slide-" + slide.id, $bb);
					var image = new Image();
					
					slide.kenBurnsImage = image;
					
					var canvas = $('<canvas>').css({
						position: 'relative',
						width: computedWidth || options.width,
						height: computedHeight || options.height
					}).attr({
						width: computedWidth || options.width,
						height: computedHeight || options.height
					}).appendTo($slide).get(0);
					
					if(typeof canvas.getContext === 'function') {
						var ctx = canvas.getContext('2d');
						slide.kenBurnsCanvasCache = ctx;
						slide.kenBurnsImageCache = false;
						
						image.onLoad = function() {
							ctx.clearRect(0, 0, computedWidth, computedHeight);
							ctx.drawImage(image, 0, 0);
						};
						image.src = slide.bg.replace('-full.', '-ken.');
					} else {
						$(canvas).remove();
						slide.kenBurnsCanvasCache = false;
						
						image.src = slide.bg.replace('-full.', '-ken.');
						
						$(image).addClass('uds-ken-burns');
					
						$slide.css({
							overflow: 'hidden'
						}).append(image);
						
						slide.kenBurnsImageCache = $(".uds-bb-slide-" + slide.id + " img.uds-ken-burns", $bb);
						
						slide.kenBurnsImageCache.css({
							'position': 'relative',
							'display': 'block',
							'-ms-interpolation-mode': 'bicubic'
						});
					}
				}
				
				if(slide.kenBurnsCanvasCache) {
					slide.kenBurnsCanvasStartingCSS = {
						top: 0,
						left: 0,
						width: computedWidth,
						height: computedHeight
					};
				} else {
					slide.kenBurnsImageCache.css({
						top: 0,
						left: 0,
						width: computedWidth,
						height: computedHeight
					});
				}
			},
			
			kenBurns: function(slide) {
				if(slide.hasVideo || (typeof slide.kenBurnsImageCache === 'undefined' && typeof slide.kenBurnsCanvasCache === 'undefined')) {
					return;
				}

				if(slide.kenBurnsCanvasCache) {					
					clearInterval(timers.kenBurnsCanvasTimer);
					var start = new Date();
					var css = _private.kenBurnsCSS(false);

					timers.kenBurnsCanvasTimer = setInterval(function(){
						var now = new Date();
						var progress = (now.getTime() - start.getTime()) / slide.kenBurnsSpeed;
						var startCSS = slide.kenBurnsCanvasStartingCSS;
						
						slide.kenBurnsCanvasCache.clearRect(0, 0, computedWidth, computedHeight);
						slide.kenBurnsCanvasCache.drawImage(
							slide.kenBurnsImage, 
							((1 - progress) * startCSS.left   + (progress) * css.left  ),
							((1 - progress) * startCSS.top    + (progress) * css.top   ),
							((1 - progress) * startCSS.width  + (progress) * css.width ),
							((1 - progress) * startCSS.height + (progress) * css.height)
						);
						
						if(progress >= 1) {
							start = new Date();
							slide.kenBurnsCanvasStartingCSS = css;
							css = _private.kenBurnsCSS(false);
						}
					}, 20);
				} else {
					slide.kenBurnsImageCache.eq(0).stop().animate(_private.kenBurnsCSS(true), {
						duration: slide.kenBurnsSpeed,
						easing: 'easeInOutQuad',
						complete: function() {
							if(slide.id === currentSlideId) {
								_private.kenBurns(slide);
							}
						}
					});
				}
			},
			
			kenBurnsCSS: function(withPx) {
				var scale = 1.2 - (Math.random() * 0.4);
				var css = {
					top: ((- 0.2 + Math.random() * 0.4) * computedHeight),
					left: ((- 0.2 + Math.random() * 0.4) * computedWidth),
					width: computedWidth * scale,
					height: computedHeight * scale
				};
				
				var ratio = parseInt(options.height, 10) / parseInt(options.width, 10);
				
				if(css.top > 0) {
					css.top = 0;
				}
				
				if(css.left > 0) {
					css.left = 0;
				}
				
				if(css.top + css.height < computedHeight) {
					css.height = - css.top + computedHeight;
					css.width = css.height / ratio;
				}
				
				if(css.left + css.width < computedWidth) {
					css.width = - css.left + computedWidth;
					css.height = css.width * ratio;
				}
				
				if(withPx) {
					css.top 	= css.top + 'px';
					css.left	= css.left + 'px';
					css.width	= css.width + 'px';
					css.height	= css.height + 'px';
				}

				return css;
			},
			
			isSlowBrowser: function() {
				return _private.isMobile();
			},
			
			isMobile: function(){
				var ua = navigator.userAgent;
				var checker = {
				  ios: $.isArray(ua.match(/(iPhone|iPod|iPad)/)),
				  blackberry: $.isArray(ua.match(/BlackBerry/)),
				  android: $.isArray(ua.match(/Android/))
				};
				
				if(checker.ios || checker.blackberry || checker.android) {
					return true;
				} else {
					return false;
				}
			},
			
			shouldUseRetina: function(){
				return typeof window.devicePixelRatio === 'number' ? window.devicePixelRatio > 1 : false;
			},
			
			maybeUseRetina: function(slideUrl, options) {
				var useRetina = typeof options.retina !== 'undefined' ? options.retina : false;

				if(_private.shouldUseRetina() && useRetina) {
					return slideUrl.replace('-full.', '-full-retina.');
					
				}
				
				return slideUrl;
			},
			
			/**
			 *	Returns input value transformed to positive pixel value string
			 */
			pos: function(dim) {
				return Math.abs(parseInt(dim, 10)) + 'px';
			},
			
			/**
			 *	Returns input value transformed to negative pixel value string
			 */
			neg: function(dim) {
				return '-' + Math.abs(parseInt(dim, 10)) + 'px';
			}
		},
		
		directions = {
			'none': {
				delay: $.noop
			},
			
			'intuitive': {
				delay: $.noop
			},
			
			'intuitiveVertical': {
				delay: $.noop
			},
			
			'left': {
				delay: function() {
					var cols = Math.ceil(parseInt(options.width, 10) / parseInt(options.squareSize, 10));
					for(var i = 0; i < cols; i++) {
						$('.uds-column-'+i, $bb).delay(i * (600/cols));
					}
				}
			},
			
			'right': {
				delay: function() {
					var cols = Math.ceil(parseInt(options.width, 10) / parseInt(options.squareSize, 10));
					for(var i = 0; i < cols; i++) {
						$('.uds-column-'+i, $bb).delay(600 - i * (600/cols));
					}
				}
			},
			
			'top': {
				delay: function() {
					var rows = Math.ceil(parseInt(options.height, 10) / parseInt(options.squareSize, 10));
					for(var i = 0; i < rows; i++) {
						$('.uds-row-'+i, $bb).delay(i * (600/rows));
					}
				}
			},
			
			'bottom': {
				delay: function() {
					var rows = Math.ceil(parseInt(options.height, 10) / parseInt(options.squareSize, 10));
					for(var i = 0; i < rows; i++) {
						$('.uds-row-'+i, $bb).delay(600 - i * (600/rows));
					}
				}
			},
			
			'center': {
				delay: function() {
					var rows = Math.ceil(parseInt(options.height, 10) / parseInt(options.squareSize, 10)),
						cols = Math.ceil(parseInt(options.width, 10) / parseInt(options.squareSize, 10));
					
					for(var x = 0; x < cols; x++) {
						for(var y = 0; y < rows; y++) {
							var delay = Math.sqrt(Math.pow(x - (cols / 2) + 0.5, 2) + Math.pow(y - (rows / 2) + 0.5, 2)) / Math.sqrt(Math.pow(cols / 2, 2) + Math.pow(rows / 2, 2));
							$('.uds-square-' + (y * cols + x)).delay(700 * delay);
						}
					}
				}
			},
			
			'randomSquares': {
				delay: function() {
					$squares.each(function(){
						$(this).delay(Math.random() * 700);
					});
				}
			},
			
			'spiralIn': {
				delay: function() {
					var cols = Math.ceil(parseInt(options.width, 10) / parseInt(options.squareSize, 10));
					var rows = Math.ceil(parseInt(options.height, 10) / parseInt(options.squareSize, 10));
					
					var leftBound = 0;
					var rightBound = cols - 1;
					var topBound = 0;
					var bottomBound = rows - 1;
					
					var n = 0, hPos = 0, vPos = 0;
					while(n < cols * rows){
						var squareId = cols * vPos + hPos;
						
						$('.uds-square-'+squareId).delay(1000 * (n/(cols*rows)));
						//d('T: '+topBound+' R:'+rightBound+' B:'+bottomBound+' L:'+leftBound+' X:'+hPos+' Y:'+vPos+' Delay:'+1000 * (n/(cols*rows)));
						
						if(vPos === topBound && hPos < rightBound) {
							hPos++;
						} else if(hPos === rightBound && vPos < bottomBound) {
							vPos++;
						} else if(vPos === bottomBound && hPos > leftBound) {
							hPos--;
						} else {
							vPos--;
							if(vPos === topBound) {
								hPos++;
								vPos++;
								leftBound++;
								rightBound--;
								topBound++;
								bottomBound--;
							}
						}
						n++;
					}
				}
			},
			
			'spiralOut': {
				delay: function() {
					var cols = Math.ceil(parseInt(options.width, 10) / parseInt(options.squareSize, 10));
					var rows = Math.ceil(parseInt(options.height, 10) / parseInt(options.squareSize, 10));
					
					var leftBound = 0;
					var rightBound = cols - 1;
					var topBound = 0;
					var bottomBound = rows - 1;
					
					var n = 0, hPos = 0, vPos = 0;
					while(n < cols * rows){
						var squareId = cols * vPos + hPos;

						$('.uds-square-'+squareId).delay(1000 - 1000 * (n/(cols*rows)));

						if(vPos === topBound && hPos < rightBound) {
							hPos++;
						} else if(hPos === rightBound && vPos < bottomBound) {
							vPos++;
						} else if(vPos === bottomBound && hPos > leftBound) {
							hPos--;
						} else {
							vPos--;
							if(vPos === topBound) {
								hPos++;
								vPos++;
								leftBound++;
								rightBound--;
								topBound++;
								bottomBound--;
							}
						}
						n++;
					}
				}
			},
			
			'chess' : {
				delay: function() {
					var rows = Math.ceil(parseInt(options.height, 10) / parseInt(options.squareSize, 10)),
						cols = Math.ceil(parseInt(options.width, 10) / parseInt(options.squareSize, 10));
					
					for(var x = 0; x < cols; x++) {
						for(var y = 0; y < rows; y++) {
							var delay = (y % 2 === 0 && x % 2 === 0) || (y % 2 === 1 && x % 2 === 1) ? 0 : 1;
							$('.uds-square-' + (y * cols + x)).delay(200 * delay);
						}
					}
				}
			},
			
			'zigzagHorizontal' : {
				delay: function() {
					var rows = Math.ceil(parseInt(options.height, 10) / parseInt(options.squareSize, 10)),
						cols = Math.ceil(parseInt(options.width, 10) / parseInt(options.squareSize, 10));
					
					for(var x = 0; x < cols; x++) {
						for(var y = 0; y < rows; y++) {
							var delay = (y * rows + (y % 2 === 0 ? x : cols - x)) / (rows * rows + cols / 2);
							$('.uds-square-' + (y * cols + x)).delay(700 * delay);
						}
					}
				}
			},
			
			'zigzagVertical' : {
				delay: function() {
					var rows = Math.ceil(parseInt(options.height, 10) / parseInt(options.squareSize, 10)),
						cols = Math.ceil(parseInt(options.width, 10) / parseInt(options.squareSize, 10));
					
					for(var x = 0; x < cols; x++) {
						for(var y = 0; y < rows; y++) {
							var delay = (x * cols + (x % 2 === 0 ? y : rows - y)) / (cols * cols + rows / 2);
							$('.uds-square-' + (y * cols + x)).delay(700 * delay);
						}
					}
				}
			}
		},
		
		/**
		 *	
		 */
		animations = {
			/**
			 *
			 */
			'none': {
				duration: 0,
				direction: '',
				setup: function() {
					// we dont want squares to interfere with the content
					$next.fastHide();
				},
				perform: $.noop()
			},
			
			/**
			 *
			 */
			'fade': {
				duration: 1500,
				direction: '',
				setup: function() {
					var $el = _private.isSlowBrowser() ? $next : $squares;
					$el.css({
						opacity: 0
					});
				},
				perform: function() {
					var $el = _private.isSlowBrowser() ? $next : $squares;
					directions[this.direction].delay();
					$el.animate({
						opacity: 1
					}, {
						duration: 500
					});
				}
			},
			
			/**
			 *
			 */
			'crossFade': {
				duration: 1000,
				direction: '',
				setup: function() {
					$squares.css({
						opacity: 0
					});
				},
				perform: function() {
					directions[this.direction].delay();
					$stage.animate({
						opacity: 0
					}, {
						duration: 1000
					});
					
					var $el = _private.isSlowBrowser() ? $next : $squares;
					$el.animate({
						opacity: 1
					}, {
						duration: 500
					});
				}
			},
			
			'slide': {
				duration: 700,
				direction: 'right',
				setup: function(currentSlideId, nextSlideId) {				
					var sq;
					
					$('.uds-bb-slides', $bb).css({
						overflow: 'hidden'
					});
					
					$stage.css({
						top: '0px',
						left: '0px'
					});
					
					var direction = this.direction;
					if(direction === 'intuitive' || direction === 'intuitiveVertical') {
						if((currentSlideId < nextSlideId || (nextSlideId === 0 && currentSlideId === slides.length - 1)) && !(nextSlideId === slides.length - 1 && currentSlideId === 0)) {
 							direction = direction === 'intuitive' ? 'right' : 'top';
						} else {
							direction = direction === 'intuitive' ? 'left' : 'bottom';
						}
					}
					
					if(direction === 'left') {
						$next.fastShow().css({
							top: '0px',
							left: _private.neg(computedWidth)
						});
					} else if(direction === 'top') {
						$next.fastShow().css({
							top: _private.neg(computedHeight),
							left: '0px'
						});
					} else if(direction === 'bottom') {
						$next.fastShow().css({
							top: _private.pos(computedHeight),
							left: '0px'
						});
					} else if(direction === 'top') {
						$next.fastShow().css({
							top: '0px',
							left: _private.pos(computedWidth)
						});
					} else if(direction === 'zigzagHorizontal') {
						sq = parseInt(options.squareSize, 10);
						$squares.each(function(){
							$(this).css({
								opacity: 0,
								top: parseInt($(this).css('top'), 10) - computedHeight + 'px'
							});
						});
						
						directions[direction].delay();
					} else if(this.direction === 'zigzagVertical') {
						sq = parseInt(options.squareSize, 10);
						$squares.each(function(){
							$(this).css({
								opacity: 0,
								left: parseInt($(this).css('left'), 10) - computedWidth + 'px'
							});
						});
						
						directions[direction].delay();
					} else {
						$next.fastShow().css({
							top: '0px',
							left: _private.pos(computedWidth)
						});
					}
				},
				perform: function(currentSlideId, nextSlideId) {
					var animOptions =  {
						duration: 700,
						easing: 'easeInOutQuad'
					}, sq;
					
					var direction = this.direction;
					if(direction === 'intuitive' || direction === 'intuitiveVertical') {
						if((currentSlideId < nextSlideId || (nextSlideId === 0 && currentSlideId === slides.length - 1)) && !(nextSlideId === slides.length - 1 && currentSlideId === 0)) {
							direction = direction === 'intuitive' ? 'right' : 'top';
						} else {
							direction = direction === 'intuitive' ? 'left' : 'bottom';
						}
					}
					
					if(direction === 'left') {
						$stage.animate({
							left: _private.pos(computedWidth)
						}, animOptions);
						$next.animate({
							left: '0px'
						}, animOptions);
					} else if(direction === 'top') {
						$stage.animate({
							top: _private.pos(computedHeight)
						}, animOptions);
						$next.animate({
							top: '0px'
						}, animOptions);
					} else if(direction === 'bottom') {
						$stage.animate({
							top: _private.neg(computedHeight)
						}, animOptions);
						$next.animate({
							top: '0px'
						}, animOptions);
					} else if(direction === 'top') {
						$stage.animate({
							left: _private.neg(computedWidth)
						}, animOptions);
						$next.animate({
							left: '0px'
						}, animOptions);
					} else if(direction === 'zigzagHorizontal') {
						sq = parseInt(options.squareSize, 10);

						$squares.each(function(){
							$(this).animate({
								opacity: 1,
								top: parseInt($(this).css('top'), 10) + computedHeight + 'px'
							}, animOptions);
						});
					} else if(direction === 'zigzagVertical') {
						sq = parseInt(options.squareSize, 10);
						
						$squares.each(function(){
							$(this).animate({
								opacity: 1,
								left: parseInt($(this).css('left'), 10) + computedWidth + 'px'
							}, animOptions);
						});
					} else {
						$stage.animate({
							left: _private.neg(computedWidth)
						}, animOptions);
						$next.animate({
							left: '0px'
						}, animOptions);
					}
				}
			},
			
			/**
			 *
			 */
			'scale': {
				duration: 1100,
				direction: '',
				setup: function() {
					var top, left, sq;
					if(this.direction === 'right') {
						top = _private.pos(computedHeight / 2);
						left = _private.pos(computedWidth);
					} else if(this.direction === 'left') {
						top = _private.pos(computedHeight / 2);
						left = '0px';
					} else if(this.direction === 'top') {
						top = '0px';
						left = _private.pos(computedWidth / 2);
					} else if(this.direction === 'bottom') {
						top = _private.pos(computedHeight);
						left = _private.pos(computedWidth / 2);
					} else if(this.direction === 'center') {
						top = _private.pos(computedHeight / 2);
						left = _private.pos(computedWidth / 2);
					} else if(this.direction === 'zigzagHorizontal') {
						sq = parseInt(options.squareSize, 10);
						$squares.each(function(){
							$(this).css({
								top: parseInt($(this).css('top'), 10) + sq / 2 + 'px',
								left: parseInt($(this).css('left'), 10) + sq / 2 + 'px',
								height: '0px'
							});
						});
						
						directions[this.direction].delay();
						
						return;
					} else if(this.direction === 'zigzagVertical') {
						sq = parseInt(options.squareSize, 10);
						$squares.each(function(){
							$(this).css({
								top: parseInt($(this).css('top'), 10) + sq / 2 + 'px',
								left: parseInt($(this).css('left'), 10) + sq / 2 + 'px',
								width: '0px'
							});
						});
						
						directions[this.direction].delay();
						
						return;
					} else {
						sq = parseInt(options.squareSize, 10);
						$squares.each(function(){
							$(this).css({
								top: parseInt($(this).css('top'), 10) + sq / 2 + 'px',
								left: parseInt($(this).css('left'), 10) + sq / 2 + 'px',
								width: '0px',
								height: '0px'
							});
						});
						
						directions[this.direction].delay();
						
						return;
					}
					
					$next.css({
						top: top,
						left: left,
						width: '1px',
						height: '1px'
					});
				},
				perform: function() {
					if($.inArray(this.direction, ['center', 'top', 'left', 'bottom', 'right']) > -1) {
						$next.animate({
							top: '0px',
							left: '0px',
							width: computedWidth,
							height: computedHeight
						}, {
							duration: 1000,
							easing: 'easeInOutQuad'
						});
					} else {
						var sq = parseInt(options.squareSize, 10);
						$squares.each(function(i, el){
							$(this).animate({
								top: parseInt($(el).css('top'), 10) - sq / 2 + 'px',
								left: parseInt($(el).css('left'), 10) - sq / 2 + 'px',
								width: sq + 'px',
								height: sq + 'px'
							}, 500);
						});
					}
				}
			}
		};
		
		
		var defaults = {
			width:			'960px',
			height:			'400px',
			squareSize:		'80px',
			autoplay:		true,
			showControls:	true,
			showPause:		true,
			showPaginator:	true,
			showThumbnails:	true,
			showTimer:		true,
			removeSlidesWithBrokenImages: true
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