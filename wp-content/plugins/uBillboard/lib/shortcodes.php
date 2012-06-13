<?php

add_shortcode('uds-billboard', 'uds_billboard_shortcode');
function uds_billboard_shortcode($atts, $content = null)
{	
	extract(shortcode_atts(array(
		'name' => 'billboard'
	), $atts));

	return get_uds_billboard($name);
}

add_shortcode('uds-description', 'uds_billboard_description');
function uds_billboard_description($atts, $content = null)
{
	global $uds_description_mode, $uds_billboard_text_evaluation;
	extract(shortcode_atts(array(
		'top' => '20px',
		'left' => '20px',
		'width' => '200px',
		'height' => '80%',
		'skin' => ''
	), $atts));

	if(isset($uds_description_mode) && $uds_description_mode == 'editor') {
		$out = "<div class='editable-box' data-skin='$skin' style='top:$top;left:$left;width:$width;height:$height;'><textarea>$content</textarea></div>";
	} else {
		if($uds_billboard_text_evaluation == 'textile') {
			$textile = new Textile();
			$content = $textile->TextileRestricted($content, '');
		}
		
		if(!empty($skin)) $skin = 'uds-' . $skin;
		$out = "<div class='uds-bb-description $skin' style='top:$top;left:$left;width:$width;height:$height;'><div class='uds-bb-description-inside'>$content</div></div>";
	}
	
	return $out;
}

?>