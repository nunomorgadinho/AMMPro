jQuery.noConflict();
jQuery(document).ready(function(jQuery) {
    jQuery("a[rel^='prettyPhoto']").prettyPhoto({
		animationSpeed: 'normal',
		padding: 40,
		opacity: 0.5,
		showTitle: false,
		allowresize: true,
		counter_separator_label: ' of ',
		theme: 'light_rounded'
	});
});