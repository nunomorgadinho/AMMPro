<?php get_header('foohfo') ?>

	<div id="loginpage">
		<div class="padder one-column">
			<?php do_action( 'bp_before_404' ); ?>
			<div id="post-0" class="post page-404 error404 not-found" role="main">
				<h2 class="posttitle"><?php _e( "Page not found", 'buddypress' ); ?></h2>
				
				<p class="georga"><?php _e( "We're sorry, but we can't find the page that you're looking for. Perhaps searching will help.", 'buddypress' ); ?></p>
                
				<?php get_search_form(); ?>

				<?php do_action( 'bp_404' ); ?>
			</div>

			<?php do_action( 'bp_after_404' ) ?>
		</div><!-- .padder -->
	</div><!-- #content -->

<?php get_footer() ?>