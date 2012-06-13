<?php

/**

 * Template Name: Deals

 *

 */

	get_header(); 

 

	do_action( 'genesis_before_content_sidebar_wrap' );

	

?>
<link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.11/themes/base/jquery-ui.css" type="text/css" media="all" />
<link rel="stylesheet" href="http://static.jquery.com/ui/css/demo-docs-theme/ui.theme.css" type="text/css" media="all" />
<script src="<?php bloginfo('template_url'); ?>/js/jquery.min.js" type="text/javascript"></script>
<script src="<?php bloginfo('template_url'); ?>/js/jquery-ui.min.js" type="text/javascript"></script>
<script src="<?php bloginfo('template_url'); ?>/js/jquery.bgiframe-2.1.2.js" type="text/javascript"></script>
<script src="<?php bloginfo('template_url'); ?>/js/jquery-ui-i18n.min.js" type="text/javascript"></script>
<script src="<?php bloginfo('template_url'); ?>/js/ajaxupload.js" type="text/javascript"></script>
<script src="<?php bloginfo('template_url'); ?>/js/jquery.bgiframe-2.1.2.js" type="text/javascript"></script>

<!-- Table Scripts -->

<script type="text/javascript" language="javascript" src="<?php echo bloginfo( 'stylesheet_directory' )."/media/js/jquery.js"; ?>"></script>
<script type="text/javascript" language="javascript" src="<?php echo bloginfo( 'stylesheet_directory' )."/media/js/jquery.dataTables.js"; ?>"></script>
<script type="text/javascript" charset="utf-8">



		$(document).ready(function() {

			$('#search_deals').dataTable({
				"bPaginate": true,
				"bInfo": true
			});
			$('#search_deals_filter input').val("Live Search");
			$('#search_deals_filter input').focus(function() {
				if($(this).val()=="Live Search")
					$(this).val("");
			});
			$('#search_deals_filter input').blur(function() {
				if($(this).val()=="") {
					$(this).val("Live Search");
				}
			});	
		} );

	

</script>

<h1><?php the_title(); ?></h1>
<!--<div id="content-sidebar-wrap">-->
<div id="businessbrowse">
  <?php do_action( 'genesis_before_content' ); ?>
  <div id="content" class="hfeed">
  <form class="wpsc_checkout_forms" enctype="multipart/form-data" method="post" action="<?php echo get_bloginfo('siteurl').'/new-deal/'; ?>">        
        <section>
            <ul>
            <li class="busdirhome"><a href="<?php echo home_url('/'); ?>business-directory" id="bbhome">Home</a></li>
            <li class="busdiradd"><input type="submit" class="submit bright" value="Add new Deal" /></li>
            <li class="busdirshow"><select size="1" name="search_deals_length"><option value="10">10</option><option value="25">25</option><option value="50">50</option><option value="100">100</option></select></li>
            <li class="busdirsearch"><input type="text" id="acpro_inp1"></li>
            </ul>
        </section>
    </form>
    <?php 
		$deals = new WP_Query();
	    $deals->Query('post_type=dealentry&post_status=publish&posts_per_page=-1');			        
		if ( $deals->have_posts() ) : 
		?>
    <div style="clear: both;"> </div>
    <table id="search_deals">
      <?php build_deals_head();?>
      <tbody>
        <?php 	
			while ( $deals->have_posts() ) : $deals->the_post(); // the loop 
				global $post;
				$post_id = $post->ID;	
				build_deals_row($post_id);
			endwhile; 
		?>
      </tbody>
    </table>
    <div style="clear: both;"> </div>
    <?php 
		 	endif; /** end loop **/
		?>
  </div>
  <!-- #content -->
  <?php 	do_action( 'genesis_after_content' );?>
</div>
<!-- #content-sidebar-wrap -->
<?php
	 do_action( 'genesis_after_content_sidebar_wrap' );
	 get_footer();	 
?>
