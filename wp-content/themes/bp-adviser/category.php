<?php get_header();?>
<h1>Category Archive</h1>
<h3 class="arched"><?php printf( __( 'You are browsing the archive for <span>%1$s</span>.', 'buddypress' ), wp_title( false, false ) ); ?></h3>
<aside class="artaside left">
<?php get_sidebar( 'archive' ) ?>
</aside>
<section class="right tagcat">
<div class="art_slider">
<h5>Recently Published</h5>
      <div class="subartrev left">
        <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
        <div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
        <h2><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>">
          <?php the_title(); ?>
          </a></h2>
        <div class="entry">
          <?php the_excerpt(); ?>
        </div>
        </div>
        <?php endwhile; else: ?>
        <?php endif; ?>
      </div>
</div>

</section>
<?php get_footer() ?>