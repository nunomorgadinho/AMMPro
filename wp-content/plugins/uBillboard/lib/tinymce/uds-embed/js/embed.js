var udsEmbed = {
	init : function(ed) {
		var dom = ed.dom, f = document.forms[0], n = ed.selection.getNode(), w;
		
		//f.text.value = ed.selection.getContent();
	},

	update : function() {
		var ed = tinyMCEPopup.editor, box, f = document.forms[0], st = '';

		var content = ed.selection.getContent();
		
		var box,
			url = '',
			width = '',
			height = '';

		if(f.url.value != '') {
			url = 'url="' + f.url.value + '" ';
		}

		if(f.width.value != '') {
			width = 'width="' + f.width.value + 'px" ';
		}
		
		if(f.height.value != '') {
			height = 'height="' + f.height.value + 'px" ';
		}
		
		box = '[uds-embed '+url+width+height+']';

		ed.execCommand("mceInsertContent", false, box);
		tinyMCEPopup.close();
	}
};

tinyMCEPopup.requireLangPack();
tinyMCEPopup.onInit.add(udsEmbed.init, udsEmbed);
