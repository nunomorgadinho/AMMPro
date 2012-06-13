<?php do_action( 'bp_before_blog_search_form' ) ?>

<form role="search" method="get" id="searchform" action="<?php echo home_url() ?>/">
	<input type="text" value="<?php the_search_query(); ?>" name="s" id="whats-new" />
    <h6><?php _e( 'No posts found. Try a different search?', 'buddypress' ) ?></h6>
    <div id="whats-new-submit">
	<input type="submit" id="searchsubmit" class="right" value="<?php _e( 'Search', 'buddypress' ) ?>" />
	</div>
	<?php do_action( 'bp_blog_search_form' ) ?>
</form>

<?php do_action( 'bp_after_blog_search_form' ) ?>


