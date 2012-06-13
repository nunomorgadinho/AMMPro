<?php if ( is_user_logged_in() ) { ?>
<?php get_header() ?>
<div id="singlepost">
  <?php do_action( 'bp_before_blog_single_post' ) ?>
  <div class="page" id="blog-single" role="main">
    <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
    <div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
      <div class="post-content">
        <header>
          <h1 class="posttitle left"><?php the_title(); ?></h1>
          <div id="sidebar-me" class="right">  
    <a href="<?php echo bp_loggedin_user_domain() ?>"><?php echo get_avatar( get_the_author_email(), '110' ); ?></a>
    <?php global $current_user;
      get_currentuserinfo();
		echo '<h6>' . $current_user->user_firstname . "\n";
		echo '' . $current_user->user_lastname . "\n";
		echo '</h6><p>' . $current_user->user_login . "\n";
		echo '</p><ul><li class="sub_twitter ir">' . $current_user->twitter  . "\n";
		echo '</li><li class="sub_facebook ir">' . $current_user->facebook . "\n";
		echo '</li><li class="sub_email ir">' . $current_user->user_email . "\n";
		echo '</li></ul>'
?>
        
</div>
<date><?php printf( __( '%1$s <span>in %2$s</span>', 'buddypress' ), get_the_date(), get_the_category_list( ', ' ) ); ?></date> 
        </header>
        
        <div class="entry">
          <?php 
			if ( has_post_thumbnail() ) { // check if the post has a Post Thumbnail assigned to it.
			the_post_thumbnail();} ?>
            <div class="hithere"><?php get_sidebar('social'); ?></div>
            <article>
          <?php the_content( __( 'Read the rest of this entry &rarr;', 'buddypress' ) ); ?>
          </article>
          <?php wp_link_pages( array( 'before' => '<div class="page-link"><p>' . __( 'Pages: ', 'buddypress' ), 'after' => '</p></div>', 'next_or_number' => 'number' ) ); ?>
        </div>
        <!--<p class="postmetadata">
          < ?php the_tags( '<span class="tags">' . __( 'Tags: ', 'buddypress' ), ', ', '</span>' ); ?>
          &nbsp;</p>-->
      </div>
    </div>
    <?php comments_template(); ?>
    <a id="comments"></a>
    <?php endwhile; else: ?>
    <p>
      <?php _e( 'Sorry, no posts matched your criteria.', 'buddypress' ) ?>
    </p>
    <?php endif; ?>
  </div>
  <?php do_action( 'bp_after_blog_single_post' ) ?>
</div>
<!-- #content -->

<?php get_footer() ?>
<?php } else {   ?>
<?php get_header('login') ?>
<div class="nineighty">
<div id="loginpage" class="left">
  <?php do_action( 'bp_before_blog_page' ) ?>
        <form name="loginform" id="loginform" class="loginbam" action="<?php echo esc_url( site_url( 'wp-login.php', 'login_post' ) ); ?>" method="post">
        <p>
        <label for="user_login"><?php _e('Username') ?>
        <input type="text" name="log" id="user_login" class="input" value="<?php echo esc_attr($user_login); ?>" size="20" tabindex="10" /></label>
        </p>
        <p>
        <label for="user_pass"><?php _e('Password') ?>
        <input type="password" name="pwd" id="user_pass" class="input" value="" size="20" tabindex="20" /></label>
        </p>
        <?php do_action('login_form'); ?>
        <p class="forgetmenot"><label for="rememberme"><input name="rememberme" type="checkbox" id="rememberme" value="forever" tabindex="90"<?php checked( $rememberme ); ?> /> <?php esc_attr_e('Remember Me'); ?></label></p>
        <p class="submit">
        <input type="submit" name="wp-submit" id="wp-submit" class="button-primary" value="<?php esc_attr_e('Log In'); ?>" tabindex="100" />
        <?php	if ( $interim_login ) { ?>
        <input type="hidden" name="interim-login" value="1" />
        <?php	} else { ?>
        <input type="hidden" name="redirect_to" value="<?php echo esc_attr($redirect_to); ?>" />
        <?php 	} ?>
        <input type="hidden" name="testcookie" value="1" />
        </p>
        </form>
        <?php if ( !$interim_login ) { ?>
        <p id="nav">
        <?php if ( isset($_GET['checkemail']) && in_array( $_GET['checkemail'], array('confirm', 'newpass') ) ) : ?>
        <?php elseif ( get_option('users_can_register') ) : ?>
        <a href="<?php echo esc_url( site_url( 'wp-login.php?action=register', 'login' ) ); ?>"><?php _e( 'Register' ); ?></a> |
        <a href="<?php echo esc_url( wp_lostpassword_url() ); ?>" title="<?php esc_attr_e( 'Password Lost and Found' ); ?>"><?php _e( 'Lost your password?' ); ?></a>
        <?php else : ?>
        <a href="<?php echo esc_url( wp_lostpassword_url() ); ?>" title="<?php esc_attr_e( 'Password Lost and Found' ); ?>"><?php _e( 'Lost your password?' ); ?></a>
        <?php endif; ?>
        </p>
        <?php } ?>
  </div>
  <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
    <div id="post-<?php the_ID(); ?>" <?php post_class('smallsample'); ?>>
      
          <h2><?php the_title(); ?></h2>
          <?php the_excerpt( __( 'Read the rest of this entry &rarr;', 'buddypress' ) ); ?>
          </div>
          <?php endwhile; else: ?>
    <p>
      <?php _e( 'Sorry, no posts matched your criteria.', 'buddypress' ) ?>
    </p>
    <?php endif; ?>
  <?php do_action( 'bp_after_blog_page' ) ?>
  </div>
</div>
<?php } ?>