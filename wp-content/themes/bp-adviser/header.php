<!DOCTYPE html>
<!--[if lt IE 7 ]> <html <?php language_attributes(); ?> class="ie6"> <![endif]-->
<!--[if IE 7 ]>    <html <?php language_attributes(); ?> class="ie7"> <![endif]-->
<!--[if IE 8 ]>    <html <?php language_attributes(); ?> class="ie8"> <![endif]-->
<!--[if IE 9 ]>    <html <?php language_attributes(); ?> class="ie9"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--> <html <?php language_attributes(); ?>> <!--<![endif]-->

<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<meta http-equiv="X-UA-Compatible" content="chrome=1">

<title><?php
	/*
	 * Print the <title> tag based on what is being viewed.
	 */
	global $page, $paged;

	wp_title( '|', true, 'right' );

	// Add the blog name.
	bloginfo( 'name' );

	// Add the blog description for the home/front page.
	$site_description = get_bloginfo( 'description', 'display' );
	if ( $site_description && ( is_home() || is_front_page() ) )
		echo " | $site_description";

	// Add a page number if necessary:
	if ( $paged >= 2 || $page >= 2 )
		echo ' | ' . sprintf( __( 'Page %s', 'themename' ), max( $paged, $page ) );
	?></title>
<meta name="description" content="">
<meta name="author" content="">
<!--  Mobile Viewport Fix -->
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Place favicon.ico and apple-touch-icon.png in the images folder -->
<link rel="shortcut icon" href="<?php echo get_template_directory_uri(); ?>/images/favicon.ico">
<link rel="apple-touch-icon" href="<?php echo get_template_directory_uri(); ?>/images/apple-touch-icon.png"><!--60X60-->
<link rel="profile" href="http://gmpg.org/xfn/11" />
<?php do_action( 'bp_head' ) ?>
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ) ?>" />
<!--[if lt IE 9]>
<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/minislides/js/basic-jquery-slider.js"></script>
		<script type="text/javascript">
        jQuery(document).ready(function(){
            jQuery('#slider').bjqs({
                'width' : 650,
                'height' : 500,
				'rotationSpeed' : 9000,
				'automatic' : true,
                'showMarkers' : true,
                'showControls' : false,
                'centerMarkers' : false
            });
        });
        </script>    
<script type="text/javascript" src="http://use.typekit.com/ekk3ikj.js"></script>
<script type="text/javascript">try{Typekit.load();}catch(e){}</script>
<?php
	if ( is_singular() && bp_is_blog_page() && get_option( 'thread_comments' ) )
		wp_enqueue_script( 'comment-reply' );

	wp_head();
?>
</head>
<body <?php body_class(); ?> id="bp-default">
<?php do_action( 'bp_before_header' ) ?>
	<div id="page" class="hfeed">
		<header id="branding" role="banner">
				<div class="nineightyone">
					<article class="ir"><a href="<?php echo home_url(); ?>" title="<?php _ex( 'Home', 'Home page banner link title', 'buddypress' ); ?>"><?php bp_site_name(); ?></a></article>
                    <form role="search" method="get" id="searchform" action="<?php echo home_url( '/' ); ?>">
    					<div><input type="text" value="" name="s" id="s" placeholder="Search the Archive" /></div>
					</form>
                    <?php do_action( 'bp_search_login_bar' ) ?>
                <div>
				<nav id="access" role="article">
					<div class="skip-link visuallyhidden"><a href="#content" title="<?php esc_attr_e( 'Skip to content', 'themename' ); ?>"><?php _e( 'Skip to content', 'themename' ); ?></a></div>
                    <?php wp_nav_menu( array( 'container' => false, 'menu_id' => 'nav', 'theme_location' => 'primary', 'fallback_cb' => 'bp_dtheme_main_nav' ) ); ?>
				</nav><!-- #access -->
                <!--< ?php get_sidebar( 'bbmenu' ) ?>-->
                </div>
                <?php do_action( 'bp_header' ) ?>
                </div>
		</header><!-- #branding -->	
        <?php do_action( 'bp_after_header' ) ?>
		<?php do_action( 'bp_before_container' ) ?>
		<div id="main">
