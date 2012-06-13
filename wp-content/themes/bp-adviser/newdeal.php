<?php get_header(); 
/**
 * Template Name: New Deal
 *
 */
	do_action( 'genesis_before_content_sidebar_wrap' );
?>
<link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.11/themes/base/jquery-ui.css" type="text/css" media="all" />
<link rel="stylesheet" href="http://static.jquery.com/ui/css/demo-docs-theme/ui.theme.css?v=1" type="text/css" media="all" />
<script src="<?php bloginfo('stylesheet_directory'); ?>/js/jquery.min.js?v=1" type="text/javascript"></script>
<script src="<?php bloginfo('stylesheet_directory'); ?>/js/jquery-ui.min.js?v=1" type="text/javascript"></script>
<script src="<?php bloginfo('stylesheet_directory'); ?>/js/jquery.bgiframe-2.1.2.js?v=1" type="text/javascript"></script>
<script src="<?php bloginfo('stylesheet_directory'); ?>/js/jquery-ui-i18n.min.js?v=1" type="text/javascript"></script>
<script src="<?php bloginfo('stylesheet_directory'); ?>/js/ajaxupload.js?v=1" type="text/javascript"></script>
<script src="<?php bloginfo('stylesheet_directory'); ?>/js/jquery.bgiframe-2.1.2.js?v=1" type="text/javascript"></script>
<script type='text/javascript'>

jQuery(document).ready(function(){

	var loadedFotos=0;

		var btnUpload= jQuery('#upload');

		var status= jQuery('#status');

		var ajaxLoader=jQuery('#ajax-loader');

		ajaxLoader.hide();

		new AjaxUpload(btnUpload, {

			action: '<?php bloginfo('stylesheet_directory'); ?>/upload-file.php',

			onSubmit: function(file, ext){

				if (! (ext && /^(jpg|png|jpeg|gif)$/.test(ext))){

	                  // check for valid file extension

					status.text('Only JPG, PNG or GIF allowed');

					return false;

				}

				ajaxLoader.show();

			},

			onComplete: function(file, response){

				//On completion clear the status

				status.text('');

				ajaxLoader.hide();



				//Add uploaded file to list

				if(response == "success"){				

					document.getElementById("uploadedfotos").value = file;

								

					var htmlblock = '<div class="foto" id="foto"><img class="foto" src="<?php bloginfo('template_url'); ?>/uploads/'+file+'" width="150" alt="thumbnail" /><br/>';

									

					jQuery('#fotopreviews').html(htmlblock).addClass('success');



					jQuery('#upload').html('Update');

				} else{

					jQuery('#fotopreviews').append(text(file).addClass('error'));

				}

			}

		});

		

			var availableTags = [

     				<?php 

     					$args = array('order'=>'ASC', 'hide_empty'=>false);

     					$myterms = get_terms('gallery', $args);

     					

     					foreach($myterms as $term)

     					{

     						echo "\"".$term->name."\",";

     					}

     				?>     		             		

          		];

         		var availableArtfairs = [

         		     				<?php 

         		     					$args = array('order'=>'ASC', 'hide_empty'=>false);

         		     					$myterms = get_terms('artfair', $args);

         		     					

         		     					foreach($myterms as $term)

         		     					{

         		     						echo "\"".$term->name."\",";

         		     					}

         		     				?>     		         

         		          		];

         		jQuery( "#gallery" ).autocomplete({

         			source: availableTags

         		});

         		jQuery( "#event" ).autocomplete({

         			source: availableArtfairs

         		});

         		jQuery('#colorselector').change(function(){
					console.log('here' + jQuery(this).val());

         			jQuery('.colors').hide();

         			jQuery('#' + jQuery(this).val()).show();

         	    });

	

			jQuery('#date_sold').datepicker();

  			jQuery.datepicker.setDefaults(jQuery.datepicker.regional['']);

  			jQuery(selector).datepicker(jQuery.datepicker.regional['']);

});   		

             	

