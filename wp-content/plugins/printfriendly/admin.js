// Throughout the admin I chose to use slow animations to make it clear that stuff is being hidden or shown depending on settings.
jQuery(document).ready(function() {
	jQuery('.show_list').change(function() {
		if (jQuery('.show_list:checked').val()=='manual') {
			jQuery('#addmanual-help').show('slow');
			jQuery('.content_placement').hide('slow');
			jQuery('.content_placement input').attr('disabled','disabled');
		} else {
			jQuery('#addmanual-help').hide('slow');
			jQuery('.content_placement').show('slow');
			jQuery('.content_placement input').removeAttr('disabled');
		}
	});
	if (jQuery('.show_list:checked').val()=='manual') {
		jQuery('#addmanual-help').show('slow');
		jQuery('.content_placement').hide();
		jQuery('.content_placement input').attr('disabled','disabled');
	}
	
	jQuery('#colorSelector').ColorPicker({
		color: jQuery('#text_color').val(),
		onShow: function (colpkr) {
			jQuery(colpkr).fadeIn(500);
			return false;
		},
		onHide: function (colpkr) {
			jQuery(colpkr).fadeOut(500);
			return false;
		},
		onChange: function (hsb, hex, rgb) {
			jQuery('#colorSelector div').css('backgroundColor', '#' + hex);
			jQuery('#text_color').val('#' + hex);
			jQuery('.printfriendly-text').css('color','#' + hex)
		}
	});
	
	jQuery('#disable_css').change(function(){
		if ( jQuery('#disable_css').is(':checked') ) {
			jQuery('.css').show('slow');
			jQuery('.css input').removeAttr('disabled');
		} else {
			jQuery('.css').hide('slow');
			jQuery('.css input').attr('disabled','disabled');
		}
	});
	if ( !jQuery('#disable_css').is(':checked') ) {
		jQuery('.css').hide();
		jQuery('.css input').attr('disabled','disabled');
	}
	
	jQuery('#text_size').change(function(){
		size = jQuery('#text_size').val();
		jQuery('.printfriendly-text').css('font-size',parseInt(size));
	}).change();
	
	jQuery('#custom_text').change(function(){
		pf_custom_text_change();
	}).change();
	
	jQuery('#custom_text').keyup(function(){
		pf_custom_text_change();
	});
	
	function pf_custom_text_change(){
		jQuery('.button_preview span:not(.printandpdf)').text( jQuery('#custom_text').val() );
	}
	
	jQuery('.printfriendly-text').css('color', jQuery('#text_color').val() );
});