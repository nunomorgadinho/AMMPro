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
<link href='http://fonts.googleapis.com/css?family=Cabin' rel='stylesheet' type='text/css'>
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ) ?>" />
<!--[if lt IE 9]>
<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->

<?php
	if ( is_singular() && bp_is_blog_page() && get_option( 'thread_comments' ) )
		wp_enqueue_script( 'comment-reply' );

	wp_head();
?>
</head>
<body <?php body_class(); ?> id="bp-default">
<?php do_action( 'bp_before_header' ) ?>
	<div id="page" class="hfeed">
		<header class="nosub crpo">
				<div class="nineighty">
                <div class="enterprise">
                	<div class="nineighty">
					<a href="<?php echo home_url( '/' ); ?>" title="Art Market Monitor Pro"><h1 class="ir"></h1></a>
                    <!--<div class="logreg right"><a href="< ?php echo home_url(); ?>/log-in">Log in</a> or Register</div>-->
                    </div>
                    <h2>eh...</h2>
					<h3>whoops? 404</h3>
                    <p>Looks as though you may be lost or we failed to provide you with a page to see what you were hunting for.</p>
                    </div>
                    <?php do_action( 'bp_search_login_bar' ) ?>
                <?php do_action( 'bp_header' ) ?>
                </div>
		</header><!-- #branding -->	
        <?php do_action( 'bp_after_header' ) ?>
		<?php do_action( 'bp_before_container' ) ?>
        <div class="nineighty relat">
                <article class="cocon">
                    <h2 class="line">Search or return to the <a href="<?php echo home_url(); ?>">home</a> page.</h2>
               </article>
        	</div>
		<div id="main">
        
