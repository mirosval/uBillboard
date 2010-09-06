jQuery.noConflict();
jQuery(function($){
	$wrapper = $('#uds-billboard-wrapper');
	$bb = $('#uds-billboard');
	$loader = $('#uds-loader');
	$controls = $('#uds-billboard-controls');
	$next = $('#uds-next-slide');
	slides = [];
	currentSlideIndex = 0;
	prevSlideIndex = 0;
	totalImages = $('.uds-slide', $bb).length;
	totalImagesLoaded = 0;
	timeout = null;
	squareSize = 100;
	columnWidth = 50;
	width = 940;
	height = 380;
	
	//d = function(a){ try {console.log(a);} catch(e){ $.noop(); }};
	
	// parse incoming data structures
	$('.uds-slide', $bb).each(function(i, el){
		var slide = {
			delay: parseInt($('input[name=uds-billboard-delay]', this).val(), 10),
			transition: $('input[name=uds-billboard-transition]', this).val(),
			layout: $('input[name=uds-billboard-layout]', this).val(),
			image: $('>img', this).attr('src'),
			description: $('.uds-descr-wrapper', this).html()
		};
		
		slides.push(slide);
		
		$(this).remove();
		
		$bb.hide();
		$controls.hide();	
		
		// preloader
		$('<img>').load(function(){
			++totalImagesLoaded;
			var pos = - 160 + 160 * (totalImagesLoaded/totalImages);
			$loader.animate({
				'background-position': pos+'px 0px'
			}, {
				duration: 100
			});
			if(totalImages == totalImagesLoaded){
				$bb.css('background-image', 'url('+slides[0].image+')');
				showPaginator(0);
				showDescription(0);
				setupSquares();
				setupColumns();

				$bb.fadeIn();
				$controls.fadeIn();
				
				if(totalImages < 2){ return; }
				timeout = setTimeout(function(){
					showSlide(currentSlideIndex + 1);
				}, slides[0].delay);
			}
		}).attr('src', slide.image);
	});
	
	function resetToSlide(index) {
		$bb.css('background-image', 'url('+slides[index].image+')');
			$('div', $next).add($next).css('background-image', '');
			resetSquares(0, 0);
			resetColumns();
			$next.css({
				"-webkit-transform": "scale(1)",
				"-moz-transform": "scale(1)",
				"-o-transform": "scale(1)",
				opacity: 1
			});
			showDescription(index);
	};
	
	// main data juggling, switch images in billboard and next slide divs, dispatch transition
	function showSlide(index){
		clearTimeout(timeout);
		prevSlideIndex = currentSlideIndex;
		currentSlideIndex = index;
		$bb.css('background-image', 'url('+slides[prevSlideIndex].image+')');
		
		callback = function(){
			resetToSlide(currentSlideIndex);
			timeout = setTimeout(function(){
				var index = slides[currentSlideIndex + 1] == null ? 0 : currentSlideIndex + 1;
				showSlide(index);
			}, slides[currentSlideIndex].delay);
		};
		
		$("#uds-billboard-paginator a").removeClass('current');
		$("#uds-billboard-paginator a:eq("+currentSlideIndex+")").addClass('current');
		
		hideDescription();
		
		var transition = getTransitionFunction(slides[currentSlideIndex].transition);
		transition(prevSlideIndex, currentSlideIndex, callback);
	};
	
	// creates and displays paginator
	function showPaginator(current) {
		$controls.append($("<div id='uds-billboard-paginator'></div>"));
		for(var i = 0; i < totalImages; i++){
			var $bullet = $("<a class='"+(i == current ? 'current' : '')+"'></a>");
			$('#uds-billboard-paginator').append($bullet);
		}
		$('#uds-billboard-paginator a').click(function(){
			if($(this).index() == currentSlideIndex){ return; }
		    if($('div', $next).add($next).is(':animated')){ resetToSlide(currentSlideIndex); }
		    clearInterval(timeout);
		    showSlide($(this).index());
		});
	};
	
	// shows description, create it if necessary
	function showDescription(current) {
		$descr = $("#uds-billboard-description");
		if($descr.size() === 0){
			$controls.append($("<div id='uds-billboard-description' class=''></div>"));
			$descr = $("#uds-billboard-description");
		}
		$descr.attr('class', '').addClass(slides[current].layout).html(slides[current].description);
		if($descr.hasClass('stripe-left')){
			$descr.css({
				left: -$descr.outerWidth() + 'px',
				bottom: '0px'
			}).stop().animate({
				left: '0px'
			}, {
				duration: 400,
				easing: 'easeOutExpo'
			});
		} else if ($descr.hasClass('stripe-right')) {
			// ?? weird JS bug outerWidth is 0 the first time description is shown so we just set it manually...
			var descrWidth = $descr.outerWidth() !== 0 ? $descr.outerWidth() : 350;
			$descr.css({
				left: width + 'px',
				bottom: '0px'
			}).stop().animate({
				left: width - descrWidth + 'px'
			}, {
				duration: 400,
				easing: 'easeOutExpo'
			});
		} else {
			$descr.css({
				left: '0px',
				bottom: -$descr.outerHeight() + 'px'
			}).stop().animate({
				bottom: '0px'
			}, {
				duration: 400,
				easing: 'easeOutExpo'
			});
		}
	};
	
	// hide description
	function hideDescription() {
		$descr = $("#uds-billboard-description");
		if($descr.hasClass('stripe-left')){
			$descr.stop().animate({
				left: -$descr.outerWidth()
			}, {
				duration: 200,
				easing: 'easeOutExpo'
			});
		} else if ($descr.hasClass('stripe-right')) { 
			$descr.css({
				left: width-$descr.outerWidth() + 'px',
				bottom: '0px'
			}).stop().animate({
				left: width + 'px'
			}, {
				duration: 400,
				easing: 'easeOutExpo'
			});
		} else {
			$descr.stop().animate({
				bottom: -$descr.outerHeight()
			}, {
				duration: 200,
				easing: 'easeOutExpo'
			});
		}
	};
	
	function setupSquares(){
		if(squareSize < 30) {
			squareSize = 30;
		}
		
		var sq = Math.ceil(width / squareSize) * Math.ceil(height / squareSize);
		
		for(i = 0; i < sq; i++){
			var current = document.createElement('div');
			$(current).attr('id', 'square'+i).addClass('square');
			$next.append(current);
		}
		
		resetSquares(0, 0);
		
		$('.square', $next).css({
			'opacity': '0',
			'width': squareSize+'px',
			'height': squareSize+'px'
		});
	};
	
	function resetSquares(offsetX, offsetY) {
		var rows = Math.ceil(height / squareSize);
		var cols = Math.ceil(width / squareSize);
		var n = 0;
		for(y = 0; y < rows; y++){
			for(x = 0; x < cols; x++){
				$('#square'+n).css({
					'background-position': -x*squareSize+'px '+(-y*squareSize)+'px',
					'left': (x*squareSize + offsetX) +'px',
					'top': (y*squareSize + offsetY) +'px'
				});
				++n;
			}
		}
	};

	function setupColumns() {
		var cols = Math.ceil(width / columnWidth);
		for(var i = 0; i < cols; i++) {
			var current = document.createElement('div');
			$(current).attr('id', 'column' + i).addClass('column');
			$next.append(current);
		}
		
		resetColumns();
		
		$('.column', $next).css({
			opacity: 0,
			width: columnWidth + 'px',
			height: height+'px'
		});
	};
	
	function resetColumns() {
		var cols = Math.ceil(width / columnWidth);
		for(var i = 0; i < cols; i++){
			$('#column'+i).css({
				'background-position': -i*columnWidth+'px 0px',
				left: (i*columnWidth)+'px',
				top: '0px',
				width: columnWidth + 'px'
			});
		}
	};
	
	// picks transition function
	function getTransitionFunction(transition) {
		switch(transition){
			case 'scaleTop': return animationScaleTop;
			case 'scaleCenter': return animationScaleCenter;
			case 'scaleBottom': return animationScaleBottom;
			case 'scaleRight': return animationScaleRight;
			case 'scaleLeft': return animationScaleLeft;
			case 'squaresRandom': return animationSquaresRandom;
			case 'squaresRows': return animationSquaresRows;
			case 'squaresCols': return animationSquaresCols;
			case 'squaresMoveIn': return animationSquaresMoveIn;
			case 'squaresMoveOut': return animationSquaresMoveOut;
			case 'columnWave': return animationColumnWave;
			case 'curtainRight': return animationCurtainRight;
			case 'curtainLeft': return animationCurtainLeft;
			case 'curtainRotateLeft': return animationCurtainRotateLeft;
			case 'curtainRotateRight': return animationCurtainRotateRight;
			case 'interweaveLeft': return animationInterweaveLeft;
			case 'interweaveRight': return animationInterweaveRight;
			case 'random':
				var animations = [
					animationScaleTop,
					animationScaleCenter,
					animationScaleBottom,
					animationScaleRight,
					animationScaleLeft,
					animationSquaresRandom,
					animationSquaresRows,
					animationSquaresCols,
					animationSquaresMoveIn,
					animationSquaresMoveOut,
					animationColumnWave,
					animationCurtainRight,
					animationCurtainLeft,
					animationCurtainRotateLeft,
					animationCurtainRotateRight,
					animationInterweaveLeft,
					animationInterweaveRight
				];
				var anim = animations[Math.floor(Math.random()*animations.length)];
				return anim;
			default: return animationFade;
		}
	};
	
////////////////////////////////////////////////////////////////////////////////////////
//
// Animations functions
//
////////////////////////////////////////////////////////////////////////////////////////
	
	animationFade = function(currentIndex, destinationIndex, callback){
		$next.css({
			backgroundImage: 'url('+slides[destinationIndex].image+')',
			opacity: 0
		}).animate({
			opacity: 1
		}, {
			duration: 1000,
			complete: callback,
			easing: 'easeOutExpo'
		});
	};

	animationScaleTop = function(currentIndex, destinationIndex, callback){
		$next.css({
			"background-image": 'url('+slides[destinationIndex].image+')',
			"-webkit-transform": "scale(0)",
			"-moz-transform": "scale(0)",
			"-o-transform": "scale(0)",
			"-webkit-transform-origin": "50% 0%",
			"-moz-transform-origin": "50% 0%",
			"-o-transform-origin": "50% 0%",
			"opacity": 0
		}).animate({
			"opacity": 1
		}, {
			duration: 1000,
			complete: callback,
			step: function(a, object){
				var now = new Date().getTime();
				var scale = $.easing.swing(null, now - object.startTime, 0, 1, object.options.duration);
				$(object.elem).css({
					'opacity': scale,
					'-o-transform': 'scale('+scale+')',
					'-moz-transform': 'scale('+scale+')',
					'-webkit-transform': 'scale('+scale+')'
				});
			}
		});
	};
	
	animationScaleCenter = function(currentIndex, destinationIndex, callback){
		$next.css({
			"background-image": 'url('+slides[destinationIndex].image+')',
			"-webkit-transform": "scale(0)",
			"-moz-transform": "scale(0)",
			"-o-transform": "scale(0)",
			"-webkit-transform-origin": "50% 50%",
			"-moz-transform-origin": "50% 50%",
			"-o-transform-origin": "50% 50%",
			"opacity": 0
		}).animate({
			"opacity": 1
		}, {
			duration: 1000,
			complete: callback,
			step: function(a, object){
				var now = new Date().getTime();
				var scale = $.easing.swing(null, now - object.startTime, 0, 1, object.options.duration);
				$(object.elem).css({
					'opacity': scale,
					'-o-transform': 'scale('+scale+')',
					'-moz-transform': 'scale('+scale+')',
					'-webkit-transform': 'scale('+scale+')'
				});
			}
		});
	};
	
	animationScaleBottom = function(currentIndex, destinationIndex, callback){
		$next.css({
			"background-image": 'url('+slides[destinationIndex].image+')',
			"-webkit-transform": "scale(0)",
			"-moz-transform": "scale(0)",
			"-o-transform": "scale(0)",
			"-webkit-transform-origin": "50% 100%",
			"-moz-transform-origin": "50% 100%",
			"-o-transform-origin": "50% 100%",
			"opacity": 0
		}).animate({
			"opacity": 1
		}, {
			duration: 1000,
			complete: callback,
			step: function(a, object){
				var now = new Date().getTime();
				var scale = $.easing.swing(null, now - object.startTime, 0, 1, object.options.duration);
				$(object.elem).css({
					'opacity': scale,
					'-o-transform': 'scale('+scale+')',
					'-moz-transform': 'scale('+scale+')',
					'-webkit-transform': 'scale('+scale+')'
				});
			}
		});
	};
	
	animationScaleRight = function(currentIndex, destinationIndex, callback){
		$next.css({
			"background-image": 'url('+slides[destinationIndex].image+')',
			"-webkit-transform": "scaleX(0)",
			"-moz-transform": "scaleX(0)",
			"-o-transform": "scaleX(0)",
			"-webkit-transform-origin": "100% 50%",
			"-moz-transform-origin": "100% 50%",
			"-o-transform-origin": "100% 50%",
			"opacity": 0
		}).animate({
			"opacity": 1
		}, {
			duration: 1000,
			complete: callback,
			step: function(a, object){
				var now = new Date().getTime();
				var scale = $.easing.swing(null, now - object.startTime, 0, 1, object.options.duration);
				$(object.elem).css({
					'opacity': scale,
					'-o-transform': 'scaleX('+scale+')',
					'-moz-transform': 'scaleX('+scale+')',
					'-webkit-transform': 'scaleX('+scale+')'
				});
			}
		});
	};
	
	animationScaleLeft = function(currentIndex, destinationIndex, callback){
		$next.css({
			"background-image": 'url('+slides[destinationIndex].image+')',
			"-webkit-transform": "scaleX(0)",
			"-moz-transform": "scaleX(0)",
			"-o-transform": "scaleX(0)",
			"-webkit-transform-origin": "0% 50%",
			"-moz-transform-origin": "0% 50%",
			"-o-transform-origin": "0% 50%",
			"opacity": 0
		}).animate({
			"opacity": 1
		}, {
			duration: 1000,
			complete: callback,
			step: function(a, object){
				var now = new Date().getTime();
				var scale = $.easing.swing(null, now - object.startTime, 0, 1, object.options.duration);
				$(object.elem).css({
					'opacity': scale,
					'-o-transform': 'scaleX('+scale+')',
					'-moz-transform': 'scaleX('+scale+')',
					'-webkit-transform': 'scaleX('+scale+')'
				});
			}
		});
	};
	
	animationSquaresRandom = function(currentIndex, destinationIndex, callback){
		$squares = $('.square', $next);
		$squares.css({
			'background-image': 'url('+slides[destinationIndex].image+')',
			'opacity': 0
		});
		
		$squares.each(function(){
			var square = this;
			$(square).stop().delay(Math.round(Math.random() * 500)).animate({
				'opacity': 1
			}, {
				duration: 600
			});
		});
		
		setTimeout(callback, 1550);
	};
	
	animationSquaresRows = function(currentIndex, destinationIndex, callback){
		$squares = $('.square', $next);
		$squares.css({
			'background-image': 'url('+slides[destinationIndex].image+')',
			'opacity': 0
		});
		
		$squares.each(function(i){
			var square = this;
			$(square).stop().delay(60 * i).animate({
				'opacity': 1
			}, {
				duration: 1000
			});
		});
		
		setTimeout(callback, 1550);
	};
	
	animationSquaresCols = function(currentIndex, destinationIndex, callback){
		$squares = $('.square', $next);
		$squares.css({
			'background-image': 'url('+slides[destinationIndex].image+')',
			'opacity': 0
		});
		
		var cols = Math.ceil(width / squareSize);
				
		$squares.each(function(i){
			var square = this;
			$(square).stop().delay(100 * (i % cols)).animate({
				'opacity': 1
			}, {
				duration: 600
			});
		});
		
		setTimeout(callback, 1550);
	};
	
	animationSquaresMoveOut = function(currentIndex, destinationIndex, callback){
		$squares = $('.square', $next);
		$next.css('background-image', 'url('+slides[destinationIndex].image+')');
		$squares.css({
			'background-image': 'url('+slides[currentIndex].image+')',
			'opacity': 1
		});
		
		var rows = Math.ceil(height / squareSize);
		var cols = Math.ceil(width / squareSize);
		var n = 0;
		for(y = 0; y < rows; y++){
			for(x = 0; x < cols; x++){
				$('#square'+n).stop().delay(100 * (x+y)).animate({
					'opacity': 0,
					'left': (x*squareSize - 20) +'px',
					'top': (y*squareSize - 20) +'px'
				}, 400);
				++n;
			}
		}
		
		setTimeout(callback, 1550);
	};
	
	animationSquaresMoveIn = function(currentIndex, destinationIndex, callback){
		$squares = $('.square', $next);
		$squares.css({
			'background-image': 'url('+slides[destinationIndex].image+')',
			'opacity': 0
		});
		
		resetSquares(-20, -20);
		
		var rows = Math.ceil(height / squareSize);
		var cols = Math.ceil(width / squareSize);
		var n = 0;
		for(y = 0; y < rows; y++){
			for(x = 0; x < cols; x++){
				$('#square'+n).stop().delay(100 * (rows - y + cols - x)).animate({
					'opacity': 1,
					'left': (x*squareSize) +'px',
					'top': (y*squareSize) +'px'
				}, 400);
				++n;
			}
		}
		
		setTimeout(callback, 1550);
	};
	
	animationColumnWave = function(currentIndex, destinationIndex, callback){
		$columns = $('.column', $next);
		$columns.css({
			'background-image': 'url('+slides[destinationIndex].image+')',
			opacity: 0,
			top: '-30px'
		});
		
		var cols = Math.ceil(width / columnWidth);
		for(i = 0; i < cols; i++){
			$('#column'+i).stop().delay(30 * i).animate({
				opacity: 1,
				top: '0px'
			}, {
				duration: 1000,
				specialEasing: {
					opacity: 'easeOutSine',
					top: 'easeOutElastic'
				}
			});
		}
		
		setTimeout(callback, 1550);
	};
	
	animationCurtainRight = function(currentIndex, destinationIndex, callback){
		$columns = $('.column', $next);
		$columns.css({
			'background-image': 'url('+slides[destinationIndex].image+')',
			opacity: 0,
			height: '0px'
		});
		
		var cols = Math.ceil(width / columnWidth);
		for(i = 0; i < cols; i++){
			$('#column'+i).stop().delay(50 * i).animate({
				opacity: 1,
				height: height+'px'
			}, {
				duration: 300,
				specialEasing: {
					opacity: 'easeInSine',
					height: 'linear'
				}
			});
		}
		
		setTimeout(callback, 1350);
	};
	
	animationCurtainLeft = function(currentIndex, destinationIndex, callback){
		$columns = $('.column', $next);
		$columns.css({
			'background-image': 'url('+slides[destinationIndex].image+')',
			opacity: 0,
			height: '0px'
		});
		
		var cols = Math.ceil(width / columnWidth);
		for(i = 0; i < cols; i++){
			$('#column'+i).stop().delay(50 * (cols - i)).animate({
				opacity: 1,
				height: height+'px'
			}, {
				duration: 300,
				specialEasing: {
					opacity: 'easeInSine',
					height: 'linear'
				}
			});
		}
		
		setTimeout(callback, 1350);
	};
	
	animationCurtainRotateRight = function(currentIndex, destinationIndex, callback){
		$columns = $('.column', $next);
		$columns.css({
			'background-image': 'url('+slides[destinationIndex].image+')',
			opacity: 0,
			width: '0px'
		});
		
		var cols = Math.ceil(width / columnWidth);
		for(i = 0; i < cols; i++){
			$('#column'+i).stop().delay(50 * i).animate({
				opacity: 1,
				width: columnWidth+'px'
			}, {
				duration: 300,
				specialEasing: {
					opacity: 'easeInSine',
					width: 'linear'
				}
			});
		}
		
		setTimeout(callback, 1550);
	};
	
	animationCurtainRotateLeft = function(currentIndex, destinationIndex, callback){
		$columns = $('.column', $next);
		$columns.css({
			'background-image': 'url('+slides[destinationIndex].image+')',
			opacity: 0,
			width: '0px'
		});
		
		var cols = Math.ceil(width / columnWidth);
		for(i = 0; i < cols; i++){
			$('#column'+i).stop().delay(50 * (cols - i)).animate({
				opacity: 1,
				width: columnWidth+'px'
			}, {
				duration: 300,
				specialEasing: {
					opacity: 'easeInSine',
					width: 'linear'
				}
			});
		}
		
		setTimeout(callback, 1550);
	};
	
	animationInterweaveLeft = function(currentIndex, destinationIndex, callback){
		$columns = $('.column', $next);
		$columns.css({
			'background-image': 'url('+slides[destinationIndex].image+')',
			opacity: 0
		});
		
		$('.column:even', $next).css('top', -height+'px');
		$('.column:odd', $next).css('top', height+'px');
		
		var cols = Math.ceil(width / columnWidth);
		for(var i = 0; i < cols; i++){
			$('#column'+i).stop().delay(50 * (cols - i)).animate({
				opacity: 1,
				top: '0px'
			}, {
				duration: 300,
				specialEasing: {
					opacity: 'easeInSine',
					top: 'easeOutExpo'
				}
			});
		}
		
		setTimeout(callback, 1550);
	};
	
	animationInterweaveRight = function(currentIndex, destinationIndex, callback){
		$columns = $('.column', $next);
		$columns.css({
			'background-image': 'url('+slides[destinationIndex].image+')',
			opacity: 0
		});
		
		$('.column:even', $next).css('top', -height+'px');
		$('.column:odd', $next).css('top', height+'px');
		
		var cols = Math.ceil(width / columnWidth);
		for(var i = 0; i < cols; i++){
			$('#column'+i).stop().delay(50 * i).animate({
				opacity: 1,
				top: '0px'
			}, {
				duration: 300,
				specialEasing: {
					opacity: 'easeInSine',
					top: 'easeOutExpo'
				}
			});
		}
		
		setTimeout(callback, 1550);
	};
});