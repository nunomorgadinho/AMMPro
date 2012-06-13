<?php if ( is_user_logged_in() ) { ?>
<div id="bbmenu">
<?php if ( is_active_sidebar( 'bbmenu-widget-area' ) ) : ?>
<?php dynamic_sidebar( 'bbmenu-widget-area' ); ?>
<?php endif; ?>
</div>
<?php } else {   ?>
<div id="bbmenu">
<?php if ( is_active_sidebar( 'bbmenureg-widget-area' ) ) : ?>
<?php dynamic_sidebar( 'bbmenureg-widget-area' ); ?>
<?php endif; ?>
</div>
<?php } ?>