<?php 

define('FRONT_PAGE_PRODUCT_ID', 119);
		
if ( is_user_logged_in() ) { ?>
<?php get_header() ?>
<!--Subscribed Home-->

<div id="content"> 
  <!--User bar-->
  <?php get_sidebar( 'user' ) ?>
  <!-- [END] User bar--> 
  <!--Leftside Float-->
  <section class="subbed">
    <aside class="subbedside left">
      <?php get_sidebar( 'adact' ) ?>
    </aside>
    <!-- [END] Leftside Float--> 
    <!--Content Float-->
    <div class="left subbedposts">
    <h5>Recently Published</h5>
      <div class="subartrev left">
        <?php echo get_uds_billboard("indexone") ?>
      </div>
      <div class="subvideorev left">
        <ol id="video_posts">
        <li><h3>Popular Videos</h3></li>
          <?php
            $pp = new WP_Query('category_name=video&posts_per_page=5'); ?>
          <?php while ($pp->have_posts()) : $pp->the_post(); ?>
          <li>
          
          <?php if (has_post_thumbnail( $post->ID ) ): ?>
				<?php $image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'single-post-thumbnail' ); ?>
			<?php endif; ?>
	<a href="<?php the_permalink(); ?>"><img src="<?php echo get_template_directory_uri(); ?>/images/play.png" width="145" height="75" border="0"style="background-position:center center; background:url('<?php echo $image[0]; ?>') no-repeat"></a>
    <h6><?php the_title(); ?></h6>            
          </li>
          <div style="clear: both;"></div>
          <?php endwhile; ?>
        </ol>
      </div> 
	  <?php get_sidebar( 'subarticles' ) ?>
    </div>
   
    <!-- [END] Content Float--> 
  </section>
  <section class="businessDir">
    <h5>Business Directory</h5>
  </section>
  <section class="salesData">
    <h5>Deal Database</h5>
  </section>
