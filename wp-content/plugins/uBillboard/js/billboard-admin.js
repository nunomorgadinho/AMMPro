jQuery(function($){
	// admin list bulk delete question
	$('.uds-bulk-actions-form').submit(function(){
		if($('.uds-bulk-actions').val() == 'delete') {
			return confirm(udsAdminL10n.bulkActionsDelete);
		} else {
			return true;
		}
	});
	
	// admin billboard delete
	$('#billboard_update_form .deletion').click(function(){
		return confirm(udsAdminL10n.billboardDeleteConfirmation);
	});
	
	// Delete from the list page
	$('.trash a').click(function(){
		return confirm(udsAdminL10n.billboardDeleteConfirmation);
	});
	
	// Boxes closing
	$('.handlediv').live('click', function(){
		$(this).parent().toggleClass('closed');
	});
	
	// Force thumb regeneration on the next billboard save
	function markForThumbRegeneration() {
		$('.uds-regenerate-marker').val(1);
	}
	
	// Observe changes and ask user if he wants to save
	function markDirty(dirty) {
		if(dirty) {
			$('#billboard_update_form').addClass('dirty');
		} else {
			$('#billboard_update_form').removeClass('dirty');
		}
	}
	
	$('input, textarea', '#billboard_update_form').live('change', function(){
		markDirty(true);
	});
	
	// Ask user for confirmation before leaving
	$(window).bind('beforeunload', function(e){
		var e = e || window.event;
		
		if($('#billboard_update_form').is('.dirty')) {
			var warning = udsAdminL10n.pageLeaveConfirmation;
			e.returnValue = warning;
			return warning;
		}
		
		return null;
	});
	
	// Before uBillboard submit
	$('#billboard_update_form').submit(function(){
		// check if not saving an empty slider
		if($('.slides .slide').length < 2) {
			var hasImage = $('.image-url-wrapper:first>input').val() !== '';
			var hasContent = $('.billboard-content:first').val() === 'editor' && $('.billboard-text:first').val() !== '';
			var hasEmbeddedContent = $('.billboard-content:first').val() === 'embed' && $('.billboard-embed-url:first').val() !== '';
			var hasBlogContent = $('.billboard-content:first').val() === 'dynamic';
			
			if(!hasImage && !hasContent && !hasEmbeddedContent && !hasBlogContent) {
				alert(udsAdminL10n.saveEmptyBillboard);
				return false;
			}
		}
		
		markDirty(false);
		
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

				if($image.css('background-image') !== 'none') {
					$image.css('height', $image.parents('.slide').height() + 'px');
				}
			}
		});
	}
	
	createTabs();
	
	function showImageAdder(offset, element) {
		tb_show('Add Image', "media-upload.php?type=image&TB_iframe=true&width=640&height=500");

		var $preview = $(element).parents('.slide').find('.image-wrapper');
		var $image = $(element).prev();
		
		// Fix jQuery UI Tabs + Thickbox tabs not working after thickox close (Fuck You Thickbox :( )
		window.tb_remove = function() {
			jQuery("#TB_imageOff").unbind("click");
			jQuery("#TB_closeWindowButton").unbind("click");
			//jQuery("#TB_window").fadeOut("fast",function(){jQuery('#TB_window,#TB_overlay,#TB_HideSelect').trigger("unload").unbind().remove();});
			jQuery("#TB_window").fadeOut("fast",function(){jQuery('#TB_window,#TB_overlay,#TB_HideSelect').unload("#TB_ajaxContent").unbind().remove();});
			jQuery("#TB_load").remove();
			if (typeof document.body.style.maxHeight == "undefined") {//if IE 6
				jQuery("body","html").css({height: "auto", width: "auto"});
				jQuery("html").css("overflow","");
			}
			jQuery(document).unbind('.thickbox');
			return false;
		}
		
		window.send_to_editor = function(img) {
			// remove hash, in order to not switch to selected tab after save
			window.location.hash = '';
			
			tb_remove();
			
			markDirty(true);
			
			if($(img).is('a')){ // work around Link URL supplied
				var src = $(img).find('img').attr('src');
			} else {
				var src = $(img).attr('src');
			}
			
			$preview.css('background-image', 'url('+src+')');
			$image.val(src);
			
			$preview.css('height', $preview.parents('.slide').height() + 'px');
			
			$('.uds-slide-tabs').tabs('destroy');
			createTabs();
			markForThumbRegeneration();
		}
	}
	
	// image upload dialog
	$('.image-upload').live('click', function(e) {
		e.preventDefault();
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
		contentType();
		markForThumbRegeneration();
	}
	
	// Slide Cloning
	var slideClone = $('#normal-sortables .slides .slide:last').clone();
	$('.slide .adddiv').live('click', function(){
		$(this).parents('.slide').after(slideClone.clone());
		resetSlides();
	});
	
	// Add New Slide Button
	$('#uds-add-slide').click(function(){
		$('.slides').append(slideClone.clone());
		resetSlides();
	});
	
	// slide Deleting
	$('.slide .deletediv').live('click', function(){
		if(confirm(udsAdminL10n.slideDeleteConfirmation)) {
			$(this).parents('.slide').remove();
			markDirty(true);
			resetSlides();
		}
	});
	
	// Slide Sortable
	$('.slides').sortable({
		axis: 'y',
		handle: '.hndle',
		placeholder: 'ui-state-highlight',
		forcePlaceholderSize: true,
		items: '>div',
		opacity: 0.8,
		update: function() {
			markDirty(true);
			resetSlides();
		}
	});
	
	// Billboard image collapsing
	$('<div id="image-preview">').appendTo('body');
	$('.image-wrapper').each(function(el, i){
		var $input = $(this).parents('.inside').find('.image-url-wrapper input');
		var preview = this;
		var totalSlides = $('.slide').length;
		
		if($input.val() !== '') {
			$(this).css({
				'background-image': 'url('+$input.val()+')',
				height: $(this).parent().height() + 'px'
			});
		}
		
		$input.change(function(){
			$(preview).css({
				'background-image': 'url('+$input.val()+')'
			});
			markForThumbRegeneration();
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
			
			markDirty(true);
			resetSlides();
		}
	});
	
	// Tooltips
	$('.option-container label').hover(function(){
		$tt = $(this).parent().find('.tooltip-content');
		$tt.stop().css({
			display: 'block',
			top: $(this).position().top + 30 + 'px',
			left: $(this).position().left - 10 + 'px',
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
	
	// handle content type switching
	function contentType(){
		$('.uds-slide-tab-content').each(function(i, el){
			var $tab = $(el);
			$('.billboard-content', $tab).change(function(){
				$('>div:not(.content-wrapper)', $tab).hide();
				switch($(this).val()) {
					case 'editor':
						$('.text-wrapper,.text-evaluation-wrapper', $tab).show();
						break;
					case 'embed':
						$('.embed-url-wrapper', $tab).show();
						break;
					case 'dynamic':
						$('.dynamic-offset-wrapper', $tab).show();
						$('.dynamic-category-wrapper', $tab).show();
						break;
					case 'none':
					default:
				}
			}).change();
		});
	}
	
	contentType();
	
	// Content editor
	$('.content-editor').hide();
	$('.uds-content-editor').live('click', function(){
		$contentEditor = $('.content-editor', $(this).parent());
		$dialog = $('.content-editor', $(this).parent()).clone().appendTo('body');
		$content = $(this).parent().find('textarea');
		
		var width =  parseInt($('#uds-billboard-width').val(), 10),
			height = parseInt($('#uds-billboard-height').val(), 10),
			image =  $(this).parents('.inside').find('.image-wrapper').css('background-image');
		
		$editor = $('.editor-area', $dialog).css({
			width: width + 'px',
			height: height + 5 + 'px',
			backgroundColor: 'gray',
			backgroundImage: image,
			backgroundPosition: 'center center',
			backgroundRepeat: 'no-repeat'
		});
		
		$dialog.dialog({
			width: width + 30,
			height: height + 130,
			modal: true
		});
		
		function setDraggableAndResizable(el) {
			$(el).draggable({
				containment: $editor
			});
			
			$(el).resizable({
				autoHide: true,
				containment: $editor,
				handles: 'all'
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
		
		$('.uds-save-content', $dialog).click(function(){
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
					
				if(content != '') {
					$content.val(val + '[uds-description '+width+height+top+left+skin+']' + content + '[/uds-description] ');
				}
				$contentEditor.html($dialog.html());
				$dialog.dialog('close');
				markDirty(true);
			});
		});
		
		$addButton = $('.uds-add-box', $dialog).blur();
		$addButton.click(createEditorArea);
		
		$removeButton = $('.uds-remove-box', $dialog);
		$removeButton.click(function(){
			$('.editable-box.focused', $editor).draggable('destroy').resizable('destroy').remove();
			
			return false;
		});
		
		$('.content-editor-help').live('click', function(){
			$.get(ajaxurl, {
				action: 'uds_billboard_content_editor_help'
			}, function(data){
				if($('#uds-contextual-help').length === 0) {
					$('<div id="uds-contextual-help">').appendTo('body');
				}
				
				$('#uds-contextual-help').html(data).dialog({
					width: 800,
					height: 600
				});
			});
			return false;
		});
		
		return false;
	});
});