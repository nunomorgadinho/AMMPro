<div id="sidebar-registerform" class="sidebar">

	<?php 
		if ( 	function_exists('dynamic_sidebar') && is_active_sidebar( 'registerform' ) ) : 
			dynamic_sidebar("registerform"); 
		else : 
		endif; 
	?>

</div>