</script>
<?php 

		// main code	

 		$post_id = 0;

		if (($_POST["action"] == "newdeal") && (empty($_POST["robofilter"])))

		{

			if ($_POST['artist'] == "Artist name..") $_POST['artist'] = '';

			if ($_POST['event'] == "Art Fair..") $_POST['event'] = '';

			if ($_POST['work_title'] == "Work title..") $_POST['work_title'] = '';

			if ($_POST['work_year'] == "Year..") $_POST['work_year'] = '';

			if ($_POST['date_sold'] == "Date Sold..") $_POST['date_sold'] = '';

			if ($_POST['gallery2'] == "Gallery..") $_POST['gallery2'] = '';

			if ($_POST['buyer_name'] == "Buyer (if public)..") $_POST['buyer_name'] = '';

			$errmsg = '';
		
			if (empty($_POST['artist']))
			{
				$errmsg = "ERROR - Please fill in the artist name";
			}
			if (empty($_POST['work_title']))
			{
				if (!empty($errmsg))
					$errmsg .= "<br/>";
				$errmsg .= "ERROR - Please fill in the title";
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
  <a href="<?php echo home_url('/'); ?>deals" id="bbhome">Home</a></div>
  <ul>
  <li class="selected"><?php printf( __( '%1$s', 'buddypress' ), wp_title( false, false ) ); ?></li>
  <li class="dealsearch"><a href="<?php echo home_url('/'); ?>deals">Browse</a></li>
  <li class="dealadd"><a href="<?php echo home_url('/'); ?>new-deal">Add New</a></li>
  </ul>
  </aside>
  <div id="content" class="hfeed right">
    
    <?php if(!empty($errmsg)) {?>
    <div id="errmsg" style="background: red; width: 100%; padding:5px 5px 5px 5px;"> <?php echo $errmsg;?> </div>
    <?php }?>
    <?php if(!empty($successmsg)) {?>
    <div class="linktodeal"> 
      <p> Your Deal with:
        artist name <span><?php echo get_post_meta($post_id, 'artist',true); ?></span> and title <span> <?php echo get_post_field('post_title', $post_id)?></span> was successfully added. </p>
      <a href="<?php echo get_permalink($post_id)?>"> Please click here to see your Deal!</a><br/>
    </div>
    <div id="successmsg" style=" text-align: right;  width: 100%; padding:5px 5px 5px 5px;"> <?php echo $successmsg;?> </div>
    <?php } 

	// only shows form if no deal was added	

	else {?>
    <form class="wpsc_checkout_forms" enctype="multipart/form-data" method="post">
    <section class="left">
        <label class="dlabel required">Artist:</label>
        <div class="formw"> <!-- value="Artist name.." onfocus="if(this.value==this.defaultValue)this.value='';" onblur="if(this.value=='')this.value=this.defaultValue;"/> -->
        
        <input type="text" class="text required" name="artist" size="25" value="<?php if ($_POST['artist']){ echo $_POST['artist']; } else { echo 'Artist name..'; } ?>" onfocus="if(this.value==this.defaultValue)this.value='';" onblur="if(this.value=='')this.value=this.defaultValue;" id="artist" >
          </input>
          </div>
          <!---->
        <label class="dlabel">Gallery:</label>
        <div class="formw">
          <input type="text" class="artist"  value="<?php if ($_POST['gallery2']){ echo $_POST['gallery2']; } else { echo 'Gallery..'; } ?>" onfocus="if(this.value==this.defaultValue)this.value='';" onblur="if(this.value=='')this.value=this.defaultValue;" id="gallery2" size="52" maxlength="100" name="gallery2">
          </input>
        </div>
        <!---->

        <label class="dlabel">Art Fair:</label>
        <div class="formw">
        <input type="text" name="event"  id="event" value="<?php if ($_POST['event']){ echo $_POST['event']; } else { echo 'Art Fair..'; } ?>" onfocus="if(this.value==this.defaultValue)this.value='';" onblur="if(this.value=='')this.value=this.defaultValue;" size="25" maxlength="25">
        </input>
        </div>
        <!---->
        <label class="dlabel">Year:</label>
        <div class="formw">
          <input type="text" class="year" name="work_year" size="4" maxlength="4" value="<?php if ($_POST['work_year']){ echo $_POST['work_year']; } else { echo 'Year..'; } ?>"  onfocus="if(this.value==this.defaultValue)this.value='';" onblur="if(this.value=='')this.value=this.defaultValue;" id="work_year">
          </input>
        </div>
        <!---->
        <label class="dlabel">Edition:</label>
        <div class="formw edition">
          <input type="text" name="edition_min" value="<?php if ($_POST['edition_min']){ echo $_POST['edition_min']; } else { echo ''; } ?>" size="2">
          </input>
          <p class="left">of</p>
          <input type="text" name="edition_max" value="<?php if ($_POST['edition_max']){ echo $_POST['edition_max']; } else { echo ''; } ?>" size="3">
          </input>
        </div>
        <!---->
        <article class="dealwrap">
        <label class="dlabel">Type:</label>
        <div class="formw">
          <?php draw_type_table();?>
        </div>
        </article>
    </section>
    <section class="right">
        <label class="dlabel required">Title:</label>
        <div class="formw">
        <input type="text" class="text" name="work_title" size="50" value="<?php if ($_POST['work_title']){ echo $_POST['work_title']; } else { echo 'Work title..'; } ?>" onfocus="if(this.value==this.defaultValue)this.value='';" onblur="if(this.value=='')this.value=this.defaultValue;" id="work_title">
        </input>   
        </div>
        <!---->
        <label class="dlabel">Image:</label>
        <div class="formw">
          <div id="upload" class="submit btn btn4">Attach</div>
          <span id="ajax-loader"><img src="<?php echo bloginfo('stylesheet_directory'); ?>/images/ajax-loader.gif"></span>
          <div id="fotopreviews"></div>
        </div>
        <input type="hidden" name="uploadedfotos" id="uploadedfotos"></input>
        <input type="hidden" name="featuredfoto" id="featuredfoto"></input>
        <!---->
        <label class="dlabel">Comment:</label>
        <div class="formw">
        <textarea id="comment" name="comment" style=" height: 100px" rows="2"><?php if ($_POST['comment']) { echo $_POST['comment']; } ?>
        </textarea>
        </div>
		<!---->
        <label class="dlabel">Buyer:</label>
        <div class="formw">
          <input type="text" name="buyer_name" value="<?php if ($_POST['buyer_name']){ echo $_POST['buyer_name']; } else { echo 'Buyer (if public)..'; } ?>" onfocus="if(this.value==this.defaultValue)this.value='';" onblur="if(this.value=='')this.value=this.defaultValue;"   size="25" >
          </input>
        </div>
        <!---->
        <label class="dlabel">Sale:</label>
        <div class="formw">
        <input type="radio" name="primary_or_secondary" value="primary" <?php if ($_POST['primary_or_secondary'] == 'primary') echo 'checked="checked"'; ?>>
        </input>
        Primary
        <input type="radio" name="primary_or_secondary" value="secondary" <?php if ($_POST['primary_or_secondary'] == 'secondary') echo 'checked="checked"'; ?>>
        </input>
        Secondary
        <input type="hidden" name="action" value="newdeal" />
        <input type="text" name="robofilter" value="" style="display:none" />
        </div>
        <!---->
        <label class="dlabel">Date Sold:</label>
        <div class="formw">
        <input type="text" id="date_sold" name="date_sold" value="<?php if ($_POST['date_sold']){ echo $_POST['date_sold']; } else { echo 'Date Sold..'; } ?>" >
        </input>
        <img src="<?php echo get_bloginfo('stylesheet_directory'); ?>/images/icon_cal.gif?v=1" height="15px"></img> 
        </div>
         <!---->
         <article class="dealwrap">
         <label class="dlabel">Price Sold:</label>
        <div class="formw">
          <select id="colorselector" name="price_type">
            <option selected="selected" value="">Enter Price as:</option>
            <option value="fixed">Fixed Price in USD</option>
            <option value="range">Estimated Range in USD</option>
            <option value="converted">Currency Other than USD</option>
          </select>
          </div>
          </article>
          <!---->
          <div id="range" class="colors" style="display:none">
          <label class="dlabel">Estimated range:</label>
          <div class="formw">
            <select name="range" style="width: 150px">
              <option selected="selected"  value=""></option>
              <option value="Emerging">Emerging ($0-$25,000)</option>
              <option value="Established">Established ($25-75,000)</option>
              <option value="Significant">Significant ($75,000 - $250,000)</option>
              <option value="Conviction Buy">Conviction Buy ($250,000 - $750,000)</option>
              <option value="Investment Grade">Investment Grade ($750k and above)</option>
            </select>         
        </div>
        </div>
        <!---->
        <div id="fixed" class="colors" style="display:none">
          <label class="dlabel">USD:</label>
          <div class="formw">
            <input type="text" name="fixed_price" value="">
            </input>
          </div>
        </div>       
        <!---->
        <div id="converted" class="colors" style="display:none">
          <label class="dlabel">Currency Converter:</label>
          <div class="formw">
            <?php my_currency_converter(); ?>
          </div>
        </div>
 </section>
      <div class="add_deal">
        <input type="submit" class="submit" value="SUBMIT" />
        </div>
    </form>
  </div>
 
  
  <?php } ?>
</div>
<!-- #content -->

<?php 	do_action( 'genesis_after_content' );?>
</div>
<!-- #content-sidebar-wrap -->

<?php
	 do_action( 'genesis_after_content_sidebar_wrap' );

	 get_footer();

/**
 * Functions area
 * 
 */

function process_form()

{

	global $user_ID;

	global $post_id;

	$new_post = array(

	    'post_title' => $_POST['work_title'],

	    'post_content' => $_POST['comment'],

	    'post_status' => 'publish',

	    'post_date' => date('Y-m-d H:i:s'),

	    'post_author' => $user_ID,

	    'post_type' => 'dealentry',

	    'post_category' => array(0)

	);

	$post_id = wp_insert_post($new_post);

	$meta_fields = array("price_sold", "estimated_price_range" , "comment", "buyer_name", "date_sold", "work_title", "artist", "work_type", "work_year", "images", "edition_min", "edition_max", "primary_or_secondary");

	

	if ($_POST['price_type'] == "fixed")

	{

		$_POST['price_sold'] = $_POST['fixed_price'];

	}

	if ($_POST['price_type'] == "range")

	{

		$_POST['estimated_price_range'] = $_POST['range'];

	}

	if ($_POST['price_type'] == "converted")

	{

		$_POST['price_sold'] = $_POST['Vresult'];

	}

	

	wp_set_object_terms($post_id, $_POST['gallery2'], 'gallery');

	wp_set_object_terms($post_id, $_POST["event"], 'artfair');

	

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

			//error_log( "WOULD DELETE HERE INSTEAD" );

			delete_post_meta($post_id, $key);

			

			// Loop through the array adding new values to the post meta as different entries with the same name

			foreach ($value as $entry)

				add_post_meta($post_id, $key, $entry);

		}

	}



	// upload

	$uploadedfotos = $_POST['uploadedfotos'];

	$featuredfoto = $_POST['featuredfoto'];

	handle_images($post_id,$uploadedfotos,$featuredfoto);

	

	return true;

}



