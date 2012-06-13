<?php get_header(); ?>

<h1>Archives</h1>
<h3 class="arched"><?php printf( __( 'You are browsing the archive for <span>%1$s</span>.', 'buddypress' ), wp_title( false, false ) ); ?></h3>
<div id="content">
    <?php do_action( 'bp_before_archive' ) ?>
    <div class="page" id="blog-archives" role="main">
      <?php if ( have_posts() ) : ?>
      <?php bp_dtheme_content_nav( 'nav-above' ); ?>
      <?php while (have_posts()) : the_post(); ?>
      <?php do_action( 'bp_before_blog_post' ) ?>
      <div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
        <div class="author-box"> <?php echo get_avatar( get_the_author_meta( 'user_email' ), '50' ); ?>
          <p><?php printf( _x( 'by %s', 'Post written by...', 'buddypress' ), bp_core_get_userlink( $post->post_author ) ) ?></p>
        </div>
        <div class="post-content">
          <h2 class="posttitle"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php _e( 'Permanent Link to', 'buddypress' ) ?> <?php the_title_attribute(); ?>">
            <?php the_title(); ?>
            </a></h2>
          <date><?php printf( __( '%1$s <span>in %2$s</span>', 'buddypress' ), get_the_date(), get_the_category_list( ', ' ) ); ?></date>
          <div class="entry">
            <?php the_excerpt( __( 'Read the rest of this entry &rarr;', 'buddypress' ) ); ?>
            <?php wp_link_pages( array( 'before' => '<div class="page-link"><p>' . __( 'Pages: ', 'buddypress' ), 'after' => '</p></div>', 'next_or_number' => 'number' ) ); ?>
          </div>
          <p class="postmetadata noline">
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
      <h2 class="center">
        <?php _e( 'Not Found', 'buddypress' ) ?>
      </h2>
      <?php get_search_form() ?>
      <?php endif; ?>
    </div>
    <?php do_action( 'bp_after_archive' ) ?>
</div>
<!-- #content -->

<?php get_footer(); ?>
