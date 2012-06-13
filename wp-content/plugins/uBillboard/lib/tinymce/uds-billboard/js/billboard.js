var udsBillboard = {
	init : function(ed) {
		var dom = ed.dom, f = document.forms[0], n = ed.selection.getNode(), w;
		
		$.post(
			window.parent.ajaxurl,
			{
				action: 'uds_billboard_list'
			},
			function(data) {
				$('#billboard').html(data);
			}
		);
		
		//f.text.value = ed.selection.getContent();
	},

	update : function() {		
		var ed = tinyMCEPopup.editor, box, f = document.forms[0], st = '';
		
		var content = ed.selection.getContent();
		
		var box,
			billboard = '',
			width = '',
			height = '';

		if(f.billboard.value != '') {
			billboard = 'name="' + f.billboard.value + '" ';
		}
		/*
		if(f.width.value != '') {
			width = 'width="' + f.width.value + 'px" ';
		}
		
		if(f.height.value != '') {
			height = 'height="' + f.height.value + 'px" ';
		}*/
		
		box = '[uds-billboard '+billboard+width+height+']';

		ed.execCommand("mceInsertContent", false, box);
		tinyMCEPopup.close();
	}
};

tinyMCEPopup.requireLangPack();
tinyMCEPopup.onInit.add(udsBillboard.init, udsBillboard);
