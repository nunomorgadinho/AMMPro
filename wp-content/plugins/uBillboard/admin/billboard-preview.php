<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php do_action('admin_xml_ns'); ?> <?php language_attributes(); ?>>
<head>
	<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php echo get_option('blog_charset'); ?>" />
	<title><?php _e('uBillboard Preview', uds_billboard_textdomain) ?></title>
	<script type="text/javascript">
	//<![CDATA[
	var ajaxurl = '<?php echo admin_url() ?>admin-ajax.php',
		pagenow = 'ubillboard_page_uds_billboard_edit',
		typenow = '',
		adminpage = 'ubillboard_page_uds_billboard_edit',
		thousandsSeparator = ',',
		decimalPoint = '.',
		isRtl = 0; 
	//]]>
	</script>
	<?php wp_head() ?>
</head>
<body>
	<div id="preview-wrapper">
		<?php the_uds_billboard('_uds_temp_billboard') ?>
	</div>
	<?php wp_footer() ?>
</body>
</html>
<?php exit; ?>