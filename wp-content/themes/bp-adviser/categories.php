<?php get_header();
/*
Template Name:
*/
?>
<h1>Articles Home</h1>
<nav class="art_cats">
<?php get_sidebar( 'artcat' ) ?>
</nav>
<aside class="artaside left">
<?php get_sidebar( 'archive' ) ?>
</aside>
<section class="right">
<div class="art_slider">
<h5>Recently Published</h5>
      <div class="subartrev left">
        <?php query_posts('cat=7&posts_per_page=5'); ?>
        <?php if (have_posts()) : ?>
        <div id="slider">
          <div id="sliderContent">
            <?php while (have_posts()) : the_post(); ?>
            <div class="sliderImage"> <a href="<?php the_permalink(); ?>" class="sliderLink">
              <?php the_post_thumbnail('slider_image'); ?>
              </a> 
              <h2><?php the_title(); ?></h2>
   				<?php the_excerpt( __( 'Read the rest of this entry &rarr;', 'buddypress' ) ); ?>
               </div>
            <?php endwhile; ?>
            <div class="clear sliderImage"></div>
          </div>
        </div>
        <?php endif; wp_reset_query(); ?>
      </div>
</div>
<div class="art_video">
<article class="left">
<h5 class="aline">Video</h5>
<?php query_posts('category_name=video&posts_per_page=5'); ?>
        <?php if (have_posts()) : ?>
        <div id="slider">
          <div id="sliderContent">
            <?php while (have_posts()) : the_post(); ?>
            <div class="sliderImage"> <a href="<?php the_permalink(); ?>" class="sliderLink">
              <?php the_post_thumbnail('slider_image'); ?>
              </a> 
              <h2>
                <?php the_title(); ?>
              </h2>
               </div>
            <?php endwhile; ?>
            <div class="clear sliderImage"></div>
          </div>
        </div>
        <?php endif; wp_reset_query(); ?>
</article>
<aside class="right">
<?php get_sidebar( 'subarticles' ) ?>
</aside>
</div>
</section>
<?php get_footer() ?>