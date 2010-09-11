jQuery(function($){
	$("#uds-billboard-table").sortable({
		axis: 'y',
		containment: 'parent',
		handle: '.billboard-move',
		items: 'tr',
		cursor: 'crosshair'
	});
	
	$('.billboard-delete').click(function(){
		if(confirm("Do you really want to delete this entry?")){
			$(this).parents('tr').fadeOut(400, function(){
				$(this).remove();
			});
		}
		return false;
	});
	
	
	$('#uds-billboard-table tr:last').addClass('new').find('.billboard-delete').hide();
	
	// General Options collapsing
	$('.uds-billboard-options').data('original_height', $('.uds-billboard-options').css('height'));
	$('.uds-billboard-options .close').click(function(e, no_cookie){
		if($(this).hasClass('closed')){
			$('.uds-billboard-options .inside').fadeIn(200);
			$('.uds-billboard-options').animate({
				height: $('.uds-billboard-options').data('original_height')
			}, 200);
			$('.uds-billboard-options .submit').animate({
				top: '250px',
				left: '40px'
			}, 200);
			$(this).removeClass('closed');
			if(no_cookie != true){
				$.cookie('uds-billboard-general-options-collapsed', null);
			}
		} else {
			$('.uds-billboard-options').animate({
				height: '30px'
			}, 200);
			$('.uds-billboard-options .submit').animate({
				top: '-3px',
				left: '0px'
			}, 200);
			$('.uds-billboard-options .inside').fadeOut(200);
			$(this).addClass('closed');
			if(no_cookie != true){
				$.cookie('uds-billboard-general-options-collapsed', 'yes');
			}
		}
	}).trigger('click', true);
	
	if($.cookie('uds-billboard-general-options-collapsed') != 'yes') {
		$('.uds-billboard-options .close').trigger('click', true);
	}
	
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
			left: $(this).position().left + 40 + 'px',
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