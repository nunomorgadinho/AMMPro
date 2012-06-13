<?php if ( is_user_logged_in() ) { ?>
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
        <div>
            <label>Email Address</label>
            <input name="" type="text">
        </div>
        <div>
            <label>Cardholder's Name</label>
            <input name="" type="text">
        </div>
        <div>
            <label>Card Number</label>
            <input name="" type="text">
        </div>
        <div class="expired">
            <label>Experation Date</label>
            <input name="" type="text" width="50px">
            <input name="" type="text" width="50px">
        </div>
        <div>
            <label>Zip Code</label>
            <input name="" type="text" class="zipcode">
        </div>
        <div>
            <label>Country</label>
            <select name="address" id="address-country">
            <option value="US" label="United States">United States</option>
            <option value="AF" label="Afghanistan">Afghanistan</option>
            <option value="AX" label="Aland Islands">Aland Islands</option>
            <option value="AL" label="Albania">Albania</option>
            <option value="DZ" label="Algeria">Algeria</option>
            <option value="AS" label="American Somoa">American Somoa</option>
            <option value="AD" label="Andorra">Andorra</option>
            <option value="AO" label="Angola">Angola</option>
            <option value="AI" label="Anguilla">Anguilla</option>
            <option value="AQ" label="Antarctica">Antarctica</option>
            <option value="AG" label="Antigua and Barbuda">Antigua and Barbuda</option>
            <option value="AR" label="Argentina">Argentina</option>
            <option value="AM" label="Armenia">Armenia</option>
            <option value="AW" label="Aruba">Aruba</option>
            <option value="AU" label="Australia">Australia</option>
            <option value="AT" label="Austria">Austria</option>
            <option value="AZ" label="Azerbaijan">Azerbaijan</option>
            <option value="BS" label="Bahamas">Bahamas</option>
            <option value="BH" label="Bahrain">Bahrain</option>
            <option value="BD" label="Bangladesh">Bangladesh</option>
            <option value="BB" label="Barbados">Barbados</option>
            <option value="BY" label="Belarus">Belarus</option>
            <option value="BE" label="Belgium">Belgium</option>
            <option value="BZ" label="Belize">Belize</option>
            <option value="BJ" label="Benin">Benin</option>
            <option value="BM" label="Bermuda">Bermuda</option>
            <option value="BT" label="Bhutan">Bhutan</option>
            <option value="BO" label="Bolivia">Bolivia</option>
            <option value="BA" label="Bosnia and Herzegovina">Bosnia and Herzegovina</option>
            <option value="BW" label="Botswana">Botswana</option>
            <option value="BV" label="Bouvet Island">Bouvet Island</option>
            <option value="BR" label="Brazil">Brazil</option>
            <option value="IO" label="British Indian Ocean Territory">British Indian Ocean Territory</option>
            <option value="BN" label="Brunei Barussalam">Brunei Barussalam</option>
            <option value="BG" label="Bulgaria">Bulgaria</option>
            <option value="BF" label="Burkina Faso">Burkina Faso</option>
            <option value="BI" label="Burundi">Burundi</option>
            <option value="KH" label="Cambodia">Cambodia</option>
            <option value="CM" label="Cameroon">Cameroon</option>
            <option value="CA" label="Canada">Canada</option>
            <option value="CV" label="Cape Verde">Cape Verde</option>
            <option value="KY" label="Cayman Islands">Cayman Islands</option>
            <option value="CF" label="Central African Republic">Central African Republic</option>
            <option value="TD" label="Chad">Chad</option>
            <option value="CL" label="Chile">Chile</option>
            <option value="CN" label="China">China</option>
            <option value="CX" label="Christmas Island">Christmas Island</option>
            <option value="CC" label="Cocos (Keeling) Island">Cocos (Keeling) Island</option>
            <option value="CO" label="Colombia">Colombia</option>
            <option value="KM" label="Comoros">Comoros</option>
            <option value="CG" label="Congo">Congo</option>
            <option value="CD" label="Congo, Democratic Republic">Congo, Democratic Republic</option>
            <option value="CK" label="Cook Islands">Cook Islands</option>
            <option value="CR" label="Costa Rica">Costa Rica</option>
            <option value="CI" label="Cote d'Ivoire">Cote d'Ivoire</option>
            <option value="HR" label="Croatia">Croatia</option>
            <option value="CU" label="Cuba">Cuba</option>
            <option value="CY" label="Cyprus">Cyprus</option>
            <option value="CZ" label="Czech Republic">Czech Republic</option>
            <option value="DK" label="Denmark">Denmark</option>
            <option value="DJ" label="Djibouti">Djibouti</option>
            <option value="DM" label="Dominica">Dominica</option>
            <option value="DO" label="Dominican Republic">Dominican Republic</option>
            <option value="EC" label="Ecuador">Ecuador</option>
            <option value="EG" label="Egypt">Egypt</option>
            <option value="SV" label="El Salvador">El Salvador</option>
            <option value="GQ" label="Equatorial Guinea">Equatorial Guinea</option>
            <option value="ER" label="Eritrea">Eritrea</option>
            <option value="EE" label="Estonia">Estonia</option>
            <option value="ET" label="Ethopia">Ethopia</option>
            <option value="FK" label="Falkland Islands (Malvinas)">Falkland Islands (Malvinas)</option>
            <option value="FO" label="Faroe Islands">Faroe Islands</option>
            <option value="FJ" label="Fiji">Fiji</option>
            <option value="FI" label="Finland">Finland</option>
            <option value="FR" label="France">France</option>
            <option value="GF" label="French Guiana">French Guiana</option>
            <option value="PF" label="French Polynesia">French Polynesia</option>
            <option value="TF" label="French Southern Territory">French Southern Territory</option>
            <option value="GA" label="Gabon">Gabon</option>
            <option value="GM" label="Gambia">Gambia</option>
            <option value="GE" label="Georgia">Georgia</option>
            <option value="DE" label="Germany">Germany</option>
            <option value="GH" label="Ghana">Ghana</option>
            <option value="GI" label="Gibraltar">Gibraltar</option>
            <option value="GR" label="Greece">Greece</option>
            <option value="GL" label="Greenland">Greenland</option>
            <option value="GD" label="Grenada">Grenada</option>
            <option value="GP" label="Guadaloupe">Guadaloupe</option>
            <option value="GU" label="Guam">Guam</option>
            <option value="GT" label="Guatemala">Guatemala</option>
            <option value="GG" label="Guernsey">Guernsey</option>
            <option value="GN" label="Guinea">Guinea</option>
            <option value="GW" label="Guinea-Bissau">Guinea-Bissau</option>
            <option value="GY" label="Guyana">Guyana</option>
            <option value="HT" label="Haiti">Haiti</option>
            <option value="HM" label="Heard and McDonald Islands">Heard and McDonald Islands</option>
            <option value="VA" label="Holy See (Vatican City State)">Holy See (Vatican City State)</option>
            <option value="HN" label="Honduras">Honduras</option>
            <option value="HK" label="Hong Kong">Hong Kong</option>
            <option value="HU" label="Hungary">Hungary</option>
            <option value="IS" label="Iceland">Iceland</option>
            <option value="IN" label="India">India</option>
            <option value="ID" label="Indonesia">Indonesia</option>
            <option value="IR" label="Iran, Islamic Republic of">Iran, Islamic Republic of</option>
            <option value="IQ" label="Iraq">Iraq</option>
            <option value="IE" label="Ireland">Ireland</option>
            <option value="IM" label="Israel">Israel</option>
            <option value="IL" label="Isle of Man">Isle of Man</option>
            <option value="IT" label="Italy">Italy</option>
            <option value="JM" label="Jamaica">Jamaica</option>
            <option value="JP" label="Japan">Japan</option>
            <option value="JE" label="Jersey">Jersey</option>
            <option value="JO" label="Jordan">Jordan</option>
            <option value="KZ" label="Kazakhstan">Kazakhstan</option>
            <option value="KE" label="Kenya">Kenya</option>
            <option value="KI" label="Kiribati">Kiribati</option>
            <option value="KP" label="Korea, Democratic People's Republic">Korea, Democratic People's Republic</option>
            <option value="KR" label="Korea, Republic of">Korea, Republic of</option>
            <option value="KW" label="Kuwait">Kuwait</option>
            <option value="KG" label="Kyrgyzstan">Kyrgyzstan</option>
            <option value="LA" label="Lao People's Democratic Republic">Lao People's Democratic Republic</option>
            <option value="LV" label="Latvia">Latvia</option>
            <option value="LB" label="Lebanon">Lebanon</option>
            <option value="LS" label="Lesotho">Lesotho</option>
            <option value="LR" label="Liberia">Liberia</option>
            <option value="LY" label="Libya">Libya</option>
            <option value="LI" label="Liechtenstein">Liechtenstein</option>
            <option value="LT" label="Lithuania">Lithuania</option>
            <option value="LU" label="Luxembourg">Luxembourg</option>
            <option value="MO" label="Macao">Macao</option>
            <option value="MK" label="Macedonia, the Former Yugoslav Republic of">Macedonia, the Former Yugoslav Republic of</option>
            <option value="MG" label="Madagascar">Madagascar</option>
            <option value="MW" label="Malawi">Malawi</option>
            <option value="MY" label="Malaysia">Malaysia</option>
            <option value="MV" label="Maldives">Maldives</option>
            <option value="ML" label="Mali">Mali</option>
            <option value="MT" label="Malta">Malta</option>
            <option value="MH" label="Marshall Islands">Marshall Islands</option>
            <option value="MQ" label="Martinique">Martinique</option>
            <option value="MR" label="Mauritania">Mauritania</option>
            <option value="MU" label="Mauritius">Mauritius</option>
            <option value="YT" label="Mayotte">Mayotte</option>
            <option value="MX" label="Mexico">Mexico</option>
            <option value="FM" label="Micronesia, Federated Republic of">Micronesia, Federated Republic of</option>
            <option value="MD" label="Moldova, Republic of">Moldova, Republic of</option>
            <option value="MC" label="Monaco">Monaco</option>
            <option value="MN" label="Mongolia">Mongolia</option>
            <option value="ME" label="Montenegro">Montenegro</option>
            <option value="MS" label="Montserrat">Montserrat</option>
            <option value="MA" label="Morocco">Morocco</option>
            <option value="MZ" label="Mozambique">Mozambique</option>
            <option value="MM" label="Myanmar">Myanmar</option>
            <option value="NA" label="Namibia">Namibia</option>
            <option value="NR" label="Nauru">Nauru</option>
            <option value="NP" label="Nepal">Nepal</option>
            <option value="NL" label="Netherlands">Netherlands</option>
            <option value="AN" label="Netherlands Antilles">Netherlands Antilles</option>
            <option value="NC" label="New Caledonia">New Caledonia</option>
            <option value="NZ" label="New Zealand">New Zealand</option>
            <option value="NI" label="Nicaragua">Nicaragua</option>
            <option value="NE" label="Niger">Niger</option>
            <option value="NG" label="Nigeria">Nigeria</option>
            <option value="NU" label="Niue">Niue</option>
            <option value="NF" label="Norfolk Island">Norfolk Island</option>
            <option value="MP" label="Nothern Mariana Islands">Nothern Mariana Islands</option>
            <option value="NO" label="Norway">Norway</option>
            <option value="OM" label="Oman">Oman</option>
            <option value="PK" label="Pakistan">Pakistan</option>
            <option value="PW" label="Palau">Palau</option>
            <option value="PS" label="Palestinian Territory, Occupied">Palestinian Territory, Occupied</option>
            <option value="PA" label="Panama">Panama</option>
            <option value="PG" label="Papua New Guinea">Papua New Guinea</option>
            <option value="PY" label="Paraguay">Paraguay</option>
            <option value="PE" label="Peru">Peru</option>
            <option value="PH" label="Philippines">Philippines</option>
            <option value="PN" label="Pitcairn">Pitcairn</option>
            <option value="PL" label="Poland">Poland</option>
            <option value="PT" label="Portugal">Portugal</option>
            <option value="PR" label="Puerto Rico">Puerto Rico</option>
            <option value="QA" label="Qatar">Qatar</option>
            <option value="RE" label="Reunion">Reunion</option>
            <option value="RO" label="Romania">Romania</option>
            <option value="RU" label="Russian Federation">Russian Federation</option>
            <option value="RW" label="Rwanda">Rwanda</option>
            <option value="BL" label="Saint Barthelemy">Saint Barthelemy</option>
            <option value="SH" label="Saint Helena">Saint Helena</option>
            <option value="KN" label="Saint Kitts and Nevis">Saint Kitts and Nevis</option>
            <option value="LC" label="Saint Lucia">Saint Lucia</option>
            <option value="MF" label="Saint Martin (French Part)">Saint Martin (French Part)</option>
            <option value="PM" label="Saint Pierre and Miquelon">Saint Pierre and Miquelon</option>
            <option value="VC" label="Saint Vincent and the Grendines">Saint Vincent and the Grendines</option>
            <option value="WS" label="Samoa">Samoa</option>
            <option value="SM" label="San Marino">San Marino</option>
            <option value="ST" label="Sao Tome and Principal">Sao Tome and Principal</option>
            <option value="SA" label="Saudi Arabia">Saudi Arabia</option>
            <option value="SN" label="Senegal">Senegal</option>
            <option value="RS" label="Serbia">Serbia</option>
            <option value="SC" label="Seychelles">Seychelles</option>
            <option value="SL" label="Sierra Leone">Sierra Leone</option>
            <option value="SG" label="Singapore">Singapore</option>
            <option value="SK" label="Slovakia">Slovakia</option>
            <option value="SI" label="Slovenia">Slovenia</option>
            <option value="SB" label="Solomon Islands">Solomon Islands</option>
            <option value="SO" label="Somalia">Somalia</option>
            <option value="ZA" label="South Africa">South Africa</option>
            <option value="GS" label="South Georgia and the South Sandwich Isands">South Georgia and the South Sandwich Isands</option>
            <option value="ES" label="Spain">Spain</option>
            <option value="LK" label="Sudan">Sudan</option>
            <option value="SD" label="Sri Lanka">Sri Lanka</option>
            <option value="SR" label="Suriname">Suriname</option>
            <option value="SJ" label="Svalbard and Jan Mayen">Svalbard and Jan Mayen</option>
            <option value="SZ" label="Swaziland">Swaziland</option>
            <option value="SY" label="Syrian Arab Republic">Syrian Arab Republic</option>
            <option value="SE" label="Sweden">Sweden</option>
            <option value="CH" label="Switzerland">Switzerland</option>
            <option value="TW" label="Taiwan">Taiwan</option>
            <option value="TJ" label="Tajikistan">Tajikistan</option>
            <option value="TZ" label="Tanzania, United Republic of">Tanzania, United Republic of</option>
            <option value="TH" label="Thailand">Thailand</option>
            <option value="TL" label="Timor-Leste">Timor-Leste</option>
            <option value="TG" label="Togo">Togo</option>
            <option value="TK" label="Tokelau">Tokelau</option>
            <option value="TO" label="Tonga">Tonga</option>
            <option value="TT" label="Trinidad and Tobago">Trinidad and Tobago</option>
            <option value="TN" label="Tunisia">Tunisia</option>
            <option value="TR" label="Turkey">Turkey</option>
            <option value="TM" label="Turkmenistan">Turkmenistan</option>
            <option value="TC" label="Turks and Caicos Islands">Turks and Caicos Islands</option>
            <option value="TV" label="Tuvalu">Tuvalu</option>
            <option value="UG" label="Uganda">Uganda</option>
            <option value="UA" label="Ukraine">Ukraine</option>
            <option value="AE" label="United Arab Emirates">United Arab Emirates</option>
            <option value="GB" label="United Kingdom">United Kingdom</option>
            <option value="UM" label="United States Minor Outlying Islands">United States Minor Outlying Islands</option>
            <option value="UY" label="Uruguay">Uruguay</option>
            <option value="UZ" label="Uzbekistan">Uzbekistan</option>
            <option value="VU" label="Vanuatu">Vanuatu</option>
            <option value="VE" label="Venezuela">Venezuela</option>
            <option value="VN" label="Viet Nam">Viet Nam</option>
            <option value="VG" label="Virgin Islands, British">Virgin Islands, British</option>
            <option value="VI" label="Virgin Islands, U.S.">Virgin Islands, U.S.</option>
            <option value="WF" label="Wallis and Futuna Islands">Wallis and Futuna Islands</option>
            <option value="EH" label="Western Sahara">Western Sahara</option>
            <option value="YE" label="Yemen">Yemen</option>
            <option value="ZM" label="Zambia">Zambia</option>
            <option value="ZW" label="Zimbabwe">Zimbabwe</option>
</select>
        </div>
  </aside>  
  </section>
  <section class="businessDir">
    	<p class="left">By clicking Create my account you agree to the Terms of Service, Privacy, and Refund policies.</p>
        <a href="" class="btn right btn6">Create my Account</a>
  </section>
</section>
<?php get_footer() ?>
<?php } ?>