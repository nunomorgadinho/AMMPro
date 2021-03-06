<?php get_header() ?>
<h1>
    <?php _e( 'Search Results', 'buddypress' ) ?>
  </h1>
<div id="searchbase">
  
  <aside class="left">
  <div class="bbpress-homeset">
  <a href="<?php echo home_url(); ?>" id="bbhome">Home</a></div>
  <ul>
  <li class="selected"><?php printf( __( '%1$s', 'buddypress' ), wp_title( false, false ) ); ?></li>
  <li class="setch"><a href="">Search</a></li>
  </ul>
  </aside>
  <section class="right">
  <?php do_action( 'bp_before_blog_search' ) ?>
  <div class="page" id="blog-search" role="main">
    <?php if (have_posts()) : ?>
    <?php bp_dtheme_content_nav( 'nav-above' ); ?>
    <?php while (have_posts()) : the_post(); ?>
    <?php do_action( 'bp_before_blog_post' ) ?>
    <div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
      <div class="author-box"> <?php echo get_avatar( get_the_author_meta( 'email' ), '50' ); ?>
        <p><?php printf( _x( 'by %s', 'Post written by...', 'buddypress' ), bp_core_get_userlink( $post->post_author ) ) ?></p>
      </div>
      <div>
        <h2 class="posttitle"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php _e( 'Permanent Link to', 'buddypress' ) ?> <?php the_title_attribute(); ?>">
          <?php the_title(); ?>
          </a></h2>
        <p class="date"><?php printf( __( '%1$s <span>in %2$s</span>', 'buddypress' ), get_the_date(), get_the_category_list( ', ' ) ); ?></p>
        <div class="entry">
          <?php the_excerpt( __( 'Read the rest of this entry &rarr;', 'buddypress' ) ); ?>
        </div>
        <p class="postmetadata">
          <?php the_tags( '<span class="tags">' . __( 'Tags: ', 'buddypress' ), ', ', '</span>' ); ?>
          <span class="comments">
          <?php comments_popup_link( __( 'No Comments &#187;', 'buddypress' ), __( '1 Comment &#187;', 'buddypress' ), __( '% Comments &#187;', 'buddypress' ) ); ?>
          </span></p>
      </div>
    </div>
    <?php do_action( 'bp_after_blog_post' ) ?>
    <?php endwhile; ?>
    <?php bp_dtheme_content_nav( 'nav-below' ); ?>
    <?php else : ?>
    <div id="whats-new-form">
    <div id="whats-new-textarea">
			<?php get_search_form() ?>
		</div>    
    </div>
    <?php endif; ?>
  </div>
  <?php do_action( 'bp_after_blog_search' ) ?>
  </section>
</div>

<!-- #content -->

<?php get_footer() ?>
