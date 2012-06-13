var udsBox = {
	init : function(ed) {
		var dom = ed.dom, f = document.forms[0], n = ed.selection.getNode(), w;
		
		f.text.value = ed.selection.getContent();
	},

	update : function() {
		var ed = tinyMCEPopup.editor, box, f = document.forms[0], st = '';

		var content = ed.selection.getContent();
		
		var box,
			skin = '',
			width = '',
			height = '',
			top = '',
			left = ''
			text = content;

		if(f.skin.value != '') {
			skin = 'skin="' + f.skin.value + '" ';
		}

		if(f.width.value != '') {
			width = 'width="' + f.width.value + 'px" ';
		}
		
		if(f.height.value != '') {
			height = 'height="' + f.height.value + 'px" ';
		}
		
		if(f.top.value != '') {
			top = 'top="' + f.top.value + 'px" ';
		}
		
		if(f.left.value != '') {
			left = 'left="' + f.left.value + 'px"';
		}
		
		if(f.text.value != content) {
			text = f.text.value;
		}
		
		box = '[uds-description '+skin+width+height+top+left+']' + text + '[/uds-description]';

		ed.execCommand("mceInsertContent", false, box);
		tinyMCEPopup.close();
	}
};

tinyMCEPopup.requireLangPack();
tinyMCEPopup.onInit.add(udsBox.init, udsBox);
