<?php get_header();
/*
Template Name: Terms of Service
*/
?>
<h1><?php the_title(); ?></h1>
<div class="ourpeople">
  <section class="tospp">
    <aside class="left">
    <?php if( is_page('Terms of Service') ) : ?>
    <?php get_sidebar(tos); ?>
    <?php elseif( is_page('Privacy Policy') ) : ?>
    <?php get_sidebar(pp); ?>
    <?php elseif( is_page('Press') ) : ?>
    <?php get_sidebar(pp); ?>
    <?php elseif( is_page('faq') ) : ?>
    <?php get_sidebar(pp); ?>
    <?php elseif( is_page('support') ) : ?>
    <?php get_sidebar(pp); ?>
    <?php else : ?>
    <?php endif; ?>
    </aside>
  </section>
  <div class="devs right tpf"> 
    <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
    <div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <div class="entry">
      <?php the_content( __( 'Read the rest of this entry &rarr;', 'buddypress' ) ); ?>
      <?php wp_link_pages( array( 'before' => '<div class="page-link"><p>' . __( 'Pages: ', 'buddypress' ), 'after' => '</p></div>', 'next_or_number' => 'number' ) ); ?>
    </div>
    <?php endwhile; else: ?>
    <?php endif; ?>
    </div>
  </div> 
</div>  
<?php get_footer() ?>

