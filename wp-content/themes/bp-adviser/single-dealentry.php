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
			<ul>		
				<!-- Image -->					
				<li class="featured_image">		
				<?php echo the_post_thumbnail('medium'); ?>
				</li>	
					
				<li>
					<b>Artist:</b> <span> <?php echo get_post_meta($post->ID, 'artist', true); ?></span>
				</li>
				
				<li>
					<b>Work Title:</b> <span> <?php echo get_post_meta($post->ID, 'work_title', true); ?> </span>
				</li>
				
				<?php $year = get_post_meta($post->ID, 'work_year', true) ;
				if(isset($year) && $year!='') {?>
				<li>
					<b>Year:</b> <?php echo $year; ?>
				</li>
				<?php }?>
				
				<?php $type = get_post_meta($post->ID, 'work_type', true);
				if(isset($type) && $type!='') {?>
					<li>
						<b>Type:</b> <?php echo $type; ?>
					</li>
				<?php }?>
				
				<?php $edition_min = get_post_meta($post->ID, 'edition_min', true);
					  $edition = $edition_min.' of '.get_post_meta($post->ID, 'edition_max', true);
				if(isset($edition_min) && $edition_min!='') {?>
				<li>
					<b>Edition:</b> <?php echo $edition; ?>
				</li>
				<?php }?>
	
	
				<?php $sale = get_post_meta($post->ID, 'primary_or_secondary', true); 
				if(isset($sale) && $sale!=''){?>
					<li>
						<b>Sale:</b> <?php echo $sale; ?>
					</li>
				<?php }?>	
					
				
				<?php 
					$estimated = get_post_meta($post->ID, 'estimated_price_range', true);
					if(isset($estimated) && $estimated!='') {
						
					switch ($estimated) {
						case 'Emerging': $estimated = "Emerging ($0-$25,000)";
						break;
						case 'Established': $estimated = "Established ($25-75,000)";
						break;
						case 'Significant': $estimated ="Significant ($75,000 - $250,000)";
						break;
						case 'Conviction Buy': $estimated = "Conviction Buy ($250,000 - $750,000)";
						break;
						case 'Investment Grade': $estimated = "Investment Grade ($750k and above)";
						break;
					}						
				?>
					<li>
						<b>Estimated Price:</b> <?php echo $estimated;?>
					</li>
				<?php }?>
				
				
				<?php 
					$price_sold = get_post_meta($post->ID, 'price_sold', true);
					$price_mark = '';
	
					if (($price_sold > 0) && ($price_sold < 25000))
					{
						$price_mark = 'Emerging';
					} elseif (($price_sold >= 25000) && ($price_sold < 75000))
					{
						$price_mark = 'Established';
					} elseif (($price_sold >= 75000) && ($price_sold < 250000))
					{
						$price_mark = 'Significant';
					} elseif (($price_sold >= 250000) && ($price_sold < 750000))
					{
						$price_mark = 'Conviction Buy';
					} elseif ($price_sold >= 750000)
					{
						$price_mark = 'Investment Grade';
					}
				if(isset($price_sold) && $price_sold !='') {	
				?>
				<li>
					<b>Price Sold:</b> <?php echo $price_sold.' USD'; ?>
				</li>
				<?php }?>
					
				<?php $terms = wp_get_object_terms($post->ID, 'gallery'); 
				if(!empty($terms)){
					$gallery =  $terms[0]->name;
				?>		
					<li>
					<b>Gallery:</b>  <?php echo $gallery;?>
					</li>
					<?php }?>

				<?php 
				$terms = wp_get_object_terms($post->ID, 'artfair'); 
				if(!empty($terms)){
					$art_fair = $terms[0]->name; ?>
					<li>
					<b>Art Fair:</b> <?php echo $art_fair;?>
					</li>
				<?php }?>
				
				
				<?php $date_sold = get_post_meta($post->ID, 'date_sold', true);
				if(isset($date_sold) && $date_sold!='') {?>
					<li>
						<b>Date Sold:</b> <?php echo $date_sold; ?>
					</li>
				<?php }?>
				
				<?php $buyer = get_post_meta($post->ID, 'buyer_name', true); 
				if(isset($buyer) && $buyer!=''){?>
					<li>
						<b>Buyer:</b> <?php echo $buyer; ?>
					</li>
				<?php } ?>
				
				<br/>
				<br/>

				<?php wp_link_pages( array( 'before' => '<div class="page-link">' . __( 'Pages:', 'twentyten' ), 'after' => '</div>' ) ); ?>

				<li>
				<?php comments_template( '', true ); ?>				
				</li>

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