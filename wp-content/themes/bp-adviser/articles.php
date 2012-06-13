<?php get_header();
/*
Template Name: Articles
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
<h5>Latest Published Articles</h5>
      <div class="subartrev left">
        <?php echo get_uds_billboard("recently-published-articles") ?>
      </div>
</div>
<div class="art_video">
<article>
<h5 class="aline">Latest Videos</h5>
<?php echo get_uds_billboard("videos-artpage") ?>
</article>
</div>
</section>
<?php get_footer() ?>