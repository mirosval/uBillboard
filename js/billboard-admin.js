jQuery(function($){
	// admin billboard delete
	$('#billboard_update_form .deletion').click(function(){
		return confirm(udsAdminL10n.billboardDeleteConfirmation);
	});
	
	// Delete from the list page
	$('.trash a').click(function(){
		return confirm(udsAdminL10n.billboardDeleteConfirmation);
	});
	
	// slides delete
	$('.slide .deletediv').click(function(){
		if(confirm(udsAdminL10n.slideDeleteConfirmation)) {
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
			cookie: {
				expire: 1
			},
			show: function(event, ui){
				var $image = $(ui.tab).parents('.slide').find('.image-wrapper');
				$image.css('height', $image.parent().height() + 'px');
			}
		});
	}
	
	createTabs();
	
	function showImageAdder(offset, element) {
		var $preview = $(element).parents('.slide').find('.image-wrapper');
		var $image = $(element).prev();
		var postsPerPage = 5;
		
		if($('#uds-dialog').length === 0) {
			$('<div id="uds-dialog" title="'+udsAdminL10n.addAnImage+'">').appendTo('body');
		}
		
		$dialog = $('#uds-dialog');
		
		$.get(ajaxurl, {
			action: 'uds_billboard_list_images',
			offset: offset
		}, function(data) {
			$dialog.html(data);
			$dialog.dialog({
				modal: true,
				width: 450,
				minHeight: 500
			});
			
			$('.uds-image-select').click(function(){
				var src = $(this).find('input.uds-image').val();
				$preview.css('background-image', 'url('+src+')');
				$image.val(src);
				$dialog.dialog('close');
			});
			
			$('.uds-paginate a').click(function(){
				var page = $(this).text();
				
				showImageAdder(postsPerPage * (page - 1), element);
				
				return false;
			});
		});
	}
	
	// image upload dialog
	$('.image-upload').live('click', function() {
		showImageAdder(0, this);
		
		return false;
	});
	
	// Update slide fields/IDs after changing/adding/sorting/deleting slides
	function resetSlides() {
		$('.uds-slides-order li').remove();
		$('.slides .slide').each(function(i, el){
			$(this).attr('id', 'uds-slide-'+i);
			$(this).find('.hndle span').text(udsAdminL10n.slideN.replace('%s', (i + 1)));
			$('.uds-slides-order').append('<li id="uds-slide-handle-'+i+'" class="uds-slide-handle">' + udsAdminL10n.slideN.replace('%s', (i + 1)) + '</li>');
		});
		
		$('.uds-slides-order').sortable('refresh');
		createTabs();
	}
	
	// Slide Cloning
	var slideClone = $('#normal-sortables .slides .slide:last').clone();
	$('.slide .adddiv').live('click', function(){
		$(this).parents('.slide').after(slideClone.clone());
		resetSlides();
	});
	
	// slide Deleting
	$('.slide .deletediv').live('click', function(){
		$(this).parents('.slide').remove();
		resetSlides();
	});
	
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

			for(var i = 0; i < order.length; i++) {
				var slide = $('#uds-slide-'+order[i]).detach();
				$('.uds-billboard-form .slides').append(slide);
			}
			
			resetSlides();
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
	return;
		tinyMCE.init({
		    theme : 'advanced',
		    mode: 'exact',
		    elements : $(this).attr('id'),
		    plugins: 'udsDescription,udsEmbed',
		    theme_advanced_toolbar_location : 'top',
		    theme_advanced_buttons1 : 'bold,italic,separator,'
		    + 'justifyleft,justifycenter,justifyright,justifyfull,formatselect,'
		    + 'bullist,numlist',
		    theme_advanced_buttons2 : 'link,unlink,separator,'
		    +'undo,redo,cleanup,code,separator,charmap,separator,udsDescription,udsEmbed',
		    theme_advanced_buttons3 : '',
		    theme_advanced_resizing : true, 
		    theme_advanced_statusbar_location : 'bottom',
		    width : '100%'
		});
		//tinyMCE.execCommand('mceRemoveControl', false, $(this).attr('id'));
	});
	
	// Admin preview
	var adminPreview = function(){
		var originalName = $('#title').val();
		
		$('#title').val('_uds_temp_billboard');
		
		var form = $('#billboard_update_form').serialize() + '&action=uds_billboard_update';
		var url = $(this).attr('href');
		
		$('#title').val(originalName);
		
		$.post(ajaxurl, form, function(data) {
			$dialog = $('<div id="uds-preview-dialog" title="' + udsAdminL10n.billboardPreview + '">').appendTo('body');
			
			$dialog.html("<iframe src='" + url + "' width='100%' height='100%'></iframe>");
			
			$dialog.dialog({
				modal: true,
				width: parseInt($('#uds-billboard-width').val(), 10) + 130,
				height: parseInt($('#uds-billboard-height').val(), 10) + 200
			});
		});

		return false;
	}
	
	$('a.preview').click(adminPreview);

	if(window.location.hash === '#preview') {
		$('a.preview').trigger('click');
	}
		
	// Content editor
	$('.content-editor').hide();
	$('.uds-content-editor').live('click', function(){
		$dialog = $('.content-editor', $(this).parent()).clone().appendTo('body');
		$content = $(this).parent().find('textarea');
		
		var width =  parseInt($('#uds-billboard-width').val(), 10),
			height = parseInt($('#uds-billboard-height').val(), 10),
			image =  $(this).parents('.inside').find('.image-wrapper').css('background-image');
		
		$editor = $('.editor-area', $dialog).css({
			width: width + 'px',
			height: height + 'px',
			backgroundColor: 'gray',
			backgroundImage: image,
			backgroundPosition: 'center center',
			backgroundRepeat: 'no-repeat'
		});
		
		function setDraggableAndResizable(el) {
			$(el).draggable({
				container: $editor
			});
			
			$(el).resizable({
				container: $editor
			});
		}
		
		function focusHandler() {
			$('.editable-box', $editor).removeClass('focused');
			$editableBox = $(this).parents('.editable-box');
			$editableBox.addClass('focused');

			$('.box-skin', $dialog).val($editableBox.data('skin'));
		}
		
		$('.editable-box textarea').focus(focusHandler);
		
		$('.editable-box', $dialog).each(function(){
			$(this).css('position', 'absolute');
			setDraggableAndResizable(this);
		});
		
		$('.box-skin', $dialog).change(function(){
			$('.editable-box.focused').data('skin', $(this).val());
		});
			
		function createEditorArea() {
			var $editorArea = $("<div class='editable-box'><textarea></textarea></div>");
			$editor.append($editorArea);
			setDraggableAndResizable($editorArea);
			$editorArea.find('textarea').focus(focusHandler).focus();
			return $editorArea;
		}
		
		$('input.save', $dialog).click(function(){
			$content.val('');
			$('.editable-box', $dialog).each(function(){
				$(this).draggable('destroy');
				$(this).resizable('destroy');
				var val = $content.val(),
					width = 'width="'+$(this).width()+'px"',
					height = ' height="'+$(this).height()+'px"',
					top = ' top="'+parseInt($(this).css('top'), 10)+'px"',
					left = ' left="'+parseInt($(this).css('left'), 10)+'px"',
					content = $(this).find('textarea').val(),
					skin = ' skin="'+$(this).data('skin')+'"';
				console.log(skin);
				if(content != '') {
					$content.val(val + '[uds-description '+width+height+top+left+skin+']' + content + '[/uds-description] ');
				}
				$dialog.dialog('close');
			});
		});
		
		$addButton = $('span.add', $dialog);
		$addButton.click(createEditorArea);
		
		$removeButton = $('span.remove', $dialog);
		$removeButton.click(function(){
			$('.editable-box.focused', $editor).draggable('destroy').resizable('destroy').remove();
			
			return false;
		});
		
		$dialog.dialog({
			width: width + 30,
			height: height + 130,
			modal: true
		});
		
		return false;
	});
});