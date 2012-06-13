<?php

/**
 *	Function, oEmbed handler, will query the service
 *	and return the embed code
 *
 *	@param string $url URL address of the content to embed
 *	@param string|int $width max width of the frame for the content
 *	@param string|int $height max height of the frame for the content
 *	
 *	@return string error or the embed code
 */
function uds_billboard_oembed($url, $width, $height)
{
	$services = array(
		'youtu.be' => 'http://www.youtube.com/oembed?',
		'youtube.com' => 'http://www.youtube.com/oembed?',
		'vimeo.com' => 'http://vimeo.com/api/oembed.json?'
	);
	
	$oembed = '';
	foreach($services as $pattern => $endpoint) {
		if(strpos($url, $pattern) !== false) {
			$oembed = 	$endpoint . 
						'url='.urlencode($url).
						"&maxwidth=$width" .
						"&maxheight=$height" .
						"&width=$width" .
						"&height=$height" .
						"&format=json" .
						"&wmode=opaque";
		}	
	}
	
	if(empty($oembed)) {
		return __('Service not supported', uds_billboard_textdomain);
	}
	
	if(@ini_get('allow_url_fopen')) {
		$response = @file_get_contents($oembed);
	} else {
		if(!function_exists('curl_init')) {
			return __('cURL not installed', uds_billboard_textdomain);
		}
		
		$ch = curl_init();
	
		$response = '';
		if($ch) {
			curl_setopt($ch, CURLOPT_URL, $oembed);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	
			$response = curl_exec($ch);
			curl_close($ch);
		}
	}
	
	if(empty($response)) {
		return __('There was an error when loading the video', uds_billboard_textdomain);
	}
	
	$out = json_decode($response);

	if($out === null) {
		return $response;
	}
	
	// Disable Related videos display after the video has finished (youtube)
	//$out->html = preg_replace('/src="([^"]*)"/', 'src="$1&rel=0"',$out->html);
	
	$out->html = uds_billboard_filter_wmode($out->html);

	return $out;
}

function uds_billboard_filter_wmode($html) {
	if (strpos($html, '<param name="movie"' ) !== false && strpos($html, 'value="opaque"') === false)
		$html = str_replace( '<embed', '<param name="wmode" value="opaque"></param><embed', $html);
	if (strpos( $html, '<embed' ) !== false && strpos( $html, 'wmode="opaque"' )=== false)
		$html = str_replace( '<embed', '<embed wmode="opaque"', $html);
	return $html;
}


?>