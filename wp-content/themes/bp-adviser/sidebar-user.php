<?php if ( is_user_logged_in() ) { ?>
<h5 class="noline">Your Public Profile</h2>
<section class="sub-accntctrl">
  <div id="sidebar-me" class="left">  
    <a href="<?php echo bp_loggedin_user_domain() ?>"><?php bp_loggedin_user_avatar( 'type=thumb&width=110&height=110' ) ?></a>
    <?php global $current_user;
      get_currentuserinfo();
		echo '<h6>' . $current_user->user_firstname . "\n";
		echo '' . $current_user->user_lastname . "\n";
		echo '</h6><p>' . $current_user->user_login .'</p>'. "\n";
		echo '<ul>';
		echo '<li class="sub_twitter ir"><a href="http://twitter.com/' . $current_user->twitter . '"></a></li>' . "\n";
		echo '<li class="sub_facebook ir"><a href="' . $current_user->facebook .'"></a></li>'. "\n";
		echo '<li class="sub_email ir"><a href="mailto:' . $current_user->user_email .'"></a></li>'. "\n";
		echo '</ul>'
		
?>
</div>
	<div class="sub-manage left">
		<h6>Network notices</h6>
		<div class="sub-role-group block7">
		<?php bp_is_group_invites() ?>
		</div>
	</div>
  <div class="sub-role left">
  <span class="editinfo"><a href="<?php echo home_url( '/' ); ?>account" title="Edit your Account settings">Edit</a></span>
    <h6>User Role</h6>
    <p>
    <?php
	global $wp_roles;
	foreach ( $wp_roles->role_names as $role => $name ) :
		if ( current_user_can( $role ) )
			echo ' ' . $role;
	endforeach;
?>
<?php $user_info = get_userdata(1); echo ' &mdash; User ID: ' . $user_info->ID . "\n"; ?></p>
      <span class="editinfo"><a href="<?php echo home_url( '/' ); ?>subscriptions" title="Edit your Account Billing settings">Edit</a></span>
    <h6>Manage Subscription</h6>
    <p>Type: Normal</p>
  </div>
</section>
<?php } else {   ?>
	<section class="sub-accntctrl">
    <h5>Please Login to see your profile</h5>
	<p>By <a href="<?php bloginfo('url'); ?>/wp-register.php">registering</a>, you can save your favorite posts for future reference.</p>
    </section
><?php } ?>
  
