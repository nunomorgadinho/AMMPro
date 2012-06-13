<?php

if(!function_exists('d')) {
	/**
	 *	Function, debug facility
	 *	
	 *	@return void
	 */
	function d($var) {
		echo "<pre>";
		var_dump($var);
		echo "</pre>";
	}
}

if(false === function_exists('lcfirst')){
	/**
	 *	Function, used in camelize methods of uBillboard and uBillboardSlide classes
	 *	lowercases the first letter of a string
	 *	
	 *	@param string $str string to lowercase first
	 *	@return string string with lowercased first letter
	 */
    function lcfirst( $str ) 
    {
    	return (string)(strtolower(substr($str,0,1)).substr($str,1));
    } 
}

if(!function_exists('uds_active_shortcodes')) {
	/**
	 *	UDS Active Shortcodes
	 *	List all shortcodes that are in use on current page (this does not include widgets
	 *	Useful to detect usage of a shortcode and include appropriate JS/CSS
	 *
	 *	@return array Flat list of active shortcodes (names only)
	 */
	function uds_active_shortcodes()
	{
		global $posts;
		static $list = null;
		
		if($list !== null) return $list;
		
		if(empty($posts[0])) return array();
		
		$list = array_unique(_uds_active_shortcodes_helper($posts[0]->post_content));
	
		return $list;
	}
	
	/**
	 *	UDS Active SHortcodes Helper
	 *	Used to recursively parse the current post, ensuring that all nested shortcodes
	 *	are found as well
	 *
	 *	@param string $haystack The content string that will be recursively searched for shortcodes
	 *	
	 *	@return array List of all found shortcodes
	 */
	function _uds_active_shortcodes_helper($haystack)
	{
		$ret = array();
		$pattern = get_shortcode_regex();
		
		preg_match_all('/'.$pattern.'/s', $haystack, $matches);
	
		if(is_array($matches[5]) && !empty($matches[5])) {
			foreach($matches[5] as $match) {
				$ret = array_merge($ret, _uds_active_shortcodes_helper($match));
			}
		}
	
		if(!empty($matches[2])) {
			$ret = array_merge($ret, $matches[2]);
		}
		
		return $ret;
	}
}

?>