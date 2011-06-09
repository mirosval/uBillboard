jQuery(function($){
	// admin billboard delete
	$('.uds-billboard-admin-table .billboard-delete').click(function(){
		return confirm("Really delete? This is not undoable");
	});
	
	$("#uds-billboard-table").sortable({
		axis: 'y',
		containment: 'parent',
		handle: '.billboard-move',
		items: 'tr',
		cursor: 'crosshair'
	});
	
	$('#uds-billboard-table .billboard-delete').click(function(){
		if(confirm("Do you really want to delete this entry?")){
			$(this).parents('tr').fadeOut(400, function(){
				$(this).remove();
			});
		}
		return false;
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
	
	// Timthumb options fading
	var timthumbHandler = function(){
		if($('#uds-billboard-use-timthumb').is(':checked')){
			$('#uds-billboard-timthumb-zoom,#uds-billboard-timthumb-quality').attr('disabled', '');
			$('.uds-billboard-timthumb-zoom label,.uds-billboard-timthumb-quality label').css('opacity', 1);
		} else {
			$('#uds-billboard-timthumb-zoom,#uds-billboard-timthumb-quality').attr('disabled', 'disabled');
			$('.uds-billboard-timthumb-zoom label,.uds-billboard-timthumb-quality label').css('opacity', 0.6);
		}
	}
	timthumbHandler();
	$('#uds-billboard-use-timthumb').change(timthumbHandler);
	
	// Tooltips
	$('.tooltip').hover(function(){
		$tt = $(this).parent().find('.tooltip-content');
		$tt.stop().css({
			display: 'block',
			top: $(this).position().top,
			left: $(this).position().left - $tt.width() - 25 + 'px',
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