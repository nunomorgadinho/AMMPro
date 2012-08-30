<?php

// Adds 2 menus
register_nav_menus( array(
	'footer' => __( 'Footer Menu', 'bp-adviser' ),
	'utility' => __( 'Utility Menu', 'bp-adviser' )
) );

function bp_dtheme_widgets_init() {
	// Register the widget columns
	// Area 1, located in the sidebar. Empty by default.
	register_sidebar( array(
		'name'          => 'Ad and Activity Widget Area',
		'id'            => 'adact-widget-area',
		'description'   => __( 'Ad and Activity widget area', 'buddypress' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h3 class="widgettitle">',
		'after_title'   => '</h3>'
	) );	
}
	// Register the widget columns
	// Area 2, located in the sidebar. Empty by default.
	register_sidebar( array(
		'name'          => 'Subbed Article Review Widget Area',
		'id'            => 'subrticles-widget-area',
		'description'   => __( 'Subbed Article Review widget area', 'buddypress' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h3 class="widgettitle">',
		'after_title'   => '</h3>'
	) );
	// Area 3, located in the sidebar. Empty by default.
	register_sidebar( array(
		'name'          => 'BB Menu Widget Area',
		'id'            => 'bbmenu-widget-area',
		'description'   => __( 'BB Menu widget area', 'buddypress' ),
		'before_widget' => '',
		'after_widget'  => '',
		'before_title'  => '<h3 class="widgettitle">',
		'after_title'   => '</h3>'
	) );	
	// Area 3.5, located in the sidebar. Empty by default.
	register_sidebar( array(
		'name'          => 'BB Menu Register Widget Area',
		'id'            => 'bbmenureg-widget-area',
		'description'   => __( 'BB Menu Register widget area', 'buddypress' ),
		'before_widget' => '',
		'after_widget'  => '',
		'before_title'  => '<h3 class="widgettitle">',
		'after_title'   => '</h3>'
	) );	
	// Area 4, located in the sidebar. Empty by default.
	register_sidebar( array(
		'name'          => 'Archived Widget Area',
		'id'            => 'archive-widget-area',
		'description'   => __( 'Archived Area widget area', 'buddypress' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h3 class="widgettitle">',
		'after_title'   => '</h3>'
	) );
	// Area 5, located in the sidebar. Empty by default.
	register_sidebar( array(
		'name'          => 'Article Categories Widget Area',
		'id'            => 'artcat-widget-area',
		'description'   => __( 'Article Categories Area widget area', 'buddypress' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s right">',
		'after_widget'  => '</div>',
		'before_title'  => '<h3 class="widgettitle">',
		'after_title'   => '</h3>'
	) );
	// Area 6, located in the sidebar. Empty by default.
	register_sidebar( array(
		'name'          => 'Last Footer Menu Widget Area',
		'id'            => 'lastmenu-widget-area',
		'description'   => __( 'Last Footer Menu Area widget area', 'buddypress' ),
		'before_widget' => '',
		'after_widget'  => '',
		'before_title'  => '',
		'after_title'   => ''
	) );
	// Area 6, located in the sidebar. Empty by default.
	register_sidebar( array(
		'name'          => 'Posts Social Widget Area',
		'id'            => 'artsocial-widget-area',
		'description'   => __( 'Posts Social widget area', 'buddypress' ),
		'before_widget' => '',
		'after_widget'  => '',
		'before_title'  => '',
		'after_title'   => ''
	) );

	
	// REGISTER FORM AREA
if ( function_exists('register_sidebars') ){
	register_sidebar(
		array(
			'id' => 'registerform',
			'name' => __( 'Register Form' ),
			'description' => __( 'Register Form Area' ),
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div>',
			'before_title' => '<h1 class="widget-title">',
			'after_title' => '</h1>'
		)
	);
}

add_action( 'widgets_init', 'bp_dtheme_widgets_init' );
//
//
//
//Displays Table Head for listing Deals
function business_directory_head(){
	
	
	echo '<thead>
			<tr>
				<th class="head">Business Name</th>
				<th class="head">Phone</th>
				<th class="head">Type</th>
			</tr>
			</thead>';
	
}

//Displays Table Head for listing Deals
function build_deals_head(){
	
	
	echo '<thead>
			<tr>
				<th class="head">Artist</th>
				<th class="head">Title</th>
 				<th class="head">Sold</th>
				<th class="head">Date Sold</th>
				<th class="head">Gallery</th>
	 			<th class="head">Art Fair</th>
			</tr>
			</thead>';
	
}

//Displays one row of table listing deals
function build_deals_row($post_id){
	
	//thumb image
/*	$thumbid=  get_post_thumbnail_id($post_id);	
	$thumb = wp_get_attachment_image_src($thumbid,'thumbnail');
	if(empty($thumb))
		$thumb[0] = get_bloginfo('stylesheet_directory')."/images/dummy.png";*/

	//$artist name - it-s mandatory so it will be filled.
	$artist =	get_post_meta($post_id, 'artist',true);
		
				
	//price		
	$price_sold = '$'.get_post_meta($post_id, 'price_sold',true);
	if(!isset($price_sold)|| $price_sold=='')
		$price_sold = '$'.get_post_meta($post_id, 'estimated_price_range');
	if(!isset($price_sold)|| $price_sold=='$')
		$price_sold = 'n/a';
					
					
	//Year 
	$date_sold = get_post_meta($post_id, 'date_sold',true);
	if(!isset($date_sold) || $date_sold=='')
		$date_sold = 'n/a';
						
	//Gallery 
	$gallery = '';
	$terms = wp_get_object_terms($post_id, 'gallery'); 
	if(!empty($terms))
		$gallery =  $terms[0]->name;
	if(!isset($gallery) || $gallery == '')
		$gallery = 'n/a';	
			
		
	//Art Fair
	$art_fair = '';
	$terms = wp_get_object_terms($post_id, 'artfair'); 
	if(!empty($terms))
		$art_fair = $terms[0]->name;
	if(!isset($art_fair) || $art_fair =='')
		$art_fair = 'n/a';
				
	?>
	<tr class="border_bottom">	
	<!-- <td class="image"><img src="<?php //echo $thumb[0];?>" alt=""></td>  -->
		<td class="artist"><?php echo $artist; ?></td>
		<td class="title"><a href="<?php echo the_permalink();?>"><?php echo the_title();?></a>
		<span class="subtitle">
			<?php echo the_excerpt(); ?>
		</span></td>
		<td class="thegreens"><?php echo $price_sold;?></td>
		<td><?php echo $date_sold;?></td>
		<td><?php echo $gallery;?></td>
		<td><?php echo $art_fair;?></td>		
	</tr>
	
	<?php 
	
}

//Displays one row of table listing deals
function build_business_directory_row($post_id){
	
	//$artist name - it-s mandatory so it will be filled.
	$email = get_post_meta($post_id, 'email', true);
	$phone = get_post_meta($post_id, 'phone', true);
	$website = get_post_meta($post_id, 'website', true);
	
	$pos = preg_match('/^http:\/\//', $website);
	if($pos==0){
		$website = 'http://'.$website;
	}
	
	$category = '';
	$terms = wp_get_object_terms($post_id, 'business_category'); 
	if(!empty($terms))
		$category = $terms[0]->name;
	if(!isset($category) || $category =='')
		$category = 'n/a';
	
	?>
	<tr class="border_bottom">	
	<!-- <td class="image"><img src="<?php //echo $thumb[0];?>" alt=""></td>  -->
		<td class="title"><a href="<?php echo the_permalink();?>"><?php echo the_title();?></a><span class="subtitle"><?php echo the_excerpt();?></span></td>
		<td><?php echo $phone; ?></td>
		<td><?php echo $category; ?></td>
	</tr>
	
	<?php 
	
}

add_filter('excerpt_length', 'my_excerpt_length');
function my_excerpt_length($length) {
	return 15; 
}

//inspired on genesis_standard_loop - does the loop to display search results
// page template
function deals_search_loop(){
	
	global $wpdb;
	
	get_header(); 
 
	do_action( 'genesis_before_content_sidebar_wrap' );
	?>
	
	<!-- Table Scripts -->
	<script type="text/javascript" language="javascript" src="<?php echo bloginfo( 'stylesheet_directory' )."/media/js/jquery.js"; ?>"></script>
	<script type="text/javascript" language="javascript" src="<?php echo bloginfo( 'stylesheet_directory' )."/media/js/jquery.dataTables.js"; ?>"></script>	
	<script type="text/javascript" charset="utf-8">

		$(document).ready(function() {
			$('#search_deals').dataTable();
		} );
	
	</script>	
		
	<div id="content-sidebar-wrap">
		<?php do_action( 'genesis_before_content' ); ?>
		<div id="content" class="hfeed">
	
		<h1>Deals Search Results</h1>	
	
		<form class="wpsc_checkout_forms" enctype="multipart/form-data" method="post" action="<?php echo get_bloginfo('siteurl').'/new-deal/'; ?>">
		<div class="row">
	    	 <div style="text-align: right; width: 90%;">
			<input type="submit" class="submit bright" value="ADD NEW DEAL" /><br/><br/>
			</div>
	    </div>							
		</form>			
		
		
		<?php 
			if ( have_posts() ) : 
		?>		
		<div style="clear: both;"> </div>
			<table id="search_deals">
			<?php build_deals_head()?>
		
			<tbody>
			
		<?php 	
			while ( have_posts() ) : the_post(); // the loop 
				global $post;
				$post_id = $post->ID;
				build_deals_row($post_id);
			
			endwhile; /** end of one post **/
		?>	
		</tbody>
		</table>
		<div style="clear: both;"> </div>

	
		<?php 
			else : /** if no posts exist **/
			do_action( 'genesis_loop_else' );
			endif; /** end loop **/
		?>
		</div><!-- #content -->
		<?php 	do_action( 'genesis_after_content' );?>
	</div><!-- #content-sidebar-wrap -->
		<?php 	
	do_action( 'genesis_after_content_sidebar_wrap' );
	 get_footer();	
			
}

function business_search_loop(){
	
	global $wpdb;
	
	get_header(); 
 
	do_action( 'genesis_before_content_sidebar_wrap' );
	?>
	
	<!-- Table Scripts -->
	<script type="text/javascript" language="javascript" src="<?php echo bloginfo( 'stylesheet_directory' )."/media/js/jquery.js"; ?>"></script>
	<script type="text/javascript" language="javascript" src="<?php echo bloginfo( 'stylesheet_directory' )."/media/js/jquery.dataTables.js"; ?>"></script>	
	<script type="text/javascript" charset="utf-8">

		$(document).ready(function() {
			$('#search_deals').dataTable();
		} );
	
	</script>	
		
	<div id="content-sidebar-wrap">
		<?php do_action( 'genesis_before_content' ); ?>
		<div id="content" class="hfeed">
	
		<h1>Business Directory Search Results</h1>	
	
		<form class="wpsc_checkout_forms" enctype="multipart/form-data" method="post" action="<?php echo get_bloginfo('siteurl').'/new-business/'; ?>">
		<div class="row">
	    	 <div style="text-align: right; width: 90%;">
			<input type="submit" class="submit bright" value="ADD NEW BUSINESS" /><br/><br/>
			</div>
	    </div>							
		</form>			
		
		
		<?php 
			if ( have_posts() ) : 
		?>		
		<div style="clear: both;"> </div>
			<table id="search_deals">
			<?php business_directory_head()?>
		
			<tbody>
			
		<?php 	
			while ( have_posts() ) : the_post(); // the loop 
				global $post;
				$post_id = $post->ID;
				build_business_directory_row($post_id);
			
			endwhile; /** end of one post **/
		?>	
		</tbody>
		</table>
		<div style="clear: both;"> </div>

	
		<?php 
			else : /** if no posts exist **/
			do_action( 'genesis_loop_else' );
			endif; /** end loop **/
		?>
		</div><!-- #content -->
		<?php 	do_action( 'genesis_after_content' );?>
	</div><!-- #content-sidebar-wrap -->
		<?php 	
	do_action( 'genesis_after_content_sidebar_wrap' );
	 get_footer();	
			
}








add_filter( 'woocommerce_billing_fields', 'filter_billing', 10, 1 );

function filter_billing($fields_array){
	
	
	
	$billing = array(
			'billing_first_name' 	=> array(
					'name'=>	'billing_first_name',
					'label'                 => __('Name','wc_disable_checkout_fields'),
					'placeholder'  		=> __('Name','wc_disable_checkout_fields'),
					'required'              => true,
					'class'                 => array('form-row-first')
			),
			
			'billing_email' 	=> array(
					'label'                 => __('Email','wc_disable_checkout_fields'),
					'placeholder'   	=> __('you@yourdomain.com','wc_disable_checkout_fields'),
					'required'              => true,
					'class'                 => array('form-row-first')
			)
	);
	
	
	$disabled_billing = array('billing_last_name','billing_company', 'billing_address_1', 'billing_address_2', 'billing_city',
			'billing_postcode', 'billing_country', 'billing_state','billing_phone');
	
	
	
	
	$fields_array = array_replace($fields_array, $billing);
	return array_diff_key($fields_array, array_flip($disabled_billing));
}





?>