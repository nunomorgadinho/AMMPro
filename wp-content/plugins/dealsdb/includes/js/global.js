jQuery.fn.fadeToggle = function(speed, easing, callback) {return this.animate({opacity: 'toggle'}, speed, easing, callback);};

jQuery(document).ready(function(){
	jQuery('a#ad-toggle').click(function() {jQuery('#formbox').fadeToggle("slow"); return false;});	
	jQuery('a#email-toggle').click(function() {jQuery('#email_form_data').fadeToggle("slow"); return false;});
	jQuery("img.size-thumbnail").parent().fancybox({"hideOnContentClick":true,"overlayShow":true,"overlayOpacity":.5,"zoomSpeedIn":300,"zoomSpeedOut":300});
});