</div>
<!-- #content -->
<?php get_footer() ?>
<!-- Logged in Users see the following --> 
<?php } else {   ?>
<?php get_header('nonsub') ?>
<section class="inforeso">
<h2><span>pro. </span>Information Resources</h2>
<div class="nineighty">
    <article class="artelli left">
    <div class="ir">Artelligence</div>
    <p>Semi-annual conference held at the New York Athletics Club</p>
    <ol>
    <li>You will have live coverage access</li>
    <li>Watch Live streams of conference events</li>
    <li>Past & future videos of Workshops & Panels</li>
    <li>Access to articles about and for the event which you can save as PDF files.</li>
    </ol>
    </article>
    <article class="networ right">
    <h3><span>Our</span> expert network</h3>
    <p>Email, PM and subscribe to expert feeds to exchange and share ideas.</p>
    <div class="infiniteCarousel">
      <div class="wrapper">
        <ul>
          <li><img src="<?php echo get_template_directory_uri(); ?>/images/marion.jpg" width="64" height="64"><br>Marion Maneker</li>
          <li><img src="<?php echo get_template_directory_uri(); ?>/images/nuno.jpg" width="64" height="64"><br>Nuno Maneker</li>
          <li><img src="<?php echo get_template_directory_uri(); ?>/images/nate.jpg" width="64" height="64"><br>Nate Maneker</li>
          <li><img src="<?php echo get_template_directory_uri(); ?>/images/marion.jpg" width="64" height="64"><br>Bob Maneker</li>
          <li><img src="<?php echo get_template_directory_uri(); ?>/images/nuno.jpg" width="64" height="64"><br>Mary Maneker</li>
          <li><img src="<?php echo get_template_directory_uri(); ?>/images/nate.jpg" width="64" height="64"><br>Steve Maneker</li>
        </ul>        
      </div>
    </div>
    </article>
</div>
<h2><span>pro. </span>Business Resources</h2>
<div id="shorty" class="minislider_shorty">	
<div class="controller toolbox-shorty"></div>
<ul>
<li class="slidetop ssc1">
<div class="normal_bg">
<div class="shorty_short_content">
<div class="shorty_title brown">Sales Database</div>
<div class="quote">
<blockquote>
<p>A comprehensive sale database of world-wide activity on 10 different art markets. View charts, download documents and add your collection.</p>				
</blockquote>
</div>
<div class="shorty_more shorty_more_style darkgreen">Show more</div>
</div>
<div class="shorty_full_content">									
<div class="shorty_title_more darkgreen">
<h3>Add Art</h3>
<p>Add your own collection.</p>
<h3>Art History Archive</h3>
<p>Search through the thousands of listings to track a specific piece's sales history.</p>
</div>
<div class="shorty_hide shorty_hide_style darkgreen">Hide</div>
</div>
</div>
</li>
<!---->
<li class="slidetop ssc2">
<div class="normal_bg">
<div class="shorty_short_content">
<div class="shorty_title brown">Business Directory</div>
<div class="quote">
<blockquote>
<p>Join hundreds of other businesses around the world who are joining our network to promote, sell, and trade their art related goods in our directory.</p>				
</blockquote>
</div>
<div class="shorty_more shorty_more_style darkgreen">Show more</div>
</div>
<div class="shorty_full_content">									
<div class="shorty_title_more darkgreen">
<h2>List your Business</h2>
<p>Add up to 3 of your businesses.</p>
<h2>Find &amp; Browse</h2>
<p>Find & browse businesses from around the world.</p>
<h2>Search Filter</h2>
<p>Narrow your search with our instant filter on the fly.</p>
</div>
<div class="shorty_hide shorty_hide_style darkgreen">Hide</div>
</div>
</div>
</li>
</ul>
</div>	
<div class="medium_spacer_negative"></div>
<?php //get_sidebar( 'registerform' ); ?>


<h2><span>pro. </span>Sign Up<a id="signup"></a></h2>
<section class="signup">
  <article class="left">
  <h4>Professional + Productivity</h4>
  <p>Art Market Monitor Pro uses a 1 click sign up system to make it easy to subscribe to our news service. Try it free for 7 days once you have signed up. If you decide to cancel just log-in to your account home and unsubscribe. If you decide you want to cancel after the 7 days we will refund your pro-rated balance.</p>
  <p>We ask for credit card information before you begin your trial so you can immediately see what we have to offer. No pre-qualifications over the phone, no hidden fees.</p>
<section class="rate block4">
<ul>
<li>Art Market Monitor Subscription</li>
<li><date>7 Days free, no charge until Day, Month, Year.</date></li>
</ul>
<ul>
<li>300.00</li>
</ul>
</section>
  </article>
  <aside class="right">
  		<?php 
		// TODO: herb - adding the front page product to cart
		include ('wpsc-ajax.functions.php');
		//ob_start();
		//ob_end_clean();
		wpsc_empty_cart_2();
		wpsc_add_to_cart_2(FRONT_PAGE_PRODUCT_ID); 
  		include ("wpsc-shopping_cart_page_ammpro.php"); ?>
  </aside>  
  </section>
  <section class="businessDir">
    	<p class="left">By clicking Create my account you agree to the Terms of Service, Privacy, and Refund policies.</p>
    	<input type='submit' value='<?php _e('CREATE MY ACCOUNT ', 'wpsc');?>' name='submit' class='make_purchase wpsc_buy_button btn right btn6' onclick="document.forms['wpsc_home_checkout_form'].submit();" />
        <!-- <a href="" class="btn right btn6" onclick="document.forms['wpsc_home_checkout_form'].submit();">Create my Account</a> -->
        <!-- <a href="#" class="btn right btn6" onclick="document.wpsc_home_checkout_form.submit(); return false;">Create my Account</a> -->

  </section>
   </form>
</section>

<?php get_footer() ?>
<?php } ?>
