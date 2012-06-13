<?php function my_alter_bp_adminbar(){
remove_action('bp_adminbar_menus', 'bp_adminbar_random_menu', 100);} 
add_action('wp_footer','my_alter_bp_adminbar',1);?>