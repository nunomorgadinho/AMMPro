<?php get_header();
/*
Template Name: Legal
*/
?>
<h1 class="line"><?php wp_title( ) ?></h1>
<section class="pageAbout">
<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
<div class="entry">
<?php the_content( __( 'Read the rest of this entry &rarr;', 'buddypress' ) ); ?>
<?php wp_link_pages( array( 'before' => '<div class="page-link"><p>' . __( 'Pages: ', 'buddypress' ), 'after' => '</p></div>', 'next_or_number' => 'number' ) ); ?>
 </div>
 <?php endwhile; else: ?>
 <?php endif; ?>
 </div>
 </section>
<?php get_footer() ?>
