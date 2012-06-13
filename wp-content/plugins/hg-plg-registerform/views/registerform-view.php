
<h2> <span>pro. </span>Sign Up<a id="signup"></a> </h2>
<section class="signup"> 

<article class="left">
	<h4>Professional + Productivity</h4>
	<p>Art Market Monitor Pro uses a 1 click sign up system to make it easy
		to subscribe to our news service. Try it free for 7 days once you have
		signed up. If you decide to cancel just log-in to your account home and
		unsubscribe. If you decide you want to cancel after the 7 days we will
		refund your pro-rated balance.</p>
	<p>We ask for credit card information before you begin your trial so you
		can immediately see what we have to offer. No pre-qualifications over
		the phone, no hidden fees.</p>
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
	<div id="result"></div>
	<form onsubmit="log_paypal_buynow(this)" target="paypal" action="https://www.paypal.com/cgi-bin/webscr" id="wp_signup_form" method="post">
		<div>
			<label>Email Address</label> <input name="email" type="text">
		</div>
		<div>
			<label>Cardholder's Name</label> <input name="name" type="text">
		</div>
		<div>
			<label>Card Number</label> <input name="cc-number" type="text">
		</div>
		<div class="expired">
			<label>Expiration Date</label> <select name="cc-exp-month" width="50" style="width: 50px">
				<?php for ($i=1; $i<13; $i++) echo '<option value="'.$i.'" label="'.$i.'">'.$i.'</option>'; ?>
			</select> <select name="cc-exp-year" width="60" style="width: 60px">
			<?php
				$year=date('Y')
				for ($i=0; $i<10; $i++) echo '<option value="'.$year.'" label="'.$year.'">'.$year++.'</option>';
			?>
			</select>
		</div>
		<div>
			<label>Zip Code</label> <input name="zipcode" class="zipcode" type="text">
		</div>
		<div>
			<label>Country</label> <select name="address" id="address-country">
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
	</form>
	</aside> 
</section>
<section class="businessDir">
<?php 
	//Check whether user registration is enabled by the administrator
	if(get_option('users_can_register')) { 
?>
    	<p class="left">By clicking Create my account you agree to the Terms of Service, Privacy, and Refund policies.</p>
        <a href="" id="submitbtn" class="btn right btn6" >Create my Account</a>
<!--       
		<form onsubmit="log_paypal_buynow(this)" target="paypal" action="https://www.paypal.com/cgi-bin/webscr" method="post">
			<input name="business" value="ana@widgilabs.com" type="hidden">
			<input name="cmd" value="_xclick" type="hidden">
			<input name="item_name" value="Basic Subscription" type="hidden">
			<input id="item_number" name="item_number" value="9" type="hidden">
			<input id="amount" name="amount" value="0.5" type="hidden">
			<input id="unit" name="unit" value="0.5" type="hidden">
			<input id="shipping" name="ship11" value="0" type="hidden">
			<input name="handling" value="0" type="hidden">
			<input name="currency_code" value="EUR" type="hidden">
			<input name="undefined_quantity" value="0" type="hidden">
			<input name="submit" src="https://www.paypal.com/en_US/i/btn/btn_buynow_LG.gif" alt="PayPal - The safer, easier way to pay online" border="0" type="image">
			<img alt="" src="https://www.paypal.com/en_US/i/scr/pixel.gif" height="1" border="0" width="1">
		</form>
 -->          
        
        
<?php
		//echo $botao;
	//echo do_shortcode( '[buy_now_button product_id=\'9\']' );
	} 
	else 
	    echo '<p class="right">Registration is currently disabled. Please try again later.</p>';
?>
  </section>
</section>




<!--
<div id="result"></div>
 To hold validation results 
<form action="" method="post">
	<label>Username</label> <input type="text" name="username" class="text" value="" /><br /> 
	<label>Email address</label> <input type="text" name="email" class="text" value="" /> <br /> 
	<input type="submit" id="submitbtn" name="submit" value="SignUp" />
</form>
-->
<script type="text/javascript">  
    //<![CDATA[ 
    $("#submitbtn").click(function() { 
        var $ = jQuery;
     
	    $('#result').html('<img src="<?php bloginfo('template_url') ?>/images/loader.gif" class="loader" />').fadeIn(); 
	    var input_data = $('#wp_signup_form').serialize(); 
	    $.ajax({ 
		    type: "POST", 
		    url:  "<?php echo bloginfo('wpurl'); ?>/wp-content/plugins/<?php echo self::INSTALL_FOLDER_NAME; ?>/php/regform-ajax.php", 
		    data: input_data, 
		    success: function(msg){ 
			    $('.loader').remove(); 

				brokenstring=msg.split("|");
				error=parseInt(brokenstring.shift());
				
				$('<div>').html(brokenstring[0]).appendTo('#result').hide().fadeIn('slow'); //show message
				/*
				if (error==0){ // redirect to paypal
					popitup("https://www.paypal.com");
				}
				*/
			     
		    } 
	    }); 
	    return false; 
     
    }); 

    function popitup(url) {
    	newwindow=window.open(url,'name','height=900,width=800');
    	if (window.focus) {newwindow.focus()}
    	return false;
    }

    

    //]]>  
</script>

