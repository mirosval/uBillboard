(function($) {
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
	var $bb,
	
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
	 *	Array of slides
	 */
	slides,
	
	/**
	 *	Object, options for the current uBillboard
	 */
	options,
	
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
	 *	Public methods callable from the outside. Call like this:
	 *	$('bb-id').uBillboard('next')
	 */
	_public = {
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
			
			// initialize timers
			timers = {};
			
			_private.initSlides();
			_private.initAnimationMarkup();
			// Runs preloader, and when it finishes, it triggers the udsBillboardLoadingComplete Event
			// to continue normal code flow
			_private.preloadImages();
			
			// Init pagination and playback controls
			_private.initControls();
			
			// load first slide
			currentSlideId = 0;
			var currentSlide = slides[currentSlideId];
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
				
				// Run Countdown Animation
				_private.animateCountdown(slides[currentSlideId].delay);
				
				_public.play();
			} else {
				$countdown.hide();
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
			
			$bb.trigger('udsBillboardSlideWillChange', currentSlideId);
			
			var slide = slides[slideId];
			
			_private.prepareForAnimation(slideId);
			
			// Handle Embedded content
			if(slide.transition == 'none') {
				$stage.html(slide.html);
				
				$stage.css({
					backgroundColor: 'black',
					backgroundImage: 'none'
				});
				
				// center content
				$element = $('>*', $stage);

				$element.css({
					position: 'absolute',
					top: parseInt(options.height, 10) / 2 - $element.attr('height') / 2,
					left: parseInt(options.width, 10) / 2 - $element.attr('width') / 2
				});
			}
			
			// Decide on transition
			var transition = 'fade';
			if(slide.transition !== null && typeof slide.transition === 'string') {
				transition = slide.transition;
			}

			if(animations[transition] === null || typeof animations[transition] !== 'object'){
				$.error('Transition "' + transition + '" is not defined');
				return;
			}
			
			// Assign Direction
			var defaultDirection = animations[transition].direction;
			animations[transition].direction = slide.direction;
			
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
			
			// Run Countdown Animation
			_private.animateCountdown(slides[currentSlideId].delay);
			
			$bb.trigger('udsBillboardSlideDidChange', currentSlideId);
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
			
			$countdown.show();
			
			if(!playing) {
				// Run Countdown Animation
				_private.animateCountdown(slides[currentSlideId].delay);
			}
			
			timers.nextSlideAnimation = setTimeout(function(){
				_public.next();
				_public.play();
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
			
			$countdown.hide();
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
			slides = [];
			$('.uds-bb-slide', $bb).each(function(i, el){
				var slide = {
					delay: parseInt($('.uds-delay', el).remove().text(), 10),
					transition: $('.uds-transition', el).remove().text(),
					direction: $('.uds-direction', el).remove().text(),
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
				
				$('<img>').data('slideID', i)
				.load(function(){
					++progress;
					
					if(progress == totalImages) {
						$bb.trigger('udsBillboardLoadingComplete');
					} else {
						d('Progress: '+(progress/totalImages));
					}
					
				}).error(function() {
					var slideID = $(this).data('slideID');
					d('Failed to load image: ' + slides[slideID].bg);
					
					++progress;
					if(progress == totalImages) {
						$bb.trigger('udsBillboardLoadingComplete');
					} else {
						d('Progress: '+(progress/totalImages));
					}
					
					if(options.removeSlidesWithBrokenImages === true) {
						// remove slide
						slides.splice(slideID, 1);
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
				backgroundColor: 'transparent',
				backgroundImage: 'url('+currentSlide.bg+')'
			}).html(currentSlide.html);
			
			_private.resetAnimation();
			
			$next.hide();
			
			if(nextSlide.transition !== 'none') { // Do not create a million copies of embedded content ;)
				$nextInsides.css({
					backgroundImage: 'url('+nextSlide.bg+')'
				}).html(nextSlide.html);
			}
		},
		
		initControls: function() {
			// Setup CSS
			$('.uds-bb-controls', $bb).css({
				width: options.width,
				height: options.height
			});
			
			// Center controls
			$('.uds-center', $bb).each(function() {
				var widthAdjustment = $(this).outerWidth() / 2;
				var heightAdjustment = $(this).outerHeight() / 2;
				
				$(this).css({
					top: parseInt(options.height, 10) / 2 - heightAdjustment,
					left: parseInt(options.width, 10) / 2 - widthAdjustment
				});
			});
			
			$('.uds-center-vertical', $bb).each(function() {
				var heightAdjustment = $(this).outerHeight() / 2;
				
				$(this).css({
					top: parseInt(options.height, 10) / 2 - heightAdjustment
				});
			});
			
			$('.uds-center-horizontal', $bb).each(function() {
				var widthAdjustment = $(this).outerWidth() / 2;
				
				$(this).css({
					left: parseInt(options.width, 10) / 2 - widthAdjustment
				});
			});
			
			// setup variables for shorter code
			var $playpause = $('.uds-bb-playpause', $bb);
			var $buttonNext = $('.uds-bb-next', $bb);
			var $buttonPrev = $('.uds-bb-prev', $bb);
			var $bullets = $('.uds-bb-position-indicator-bullets', $bb);
			var $thumbs = $('.uds-bb-thumbnails', $bb);
			
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
			for(slide in slides) {
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
				$('.uds-bb-thumb', $bb).removeClass('active').eq(currentSlideId).addClass('active');
			});
			
			// Thumbnails
			$('.uds-bb-thumb', $thumbs).click(function(){
				_public.animateSlide($(this).index());
			});
			
			$bb.has('.uds-bb-thumbnails.top').css('margin-top', $thumbs.outerHeight());
			$bb.has('.uds-bb-thumbnails.bottom').css('margin-bottom', $thumbs.outerHeight());
			$bb.has('.uds-bb-thumbnails.left').css('margin-left', $thumbs.outerWidth());
			$bb.has('.uds-bb-thumbnails.right').css('margin-right', $thumbs.outerWidth());
			
			// Comply with options
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
			
			$controlsToHover.fadeTo(0, 0);
			$bb.hover(function(){
				$controlsToHover.stop().fadeTo(300, 1);
			}, function(){
				$controlsToHover.stop().fadeTo(300, 0);
			});
			
			// Hide controls based on the options
			if(options.showControls === false) {
				$buttonNext.hide()
				$buttonPrev.hide();
			}

			if(options.showPause === false) {
				$playpause.hide();
			}
			
			if(options.showPaginator === false) {
				$bullets.hide();
				$('.uds-bb-position-indicator', $bb).hide();
			}
			
			if(options.showThumbnails === false) {
				$thumbs.hide();
			}
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
			$countdown = $('<div class="uds-bb-countdown"></div>').appendTo($controls);
			canvas = $countdown.append('<canvas width="100" height="100">').find('canvas').get(0);
			if(canvas.getContext) {
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
			if($countdown === null || typeof $countdown === 'undefined') {
				return;
			}
			
			var ctx = $countdown.data('context'),
				progress = 0;
			
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
			
			ctx.clearRect(0,0,100,100);
			ctx.beginPath();
			ctx.arc(50, 50, 20, - Math.PI / 2, - Math.PI / 2 + (2*Math.PI) * (progress/duration), false);
			ctx.stroke();
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
			setup: $.noop(),
			perform: $.noop()
		},
		
		/**
		 *
		 */
		'fade': {
			duration: 500,
			direction: '',
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
			}
		},
		
		/**
		 *
		 */
		'fadeSquaresRandom': {
			duration: 1100,
			direction: '',
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
			}
		},
		
		'slide': {
			duration: 500,
			direction: 'right',
			setup: function() {
				$bb.css({
					overflow: 'hidden'
				});
				$stage.css({
					top: '0px',
					left: '0px'
				});
				
				if(this.direction === 'left') {
					$next.show().css({
						top: '0px',
						left: _private.neg(options.width)
					});
				} else if(this.direction === 'top') {
					$next.show().css({
						top: _private.neg(options.height),
						left: '0px'
					});
				} else if(this.direction === 'bottom') {
					$next.show().css({
						top: _private.pos(options.width),
						left: '0px'
					});
				} else {
					$next.show().css({
						top: '0px',
						left: _private.pos(options.width)
					});
				}
			},
			perform: function() {
				var animOptions =  {
					duration: 500,
					easing: 'easeInOutQuad'
				};
				
				if(this.direction === 'left') {
					$stage.animate({
						left: _private.pos(options.width)
					}, animOptions);
					$next.animate({
						left: '0px'
					}, animOptions);
				} else if(this.direction === 'top') {
					$stage.animate({
						top: _private.pos(options.height)
					}, animOptions);
					$next.animate({
						top: '0px'
					}, animOptions);
				} else if(this.direction === 'bottom') {
					$stage.animate({
						top: _private.neg(options.height)
					}, animOptions);
					$next.animate({
						top: '0px'
					}, animOptions);
				} else {
					$stage.animate({
						left: _private.neg(options.width)
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
				var top, left;
				if(this.direction === 'right') {
					top = _private.pos(parseInt(options.height, 10) / 2);
					left = _private.pos(options.width);
				} else if(this.direction === 'left') {
					top = _private.pos(parseInt(options.height, 10) / 2);
					left = '0px';
				} else if(this.direction === 'top') {
					top = '0px';
					left = _private.pos(parseInt(options.width, 10) / 2);
				} else if(this.direction === 'bottom') {
					top = _private.pos(options.height);
					left = _private.pos(parseInt(options.width, 10) / 2);
				} else {
					top = _private.pos(parseInt(options.height, 10) / 2);
					left = _private.pos(parseInt(options.width, 10) / 2);
				}
				
				$next.show().css({
					top: top,
					left: left,
					width: '1px',
					height: '1px'
				});
			},
			perform: function() {
				$next.animate({
					top: '0px',
					left: '0px',
					width: options.width,
					height: options.height
				}, {
					duration: 1000,
					easing: 'easeInOutQuad'
				});
			}
		},
		
		'fadeSquaresRows': {
			duration: 1100,
			direction: '',
			setup: function() {
				$next.show();
				$squares.css('opacity', 0);
			},
			perform: function() {
				var rows = Math.ceil(parseInt(options.height, 10) / parseInt(options.squareSize, 10));
				for(var i = 0; i < rows; i++) {
					$('.uds-row-'+i, $bb).delay(i * 100).animate({
						opacity: 1
					}, {
						duration: 400,
						easing: 'easeInOutQuad'
					});
				}
			}
		},
		
		'fadeSquaresCols': {
			duration: 1000,
			direction: '',
			setup: function() {
				$next.show();
				$squares.css('opacity', 0);
			},
			perform: function() {
				var cols = Math.ceil(parseInt(options.width, 10) / parseInt(options.squareSize, 10));
				for(var i = 0; i < cols; i++) {
					$('.uds-column-'+i, $bb).delay(i * (600/cols)).animate({
						opacity: 1
					}, {
						duration: 400,
						easing: 'easeInOutQuad'
					});
				}
			}
		},
		
		'fadeSquaresSpiralIn': {
			duration: 1300,
			direction: '',
			setup: function() {
				$next.show();
				$squares.css('opacity', 0);
			},
			perform: function() {
				var cols = Math.ceil(parseInt(options.width, 10) / parseInt(options.squareSize, 10));
				var rows = Math.ceil(parseInt(options.height, 10) / parseInt(options.squareSize, 10));
				
				var leftBound = 0;
				var rightBound = cols - 1;
				var topBound = 0;
				var bottomBound = rows - 1;
				
				var n = 0, hPos = 0, vPos = 0;
				while(n < cols * rows){
					var squareId = cols * vPos + hPos;
					
					$('.uds-square-'+squareId).delay(20 * n).animate({
						opacity: 1
					}, 500);
					
					if(vPos == topBound && hPos < rightBound) {
						hPos++;
					} else if(hPos == rightBound && vPos < bottomBound) {
						vPos++;
					} else if(vPos == bottomBound && hPos > leftBound) {
						hPos--;
					} else {
						vPos--;
						if(vPos == 0) {
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
				
				/*
				var cols = Math.ceil(parseInt(options.width, 10) / parseInt(options.squareSize, 10));
				var rows = Math.ceil(parseInt(options.height, 10) / parseInt(options.squareSize, 10));
				
				$squares.each(function(i, el){
					var x = $(el).data('position').x - cols / 2 + 0.5;
					var y = $(el).data('position').y - rows / 2 + 0.5;
					$(el).delay((Math.sqrt(Math.pow(x, 2) + Math.pow(y, 2)) + Math.atan2(x, y)) * 100).animate({
						opacity: 1
					}, 500);
				});
				//*/
			}
		},
		
		'fadeSquaresSpiralOut': {
			duration: 1100,
			direction: '',
			setup: function() {
				$next.show();
				$squares.css('opacity', 0);
			},
			perform: function() {
				var cols = Math.ceil(parseInt(options.width, 10) / parseInt(options.squareSize, 10));
				var rows = Math.ceil(parseInt(options.height, 10) / parseInt(options.squareSize, 10));
				
				$squares.each(function(i, el){
					var x = $(el).data('position').x - cols / 2 + 0.5;
					var y = $(el).data('position').y - rows / 2 + 0.5;
					$(el).delay((5 - Math.sqrt(Math.pow(x, 2) + Math.pow(y, 2)) + Math.atan2(x, y)) * 100).animate({
						opacity: 1
					}, 500);
				});
			}
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
	 *	Main jQuery plugin definition
	 */
	$.fn.uBillboard = function(options){
		
		var defaults = {
			width:		'960px',
			height:		'400px',
			squareSize:	'80px',
			autoplay:	true,
			
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