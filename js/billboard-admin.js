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
	
	$('.uds-billboard-options').data('original_height', $('.uds-billboard-options').css('height'));
	$('.uds-billboard-options .close').click(function(e, no_cookie){
		if($(this).hasClass('closed')){
			$('.uds-billboard-options .inside').fadeIn(200);
			$('.uds-billboard-options').animate({
				height: $('.uds-billboard-options').data('original_height')
			}, 200);
			$('.uds-billboard-options .submit').animate({
				top: '120px',
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
	
	$('#uds-billboard-table tr:last').addClass('new').find('.billboard-delete').hide();
});