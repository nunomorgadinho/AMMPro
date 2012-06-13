<h5>Artelligence</h5>
      <div class="ad"></div>
      <ol id="telli_posts">
        <li><h3>Recent Posts</h3></li>
          <?php
            $pp = new WP_Query('category_name=artelligence&posts_per_page=5'); ?>
          <?php while ($pp->have_posts()) : $pp->the_post(); ?>
          <li><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a>            
          </li>
          <div style="clear: both;"></div>
          <?php endwhile; ?>
        </ol>
<?php if ( is_active_sidebar( 'archive-widget-area' ) ) : ?>
				<div class="widget-area">
					<ul>
						<?php dynamic_sidebar( 'archive-widget-area' ); ?>
					</ul>
				</div><!-- .widget-area -->
<?php endif; ?>