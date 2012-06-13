<?php get_header() ?>

<div id="content">
  <?php get_sidebar('user') ?>
  <h3 class="line">
    <?php the_title(); ?>
  </h3>
  <?php do_action( 'bp_before_blog_page' ) ?>
  <div class="page" id="blog-page" role="main">
    <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
    <div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
      <div class="entry">
        <?php the_content( __( '<p class="serif">Read the rest of this page &rarr;</p>', 'buddypress' ) ); ?>
        <?php wp_link_pages( array( 'before' => '<div class="page-link"><p>' . __( 'Pages: ', 'buddypress' ), 'after' => '</p></div>', 'next_or_number' => 'number' ) ); ?>
      </div>
    </div>
    <?php endwhile; endif; ?>
  </div>
  <!-- .page -->
  
  <?php do_action( 'bp_after_blog_page' ) ?>
</div>
<!-- #content -->

<?php get_footer(); ?>
