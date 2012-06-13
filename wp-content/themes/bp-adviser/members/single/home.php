<?php

/**
 * BuddyPress - Users Home
 *
 * @package BuddyPress
 * @subpackage bp-default
 */

?>
<?php get_header( 'buddypress' ); ?>

<div id="content">
  <h1>Activity Home</h1>
  <div class="buddybox">
    <?php do_action( 'bp_before_member_home_content' ); ?>
    <?php
                    if ( is_user_logged_in() && bp_is_my_profile() && ( !bp_current_action() || bp_is_current_action( 'just-me' ) ) )
                    locate_template( array( 'activity/post-form.php'), true );
                    
                    do_action( 'bp_after_member_activity_post_form' );
                    do_action( 'bp_before_member_activity_content' ); ?>
    <aside class="bbaside">
      <?php get_sidebar('bbcontrols'); ?>
      <div id="item-nav">
        <div class="item-list-tabs no-ajax" id="object-nav" role="navigation">
          <ul>
            <?php bp_get_displayed_user_nav(); ?>
            <?php do_action( 'bp_member_options_nav' ); ?>
          </ul>
        </div>
      </div>
      <!-- #item-nav -->
      <?php do_action( 'bp_before_member_body' );

				if ( bp_is_user_activity() || !bp_current_component() ) : ?>
      <?php bp_get_options_nav() ?>
      <div class="item-list-tabs no-ajax" id="subnav" role="navigation">
        <ul>
          <li id="activity-filter-select" class="last">
            <label for="activity-filter-by">
              <?php _e( 'Show:', 'buddypress' ); ?>
            </label>
            <select id="activity-filter-by">
              <option value="-1">
              <?php _e( 'Everything', 'buddypress' ) ?>
              </option>
              <option value="activity_update">
              <?php _e( 'Updates', 'buddypress' ) ?>
              </option>
              <?php
                    if ( !bp_is_current_action( 'groups' ) ) :
                    if ( bp_is_active( 'blogs' ) ) : ?>
              <option value="new_blog_post">
              <?php _e( 'Posts', 'buddypress' ) ?>
              </option>
              <option value="new_blog_comment">
              <?php _e( 'Comments', 'buddypress' ) ?>
              </option>
              <?php
                    endif;
                    
                    if ( bp_is_active( 'friends' ) ) : ?>
              <option value="friendship_accepted,friendship_created">
              <?php _e( 'Friendships', 'buddypress' ) ?>
              </option>
              <?php endif;
                    
                    endif;
                    
                    if ( bp_is_active( 'forums' ) ) : ?>
              <option value="new_forum_topic">
              <?php _e( 'Forum Topics', 'buddypress' ) ?>
              </option>
              <option value="new_forum_post">
              <?php _e( 'Forum Replies', 'buddypress' ) ?>
              </option>
              <?php endif;
                    
                    if ( bp_is_active( 'groups' ) ) : ?>
              <option value="created_group">
              <?php _e( 'New Groups', 'buddypress' ) ?>
              </option>
              <option value="joined_group">
              <?php _e( 'Group Memberships', 'buddypress' ) ?>
              </option>
              <?php endif;
                    
                    do_action( 'bp_member_activity_filter_options' ); ?>
            </select>
          </li>
        </ul>
      </div>
      <!-- .item-list-tabs --> 
    </aside>
   
     <?php do_action( 'bp_before_member_activity_post_form' ); ?>
    <div class="activity" id="bbactive" role="main">
      <?php locate_template( array( 'activity/activity-loop.php' ), true ); ?>
    </div>  
      <?php do_action( 'bp_after_member_activity_content' ); ?>
       
<?php do_action( 'bp_before_member_body' ); 
				 elseif ( bp_is_user_blogs() ) : 
					locate_template( array( 'members/single/blogs.php'     ), true ); 
				
				elseif ( bp_is_user_friends() ) :
					locate_template( array( 'members/single/friends.php'   ), true );
                   
				elseif ( bp_is_user_groups() ) :
					locate_template( array( 'members/single/groups.php'    ), true );

				elseif ( bp_is_user_messages() ) :
					locate_template( array( 'members/single/messages.php'  ), true );

				elseif ( bp_is_user_profile() ) :
					locate_template( array( 'members/single/profile.php'   ), true );

				elseif ( bp_is_user_forums() ) :
					locate_template( array( 'members/single/forums.php'    ), true );

				elseif ( bp_is_user_settings() ) :
					locate_template( array( 'members/single/settings.php'  ), true );

				// If nothing sticks, load a generic template
				else :
					locate_template( array( 'members/single/plugins.php'   ), true );

				endif;

				do_action( 'bp_after_member_body' ); ?>
       
    <?php do_action( 'bp_after_member_home_content' ); ?>
  </div><!-- .padder --> 
  
</div><!-- #content -->
<?php get_footer( 'buddypress' ); ?>