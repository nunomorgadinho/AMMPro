<?php
//http://localhost/th/img_resize.php?url=nike.jpg&width=100&height=500

//Require Class
require_once("PThumb.php");

$image_url = "../../../../wp-content/uploads/classipress/".$_GET["url"];

//Child Class for configuration
class pthumb_example extends PThumb{
             //Use Cache?
	var $use_cache = true;
            //Cache Dir. MUST be writable
	var $cache_dir = "cache/";		//Make sure to include trailing slash!
             //Error mode. Set to 2 to show a nicer error than the user-intruiguing error
	var $error_mode = 2;
	
	function pthumb_example(){
		$this -> PThumb();
	}
    //Custom method to display an "X" in case of any errors
    function display_x(){
    }
}

$thumbnail = new pthumb_example;

	if (!isset($image_url) || !isset($_GET["width"]) || !isset($_GET["height"])){
		$thumbnail -> display_x();
	}
	else{
		if (!$thumbnail -> fit_thumbnail($image_url,$_GET["width"],$_GET["height"])){
			$thumbnail -> display_x();
		}
	}

//Other Errors
if ($thumbnail -> isError()){
    $thumbnail -> display_x();
}
?>