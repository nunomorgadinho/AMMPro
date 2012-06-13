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
<script type="text/javascript" src="http://use.typekit.com/ses1xtk.js"></script>
<script type="text/javascript">try{Typekit.load();}catch(e){}</script>
<script type="text/javascript" src="http://use.typekit.com/ekk3ikj.js"></script>
<script type="text/javascript">try{Typekit.load();}catch(e){}</script>
<?php
	if ( is_singular() && bp_is_blog_page() && get_option( 'thread_comments' ) )
		wp_enqueue_script( 'comment-reply' );

	wp_head();
?>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/minislides/js/freshline/jquery.freshline.boxer.js"></script>
<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/minislides/js/freshline/jquery.freshline.minislides.js"></script>
<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/minislides/js/jquery.touchwipe.min.js"></script>
<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/minislides/js/jquery.easing.1.3.js"></script>
<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/minislides/js/jquery.mousewheel.min.js"></script>

<link rel="stylesheet" type="text/css" href="<?php echo get_template_directory_uri(); ?>/minislides/css/settings.css" media="screen">
<link rel="stylesheet" type="text/css" href="<?php echo get_template_directory_uri(); ?>/minislides/css/lightbox.css" media="screen">
<script type="text/javascript">
$.fn.infiniteCarousel = function () {

    function repeat(str, num) {
        return new Array( num + 1 ).join( str );
    }
  
    return this.each(function () {
        var $wrapper = $('> div', this).css('overflow', 'hidden'),
            $slider = $wrapper.find('> ul'),
            $items = $slider.find('> li'),
            $single = $items.filter(':first'),
            
            singleWidth = $single.outerWidth(), 
            visible = Math.ceil($wrapper.innerWidth() / singleWidth),
            currentPage = 1,
            pages = Math.ceil($items.length / visible);            


        // 1. Pad so that 'visible' number will always be seen, otherwise create empty items
        if (($items.length % visible) != 0) {
            $slider.append(repeat('<li class="empty" />', visible - ($items.length % visible)));
            $items = $slider.find('> li');
        }

        // 2. Top and tail the list with 'visible' number of items, top has the last section, and tail has the first
        $items.filter(':first').before($items.slice(- visible).clone().addClass('cloned'));
        $items.filter(':last').after($items.slice(0, visible).clone().addClass('cloned'));
        $items = $slider.find('> li'); // reselect
        
        // 3. Set the left position to the first 'real' item
        $wrapper.scrollLeft(singleWidth * visible);
        
        // 4. paging function
        function gotoPage(page) {
            var dir = page < currentPage ? -1 : 1,
                n = Math.abs(currentPage - page),
                left = singleWidth * dir * visible * n;
            
            $wrapper.filter(':not(:animated)').animate({
                scrollLeft : '+=' + left
            }, 500, function () {
                if (page == 0) {
                    $wrapper.scrollLeft(singleWidth * visible * pages);
                    page = pages;
                } else if (page > pages) {
                    $wrapper.scrollLeft(singleWidth * visible);
                    // reset back to start position
                    page = 1;
                } 

                currentPage = page;
            });                
            
            return false;
        }
        
        $wrapper.after('<a class="arrow back">&lt;</a><a class="arrow forward">&gt;</a>');
        
        // 5. Bind to the forward and back buttons
        $('a.back', this).click(function () {
            return gotoPage(currentPage - 1);                
        });
        
        $('a.forward', this).click(function () {
            return gotoPage(currentPage + 1);
        });
        
        // create a public interface to move to a specific page
        $(this).bind('goto', function (event, page) {
            gotoPage(page);
        });
    });  
};

$(document).ready(function () {
  $('.infiniteCarousel').infiniteCarousel();
});
</script>
<script type="text/javascript">
			$(document).ready(function() {
				$.noConflict();					 									
				 jQuery('#shorty').minislides(
                    {                                       
                        width:980,
                        height:255, 
                        slides:2,
                        padding:40,
                        ease:'easeOutQuad',
                        speed:300,
                        hidetoolbar:2000,
                        animtype:1,
                        mousewheel:'off',
                        timer:4000
                    })
				<!-- THE ACTIVATION OF THE LIGHTBOX PLUGIN -->
				 jQuery('.freshlightbox').fhboxer({})
				 jQuery('.freshlightbox_round').fhboxer({
						hover_round:"true"
				 })
		});
</script>
</head>
<body <?php body_class(); ?> id="bp-default">
<?php do_action( 'bp_before_header' ) ?>
	<div id="page" class="hfeed">
		<header class="nosub nett">
        <article class="globalwall">
            <div class="nineighty">
            <h1 class="ir"><a href="/" title="Art Market Monitor Pro">Art Market Monitor Pro</a></h1>
            <div class="logreg right"><a href="<?php echo home_url( '/' ); ?>log-in">Log in</a> or <a href="#signup" class="color1">Register</a></div>
            </div>
            <div class="nineighty global">
            <h2>GLOBAL</h2>
            <h3>NETWORKING</h3>
            <p>Get access to industry leaders from around the globe for 1 on 1 conversations about emerging and traditional art markets.</p>
            <a href="#signup" class="btn btn4">Get Started</a>
            </div>
            </article>
        <script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/minislides/js/basic-jquery-slider.js"></script>
		<?php do_action( 'bp_search_login_bar' ) ?>
        <?php do_action( 'bp_header' ) ?>
		</header><!-- #branding -->	
        <?php do_action( 'bp_after_header' ) ?>
		<?php do_action( 'bp_before_container' ) ?>
		<div id="main">