function set_featured_foto($post_id, $att_id)

{

	$resuly = 	update_post_meta($post_id, '_thumbnail_id', $att_id);	

	$featuredid = get_post_thumbnail_id($post_id);	

   	

}



function already_uploaded($post_id, $file)

{

	$args = array( 'post_type' => 'attachment', 'numberposts' => -1, 'post_status' => null, 'post_parent' => $post_id ); 

	$attachments = get_posts($args);



	

	if ($attachments) {

		foreach ( $attachments as $attachment ) {

			if ($file == $attachment->post_title)

				return $attachment;

		}

	}	

	return false; 

}



function handle_images($post_id, $uploadedfotos, $featuredfoto){

	//upload images

	$arr_files = split(" ", $uploadedfotos);

	$possible_featured = 0;

	foreach ($arr_files as $file) 

	{ 

		// if the file has been uploaded already break

		$uploaded = already_uploaded($post_id, $file);

		if ($uploaded) 

		{ 

			//continues after checking if featured changed

			if ($featuredfoto == $file) 

			{  

				set_featured_foto($post_id, $uploaded->ID);

			}

			continue;

		}

		if (!file_exists("wp-content/themes/genesis/uploads/".$file)) 

			continue;		

		if (empty($file))

			continue;		

		// como ja fizemos o upload por ajax agora basta ir buscar as imagens ah directoria temp

		$upload = wp_upload_bits($file, null, file_get_contents(ABSPATH."wp-content/themes/genesis/uploads/".$file));

	    $type = '';

	    if ( !empty($upload['type']) )

	        $type = $upload['type'];

	    else {

	        $mime = wp_check_filetype( $upload['file'] );

	        if ($mime)

	          $type = $mime['type'];

	    }

	   $attachment = array(

	            'post_title' => basename( $upload['file'] ),

	            'post_content' => '',

	            'post_type' => 'attachment',

	            'post_parent' => $post_id,

	            'post_mime_type' => $type,

	            'guid' => $upload[ 'url' ],

	   	);

	   	require_once("wp-admin/includes/image.php");

	   	// Save the data

	   	$id = wp_insert_attachment( $attachment, $upload[ 'file' ], $post_id );

	   	if($possible_featured==0)

		   	$possible_featured = $id;

	   	wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $upload[ 'file' ] ) );

   		// Set as featured

	   	if ($_POST['featuredfoto'] == $file) 

		    set_featured_foto($post_id, $id);
	}	

 	if ($_POST['featuredfoto'] == 'removefeatured')

   	{

   		$featuredid = get_post_thumbnail_id($post_id);

   		wp_delete_attachment($featuredid);

   	}
   	//no featured photo was selected

 	if(empty($_POST['featuredfoto']) && ($possible_featured != 0) && ($post_id!=0))

	{ 

		$meta = get_post_meta($post_id, '_thumbnail_id'); 

		//checks if also the post_id does not have any featured image

		if(empty($meta))  

			set_featured_foto($post_id, $possible_featured);

	}

}

