<?php
/**
 * Template Name: New Business
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
             	
 <?php 
		// main code	
 		$post_id = 0;
		if (($_POST["action"] == "newbusiness") && (empty($_POST["robofilter"])))
		{
			if ($_POST['businessname'] == "Business name..") $_POST['businessname'] = '';
			if ($_POST['email'] == "E-Mail..") $_POST['email'] = '';
			if ($_POST['phone'] == "Phone..") $_POST['phone'] = '';
			if ($_POST['website'] == "Website..") $_POST['website'] = '';
			
			$errmsg = '';
		
			if (empty($_POST['businessname']))
			{
				$errmsg = "ERROR - Please fill in the business name";
			}
			if (empty($_POST['website']))
			{
				if (!empty($errmsg))
					$errmsg .= "<br/>";
					$errmsg .= "ERROR - Please fill in the website url";
			}
			if (empty($_POST['email']))
			{
				if (!empty($errmsg))
					$errmsg .= "<br/>";
		
				$errmsg .= "ERROR - Please fill in the email";
			}
			if (empty($_POST['phone']))
			{
				if (!empty($errmsg))
					$errmsg .= "<br/>";
					$errmsg .= "ERROR - Please fill in the phone";
			}

			
			if (add_exists('businessentry', $_POST['businessname']))
			{
				$errmsg .= "ERROR - Business with same name already exists";
			}
	
			if (empty($errmsg)) // no errors
			{
				 process_form(); 
				
				if ($post_id && $post_id!=0) 
				{
					$successmsg = "Thank you for your submission!";
				}
			}	
		}
		// end of main code

?>
        
<!--<div id="content-sidebar-wrap">-->
<h1><?php the_title(); ?></h1>
<div id="addnewdeal">
	<?php do_action( 'genesis_before_content' ); ?>
    <aside class="left">
  <div class="bbpress-homeset">
  <a href="<?php echo home_url('/'); ?>business-directory" id="bbhome">Home</a></div>
  <ul>
  <li class="selected"><?php printf( __( '%1$s', 'buddypress' ), wp_title( false, false ) ); ?></li>
  <li class="dealsearch"><a href="<?php echo home_url('/'); ?>business-directory">Browse</a></li>
  <li class="dealadd"><a href="<?php echo home_url('/'); ?>new-business">Add New</a></li>
  </ul>
  </aside>
	<div id="content" class="hfeed">
	<?php if(!empty($errmsg)) {?>
	
	<div id="errmsg" style="background: red; width: 100%; padding:5px 5px 5px 5px;">
	<?php echo $errmsg;?>
	</div>
	<?php }?>

	<?php if(!empty($successmsg)) {?>
	
		<div class="linktodeal">
			<br/>
			<br/>
			<p>	Your Business:
			
				<span class="purple"><?php echo get_the_title($post_id); ?></span> was successfully added.  
			</p>				
			<a href="<?php echo get_permalink($post_id)?>"> Please click here to see your Business!</a><br/>
		</div>	
		<div id="successmsg" style=" text-align: right;  width: 100%; padding:5px 5px 5px 5px;">
		<?php echo $successmsg;?>
		</div>	
	<?php } 
	// only shows form if no deal was added	
	else {?>
	<?php 
	$args = array(
					'show_option_all' => '', 'show_option_none' => 'Please Select',
							'orderby' => 'id', 'order' => 'ASC',
							'show_last_update' => 0, 'show_count' => 0,
							'hide_empty' => 0, 'child_of' => 0,
							'exclude' => '', 'echo' => 1,
							'selected' => $selected, 'hierarchical' => 1,
							'name' => 'business_cat', 'id' => 'business_cat',
							'class' => 'required', 'depth' => 1,
							'tab_index' => 0, 'taxonomy' => 'business_category',
							'hide_if_empty' => false
				);
	?>
	
        <form name="addbusiness" method="POST">
        <section class="left">
        <input type="hidden" name="action" value="newbusiness" />
        <input type="text" name="robofilter" value="" style="display:none" />
        <!---->
        <label class="dlabel required">Name</label>:
        <div class="formw">
        <input type="text" class="text required" name="businessname" id="businessname" size="25" value="<?php if ($_POST['businessname']){ echo $_POST['businessname']; } else { echo 'Business name..'; } ?>" onfocus="if(this.value==this.defaultValue)this.value='';" onblur="if(this.value=='')this.value=this.defaultValue;"  style="color:#555555;" ></input>
        </div>
        <!---->
        <label class="dlabel required">Website</label>:
        <div class="formw">
        <input type="text" class="text required" name="website" id="website" size="25" value="<?php if ($_POST['website']){ echo $_POST['website']; } else { echo 'Website..'; } ?>" onfocus="if(this.value==this.defaultValue)this.value='';" onblur="if(this.value=='')this.value=this.defaultValue;"  style="color:#555555;" ></input>
        </div>
        <!---->
        <label class="dlabel required">E-Mail</label>:
        <div class="formw">
        <input type="text" class="text" name="email" size="50" value="<?php if ($_POST['email']){ echo $_POST['email']; } else { echo 'E-Mail..'; } ?>" onfocus="if(this.value==this.defaultValue)this.value='';" onblur="if(this.value=='')this.value=this.defaultValue;" style="color:#555555;"  id="work_title"></input> 	  </div>
        <!---->
        <label class="dlabel required">Phone</label>:
        <div class="formw">
        <input type="text" class="text" name="phone" size="50" value="<?php if ($_POST['phone']){ echo $_POST['phone']; } else { echo 'Phone..'; } ?>" onfocus="if(this.value==this.defaultValue)this.value='';" onblur="if(this.value=='')this.value=this.defaultValue;" style="color:#555555;"  id="work_title"></input> 	  </div>
        <!---->
        </section>
         <section class="right">
        <label class="dlabel">Category:</label>
        <div class="formw">
        <?php wp_dropdown_categories($args); ?>
        </div>
        <label class="dlabel">Description:</label>
        <div class="formw">
        <textarea id="description" name="description" style=" height: 100px" rows="2"><?php if ($_POST['description']) { echo $_POST['description']; } ?></textarea>
        </div>
        <!---->
        <label class="dlabel">Comment:</label>
        <div class="formw">
        <textarea id="comment" name="comment" style=" height: 100px" rows="2"><?php if ($_POST['comment']) { echo $_POST['comment']; } ?></textarea>
        </div>
        <!---->
        </section>
        <div class="add_deal">
        <input type="submit" class="submit" value="SUBMIT" /><br/><br/>
        </div>
        
        
        </form>

<?php } ?>

	</div><!-- #content -->
<?php 	do_action( 'genesis_after_content' );?>
</div><!-- #content-sidebar-wrap -->

<?php
		
	 do_action( 'genesis_after_content_sidebar_wrap' );
	 get_footer();


/**
 * Functions area
 * 
 */
