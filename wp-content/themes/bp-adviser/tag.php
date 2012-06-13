<?php get_header();?>
<!--<h1 class="line">< ?php the_title(); ?></h1>-->

<section class="tagcat">
	<?php the_post(); ?>
    <h1 class="page-title">
      <?php
        printf( __( 'Tag Archives: %s', 'themename' ), '<span>' . single_tag_title( '', false ) . '</span>' ); ?>
    </h1>
    <?php rewind_posts(); ?>
    <article class="left">
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
    </article>
    <aside class="right">
          <h2 class="line">Other Tags</h2>
          <?php wp_tag_cloud(); ?>
    </aside>
</section>
<?php get_footer() ?>
