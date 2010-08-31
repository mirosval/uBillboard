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
});