function add_exists($add_type,$matricula)
{
	global $wpdb,$wp_version,$wp_locale,$current_blog,$wp_rewrite;
	
	$posts = new WP_Query();
	$posts->Query('post_type='.$add_type.'&post_status=publish');
	
	while($posts->have_posts()) : $posts->the_post();
		if(strcmp($matricula,get_the_title())==0)
			return true;
	
	endwhile;
	
	return false;
}

function process_form()
{
	global $user_ID;
	global $post_id;
				
	$new_post = array(
	    'post_title' => $_POST['businessname'],
	    'post_content' => $_POST['description'],
	    'post_status' => 'publish',
	    'post_date' => date('Y-m-d H:i:s'),
	    'post_author' => $user_ID,
	    'post_type' => 'businessentry',
	    'post_category' => array(0)
	);
	$post_id = wp_insert_post($new_post);

	
	// assign the correct category for the business
	$term = get_term_by('id', $_POST['business_cat'], 'business_category');
	
	wp_set_object_terms($post_id, $term->name, 'business_category');
	
	$meta_fields = array("website", "email", "phone", "comment");
	
	foreach ($meta_fields as $key)
	{
		$value = @$_POST[$key];
		
		// If value is a string it should be unique
		if (!is_array($value))
		{
			// Update meta
			if (!update_post_meta($post_id, $key, $value))
			{
				// Or add the meta data
				add_post_meta($post_id, $key, $value, true);
			}
		}
		else
		{
			// If passed along is an array, we should remove all previous data
			delete_post_meta($post_id, $key);
			
			// Loop through the array adding new values to the post meta as different entries with the same name
			foreach ($value as $entry)
				add_post_meta($post_id, $key, $entry);
		}
	}
	
	return true;
}
?>