function is_checked($vtype)
{
	$work_types = $_POST['work_type'];

	if (!is_array($work_types))

		return false;
	foreach ($work_types as $val=>$type)
	{
		if ($type == $vtype)
			return true;
	}
	return false;

}
	function draw_type_table()

{

	?>
<table>
  <?php 

	$types = array("Painting", "Sculpture", "Drawing", "Photograph", "Print", "Watercolor", "Work on Paper",

						"Work of Art", "Ceramic", "Bronze"); 

			$i = 0;

			$j = 0;

			$checked = '';

			foreach ($types as $type) 

			{

				if (($j % 4)==0)

					echo "<tr>"; $j++;

				

				if (is_checked($type)) {

					$checked = 'checked';

				}

				?>
  
    <td><input type="checkbox"  name="work_type[]" value="<?php echo $type ?>" <?php echo $checked ?>>
      <p><?php echo $type ?></p>
      </input></td>
    <?php

				if (($j % 4)==0)

					echo "</tr>"; $j++;

				$i++; 

				$j++;

				$checked = '';

			}

	?>
</table>
<?php		

}		

function my_currency_converter($args) 

     {

	// Get values 

      	extract($args);

      	$options = get_option('currency_converter');

      	// Extract value from vars

      	$currency_code = htmlspecialchars($options['currency_code'], ENT_QUOTES);

		$currency_name = htmlspecialchars($options['currency_name'], ENT_QUOTES);

		$title = $currency_name;

      	$country_code = htmlspecialchars($options['country_code'], ENT_QUOTES);

      	$length = htmlspecialchars($options['length'], ENT_QUOTES);

      	$layout = htmlspecialchars($options['layout'], ENT_QUOTES);

      	$length = htmlspecialchars($options['length'], ENT_QUOTES);

      	$width = htmlspecialchars($options['width'], ENT_QUOTES);

      	$default_amount = htmlspecialchars($options['default_amount'], ENT_QUOTES);

      	$default_from = htmlspecialchars($options['default_from'], ENT_QUOTES);

      	$default_to = htmlspecialchars($options['default_to'], ENT_QUOTES);

      	$text_color = htmlspecialchars($options['text_color'], ENT_QUOTES);

      	$border_color = htmlspecialchars($options['border_color'], ENT_QUOTES);

      	$background_color = htmlspecialchars($options['background_color'], ENT_QUOTES);

      	$transparentflag = htmlspecialchars($options['transparentflag'], ENT_QUOTES);

	if($transparentflag == "1"){

  	     $background_color ="";

  	     $border_color ="";

	}
	if($currency_code)

		$length = "medium";

	$text_color = str_replace("#","",$text_color);

	// Output calculator
	$widget_call_string = 'http://fx-rate.net/wp_converter.php?';
	if($currency_code) $widget_call_string .= 'currency='.$currency_code ."&";
	$widget_call_string .="size=". $length;
	$widget_call_string .="&layout=". $layout;
	$widget_call_string .="&amount=". $default_amount;
	$widget_call_string .="&tcolor=". $text_color;
	$widget_call_string .="&default_pair=". $default_from . "/" . $default_to;
	$country_code = strtolower($country_code);
	$image_url = 'http://fx-rate.net/images/countries/'.$country_code.'.png';
       $calc_label= strtoupper(substr($layout,0,1));
       if($length == "short") $calc_label .= "S";
	if($currency_code){
		$target_url= "http://fx-rate.net/$currency_code/";
		$flag_string = '<img style="margin:0;padding:0;border:0;" src="http://fx-rate.net/images/countries/'.$country_code.'.png" border=0 >&nbsp;<b>';
		$flag_string2 = '</b>';
		$title = UCWords($currency_name) . " Converter";
		$calc_label .=  "1";
	}
	else{
		$target_url= "http://fx-rate.net/";
		$title = "Currency Converter";
	}
	$tsize=12;
	if($layout == "vertical" && $length =="short") $tsize = 10;
	echo '
	<div  style="width:'.$width.'px;border:2px solid #888;text-align:center;margin: 0px; padding: 0px;margin-top:10px!important">';
	echo '<div style="margin: 0px; padding: 0px;text-align:center;align:center;background-color:'.$border_color. ';border-bottom:1px solid #888;width:100%">';
	echo $flag_string;
	echo $title.$flag_string2;
     	echo'<script type="text/javascript" src="'.$widget_call_string.'"></script></div><!-end of code-->';
}
