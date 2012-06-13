<div class="subarts">
  <?php if ( is_active_sidebar( 'subrticles-widget-area' ) ) : ?>
  <div class="widget-area left">
    <ul class="xoxo">
      <?php dynamic_sidebar( 'subrticles-widget-area' ); ?>
    </ul>
  </div>
  <!-- #first .widget-area -->
  <?php endif; ?>
  <ol id="popular_posts" class="left">
  <li><h3>Popular Entries</h3></li>
    <?php
$pp = new WP_Query('orderby=comment_count&posts_per_page=5'); ?>
    <?php while ($pp->have_posts()) : $pp->the_post(); ?>
    <li>
      <div class="pop_image"><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"></a></div>
      <a class="pop_link" href="<?php the_permalink(); ?>" title="<?php the_title(); ?>">
      <?php the_title(); ?>
      </a></li>
    <div style="clear: both;"></div>
    <?php endwhile; ?>
  </ol>
</div>
