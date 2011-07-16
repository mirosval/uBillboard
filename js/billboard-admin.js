jQuery(function($){
	// admin billboard delete
	$('#billboard_update_form .deletion').click(function(){
		return confirm("Really delete? This is not undoable");
	});
	
	// slides delete
	$('.slide .deletediv').click(function(){
		if(confirm("Really delete slide?")) {
			$(this).parents('.slide').remove();
		}
	});
	
	// Boxes closing
	$('.handlediv').click(function(){
		$(this).parent().toggleClass('closed');
	});
	
	// Before uBillboard submit
	$('#billboard_update_form').submit(function(){
		// remove all hidden fields from before checked checkboxes
		$('.slides input:checked').each(function(){
			$(this).prev().remove();
		});
	});
	
	// Slide Tabs
	function createTabs() {
		$('.uds-slide-tabs').tabs({
			show: function(event, ui){
				var $image = $(ui.tab).parents('.slide').find('.image-wrapper');
				$image.css('height', $image.parent().height() + 'px');
			}
		});
	}
	
	createTabs();
	
	// Billboard image collapsing
	$('<div id="image-preview">').appendTo('body');
	$('.image-wrapper').each(function(el, i){
		var $input = $(this).parents('.inside').find('.image-url-wrapper input');
		var preview = this;
		var totalSlides = $('.slide').length;
		
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
			$('.image-wrapper').css({
				zIndex: 1
			});
			
			$(this).css({
				zIndex: 10
			});
			
			$('#image-preview').show().css({
				'background-image': 'url('+$input.val()+')',
				width: $('#uds-billboard-width').val()+'px',
				height: $('#uds-billboard-height').val()+'px',
				top: $(preview).offset().top,
				left: $(preview).offset().left,
				position: 'absolute',
				opacity: 0,
				zIndex: 9
			}).stop().animate({opacity: 1}, 300);
		}, function(){
			var $el = $(this);
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
	});
	
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
	
	// tinymce
	//tinyMCE.PluginManager.load("udsDescription", "http://meosoft/code/plugins/wp-content/plugins/uBillboard/lib/tinymce/uds-description/editor_plugin.js");
	
	$('.billboard-text').each(function() {
		tinyMCE.init({
		    theme : 'advanced',
		    mode: 'exact',
		    elements : $(this).attr('id'),
		    plugins: 'udsDescription,udsEmbed',
		    theme_advanced_toolbar_location : 'top',
		    theme_advanced_buttons1 : 'bold,italic,underline,strikethrough,separator,'
		    + 'justifyleft,justifycenter,justifyright,justifyfull,formatselect,'
		    + 'bullist,numlist',
		    theme_advanced_buttons2 : 'link,unlink,image,separator,'
		    +'undo,redo,cleanup,code,separator,sub,sup,charmap,outdent,indent,separator,udsDescription,udsEmbed',
		    theme_advanced_buttons3 : '',
		    theme_advanced_resizing : true, 
		    theme_advanced_statusbar_location : 'bottom',
		    width : '100%'
		});
	});
	
	// Admin preview
	var adminPreview = function(){
		var url = $(this).attr('href');
		
		if(typeof tb_show === 'function') {
			// save uBillboard
			var originalName = $('#title').val();
			$('#title').val('_uds_temp_billboard');
			var form = $('#billboard_update_form').serialize() + '&action=uds_billboard_update';
			$('#title').val(originalName);
			
			$.post(ajaxurl, form, function(data) {
				if(data !== 'OK') {
					console.log('Failed to save uBillboard');
					return;
				}
				// Show Thickbox
				
				var originalRemove = window.tb_remove;
				window.tb_remove = function() {
					console.log(123);
					originalRemove();
					createTabs();
				};
				
				var width = parseInt($('#uds-billboard-width').val(), 10) + 130;
				var height = parseInt($('#uds-billboard-height').val(), 10) + 60;
				tb_show('uBillboard Preview', url + '&width='+width+'&height='+height);
				$('#TB_window').addClass('uds-preview');
			});
		} else {
			alert('Thickbox not loaded!');
		}
		
		return false;
	}
	
	$('a.preview').click(adminPreview);

	if(window.location.hash === '#preview') {
		$('a.preview').trigger('click');
	}
});