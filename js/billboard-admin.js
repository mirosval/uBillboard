jQuery(function($){
	// admin billboard delete
	$('.uds-billboard-admin-table .billboard-delete').click(function(){
		return confirm("Really delete? This is not undoable");
	});
	
	
	// Billboard image collapsing
	$('<div id="image-preview">').appendTo('body');
	$('.image-wrapper').each(function(){
		var $input = $(this).parents('.inside').find('.image-url-wrapper input');
		var preview = this;
		$(this).css({
			'background-image': 'url('+$input.val()+')',
			height: $(this).parent().height() + 'px'
		});
		
		$input.change(function(){
			console.log($input.val());
			$(preview).css({
				'background-image': 'url('+$input.val()+')'
			});
		});
		
		$(this).hover(function(e){
			$('#image-preview').show().css({
				'background-image': 'url('+$input.val()+')',
				width: $('#uds-billboard-width').val()+'px',
				height: $('#uds-billboard-height').val()+'px',
				top: $(preview).offset().top,
				left: $(preview).offset().left,
				position: 'absolute',
				opacity: 0
			}).stop().animate({opacity: 1}, 300);
		}, function(){
			$('#image-preview').css({
				opacity: 1
			}).stop().animate({opacity: 0}, {
				duration: 300,
				complete: function() {
					$(this).hide();
				}
			});
		});
	});
	
	// Slides Reorder
	$('.uds-slides-order').sortable({
		axis: 'y',
		placeholder: 'uds-slide-placeholder',
		update: function(event, ui) {
			var order = [];
			$('.uds-slides-order li').each(function(){
				order.push(parseInt($(this).attr('id').replace('uds-slide-handle-', ''), 10));
			});

			console.log(order);

			for(var i = 0; i < order.length; i++) {
				var slide = $('#uds-slide-'+order[i]).detach();
				console.log(slide);
				$('.uds-billboard-form .slides').append(slide);
			}
		}
	}).trigger('click', true);
	
	if($.cookie('uds-billboard-general-options-collapsed') != 'yes') {
		$('.uds-billboard-options .close').trigger('click', true);
	}
	
	// Timthumb options fading
	var timthumbHandler = function(){
		if($('#uds-billboard-use-timthumb').is(':checked')){
			if(typeof jQuery.prop === 'function'){
				$('#uds-billboard-timthumb-zoom,#uds-billboard-timthumb-quality').prop('disabled', false);
			} else {
				$('#uds-billboard-timthumb-zoom,#uds-billboard-timthumb-quality').removeAttr('disabled');
			}
			
			$('.uds-billboard-timthumb-zoom label,.uds-billboard-timthumb-quality label').css('opacity', 1);
		} else {
			if(typeof jQuery.prop === 'function'){
				$('#uds-billboard-timthumb-zoom,#uds-billboard-timthumb-quality').prop('disabled', true);
			} else {
				$('#uds-billboard-timthumb-zoom,#uds-billboard-timthumb-quality').attr('disabled', 'disabled');
			}
			
			$('.uds-billboard-timthumb-zoom label,.uds-billboard-timthumb-quality label').css('opacity', 0.6);
		}
	}
	timthumbHandler();
	$('#uds-billboard-use-timthumb').change(timthumbHandler);
	
	// Tooltips
	$('.option-container label').hover(function(){
		$tt = $(this).parent().find('.tooltip-content');
		$tt.stop().css({
			display: 'block',
			top: $(this).position().top + 30 + 'px',
			left: $(this).position().left + 'px',
			opacity: 0
		}).animate({
			opacity: 1
		}, 300);
	}, function(){
		$tt = $(this).parent().find('.tooltip-content');
		$tt.stop().css({
			opacity: 1
		}).animate({
			opacity: 0
		}, {
			duration: 300,
			complete: function(){
				$(this).css('display', 'none');
			}
		});
	});
});