<?php
/**
 * The Template for displaying all single posts.
 *
 * @package WordPress
 * @subpackage Twenty_Ten
 * @since Twenty Ten 1.0
 */

	get_header(); 
	do_action( 'genesis_before_content_sidebar_wrap' );
?>

	
<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>

<h1><?php the_title(); ?></h1>
<div id="businessdealsingle">
<!--<div id="content-sidebar-wrap">-->
	<?php do_action( 'genesis_before_content' ); ?>
	<div id="content" class="hfeed">
		<aside class="left">
          <div class="bbpress-homeset">
          <a href="<?php echo home_url('/'); ?>deals" id="bbhome">Home</a></div>
          <ul>
          <li class="selected"><?php printf( __( '%1$s', 'buddypress' ), wp_title( false, false ) ); ?></li>
          <li class="dealsearch"><a href="<?php echo home_url('/'); ?>deals">Browse</a></li>
          <li class="dealadd"><a href="<?php echo home_url('/'); ?>new-deal">Add New</a></li>
          </ul>
        </aside>
        
		<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            <h4>Business Details</h4>
			<ul>
				<li>
					<h2>Website:</h2> <a href="<?php echo get_post_meta($post->ID, 'website', true); ?>" target="_blank"><?php echo get_post_meta($post->ID, 'website', true); ?></a>
				</li>
				
				<li>
					<b>E-Mail:</b> <span> <?php echo get_post_meta($post->ID, 'email', true); ?> </span>
				</li>
				
				<li>
					<b>Phone:</b> <span> <?php echo get_post_meta($post->ID, 'phone', true); ?> </span>
				</li>
				
				<?php $terms = wp_get_object_terms($post->ID, 'business_category'); 
				if(!empty($terms)){
					$category =  $terms[0]->name;
				?>		
					<li>
					<b>Type:</b>  <?php echo $category;?>
					</li>
					<?php }?>
				
				<b>Description:</b>
				<?php the_content(); ?>

				<li>
				<b>Comment:</b>
				<?php echo get_post_meta($post->ID, 'comment', true); ?>
				</li>
				
				<br/>
				<?php wp_link_pages( array( 'before' => '<div class="page-link">' . __( 'Pages:', 'twentyten' ), 'after' => '</div>' ) ); ?>

				<?php comments_template(); ?>

				<?php if ( get_the_author_meta( 'description' ) ) : // If a user has filled out their description, show a bio on their entries  ?>
					<div id="entry-author-info">
						<div id="author-avatar">
							<?php echo get_avatar( get_the_author_meta( 'user_email' ), apply_filters( 'twentyten_author_bio_avatar_size', 60 ) ); ?>
						</div><!-- #author-avatar -->
						<div id="author-description">
							<h2><?php printf( esc_attr__( 'About %s', 'twentyten' ), get_the_author() ); ?></h2>
							<?php the_author_meta( 'description' ); ?>
							<div id="author-link">
								<a href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>">
									<?php printf( __( 'View all posts by %s <span class="meta-nav">&rarr;</span>', 'twentyten' ), get_the_author() ); ?>
								</a>
							</div><!-- #author-link	-->
						</div><!-- #author-description -->
					</div><!-- #entry-author-info -->
				<?php endif; ?>

			</ul>	
			</div><!-- #post-## -->
			
			
		
			<h1 class="entry-title"></h1>
			

			<div id="nav-below" class="navigation">
				<div class="nav-previous"><?php previous_post_link( '%link', '<span class="meta-nav">' . _x( '&larr;', 'Previous post link', 'twentyten' ) . '</span> %title' ); ?></div>
				<div class="nav-next"><?php next_post_link( '%link', '%title <span class="meta-nav">' . _x( '&rarr;', 'Next post link', 'twentyten' ) . '</span>' ); ?></div>
			</div><!-- #nav-below -->

			

		</div><!-- #content -->
		<?php do_action( 'genesis_after_content' ); ?>
	</div><!-- #content-sidebar-wrap -->
<?php endwhile; // end of the loop. ?>

<?php 
	
/*	do_action( 'genesis_after_content_sidebar_wrap' ); */
	get_footer(); 
?>